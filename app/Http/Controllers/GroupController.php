<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\FileRequest;
use App\Services\GroupService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{

    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }
    public function createGroup(CreateGroupRequest  $request)
    {
        $userId = Auth::id();
        $groupName = $request->input('name');
        $userIds = $request->input('user_ids', []);

        $group = $this->groupService->createGroup($userId, $groupName, $userIds);

        return response()->json([
            'status' => true,
            'data' => $group,
            'message' => 'Group created successfully and users invited',
        ], 200);
    }


    public function updateGroup(Request $request, $groupId)
    {
        $newGroupName = $request->name;

        try {
            $group = $this->groupService->updateGroup($groupId, $newGroupName);

            return response()->json([
                'status' => true,
                'data' => $group,
                'message' => 'Group updated successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

// Delete a group (only by the owner)
    public function deleteGroup($groupId)
    {
        try {
            $this->groupService->deleteGroup($groupId);

            return response()->json([
                'status' => true,
                'message' => 'Group deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }


    // Invite users to an existing group (can be used later)
    public function inviteUsers(Request $request, $groupId)
    {
        $userIds = $request->input('user_ids');

        $this->groupService->inviteUsers($groupId, $userIds);

        return response()->json([
            'status' => true,
            'message' => 'Users invited successfully',
        ]);
    }


    // Add a file to the group (needs approval)
    public function addFile(FileRequest $request, int $groupId)
    {
        $userId = Auth::id();
        $file = $request->file('file');
        try {
            // Delegate the file storage and checks to the service
            $data = $this->groupService->addFileToGroup($groupId, $userId, $file);

            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'File uploaded and awaiting approval',
            ]);

        } catch (ModelNotFoundException $e) {
            // Return an error response if the user is not a member or not accepted
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 403);
        }


    }

    // Approve a file (only group creator can approve)
    public function approveFile($fileId)
    {
        $creatorId = Auth::id();

        $approved = $this->groupService->approveFile($fileId, $creatorId);

        if ($approved) {
            return response()->json([
                'status' => true,
                'message' => 'File approved successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to approve this file',
            ], 403);
        }
    }

    // List groups the user has joined
    public function listUserGroups()
    {
        $userId = Auth::id();
        $groups = $this->groupService->listUserGroups($userId);

        return response()->json([
            'status' => true,
            'data' => $groups,
        ],200);
    }

    public function acceptInvite($groupId)
    {
        $userId = Auth::id(); // Get the authenticated user

        try {
            // Call the service to accept the invitation
            $groupUser = $this->groupService->acceptInvite($groupId, $userId);

            return response()->json([
                'status' => true,
                'message' => 'You have successfully accepted the invitation to join the group.',
                'group_user' => $groupUser
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'No invitation found or it has already been accepted.',
            ], 404);
        }
    }
    public function showGroupFiles($groupId)
    {
        $userId = Auth::id(); // Get the authenticated user ID

        try {
            // Retrieve group files for the user
            $files = $this->groupService->getGroupFiles($groupId, $userId);

            return response()->json([
                'status' => true,
                'data' => $files
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'data'=>[],
                'message' => $e->getMessage(),
            ], 403);
        }
    }
    public function getMyGroups(){
        $userId=Auth::id();
        $groups=$this->groupService->getMyGroups($userId);
        return response()->json([
            'status' => true,
            'data'=>[],
            'message' => $groups,
        ], 200);
    }
    public function test(){
        return $this->groupService->test();
    }
}
