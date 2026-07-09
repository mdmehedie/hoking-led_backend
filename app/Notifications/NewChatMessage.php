<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessage extends Notification
{
    use Queueable;

    protected $conversation;
    protected $chatMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(Conversation $conversation, $chatMessage)
    {
        $this->conversation = $conversation;
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('filament.admin.resources.conversations.view', ['record' => $this->conversation]);

        return (new MailMessage)
            ->subject('New Chat Message from ' . ($this->conversation->visitor_name ?? 'Visitor'))
            ->greeting('Hello!')
            ->line('You have received a new message in a live chat conversation.')
            ->line('**From:** ' . ($this->conversation->visitor_name ?? 'Visitor'))
            ->line('**Message:**')
            ->line($this->chatMessage)
            ->action('View Conversation', $url)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
