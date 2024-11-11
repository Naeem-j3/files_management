<?php

namespace App\Services;
use App\Models\File;
use App\Models\FileBackup;
use App\Models\FileCheckout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileService
{

    public function checkInFiles(array $fileIds, int $userId)
    {
        DB::beginTransaction();
        try {
            // Fetch files and ensure they are all free for check-out
            $files = File::whereIn('id', $fileIds)
                ->where('status', 'free')
                ->lockForUpdate()
                ->get();

            if ($files->count() !== count($fileIds)) {
                throw new Exception("All selected files must be free to check in.");
            }

            // Update each file's status and create an entry in file_checkouts
            foreach ($files as $file) {
                $this->backupFile($file);
                $file->update(['status' => 'reserved']);

                // Log the check-out in file_checkouts
                FileCheckout::create([
                    'file_id' => $file->id,
                    'user_id' => $userId,
                    'checked_out_at' => null,
                    'checked_in_at' => now()
                ]);
            }

            DB::commit();
            return $files;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception( $e->getMessage());
        }
    }
    public function checkOutFiles(Request  $request, int $userId)
    {

        DB::beginTransaction();
        try {
            $fileData = [];

            // Retrieve file_id and uploaded_file from the request
            foreach ($request->input('files') as $index => $data) {
                $fileId = $data['file_id'];
                $uploadedFile = $request->file("files.$index.uploaded_file");

                $fileData[] = [
                    'file_id' => $fileId,
                    'uploaded_file' => $uploadedFile,
                ];
            }

            foreach ($fileData as $data) {
                $file = File::where('id', $data['file_id'])
                    ->where('status', 'reserved')
                    ->firstOrFail();

                if ($file->name !== $data['uploaded_file']->getClientOriginalName()) {
                    throw new Exception("File name mismatch for file: " . $file->name);
                }

                $filePath = $data['uploaded_file']->storeAs("group_files/{$file->group_id}", $file->name, 'public');

                $file->update(['path' => $filePath, 'status' => 'free']);

                FileCheckout::where('file_id', $file->id)
                    ->where('user_id', $userId)
                    ->whereNull('checked_out_at')
                    ->update(['checked_out_at' => now()]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error during check-out: " . $e->getMessage());
        }
    }
    protected function backupFile(File $file)
    {
        try {
            $backupPath = "backups/" . $file->name . "-" . now()->format('YmdHis');

            Storage::disk('public')->copy($file->path, $backupPath);

            FileBackup::create([
                'file_id' => $file->id,
                'backup_path' => $backupPath,
                'created_at' => now()
            ]);

        } catch (Exception $e) {
            // Log any backup errors for debugging
            Log::error("Failed to back up file ID {$file->id}: " . $e->getMessage());
            throw new Exception("Could not back up file before operation.");
        }
    }
    // Retrieve backups for a specified file
    public function getFileBackups(int $fileId)
    {
        try {
            return FileBackup::where('file_id', $fileId)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            throw new Exception("Unable to retrieve backups for file ID {$fileId}: " . $e->getMessage());
        }
    }
    public function restoreFileFromBackup(int $fileId, int $backupId)
    {
        DB::beginTransaction();
        try {
            // Fetch the file and the backup to restore from
            $file = File::findOrFail($fileId);
            $backup = FileBackup::where('id', $backupId)
                ->where('file_id', $fileId)
                ->firstOrFail();

            // Restore the file by copying the backup file to the current file path
            Storage::disk('public')->copy($backup->backup_path, $file->path);

            $file->update(['updated_at' => now()]);

            DB::commit();
            return $file;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to restore file ID {$fileId} from backup: " . $e->getMessage());
        }
    }
}
