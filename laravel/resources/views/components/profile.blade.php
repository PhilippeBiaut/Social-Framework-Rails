<?php

use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    public User $user;

    /** In the URL so the active tab is shareable, like the Rails port. */
    #[Url]
    public string $tab = 'posts';

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    /**
     * Counts are loaded on every render, not in mount(): Livewire re-hydrates
     * the model from its id on each request, which drops withCount attributes
     * and left the header showing no numbers after a tab switch or a follow.
     */
    public function with(): array
    {
        $this->user->loadCount(['posts', 'following', 'followers']);

        $posts = $this->tab === 'likes'
            ? $this->user->likedPosts()->withCardData()->recent()->get()
            : $this->user->posts()->withCardData()->recent()->get();

        return [
            'posts' => $posts,
            'title' => $this->user->displayName(),
        ];
    }
};
?>

<div>
    <div class="card overflow-hidden">
        <div class="h-28 bg-gradient-to-r from-indigo-500 via-purple-500 to-fuchsia-500"></div>
        <div class="px-4 pb-4">
            <div class="-mt-12 flex items-end justify-between">
                <div class="rounded-full border-4 border-white dark:border-gray-800">
                    <x-avatar :user="$user" size="size-24" text="text-2xl" :link="false" :ring="false" />
                </div>
                <div class="mb-1 flex items-center gap-2">
                    <button type="button" class="btn-outline !px-2.5" title="Copy profile link"
                            x-data="{ label: 'Share' }"
                            @click="navigator.clipboard.writeText('{{ route('users.show', $user) }}').then(() => {
                                label = 'Copied!'; setTimeout(() => label = 'Share', 1500)
                            })">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                        <span class="text-sm" x-text="label">Share</span>
                    </button>
                    <livewire:follow-button :user="$user" :key="'follow-'.$user->id" />
                </div>
            </div>

            <div class="mt-2">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->displayName() }}</h1>
                <p class="text-sm text-gray-400">&#64;{{ $user->username }}</p>
                @if ($user->bio)
                    <p class="mt-2 whitespace-pre-wrap text-gray-700 dark:text-gray-200">{{ $user->bio }}</p>
                @endif
            </div>

            <div class="mt-3 flex gap-5 text-sm text-gray-500">
                <span><span class="font-semibold text-gray-900 dark:text-white">{{ $user->posts_count }}</span> Posts</span>
                <a href="{{ route('users.following', $user) }}" wire:navigate class="hover:underline">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $user->following_count }}</span> Following
                </a>
                <a href="{{ route('users.followers', $user) }}" wire:navigate class="hover:underline">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $user->followers_count }}</span> Followers
                </a>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <div class="flex border-b border-gray-200 text-sm font-medium dark:border-gray-700">
            @foreach (['posts' => 'Posts', 'likes' => 'Likes'] as $value => $label)
                <button type="button" wire:click="$set('tab', '{{ $value }}')" role="tab"
                        @class([
                            '-mb-px border-b-2 px-4 py-2.5',
                            'border-indigo-600 text-indigo-600 dark:text-indigo-400' => $tab === $value,
                            'border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-white' => $tab !== $value,
                        ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="mt-4 space-y-4">
            @forelse ($posts as $post)
                <livewire:post-card :post="$post" :key="'post-'.$post->id" />
            @empty
                <p class="card p-6 text-center text-sm text-gray-500">
                    {{ $tab === 'likes' ? 'No liked posts yet.' : $user->displayName().' hasn\'t posted yet.' }}
                </p>
            @endforelse
        </div>
    </div>
</div>
