<?php

namespace Modules\Requests\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Requests\Models\RepairRequest;
use Modules\Requests\Services\RepairRequestService;
use Modules\User\Models\User;
use Throwable;

class RepairRequestController extends Controller
{
    public function __construct(
        private readonly RepairRequestService $repairRequestService,
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:plumbing,electricity,furniture,heating,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'file|image|max:5120',
        ]);

        $storedPaths = [];

        try {
            foreach ($request->file('photos', []) ?? [] as $file) {
                $storedPaths[] = $file->store('repair-requests', 'public');
            }

            $repairRequest = $this->repairRequestService->create(
                (int) $request->user()->id,
                $validated,
                $storedPaths,
            );
        } catch (Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('public')->delete($storedPaths);
            }

            throw $exception;
        }

        return result($this->serializeRepairRequest($repairRequest), 201, 'Заявка на ремонт отправлена');
    }

    public function mine(Request $request)
    {
        $requests = RepairRequest::query()
            ->with(['student', 'room.floor.building', 'attachments', 'handledBy'])
            ->where('user_id', (int) $request->user()->id)
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'in_progress' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->get();

        return result(
            $requests->map(fn (RepairRequest $repairRequest) => $this->serializeRepairRequest($repairRequest))->values(),
            200,
            'Мои заявки на ремонт'
        );
    }

    public function index()
    {
        $requests = RepairRequest::query()
            ->with(['student', 'room.floor.building', 'attachments', 'handledBy'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'in_progress' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->get();

        return result(
            $requests->map(fn (RepairRequest $repairRequest) => $this->serializeRepairRequest($repairRequest))->values(),
            200,
            'Заявки на ремонт'
        );
    }

    public function start(int $id)
    {
        $repairRequest = $this->repairRequestService->start($id, (int) Auth::id());

        return result($this->serializeRepairRequest($repairRequest), 200, 'Заявка взята в работу');
    }

    public function resolve(Request $request, int $id)
    {
        $validated = $request->validate([
            'employee_comment' => 'nullable|string|max:2000',
        ]);

        $repairRequest = $this->repairRequestService->resolve(
            $id,
            (int) Auth::id(),
            $validated['employee_comment'] ?? null,
        );

        return result($this->serializeRepairRequest($repairRequest), 200, 'Заявка отмечена как выполненная');
    }

    private function serializeRepairRequest(RepairRequest $repairRequest): array
    {
        return [
            'id' => $repairRequest->id,
            'status' => $repairRequest->status,
            'category' => $repairRequest->category,
            'title' => $repairRequest->title,
            'description' => $repairRequest->description,
            'employee_comment' => $repairRequest->employee_comment,
            'created_at' => $repairRequest->created_at?->toIso8601String(),
            'updated_at' => $repairRequest->updated_at?->toIso8601String(),
            'started_at' => $repairRequest->started_at?->toIso8601String(),
            'resolved_at' => $repairRequest->resolved_at?->toIso8601String(),
            'student' => $this->serializeUser($repairRequest->student),
            'handled_by' => $this->serializeUser($repairRequest->handledBy),
            'room' => [
                'id' => $repairRequest->room?->id,
                'room_number' => $repairRequest->room?->room_number,
                'capacity' => $repairRequest->room?->capacity,
                'floor' => [
                    'id' => $repairRequest->room?->floor?->id,
                    'floor_number' => $repairRequest->room?->floor?->floor_number,
                    'building' => [
                        'id' => $repairRequest->room?->floor?->building?->id,
                        'name' => $repairRequest->room?->floor?->building?->name,
                        'address' => $repairRequest->room?->floor?->building?->address,
                    ],
                ],
            ],
            'attachments' => $repairRequest->attachments
                ->map(fn ($attachment) => [
                    'id' => $attachment->id,
                    'file_path' => $attachment->file_path,
                    'url' => $this->buildFileUrl($attachment->file_path),
                ])
                ->values()
                ->all(),
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
}
