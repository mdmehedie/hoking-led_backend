<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\RichEditor;

class CustomRichEditor extends RichEditor
{
    protected string $uploadUrl = '/admin/editor-image-upload';

    public function fileUploadsUrl(string $url): static
    {
        $this->uploadUrl = $url;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->extraAttributes([
            'data-upload-url' => $this->uploadUrl,
        ]);
    }
}
