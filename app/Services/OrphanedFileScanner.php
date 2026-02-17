<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OrphanedFileScanner
{
    /**
     * Scan storage for files that are not referenced in the media table.
     * Note: This is a basic implementation for the 'public' disk.
     */
    public function scan()
    {
        $allFiles = Storage::disk('public')->allFiles();
        $dbFiles = Media::pluck('file_name')->toArray();
        
        $orphanedFiles = [];
        $totalSize = 0;

        foreach ($allFiles as $file) {
            $filename = basename($file);
            if (!in_array($filename, $dbFiles)) {
                $orphanedFiles[] = $file;
                $totalSize += Storage::disk('public')->size($file);
            }
        }

        return [
            'count' => count($orphanedFiles),
            'total_size' => $totalSize,
            'files' => $orphanedFiles
        ];
    }
}
