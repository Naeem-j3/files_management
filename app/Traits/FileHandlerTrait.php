<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FileHandlerTrait
{
    // Delete all files in the specified directory
    public function deleteDirectory($directoryPath)
    {
        if (Storage::exists($directoryPath)) {
            Storage::deleteDirectory($directoryPath);
        }
    }

    // Delete a specific file if needed
    public function deleteFile($filePath)
    {
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }
    }
}
