<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Tobuli\Entities\Popup;

class PopupNotification extends Notification
{
    use Queueable;

    public $popup;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Popup $popup)
    {
        $this->popup = $popup;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->popup->toArray();
    }
}
