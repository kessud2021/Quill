<?php

namespace Framework\Notifications\Channels;

class MailChannel {
    public function send($notifiable, $notification) {
        $message = $notification->toMail();

        if (empty($notifiable->getAttribute('email'))) {
            return;
        }

        $to = $notifiable->getAttribute('email');
        $subject = $message['subject'] ?? 'Notification';
        $body = $message['body'] ?? '';

        mail($to, $subject, $body);
    }
}
