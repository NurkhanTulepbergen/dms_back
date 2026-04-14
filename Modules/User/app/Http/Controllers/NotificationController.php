<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Modules\User\Models\SystemNotification;
use Modules\User\Models\User;
use Modules\User\Notifications\SystemBroadcastNotification;

class NotificationController extends Controller
{
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

    private function serializeInboxNotification(DatabaseNotification $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];

        return [
            'id' => $notification->id,
            'title' => $data['title'] ?? 'Уведомление',
            'message' => $data['message'] ?? '',
            'action_url' => $data['action_url'] ?? null,
            'sender_name' => $data['sender_name'] ?? null,
            'broadcast_id' => $data['broadcast_id'] ?? null,
            'created_at' => $notification->created_at?->toIso8601String(),
            'read_at' => $notification->read_at?->toIso8601String(),
        ];
    }

    private function serializeBroadcast(SystemNotification $broadcast): array
    {
        return [
            'id' => $broadcast->id,
            'title' => $broadcast->title,
            'message' => $broadcast->message,
            'action_url' => $broadcast->action_url,
            'created_at' => $broadcast->created_at?->toIso8601String(),
            'updated_at' => $broadcast->updated_at?->toIso8601String(),
            'created_by' => $broadcast->created_by,
            'sender_name' => $this->resolveSenderName($broadcast->creator),
        ];
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $limit = (int) $request->integer('limit', 8);
        $limit = max(1, min($limit, 50));

        $notifications = $user->notifications()
            ->orderByRaw('read_at IS NULL DESC')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return result([
            'items' => $notifications
                ->map(fn (DatabaseNotification $notification) => $this->serializeInboxNotification($notification))
                ->values(),
            'unread_count' => $user->unreadNotifications()->count(),
        ], 200, 'Уведомления');
    }

    public function markAllAsRead(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->unreadNotifications()->update([
            'read_at' => now(),
            'updated_at' => now(),
        ]);

        return result([
            'unread_count' => 0,
        ], 200, 'Все уведомления отмечены как прочитанные');
    }

    public function markAsRead(Request $request, string $notificationId)
    {
        /** @var User $user */
        $user = $request->user();

        /** @var DatabaseNotification $notification */
        $notification = $user->notifications()->findOrFail($notificationId);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return result(
            $this->serializeInboxNotification($notification->fresh()),
            200,
            'Уведомление отмечено как прочитанное'
        );
    }

    public function broadcasts()
    {
        $notifications = SystemNotification::query()
            ->with('creator:id,email,lastname,name,middlename')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return result(
            $notifications
                ->map(fn (SystemNotification $notification) => $this->serializeBroadcast($notification))
                ->values(),
            200,
            'Глобальные уведомления'
        );
    }

    public function store(Request $request)
    {
        /** @var User $sender */
        $sender = $request->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'action_url' => ['nullable', 'string', 'max:255'],
        ]);

        $broadcast = SystemNotification::query()->create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'action_url' => $validated['action_url'] ?? null,
            'created_by' => $sender->id,
        ]);

        $broadcast->load('creator:id,email,lastname,name,middlename');

        User::query()
            ->select(['id', 'email', 'lastname', 'name', 'middlename'])
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($broadcast) {
                NotificationFacade::send($users, new SystemBroadcastNotification($broadcast));
            });

        return result(
            $this->serializeBroadcast($broadcast),
            201,
            'Уведомление отправлено всем пользователям'
        );
    }
}
