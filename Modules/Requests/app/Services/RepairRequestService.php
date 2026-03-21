<?php

namespace Modules\Requests\Services;

use App\Exceptions\BusinessException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Requests\Models\RepairRequest;
use Modules\Requests\Models\RepairRequestAttachment;
use Modules\Settlement\Services\SettlementService;

class RepairRequestService
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';

    public function __construct(
        private readonly SettlementService $settlementService,
    ) {}

    public function create(int $userId, array $data, array $attachmentPaths = []): RepairRequest
    {
        $settlement = $this->settlementService->getActiveSettlement($userId, ['room.floor.building']);

        if ($settlement === null) {
            throw new BusinessException('Подать заявку на ремонт можно только при активном заселении', 422);
        }

        return DB::transaction(function () use ($userId, $data, $attachmentPaths, $settlement) {
            $repairRequest = RepairRequest::query()->create([
                'user_id' => $userId,
                'room_id' => (int) $settlement->room_id,
                'category' => $data['category'],
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => self::STATUS_PENDING,
            ]);

            foreach ($attachmentPaths as $path) {
                RepairRequestAttachment::query()->create([
                    'repair_request_id' => $repairRequest->id,
                    'file_path' => $path,
                ]);
            }

            return $repairRequest->fresh(['student', 'room.floor.building', 'attachments', 'handledBy']);
        });
    }

    public function start(int $requestId, int $employeeId): RepairRequest
    {
        return DB::transaction(function () use ($requestId, $employeeId) {
            /** @var RepairRequest $repairRequest */
            $repairRequest = RepairRequest::query()->lockForUpdate()->findOrFail($requestId);

            if ($repairRequest->status === self::STATUS_RESOLVED) {
                throw new BusinessException('Заявка уже закрыта', 422);
            }

            if ($repairRequest->status === self::STATUS_IN_PROGRESS) {
                throw new BusinessException('Заявка уже взята в работу', 422);
            }

            $repairRequest->update([
                'status' => self::STATUS_IN_PROGRESS,
                'handled_by_id' => $employeeId,
                'started_at' => Carbon::now(),
            ]);

            return $repairRequest->fresh(['student', 'room.floor.building', 'attachments', 'handledBy']);
        });
    }

    public function resolve(int $requestId, int $employeeId, ?string $employeeComment): RepairRequest
    {
        return DB::transaction(function () use ($requestId, $employeeId, $employeeComment) {
            /** @var RepairRequest $repairRequest */
            $repairRequest = RepairRequest::query()->lockForUpdate()->findOrFail($requestId);

            if ($repairRequest->status === self::STATUS_RESOLVED) {
                throw new BusinessException('Заявка уже закрыта', 422);
            }

            $repairRequest->update([
                'status' => self::STATUS_RESOLVED,
                'handled_by_id' => $employeeId,
                'employee_comment' => $employeeComment,
                'started_at' => $repairRequest->started_at ?: Carbon::now(),
                'resolved_at' => Carbon::now(),
            ]);

            return $repairRequest->fresh(['student', 'room.floor.building', 'attachments', 'handledBy']);
        });
    }
}
