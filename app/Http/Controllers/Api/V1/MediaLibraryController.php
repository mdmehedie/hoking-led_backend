<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MediaLibrary::query();

        if ($request->get('search')) {
            $query->where('original_name', 'like', '%' . $request->get('search') . '%');
        }

        $media = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $media->map(fn ($item) => [
                'id' => $item->id,
                'original_name' => $item->original_name,
                'url' => $item->url,
                'alt_text' => $item->alt_text,
                'mime_type' => $item->mime_type,
                'file_size' => $item->file_size,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ]),
            'pagination' => [
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'total' => $media->total(),
                'per_page' => $media->perPage(),
            ],
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|max:10240',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media-library', $filename, 'public');

        $media = MediaLibrary::create([
            'user_id' => auth()->id(),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'alt_text' => $request->input('alt_text'),
        ]);

        return response()->json([
            'id' => $media->id,
            'url' => $media->url,
        ]);
    }

    public function destroy(MediaLibrary $media): JsonResponse
    {
        $media->delete();
        return response()->json(['success' => true]);
    }
}
