<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\User\Models\SystemNotification;
use Modules\User\Models\User;

class SystemBroadcastNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly SystemNotification $broadcast
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'broadcast_id' => $this->broadcast->id,
            'title' => $this->broadcast->title,
            'message' => $this->broadcast->message,
            'action_url' => $this->broadcast->action_url,
            'sender_id' => $this->broadcast->created_by,
            'sender_name' => $this->resolveSenderName($this->broadcast->creator),
        ];
    }

    private function resolveSenderName(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        $parts = array_filter([
            $user->lastname,
            $user->name,
            $user->middlename,
        ]);

        return $parts ? implode(' ', $parts) : $user->email;
    }
}
