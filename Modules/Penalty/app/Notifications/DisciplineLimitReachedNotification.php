<?php

namespace Modules\Penalty\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Penalty\Models\Penalty;
use Modules\User\Models\User;

class DisciplineLimitReachedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $student,
        private readonly Penalty $penalty,
        private readonly int $activePoints,
        private readonly int $disciplineLimit,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isStudent = $notifiable instanceof User
            && (int) $notifiable->id === (int) $this->student->id;

        $translations = $isStudent
            ? $this->studentTranslations()
            : $this->staffTranslations();
        $ru = $translations['ru'];

        return [
            'notification_type' => 'discipline_limit_reached',
            'title' => $ru['title'],
            'message' => $ru['message'],
            'title_ru' => $ru['title'],
            'message_ru' => $ru['message'],
            'translations' => $translations,
            'action_url' => $this->actionUrl($notifiable, $isStudent),
            'sender_name' => null,
            'student_id' => $this->student->id,
            'student_name' => $this->studentName(),
            'penalty_id' => $this->penalty->id,
            'active_points' => $this->activePoints,
            'discipline_limit' => $this->disciplineLimit,
        ];
    }

    private function actionUrl(object $notifiable, bool $isStudent): string
    {
        if ($isStudent) {
            return '/penalty';
        }

        if ($notifiable instanceof User && $notifiable->role === 'manager') {
            return '/manager/penalties';
        }

        return '/dorm-admin/penalties';
    }

    private function studentName(): string
    {
        $parts = array_filter([
            $this->student->lastname,
            $this->student->name,
            $this->student->middlename,
        ]);

        return $parts ? implode(' ', $parts) : $this->student->email;
    }

    private function studentTranslations(): array
    {
        return [
            'ru' => [
                'title' => 'Лимит штрафов достигнут',
                'message' => "Ваши штрафные баллы достигли {$this->activePoints}/{$this->disciplineLimit}. Проживание закрыто по дисциплинарной причине. Обратитесь к администрации.",
            ],
            'kk' => [
                'title' => 'Айыппұл лимитіне жетті',
                'message' => "Сіздің айыппұл ұпайларыңыз {$this->activePoints}/{$this->disciplineLimit} деңгейіне жетті. Тұру тәртіптік себеппен жабылды. Әкімшілікке хабарласыңыз.",
            ],
            'en' => [
                'title' => 'Penalty limit reached',
                'message' => "Your penalty points reached {$this->activePoints}/{$this->disciplineLimit}. Housing was closed for a disciplinary reason. Contact administration.",
            ],
        ];
    }

    private function staffTranslations(): array
    {
        $studentName = $this->studentName();

        return [
            'ru' => [
                'title' => 'Студент достиг лимита штрафов',
                'message' => "{$studentName} набрал {$this->activePoints}/{$this->disciplineLimit} штрафных баллов. Нужно рассмотреть отчисление или выселение из общежития.",
            ],
            'kk' => [
                'title' => 'Студент айыппұл лимитіне жетті',
                'message' => "{$studentName} {$this->activePoints}/{$this->disciplineLimit} айыппұл ұпайын жинады. Жатақханадан шығару немесе оқудан шығару мәселесін қарау керек.",
            ],
            'en' => [
                'title' => 'Student reached the penalty limit',
                'message' => "{$studentName} reached {$this->activePoints}/{$this->disciplineLimit} penalty points. Review expulsion or dormitory eviction.",
            ],
        ];
    }
}
