<?php

namespace Framework\Notifications\Channels;

class DatabaseChannel {
    public function send($notifiable, $notification) {
        $message = $notification->toDatabase();

        if (method_exists($notifiable, 'notify')) {
            app('db')->table('notifications')->insert([
                'notifiable_id' => $notifiable->getAttribute('id'),
                'notifiable_type' => get_class($notifiable),
                'type' => get_class($notification),
                'data' => json_encode($message),
                'read_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
