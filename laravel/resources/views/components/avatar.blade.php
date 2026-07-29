@props([
    'user',
    'size' => 'size-10',
    'text' => 'text-sm',
    'link' => true,
    'ring' => true,
])

@php
    $ringClass = $ring ? 'avatar-ring' : '';
@endphp

@if ($link)
    <a href="{{ route('users.show', $user) }}" class="shrink-0" wire:navigate>
@endif

@if ($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->displayName() }}"
         class="{{ $size }} {{ $ringClass }} rounded-full object-cover">
@else
    <span class="{{ $size }} {{ $text }} {{ $ringClass }} inline-flex items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-fuchsia-500 font-semibold text-white select-none">
        {{ $user->initials() }}
    </span>
@endif

@if ($link)
    </a>
@endif
