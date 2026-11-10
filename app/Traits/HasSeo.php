<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasSeo
{
    public function initializeHasSeo()
    {
        $this->fillable = array_merge($this->fillable ?? [], [
            'meta_title',
            'meta_description',
            'meta_keywords',
            'canonical_url',
        ]);
    }

    protected function metaTitle(): Attribute
    {
        return $this->seoTextAttribute();
    }

    protected function metaDescription(): Attribute
    {
        return $this->seoTextAttribute();
    }

    protected function metaKeywords(): Attribute
    {
        return $this->seoTextAttribute();
    }

    // Imported rows contain placeholder values like "[]" and "0" that would otherwise be rendered as page titles.
    private function seoTextAttribute(): Attribute
    {
        $clean = static function ($value): ?string {
            if (!is_string($value)) {
                return $value;
            }

            $value = trim($value);

            return in_array($value, ['', '[]', '0'], true) ? null : $value;
        };

        return Attribute::make(get: $clean, set: $clean);
    }
}
