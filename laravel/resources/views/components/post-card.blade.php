<?php

use App\Models\Post;
use Livewire\Component;

new class extends Component
{
    public Post $post;

    public function toggleLike(): void
    {
        $existing = $this->post->likes()->where('user_id', auth()->id());

        if ($existing->exists()) {
            $existing->delete();
        } else {
            $this->post->likes()->create(['user_id' => auth()->id()]);
        }

        $this->post->refresh()->loadCount(['likes', 'comments'])->load('likes');
    }

    public function delete(): void
    {
        abort_unless($this->post->user_id === auth()->id(), 403);

        $postId = $this->post->id;
        $this->post->delete();

        $this->dispatch('post-deleted', postId: $postId);
    }

    public function with(): array
    {
        return ['liked' => $this->post->isLikedBy(auth()->user())];
    }
};
?>

<article class="card p-4">
    <header class="flex items-start justify-between gap-2">
        <div class="flex items-center gap-3">
            <x-avatar :user="$post->user" />
            <div class="leading-tight">
                <a href="{{ route('users.show', $post->user) }}" wire:navigate
                   class="font-semibold text-gray-900 hover:underline dark:text-white">{{ $post->user->displayName() }}</a>
                <span class="text-sm text-muted">&#64;{{ $post->user->username }}</span>
                <div><x-timestamp :value="$post->created_at" /></div>
            </div>
        </div>

        @if ($post->user_id === auth()->id())
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" aria-label="Post menu"
                        class="rounded-lg p-1.5 text-muted hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition.opacity
                     class="absolute right-0 z-30 mt-1 w-40 rounded-xl border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                    <button type="button" wire:click="delete" wire:confirm="Delete this post?"
                            class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-700">
                        Delete post
                    </button>
                </div>
            </div>
        @endif
    </header>

    <div class="mt-3">
        <p class="whitespace-pre-wrap break-words text-gray-800 dark:text-gray-100">{{ $post->body }}</p>

        @if ($post->imageUrl())
            <div class="mt-3" x-data="{ open: false }">
                <button type="button" @click="open = true" class="block w-full">
                    <img src="{{ $post->imageUrl() }}" alt="Post image" class="max-h-96 w-full rounded-xl object-cover">
                </button>
                <div x-show="open" x-cloak @click.self="open = false" @keydown.escape.window="open = false"
                     class="fixed inset-0 z-[70] flex items-center justify-center bg-black/80 p-4">
                    <img src="{{ $post->imageUrl() }}" alt="Post image" class="max-h-[90vh] max-w-full rounded-lg shadow-2xl">
                    <button type="button" @click="open = false" aria-label="Close"
                            class="absolute right-4 top-4 grid size-10 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <footer class="mt-3 flex items-center gap-1 border-t border-gray-100 pt-2 dark:border-gray-700">
        {{-- Optimistic like: Alpine flips the heart instantly, the server response reconciles it. --}}
        <button type="button" wire:click="toggleLike"
                x-data="{ liked: @js($liked), count: @js($post->likes_count) }"
                @click="liked = !liked; count += liked ? 1 : -1"
                class="btn-ghost !px-2.5 text-muted">
            <svg class="size-5 transition-transform duration-150"
                 :class="liked ? 'fill-current text-red-500 scale-110' : ''"
                 fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
            <span x-text="count">{{ $post->likes_count }}</span>
        </button>

        <a href="{{ route('posts.show', $post) }}" wire:navigate class="btn-ghost !px-2.5 text-muted">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
            <span>{{ $post->comments_count }}</span>
        </a>

        <button type="button" class="btn-ghost !px-2.5 text-muted ml-auto"
                x-data="{ label: 'Share' }"
                @click="navigator.clipboard.writeText('{{ route('posts.show', $post) }}').then(() => {
                    label = 'Copied!'; setTimeout(() => label = 'Share', 1500)
                })">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/></svg>
            <span class="text-sm" x-text="label">Share</span>
        </button>
    </footer>
</article>
