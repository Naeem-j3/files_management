<?php

namespace App\Services;

use App\Models\File;
use App\Models\Notification;
use App\Models\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Exception;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendNotificationToUser($title, $body, $user)
    {
        try {

            if (empty($user->fcm_token)) {
                throw new Exception('User does not have a valid FCM token.');
            }
            Notification::create([
                'type' => 'user_notification', // Define the type
                'user_id' => $user->id,
                'data' => json_encode([
                    'title' => $title,
                    'body' => $body,
                ]),
            ]);

            // Path to the service account key JSON file
            $serviceAccountPath = storage_path('first-24-a76b8-firebase-adminsdk-intxu-1807562a2f.json');

            // Initialize the Firebase Factory with the service account
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);

            // Create the Messaging instance
            $messaging = $factory->createMessaging();

            // Create the notification array
            $notification = [
                'title' => $title,
                'body' => $body,
                'sound' => 'Tri-tone',
            ];

            // Create the CloudMessage instance
            $cloudMessage = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification($notification)
                ->withData(['priority' => 'high', 'contentAvailable' => true]);

            // Send the notification
            $messaging->send($cloudMessage);

            return response()->json([
                'msg' => 'Notification sent successfully',
                'status' => 'success',
                'user_id' => $user->id,
            ], 200);

        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Failed to send notification to user: '.$user->id, ['error' => $e->getMessage()]);

            return response()->json([
                'msg' => 'Failed to send notification',
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function notifyGroupMembers(File $file)
    {
        // Fetch the group associated with the file
        $group = $file->group;

        if (!$group) {
            throw new Exception("File does not belong to any group.");
        }

        // Get all accepted users in the group
        $groupUsers = $group->users;

        // Send notifications to each user
        foreach ($groupUsers as $user) {
            if ($user->fcm_token) { // Exclude the file owner
                $title = "File Check-In Notification";
                $body = "The file '{$file->name}' has been checked in.";
                $this->sendNotificationToUser($title, $body, $user);
            }
        }
    }
    public  function getMyNotifications($user){

        $notifications = $user->notifications;
        // Decode the `data` field for each notification
        $notifications->transform(function ($notification) {
            $notification->data = json_decode($notification->data, true); // Decode JSON string to array
            return $notification;
        });
        return response()->json([
            'status' => true,
            'data' => $notifications,
        ], 200);
    }
    public function deleteNotification($user,$id)
    {
        $notification = Notification::where('id', $id)->where('user_id', $user->id)->first();

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found or does not belong to you.',
            ], 404);
        }

        // Delete the notification
        $notification->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notification deleted successfully.',
        ], 200);
    }




}
