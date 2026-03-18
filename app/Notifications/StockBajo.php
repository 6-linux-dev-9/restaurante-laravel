<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Insumo;

class StockBajo extends Notification
{
    use Queueable;

    public $insumo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Insumo $insumo)
    {
        $this->insumo = $insumo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toDatabase(object $notifiable): array
    {
        $nombreInsumo = $this->insumo->nombre;
        return [
            'message' => "⚠️ El insumo '{$nombreInsumo}' tiene un stock bajo.",
            'insumo_id' => $this->insumo, // Aquí puedes agregar el ID del insumo si es necesario
        ];
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
