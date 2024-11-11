<?php

namespace App\Http\Controllers;

use App\Services\FileLogService;
use Exception;
use Illuminate\Http\Request;

class FileLogController extends Controller
{
    protected $fileLogService;

    public function __construct(FileLogService $fileLogService)
    {
        $this->fileLogService = $fileLogService;
    }
    // Get logs by file
    public function getLogsByFile(int $groupId, int $fileId)
    {
        try {
            $logs = $this->fileLogService->getLogsByFile($groupId, $fileId);
            return response()->json(['status' => true, 'logs' => $logs], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 403);
        }
    }

    // Get logs by user
    public function getLogsByUser(Request $request, int $groupId, int $userId)
    {
        try {
            $logs = $this->fileLogService->getLogsByUser($groupId, $userId);
            return response()->json(['status' => true, 'logs' => $logs], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 403);
        }
    }

    public function downloadLogsByFilePdf( int $groupId, int $fileId)
    {
        return $this->fileLogService->generateLogsByFilePdf($groupId, $fileId);
    }

    public function downloadLogsByUserPdf( int $groupId, int $userId)
    {
        return $this->fileLogService->generateLogsByUserPdf($groupId, $userId);
    }

}
