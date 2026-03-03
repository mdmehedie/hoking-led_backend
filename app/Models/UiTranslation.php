<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UiTranslation extends Model
{
    protected $table = 'ui_translations';

    protected $fillable = [
        'key',
        'locale',
        'value',
    ];
}
