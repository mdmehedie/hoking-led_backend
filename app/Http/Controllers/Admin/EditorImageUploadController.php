<?php

namespace App\Http\Controllers\Admin;

use App\Models\MediaLibrary;
use Illuminate\Http\Request;

class EditorImageUploadController
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:10240',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('editor-images', $filename, 'public');

        $media = MediaLibrary::create([
            'user_id' => auth()->id(),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json([
            'url' => $media->url,
        ]);
    }
}
