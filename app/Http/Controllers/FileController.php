<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Proxies\FileServiceProxy;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileServiceProxy $fileService)
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
            $file = File::findOrFail($fileId);
            if($file->status=='reserved'){
                return response()->json(['status' => false,'message' => "The file is reserved you can't restore it util be free"], 400);
            }

            $restoredFile = $this->fileService->restoreFileFromBackup($fileId, $backupId);
            return response()->json(['status' => true, 'file' => $restoredFile,'message' => "The file was successfully restored"], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }
    public function downloadFile($groupId, $fileId)
    {
        $userId = Auth::id(); // Get the authenticated user ID

        try {
            // Delegate file download to the service
            return $this->fileService->downloadFile($groupId, $fileId, $userId);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }
    public function showFile($fileId){
        $file=$this->fileService->showFile($fileId);

        if(!$file){
            return response()->json([
                'status' => false,
                'message'=>'file not found'
            ], 200);
        }
        return response()->json([
            'status' => true,
            'file'=>$file
        ], 200);


    }
    public function deleteFile($fileId)
    {
        $userId = Auth::id(); // Get the authenticated user ID

        try {
            $this->fileService->deleteFileG($fileId, $userId);
            return response()->json([
                'status' => true,
                'message' => 'File deleted successfully.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

}
