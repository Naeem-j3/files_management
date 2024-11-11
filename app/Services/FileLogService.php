<?php

namespace App\Services;

use App\Models\File;
use App\Models\FileCheckout;
use App\Models\Group;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class FileLogService
{
    public function generateLogsByFilePdf(int $groupId, int $fileId)
    {
        // Get the file with actions
        $file = $this->getLogsByFile($groupId, $fileId);
        $groupName=Group::where('id',$groupId)->first()->name;
        // Render PDF view with the file and its actions
        $pdf = Pdf::loadView('logs.pdf', [
            'file' => $file,
            'groupName' => $groupName,
            'identifier' => $file->name
        ]);
        return $pdf->download("file_logs_{$fileId}.pdf");
    }

    public function generateLogsByUserPdf(int $groupId, int $userId)
    {
        $user = $this->getLogsByUser($groupId, $userId);

        $pdf = Pdf::loadView('logs.user_pdf', [
            'user' => $user
        ]);
        return $pdf->download("user_logs_{$userId}.pdf");
    }
    // Get logs by file within a specific group
    public function getLogsByFile(int $groupId, int $fileId)
    {
        // Retrieve the file and load only actions for the specified group
        $file = File::where('id', $fileId)
            ->where('group_id', $groupId)
            ->with(['actions' => function ($query) {
                $query->with('user:id,name,email')
                    ->orderBy('checked_out_at', 'desc');
            }])
            ->firstOrFail();

        return $file;
    }

    // Get logs by user within a specific group
    public function getLogsByUser(int $groupId, int $userId)
    {
        // Retrieve the user and load only actions for the specified group
        $user = User::where('id', $userId)->select(['id','name','email','created_at',
        'updated_at'])
            ->with(['actions' => function ($query) use ($groupId) {
                $query->whereHas('file', function ($fileQuery) use ($groupId) {
                    $fileQuery->where('group_id', $groupId);
                })
                    ->orderBy('checked_out_at', 'desc')
                  ->with('file');
            }])
            ->firstOrFail();

        return $user;
    }
}
