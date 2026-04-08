<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class TinyEditor extends Field
{
    protected string $view = 'filament.forms.components.tiny-editor';

    protected array $plugins = [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'wordcount', 'codesample',
    ];

    protected string $toolbar = 'undo redo | blocks | fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table codesample | removeformat code fullscreen';

    protected array $extraOptions = [];

    public function plugins(array $plugins): static
    {
        $this->plugins = $plugins;
        return $this;
    }

    public function toolbar(string $toolbar): static
    {
        $this->toolbar = $toolbar;
        return $this;
    }

    public function extraOptions(array $options): static
    {
        $this->extraOptions = $options;
        return $this;
    }

    public function getPlugins(): string
    {
        return implode(' ', $this->plugins);
    }

    public function getToolbar(): string
    {
        return $this->toolbar;
    }

    public function getExtraOptions(): array
    {
        return $this->extraOptions;
    }

    public function getExtraOptionsJson(): string
    {
        return json_encode($this->extraOptions);
    }
}
