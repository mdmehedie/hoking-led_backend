<form method="POST" action="{{ route('admin.locale.update') }}" class="flex items-center gap-2">
    @csrf

    <label for="admin-locale" class="text-sm text-gray-600 dark:text-gray-300">
        {{ __('Language') }}
    </label>

    <select
        id="admin-locale"
        name="locale"
        class="fi-input block w-full rounded-lg border-none bg-white px-3 py-1.5 text-sm text-gray-900 shadow-sm ring-1 ring-gray-950/10 dark:bg-gray-900 dark:text-white dark:ring-white/20"
        onchange="this.form.submit()"
    >
        @foreach ($locales as $locale)
            <option value="{{ $locale->code }}" @selected(app()->getLocale() === $locale->code)>
                {{ $locale->name }} ({{ strtoupper($locale->code) }})
            </option>
        @endforeach
    </select>
</form>
