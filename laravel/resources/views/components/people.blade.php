<?php

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('People')] class extends Component
{
    #[Url]
    public string $q = '';

    public function with(): array
    {
        $query = User::whereKeyNot(auth()->id())->latest();

        if ($this->q !== '') {
            // Escape %/_ so they are searched literally instead of as wildcards.
            $term = '%'.addcslashes($this->q, '%_\\').'%';
            $query->where(fn ($q) => $q->where('username', 'like', $term)->orWhere('name', 'like', $term));
        }

        return ['users' => $query->get()];
    }
};
?>

<div>
    <h1 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Discover people</h1>

    <div class="relative mb-4">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        </span>
        <input wire:model.live.debounce.300ms="q" type="search" autocomplete="off"
               placeholder="Search by name or &#64;username" class="input !pl-10">
    </div>

    <div class="space-y-3">
        @forelse ($users as $user)
            <x-user-row :user="$user" :key="'user-'.$user->id" />
        @empty
            <p class="card p-6 text-center text-sm text-gray-500">No one matches “{{ $q }}”.</p>
        @endforelse
    </div>
</div>
