<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * One in-app notification for staff (stored in the `notifications` table
 * and shown under the bell in the admin header).
 *
 * Every alert has a short title, an optional body, a link and an icon.
 * Create alerts through the named constructors in App\Services\StaffNotifier
 * so wording stays consistent.
 */
class StaffAlert extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $title,
        public readonly ?string $body = null,
        public readonly ?string $url = null,
        public readonly string $icon = 'star',
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array{title: string, body: ?string, url: ?string, icon: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'icon' => $this->icon,
        ];
    }
}
