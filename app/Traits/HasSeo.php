<?php

namespace App\Traits;

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
}
