<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminLocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => ['required', 'string', 'max:10'],
        ]);

        $supported = config('app.supported_locales', []);
        $code = strtolower($request->string('locale')->toString());

        if ($supported !== [] && !in_array($code, $supported, true)) {
            return back();
        }

        $isActive = Locale::query()->where('code', $code)->where('is_active', true)->exists();
        if (!$isActive) {
            return back();
        }

        $request->session()->put('locale', $code);

        return back();
    }
}
