@php
    $messages = collect([
        'success' => session('status'),
        'error' => session('error'),
    ])->filter();
@endphp

<div class="pointer-events-none fixed inset-x-0 top-4 z-[60] flex flex-col items-center gap-2 px-4">
    @foreach ($messages as $type => $message)
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             x-transition.opacity.duration.300ms
             class="pointer-events-auto flex w-full max-w-sm items-center gap-3 rounded-xl border border-gray-100 bg-white p-3 shadow-lg dark:border-gray-700 dark:bg-gray-800"
             role="alert">
            <span @class([
                'grid size-8 shrink-0 place-items-center rounded-lg text-sm font-bold',
                'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200' => $type === 'success',
                'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200' => $type === 'error',
            ])>{{ $type === 'success' ? '✓' : '!' }}</span>
            <p class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-200">{{ $message }}</p>
            <button type="button" @click="show = false"
                    class="rounded-lg p-1 text-muted hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700"
                    aria-label="Dismiss">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endforeach
</div>
