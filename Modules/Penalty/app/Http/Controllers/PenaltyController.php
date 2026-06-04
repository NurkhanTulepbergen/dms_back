<?php

namespace Modules\Penalty\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Penalty\Models\Penalty;
use Modules\Penalty\Models\PenaltyRedemption;
use Modules\Penalty\Models\PenaltyRule;
use Modules\Settlement\Models\Settlement;
use Modules\User\Models\User;
use Modules\Penalty\Http\Requests\CancelPenaltyRequest;
use Modules\Penalty\Http\Requests\StorePenaltyRedemptionRequest;
use Modules\Penalty\Http\Requests\StorePenaltyRequest;
use Modules\Penalty\Services\PenaltyService;
use Modules\Penalty\Services\RedemptionService;
use Throwable;

class PenaltyController extends Controller
{
    private array $activePenaltyPointsByUserId = [];

    public function __construct(
        private readonly PenaltyService $penaltyService,
        private readonly RedemptionService $redemptionService,
    ) {}

    public function index(Request $request)
    {
        $penalties = $this->penaltyService->getUserPenalties((int) $request->user()->id);

        return result($penalties, 200, 'Штрафы пользователя');
    }

    public function show(Request $request, int $id)
    {
        $penalty = $this->penaltyService->getUserPenaltyById((int) $request->user()->id, $id);

        return result($penalty, 200, 'Штраф');
    }

    public function manageIndex(Request $request)
    {
        $penalties = $this->penaltyService->getManagePenalties([
            'search' => $request->query('search'),
            'status' => $request->query('status'),
            'redemption_status' => $request->query('redemption_status'),
        ]);

        return result(
            $penalties->map(fn (Penalty $penalty) => $this->serializeManagePenalty($penalty))->values(),
            200,
            'Штрафы общежития'
        );
    }

    public function rules()
    {
        $rules = $this->penaltyService->getPenaltyRules();

        return result(
            $rules->map(fn (PenaltyRule $rule) => $this->serializeRule($rule))->values(),
            200,
            'Правила штрафов'
        );
    }

    public function targets(Request $request)
    {
        $limit = max(1, min((int) $request->query('limit', 50), 100));
        $targets = $this->penaltyService->getPenaltyTargets(
            $request->query('search'),
            $limit,
        );

        return result(
            $targets->map(fn (Settlement $settlement) => $this->serializeTarget($settlement))->values(),
            200,
            'Студенты для штрафов'
        );
    }

    public function rooms(Request $request)
    {
        $limit = max(1, min((int) $request->query('limit', 50), 100));
        $roomTargets = $this->penaltyService->getPenaltyRoomTargets(
            $request->query('search'),
            $limit,
        );

        return result(
            $roomTargets
                ->map(function ($settlements, $roomId) {
                    $firstSettlement = $settlements->first();

                    return [
                        'room_id' => (int) $roomId,
                        'room' => $this->serializeRoomNumber($firstSettlement?->room?->room_number),
                        'active_residents_count' => $settlements->count(),
                        'residents' => $settlements
                            ->map(fn (Settlement $settlement) => $this->serializeUser($settlement->user))
                            ->filter()
                            ->values()
                            ->all(),
                    ];
                })
                ->values(),
            200,
            'Комнаты для штрафов'
        );
    }

    public function store(StorePenaltyRequest $request)
    {
        $validated = $request->validated();
        $storedEvidencePaths = [];

        try {
            foreach ($request->file('evidences', []) ?? [] as $file) {
                $storedEvidencePaths[] = $file->store('penalties/evidences', 'public');
            }

            $payload = $this->penaltyService->createPenalty(
                array_merge($validated, [
                    'evidences' => array_values(array_filter([
                        ...($validated['evidence_paths'] ?? []),
                        ...$storedEvidencePaths,
                    ])),
                ]),
                (int) $request->user()->id,
            );
        } catch (Throwable $exception) {
            if ($storedEvidencePaths !== []) {
                Storage::disk('public')->delete($storedEvidencePaths);
            }

            throw $exception;
        }

        $message = ! empty($validated['room_id'])
            ? 'Штрафы начислены всем студентам комнаты'
            : 'Штраф создан';

        return result($payload, 201, $message);
    }

    public function cancel(CancelPenaltyRequest $request, int $id)
    {
        $penalty = $this->penaltyService->cancelPenalty(
            $id,
            $request->validated()['description'] ?? null,
        );

        return result($penalty, 200, 'Штраф отменен');
    }

    public function redeem(StorePenaltyRedemptionRequest $request, int $id)
    {
        $redemption = $this->redemptionService->createRedemption(
            $id,
            (int) $request->user()->id,
            $request->validated(),
        );

        return result($redemption, 201, 'Заявка на отработку отправлена');
    }

    private function serializeManagePenalty(Penalty $penalty): array
    {
        $redemptions = $penalty->redemptions->sortByDesc('id')->values();
        $pendingRedemption = $redemptions->first(fn (PenaltyRedemption $redemption) => $redemption->status === 'pending');
        $latestRedemption = $redemptions->first();

        return [
            'id' => $penalty->id,
            'status' => $penalty->status,
            'points' => (int) $penalty->points,
            'description' => $penalty->description,
            'created_at' => $penalty->created_at?->toIso8601String(),
            'updated_at' => $penalty->updated_at?->toIso8601String(),
            'student' => $this->serializeUser($penalty->user),
            'created_by' => $this->serializeUser($penalty->creator),
            'room' => $this->serializeRoomNumber($penalty->settlement?->room?->room_number),
            'rule' => $this->serializeRule($penalty->rule),
            'evidences' => $penalty->evidences
                ->map(fn ($evidence) => $this->serializeEvidence($evidence->id, $evidence->file_path))
                ->values()
                ->all(),
            'pending_redemption' => $this->serializeRedemption($pendingRedemption),
            'latest_redemption_status' => $latestRedemption?->status,
            'redemptions_count' => $redemptions->count(),
            'pending_redemptions_count' => $redemptions->where('status', 'pending')->count(),
            'discipline' => $this->serializeDiscipline($penalty),
        ];
    }

    private function serializeRule(?PenaltyRule $rule): ?array
    {
        if ($rule === null) {
            return null;
        }

        return [
            'id' => $rule->id,
            'code' => $rule->code,
            'title' => $rule->title,
            'default_points' => (int) $rule->default_points,
            'redeemable' => (bool) $rule->redeemable,
            'creates_financial_charge' => (bool) $rule->creates_financial_charge,
            'financial_amount' => $rule->financial_amount,
        ];
    }

    private function serializeTarget(Settlement $settlement): array
    {
        return [
            'settlement_id' => $settlement->id,
            'user' => $this->serializeUser($settlement->user),
            'room' => $this->serializeRoomNumber($settlement->room?->room_number),
        ];
    }

    private function serializeUser(?User $user): ?array
    {
        if ($user === null) {
            return null;
        }

        return [
            'id' => $user->id,
            'full_name' => trim(implode(' ', array_filter([
                $user->lastname,
                $user->name,
                $user->middlename,
            ]))),
            'email' => $user->email,
            'uni_id' => $user->uni_id,
            'role' => $user->role,
            'discipline_limit' => (int) ($user->discipline_limit ?? 0),
        ];
    }

    private function serializeDiscipline(Penalty $penalty): array
    {
        $userId = (int) $penalty->user_id;
        $disciplineLimit = (int) ($penalty->user?->discipline_limit ?? 0);
        $activePoints = $this->activePenaltyPoints($userId);

        return [
            'active_points' => $activePoints,
            'discipline_limit' => $disciplineLimit,
            'limit_reached' => $disciplineLimit > 0 && $activePoints >= $disciplineLimit,
        ];
    }

    private function activePenaltyPoints(int $userId): int
    {
        if (! array_key_exists($userId, $this->activePenaltyPointsByUserId)) {
            $this->activePenaltyPointsByUserId[$userId] = (int) Penalty::query()
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->sum('points');
        }

        return $this->activePenaltyPointsByUserId[$userId];
    }

    private function serializeRedemption(?PenaltyRedemption $redemption): ?array
    {
        if ($redemption === null) {
            return null;
        }

        return [
            'id' => $redemption->id,
            'status' => $redemption->status,
            'event_type' => $redemption->event_type,
            'description' => $redemption->description,
            'file_path' => $redemption->file_path,
            'created_at' => $redemption->created_at?->toIso8601String(),
            'reviewed_at' => $redemption->reviewed_at?->toIso8601String(),
            'user' => $this->serializeUser($redemption->user),
            'reviewer' => $this->serializeUser($redemption->reviewer),
        ];
    }

    private function serializeEvidence(int $id, ?string $filePath): array
    {
        return [
            'id' => $id,
            'file_path' => $filePath,
            'url' => $this->buildFileUrl($filePath),
        ];
    }

    private function buildFileUrl(?string $filePath): ?string
    {
        if ($filePath === null || $filePath === '') {
            return null;
        }

        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return $filePath;
        }

        return Storage::disk('public')->url($filePath);
    }

    private function serializeRoomNumber(mixed $roomNumber): array
    {
        if ($roomNumber === null || $roomNumber === '') {
            return [
                'room_number' => null,
                'label' => 'Комната не указана',
            ];
        }

        return [
            'room_number' => (string) $roomNumber,
            'label' => 'Комната '.$roomNumber,
        ];
    }
}
