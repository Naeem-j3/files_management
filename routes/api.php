<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileLogController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test',function (){
//    return \App\Models\Group::where('id',1)->first()->users;
    return response()->json("hi",200);

});

Route::get('/test1', [GroupController::class, 'test']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/groups', [GroupController::class, 'createGroup']);
    Route::post('/groups/{groupId}/invite', [GroupController::class, 'inviteUsers']);
    Route::post('/groups/{groupId}/files', [GroupController::class, 'addFile']);
    Route::post('/files/{fileId}/approve', [GroupController::class, 'approveFile']);
    Route::get('/groups', [GroupController::class, 'listUserGroups']);
    Route::post('/groups/{groupId}/accept-invite', [GroupController::class, 'acceptInvite']);
    Route::get('/groups/{groupId}/files', [GroupController::class, 'showGroupFiles']);
    Route::put('/groups/{groupId}/update', [GroupController::class, 'updateGroup'])->middleware('check.owner');;
    Route::delete('/groups/{groupId}/delete', [GroupController::class, 'deleteGroup'])->middleware('check.owner');;
    Route::get('groups/{groupId}/details', [GroupController::class, 'showGroupDetails']);

    //users
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::get('/users/exclude-group/{groupId}', [UserController::class, 'getUsersExcludingGroup']);
    Route::get('/users/search', [UserController::class, 'searchUsers']);
    Route::get('/users/invitations', [UserController::class, 'showInvitaion']);
    //files
    Route::post('/files/check-out', [FileController::class, 'checkOutFiles']);
    Route::post('/files/check-in', [FileController::class, 'checkInFiles']);
    Route::get('/files/{fileId}/show', [FileController::class, 'showFile']);
    Route::get('/files/{fileId}/backups', [FileController::class, 'getFileBackups']);
    Route::post('/files/{fileId}/backups/{backupId}/restore', [FileController::class, 'restoreFileFromBackup']);
    Route::delete('/files/{fileId}/delete', [FileController::class, 'deleteFile']);


    //log
    Route::get('/groups/{groupId}/files/{fileId}/logs', [FileLogController::class, 'getLogsByFile'])->middleware('check.group.membership');
    Route::get('/groups/{groupId}/users/{userId}/logs', [FileLogController::class, 'getLogsByUser'])->middleware('check.owner');
    Route::get('/groups/{groupId}/files/{fileId}/logs/download', [FileLogController::class, 'downloadLogsByFilePdf'])->middleware('check.group.membership');
    Route::get('/groups/{groupId}/users/{userId}/logs/download', [FileLogController::class, 'downloadLogsByUserPdf'])->middleware('check.owner');
    Route::get('/groups/{groupId}/files/{fileId}/download', [FileController::class, 'downloadFile'])->middleware('check.group.membership');

    Route::get('/notifications', [UserController::class, 'getMyNotifications']);
    Route::delete('/notifications/{id}', [UserController::class, 'deleteNotification']);
});


