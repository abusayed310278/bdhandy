<?php

namespace App\Http\Controllers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageController extends Controller
{
    /**
     * Upload and resize a photo to 512x512 using Intervention Image.
     * Stores in public disk under 'inventory/YYYY/MM/' path.
     * Deletes the old photo if provided.
     *
     * @param UploadedFile $file
     * @param string|null $oldPhotoPath
     * @return string
     */
    public function uploadInventoryPhoto(UploadedFile $file, ?string $oldPhotoPath = null): string
    {
        // 1. Delete old photo if it exists to prevent garbage accumulation
        if ($oldPhotoPath) {
            $this->deletePhoto($oldPhotoPath);
        }

        // 2. Generate path: inventory/YYYY/MM/
        $year = date('Y');
        $month = date('m');
        $directory = "inventory/{$year}/{$month}";
        
        // Generate unique filename with jpg extension
        $filename = uniqid('inv_', true) . '.jpg';
        $relativePath = "{$directory}/{$filename}";
        
        // Create directory if it does not exist
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $absolutePath = Storage::disk('public')->path($relativePath);

        // 3. Process image with Intervention Image v4 (GD driver)
        $manager = new ImageManager(new Driver());
        $image = $manager->decode($file);
        
        // Resize and crop to exactly 512x512 px
        $image->cover(512, 512);

        // Save as Jpeg with high quality
        $image->save($absolutePath, quality: 90);

        return $relativePath;
    }

    /**
     * Delete a photo from public storage if it exists.
     *
     * @param string|null $path
     * @return void
     */
    public function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
