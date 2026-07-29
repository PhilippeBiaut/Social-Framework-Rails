<?php

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Post $post;

    public int $maxLength = 500;

    #[Validate('required|string|max:500')]
    public string $body = '';

    public function addComment(): void
    {
        $this->validate();

        $this->post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->body,
        ]);

        $this->reset('body');
        $this->dispatch('comment-added');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);
        abort_unless($comment->user_id === auth()->id(), 403);

        $comment->delete();
    }

    /** @return Collection<int, Comment> */
    public function with(): array
    {
        return ['comments' => $this->post->comments()->with('user')->get()];
    }
};
?>

<section class="mt-6" x-data="{ count: 0 }" x-on:comment-added.window="count = 0">
    <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-500">
        Comments
        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs dark:bg-gray-700">{{ $comments->count() }}</span>
    </h2>

    <form wire:submit="addComment" class="mb-4 flex gap-3">
        <x-avatar :user="auth()->user()" size="size-9" text="text-xs" :link="false" />
        <div class="flex-1">
            @error('body') <p class="mb-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <div class="card flex items-end gap-2 px-3 py-2">
                <textarea wire:model="body" rows="1" placeholder="Write a comment…"
                          x-on:input="count = $event.target.value.length; $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                          class="w-full resize-none border-0 bg-transparent p-0 text-sm text-gray-900 placeholder-gray-400 focus:ring-0 dark:text-white"></textarea>
                <span class="text-xs tabular-nums text-gray-300" x-text="{{ $maxLength }} - count">{{ $maxLength }}</span>
                <button type="submit" class="btn-primary !px-3 !py-1.5 cursor-pointer disabled:opacity-40"
                        :disabled="count === 0 || count > {{ $maxLength }}">
                    Send
                </button>
            </div>
        </div>
    </form>

    <div class="space-y-3">
        @foreach ($comments as $comment)
            <div class="flex gap-3" wire:key="comment-{{ $comment->id }}">
                <x-avatar :user="$comment->user" size="size-9" text="text-xs" />
                <div class="flex-1">
                    <div class="card px-4 py-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex flex-wrap items-baseline gap-x-2">
                                <a href="{{ route('users.show', $comment->user) }}" wire:navigate
                                   class="text-sm font-semibold text-gray-900 hover:underline dark:text-white">{{ $comment->user->displayName() }}</a>
                                <span class="text-xs text-gray-400">&#64;{{ $comment->user->username }}</span>
                                <x-timestamp :value="$comment->created_at" />
                            </div>
                            @if ($comment->user_id === auth()->id())
                                <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                        wire:confirm="Delete this comment?"
                                        class="text-gray-300 hover:text-red-500">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif
                        </div>
                        <p class="mt-0.5 whitespace-pre-wrap break-words text-sm text-gray-800 dark:text-gray-100">{{ $comment->body }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
