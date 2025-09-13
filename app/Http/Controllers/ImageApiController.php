<?php

namespace App\Http\Controllers;

use Exception;

class ImageApiController extends Controller
{
    public function publicImages()
    {
        try {
            $imgPath = public_path('img/public_images');

            if (!is_dir($imgPath)) {
                if (!mkdir($imgPath, 0755, true)) {
                    return response()->json([
                        'error' => 'img directory does not exist',
                        'path' => $imgPath,
                        'images' => []
                    ]);
                }
            }

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
            $images = [];

            $files = scandir($imgPath);
            if ($files === false) {
                return response()->json([
                    'error' => 'Cannot read img directory',
                    'path' => $imgPath,
                    'images' => []
                ]);
            }

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;

                $filePath = $imgPath . '/' . $file;
                if (!is_file($filePath)) continue;

                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowedExtensions)) continue;

                $images[] = [
                    'name' => $file,
                    'path' => '/img/public_images/' . $file,
                    'url' => asset('img/public_images/' . $file),
                    'size' => filesize($filePath),
                    'modified' => date('Y-m-d H:i:s', filemtime($filePath))
                ];
            }

            usort($images, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return response()->json([
                'success' => true,
                'path' => $imgPath,
                'count' => count($images),
                'images' => $images
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unable to load images',
                'message' => $e->getMessage(),
                'images' => []
            ], 500);
        }
    }

    public function storageImages()
    {
        $images = [];
        $storagePath = storage_path('app/public/images');

        if (is_dir($storagePath)) {
            $files = glob($storagePath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
            foreach ($files as $file) {
                $filename = basename($file);
                $images[] = [
                    'name' => $filename,
                    'url' => '/storage/images/' . $filename
                ];
            }
        }

        return response()->json($images);
    }
}