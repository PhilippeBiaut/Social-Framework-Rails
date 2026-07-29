@props(['user'])

<div class="card flex items-center gap-3 p-3">
    <x-avatar :user="$user" size="size-11" />
    <div class="min-w-0 flex-1">
        <a href="{{ route('users.show', $user) }}" wire:navigate class="block">
            <p class="truncate font-semibold text-gray-900 hover:underline dark:text-white">{{ $user->displayName() }}</p>
            <p class="truncate text-sm text-gray-400">&#64;{{ $user->username }}</p>
        </a>
        @if ($user->bio)
            <p class="mt-0.5 line-clamp-1 text-sm text-gray-500">{{ $user->bio }}</p>
        @endif
    </div>
    <livewire:follow-button :user="$user" :key="'follow-'.$user->id" />
</div>
