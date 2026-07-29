<?php

use App\Models\Post;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Post')] class extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post->loadCount(['likes', 'comments']);
    }
};
?>

<div>
    <a href="{{ route('feed') }}" wire:navigate
       class="mb-4 inline-flex items-center gap-1 text-sm text-muted hover:text-gray-900 dark:hover:text-white">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        Back to feed
    </a>

    <livewire:post-card :post="$post" :key="'post-'.$post->id" />

    <livewire:comment-section :post="$post" :key="'comments-'.$post->id" />
</div>
