<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendFirebaseNotification($title, $body, $fcmToken, $type, $data)
    {
        try {
            // Create a notification message
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification([
                    'title' => $title,
                    'body' => $body,
                ])
                ->withData($data);

            // Send the message using Firebase
            Firebase::messaging()->send($message);

            // Log the notification in the database
            Notification::create([
                'type' => $type,
                'user_id' => $data['user_id'],
                'data' => json_encode($data),
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage());
        }
    }
}
