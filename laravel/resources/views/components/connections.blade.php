<?php

use App\Models\User;
use Livewire\Component;

new class extends Component
{
    public User $user;

    /** Either "following" or "followers", derived from the matched route. */
    public string $mode = 'followers';

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->mode = request()->routeIs('users.following') ? 'following' : 'followers';
    }

    public function with(): array
    {
        $people = $this->mode === 'following'
            ? $this->user->following()->orderBy('username')->get()
            : $this->user->followers()->orderBy('username')->get();

        return [
            'people' => $people,
            'title' => $this->mode === 'following'
                ? $this->user->displayName().' follows'
                : 'Followers of '.$this->user->displayName(),
        ];
    }
};
?>

<div>
    <a href="{{ route('users.show', $user) }}" wire:navigate
       class="mb-4 inline-flex items-center gap-1 text-sm text-muted hover:text-gray-900 dark:hover:text-white">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        {{ $user->displayName() }}
    </a>

    <h1 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>

    <div class="space-y-3">
        @forelse ($people as $person)
            <x-user-row :user="$person" :key="'user-'.$person->id" />
        @empty
            <p class="card p-6 text-center text-sm text-muted">Nobody here yet.</p>
        @endforelse
    </div>
</div>
