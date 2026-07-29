<?php

use App\Models\User;
use Livewire\Component;

new class extends Component
{
    public User $user;

    public function toggle(): void
    {
        $me = auth()->user();

        $me->isFollowing($this->user)
            ? $me->unfollow($this->user)
            : $me->follow($this->user);

        $this->dispatch('follow-changed', userId: $this->user->id);
    }

    public function with(): array
    {
        return ['following' => auth()->user()->isFollowing($this->user)];
    }
};
?>

<span class="inline-flex">
    @if ($user->is(auth()->user()))
        <a href="{{ route('profile.edit') }}" wire:navigate class="btn-outline">Edit profile</a>
    @elseif ($following)
        <button type="button" wire:click="toggle" class="btn-outline group">
            <span class="group-hover:hidden">Following</span>
            <span class="hidden text-red-500 group-hover:inline">Unfollow</span>
        </button>
    @else
        <button type="button" wire:click="toggle" class="btn-primary">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
            Follow
        </button>
    @endif
</span>
