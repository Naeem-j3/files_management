<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Models\File;
use App\Models\GroupUser;
use App\Repositories\files\FileRepositoryInterface;
use App\Repositories\groups\GroupRepositoryInterface;
use App\Traits\FileHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class GroupService
{
    use FileHandlerTrait;
    protected $groupRepository,$fileRepository,$notificationService;

    public function __construct(GroupRepositoryInterface $groupRepository,
                                FileRepositoryInterface $fileRepository,
                                NotificationService $notificationService)
    {
        $this->groupRepository = $groupRepository;
        $this->fileRepository=$fileRepository;
        $this->notificationService=$notificationService;
    }
    // Create a group
    public function createGroup($userId, $groupName, $userIds = [])
    {
        DB::beginTransaction();
        try {
            // Create the group
            $group = $this->groupRepository->create([
                'name' => $groupName,
                'owner_id' => $userId
            ]);

            $group->groupUsers()->create([
                'user_id' => $userId,
                'status' => 'accepted'
            ]);

            // Invite additional users (if provided)
            if (!empty($userIds)) {
                $this->inviteUsers($group->id, $userIds);
            }

            DB::commit();
            return $group;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error creating group and inviting users: ' . $e->getMessage());
        }
    }
    // Update a group (only by the owner)
    public function updateGroup($groupId, $newGroupName)
    {
        $group = Group::findOrFail($groupId);
        $group->name = $newGroupName;
        $group->save();
        return $group;
    }

    // Delete a group (only by the owner)
    public function deleteGroup($groupId)
    {
        $group = Group::findOrFail($groupId);
        DB::beginTransaction();
        try {
            // Delete all related GroupUser and File records
            GroupUser::where('group_id', $groupId)->delete();
            File::where('group_id', $groupId)->delete();
            $this->deleteDirectory("group_files/{$groupId}");
            $group->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error deleting group: ' . $e->getMessage());
        }
    }

    // Invite users to the group
    public function inviteUsers($groupId, $userIds)
    {
        foreach ($userIds as $userId) {
            // Check if the user is already invited or a member of the group
            $existingMembership = GroupUser::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->first();

            if (!$existingMembership) {
                GroupUser::create([
                    'group_id' => $groupId,
                    'user_id' => $userId,
                    'status' => 'invited'
                ]);
            }
            // Fetch user to access FCM token
            $user = User::find($userId);

            if ($user && $user->fcm_token) {
                // Prepare notification data
                $title = "Group Invitation";
                $body = "You've been invited to join a group.";
                $data = [
                    'group_id' => $groupId,
                    'user_id' => $userId,
                    'type' => 'invite',
                ];

                // Send Firebase notification
                $this->notificationService->sendFirebaseNotification($title, $body, $user->fcm_token, 'invite', $data);

            }
        }
    }

    // Add a file to the group (pending approval)
    public function addFileToGroup($groupId, $userId, $file)
    {
        $groupUser = GroupUser::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->first();

        // If the user is not a member or is not accepted, throw an error
        if (!$groupUser) {
            throw new ModelNotFoundException('You must be an accepted member of the group to add files.');
        }

        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs("group_files/{$groupId}", $fileName, 'public');
        $is_owner=$groupUser->groups->owner_id==$userId;
    if($is_owner){
        return $this->fileRepository->create([
            'name' => $fileName,
            'path' => $filePath,
            'group_id' => $groupId,
            'user_id' => $userId,
            'is_approved' => true
        ]);
    }
    else{
        return $this->fileRepository->create([
            'name' => $fileName,
            'path' => $filePath,
            'group_id' => $groupId,
            'user_id' => $userId,
            'is_approved' => false
        ]);
    }

    }

    // Approve a file (only the creator can approve)
    public function approveFile($fileId, $owner_id)
    {
        $file = File::findOrFail($fileId);
        $group = Group::findOrFail($file->group_id);

        if ($group->owner_id !== $owner_id) {
            return false; // Only the group creator can approve the file
        }

        $file->is_approved = true;
        return $file->save();
    }

    // List groups a user is a member of
    public function listUserGroups($userId)
    {
        $user = User::find($userId);
        $groups = $user->groups()->with('owner:id,name,email')->get();

        // Hide pivot data from each group
        $groups->makeHidden('pivot');
       return $groups;

    }

    public function acceptInvite($groupId, $userId)
    {
        // Find the invitation record for the user in the group
        $groupUser = GroupUser::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'invited')  // Ensure they were invited
            ->first();

        // If no invitation found, throw an error
        if (!$groupUser) {
            throw new ModelNotFoundException('Invitation not found or already accepted.');
        }

        $groupUser->status = 'accepted';
        $groupUser->save();

        return $groupUser;
    }

    public function getGroupFiles($groupId, $userId)
    {
        // Check if the user is an accepted member of the group
        $groupUser = GroupUser::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->first();

        if (!$groupUser) {
            throw new ModelNotFoundException('You must be an accepted member of the group to view files.');
        }
        if($userId==$groupUser->groups->owner_id){
            return File::where('group_id', $groupId)->get();
        }
        // Retrieve and return all files for the group
        return File::where('group_id', $groupId)->where('is_approved',1)->get();
    }
    public function test(){
        $title = "Group Invitation";
        $body = "You've been invited to join a group.";
        $data = [

            'user_id' => "1",
            'type' => 'invite',
        ];

        // Send Firebase notification
      return  $this->notificationService->sendFirebaseNotification($title, $body, "eQGjBdEXSt-D-LKYG-5vR6:APA91bHI7pAHH_S686gCvXBbp-x0Gey43PTaZm_lWIhAZCSSZnUhhEKtdonrtwE4l7wqzKL4wAXByxN2EW_F0NuTcXr3ZkKIIJj3MhG0Ugo0kEi9F__Ql6-Th5vMvPVL99PWLAFOTLV-", 'invite', $data);
    }

}
