<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    private $imagePath = 'img/newsletters';
    
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $image = $request->file('image');
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        
        // Store in configured directory
        $image->move(public_path($this->imagePath), $filename);
        
        return response()->json([
            'url' => asset($this->imagePath . '/' . $filename),
            'filename' => $filename
        ]);
    }

    public function list()
    {
        $images = [];
        $imgPath = public_path($this->imagePath);
        
        if (!is_dir($imgPath)) {
            mkdir($imgPath, 0755, true);
        }
        
        $files = glob($imgPath . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        foreach ($files as $file) {
            $filename = basename($file);
            $images[] = [
                'name' => $filename,
                'url' => asset($this->imagePath . '/' . $filename)
            ];
        }
        
        return response()->json(['images' => $images]);
    }
}