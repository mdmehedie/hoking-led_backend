<div
    x-data="{ showDelete: false, deleteId: null }"
>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl h-[85vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold">Media Library</h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>

            <!-- Upload & Search Bar -->
            <div class="p-4 border-b flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live="search" placeholder="Search files..." class="w-full px-3 py-2 border rounded-md text-sm">
                </div>
                <div class="flex items-center gap-2">
                    <input type="file" wire:model="file" accept="image/*" class="text-sm">
                    <button wire:click="uploadFile" wire:loading.attr="disabled" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="uploadFile">Upload</span>
                        <span wire:loading wire:target="uploadFile">Uploading...</span>
                    </button>
                </div>
            </div>

            <!-- Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($mediaItems as $item)
                    <div class="group relative border rounded-lg overflow-hidden hover:shadow-md transition cursor-pointer" wire:key="media-{{ $item['id'] }}">
                        <img src="{{ $item['url'] }}" alt="{{ $item['alt_text'] ?? '' }}" class="w-full h-28 object-cover" loading="lazy">
                        <div class="p-1.5 text-xs truncate" title="{{ $item['original_name'] }}">{{ $item['original_name'] }}</div>
                        
                        <!-- Overlay buttons -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                            <button type="button" @click="selectMediaImage('{{ $item['url'] }}')" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">Select</button>
                            <button type="button" @click="showDelete = true; deleteId = {{ $item['id'] }}" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">Delete</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(empty($mediaItems))
                <div class="text-center py-12 text-gray-500">No media found. Upload some images!</div>
                @endif
            </div>

            <!-- Pagination -->
            @if($pagination['last_page'] > 1)
            <div class="p-3 border-t flex items-center justify-between text-sm">
                <span class="text-gray-500">Page {{ $pagination['current_page'] }} of {{ $pagination['last_page'] }} ({{ $pagination['total'] }} total)</span>
                <div class="flex gap-2">
                    @for($i = 1; $i <= $pagination['last_page']; $i++)
                        <button wire:click="$set('page', {{ $i }})" class="px-2 py-1 rounded text-xs {{ $pagination['current_page'] == $i ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">{{ $i }}</button>
                    @endfor
                </div>
            </div>
            @endif
        </div>

        <!-- Delete confirmation modal -->
        <template x-if="showDelete">
            <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60">
                <div class="bg-white rounded-lg p-6 w-80 shadow-xl">
                    <h3 class="text-lg font-semibold mb-2">Delete Image?</h3>
                    <p class="text-sm text-gray-600 mb-4">This will permanently delete this image from the media library.</p>
                    <div class="flex gap-2 justify-end">
                        <button type="button" @click="showDelete = false" class="px-3 py-1.5 bg-gray-200 rounded text-sm hover:bg-gray-300">Cancel</button>
                        <button type="button" @click="$wire.deleteItem(deleteId); showDelete = false" class="px-3 py-1.5 bg-red-600 text-white rounded text-sm hover:bg-red-700">Delete</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
    @endif
</div>

<script>
    window.selectMediaImage = (url) => {
        if (window._tinymceCb) {
            window._tinymceCb(url);
            window._tinymceCb = null;
        }
        if (window.Livewire) {
            Livewire.dispatch('close-media-manager');
        }
    };
</script>
