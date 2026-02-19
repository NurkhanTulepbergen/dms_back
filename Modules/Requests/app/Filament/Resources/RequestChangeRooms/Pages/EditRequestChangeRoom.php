<?php

namespace Modules\Requests\Filament\Resources\RequestChangeRooms\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requests\Filament\Resources\RequestChangeRooms\RequestChangeRoomResource;
use Modules\Requests\Models\RequestChangeRoom;
use Modules\Requests\Services\RequestChangeRoomService;

class EditRequestChangeRoom extends EditRecord
{
    protected static string $resource = RequestChangeRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate($record, array $data): RequestChangeRoom
    {
        $newStatus = $data['status'] ?? $record->status;
        $payloadWithoutStatus = collect($data)->except('status')->all();

        if (!empty($payloadWithoutStatus)) {
            $record->update($payloadWithoutStatus);
        }

        /** @var RequestChangeRoomService $service */
        $service = app(RequestChangeRoomService::class);

        if ($record->status === 'pending' && $newStatus === 'accepted') {
            $service->approve((int) $record->id);
            return $record->fresh();
        }

        if ($record->status === 'pending' && $newStatus === 'rejected') {
            $service->reject((int) $record->id);
            return $record->fresh();
        }

        if ($newStatus !== $record->status) {
            $record->update(['status' => $newStatus]);
        }

        return $record;
    }
}
