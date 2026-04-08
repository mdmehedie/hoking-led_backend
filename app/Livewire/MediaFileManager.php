<?php

namespace App\Livewire;

use App\Models\MediaLibrary;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MediaFileManager extends Component
{
    use WithFileUploads;

    protected $listeners = ['close-media-manager' => 'close'];

    public $isOpen = false;
    public $mediaItems = [];
    public $search = '';
    public $pagination = [];
    public $page = 1;
    public $file = null;
    public $uploading = false;

    public function mount()
    {
        $this->loadMedia();
    }

    public function loadMedia()
    {
        $query = MediaLibrary::query();

        if ($this->search) {
            $query->where('original_name', 'like', '%' . $this->search . '%');
        }

        $media = $query->orderBy('created_at', 'desc')->paginate(12, ['*'], 'mediaPage', $this->page);

        $this->mediaItems = $media->map(fn ($item) => [
            'id' => $item->id,
            'original_name' => $item->original_name,
            'url' => $item->url,
            'alt_text' => $item->alt_text,
            'mime_type' => $item->mime_type,
            'file_size' => $item->file_size,
            'created_at' => $item->created_at->format('Y-m-d'),
        ])->toArray();

        $this->pagination = [
            'current_page' => $media->currentPage(),
            'last_page' => $media->lastPage(),
            'total' => $media->total(),
            'per_page' => $media->perPage(),
        ];
    }

    public function updatedSearch()
    {
        $this->page = 1;
        $this->loadMedia();
    }

    public function updatedPage()
    {
        $this->loadMedia();
    }

    public function uploadFile()
    {
        if (!$this->file) {
            return;
        }

        $file = $this->file;
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media-library', $filename, 'public');

        MediaLibrary::create([
            'user_id' => auth()->id(),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $this->file = null;
        $this->loadMedia();
        $this->dispatch('upload-complete');
    }

    public function deleteItem($id)
    {
        $media = MediaLibrary::find($id);
        if ($media) {
            $media->delete();
            $this->loadMedia();
        }
    }

    public function open()
    {
        $this->isOpen = true;
        $this->loadMedia();
        $this->dispatch('open-media-manager');
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.media-file-manager');
    }
}
