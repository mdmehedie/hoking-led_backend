<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EditorImageUploadController
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:10240', // 10MB max
        ]);

        $media = Media::createFromRequest('file', 'public');
        $media->collection_name = 'editor-images';
        $media->save();

        return response()->json([
            'url' => $media->getUrl(),
        ]);
    }
}
