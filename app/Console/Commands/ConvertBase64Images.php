<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ConvertBase64Images extends Command
{
    protected $signature = 'images:convert-base64 {file}';
    protected $description = 'Convert base64 images in a file to actual image files';

    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $content = File::get($filePath);
        $imgDir = public_path('img/newsletters');
        
        if (!File::exists($imgDir)) {
            File::makeDirectory($imgDir, 0755, true);
        }

        // Find base64 images
        preg_match_all('/src="data:image\/([^;]+);base64,([^"]+)"/', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $fullMatch = $match[0];
            $extension = $match[1];
            $base64Data = $match[2];
            
            // Generate filename
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
            $filePath = $imgDir . '/' . $filename;
            
            // Decode and save
            $imageData = base64_decode($base64Data);
            File::put($filePath, $imageData);
            
            // Replace in content
            $newSrc = 'src="' . asset('img/newsletters/' . $filename) . '"';
            $content = str_replace($fullMatch, $newSrc, $content);
            
            $this->info("Converted image to: {$filename}");
        }

        // Save updated content
        File::put($this->argument('file'), $content);
        
        $this->info('Conversion complete!');
        return 0;
    }
}