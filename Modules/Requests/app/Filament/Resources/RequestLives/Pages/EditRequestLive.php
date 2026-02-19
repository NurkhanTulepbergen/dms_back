<?php

namespace Modules\Requests\Filament\Resources\RequestLives\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requests\Filament\Resources\RequestLives\RequestLiveResource;
use Modules\Requests\Models\RequestLive;
use Modules\Requests\Services\RequestLiveService;

class EditRequestLive extends EditRecord
{
    protected static string $resource = RequestLiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate($record, array $data): RequestLive
    {
        $newStatus = $data['status'] ?? $record->status;
        $payloadWithoutStatus = collect($data)->except('status')->all();

        if (!empty($payloadWithoutStatus)) {
            $record->update($payloadWithoutStatus);
        }

        /** @var RequestLiveService $service */
        $service = app(RequestLiveService::class);

        if ($record->status === 'pending' && $newStatus === 'accepted') {
            $service->approveByManager($record->id);
            return $record->fresh();
        }

        if ($record->status === 'pending' && $newStatus === 'rejected') {
            $service->rejectByManager($record->id);
            return $record->fresh();
        }

        if ($newStatus !== $record->status) {
            $record->update(['status' => $newStatus]);
        }

        return $record;
    }
}
