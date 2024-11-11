<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function checkInFiles(Request $request)
    {
        $userId = Auth::id();
        $fileIds = $request->input('file_ids');

        try {

            $files = $this->fileService->checkInFiles($fileIds, $userId);

            return response()->json([
                'status' => true,
                'data' => $files,
                'message' => 'Files checked in (reserved) successfully.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    public function checkOutFiles(Request $request)
    {
        $user_id=Auth::id();

        try {
            // Call the service to check-in files
            $this->fileService->checkOutFiles($request,$user_id);

            return response()->json([
                'status' => true,
                'message' => 'Files checked out successfully.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function getFileBackups(int $fileId)
    {
        try {
            $backups = $this->fileService->getFileBackups($fileId);
            return response()->json(['status' => true, 'backups' => $backups], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // Restore a file from a specific backup
    public function restoreFileFromBackup(int $fileId, int $backupId)
    {
        try {
            $restoredFile = $this->fileService->restoreFileFromBackup($fileId, $backupId);
            return response()->json(['status' => true, 'file' => $restoredFile,'message' => "The file was successfully restored"], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

}
