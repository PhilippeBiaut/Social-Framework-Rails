<?php

use App\Models\Post;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    /** Exposed to the template (the Blade half can't see class constants). */
    public int $maxLength = 1000;

    #[Validate('required|string|max:1000')]
    public string $body = '';

    #[Validate('nullable|image|mimes:png,jpeg,jpg,webp,gif|max:10240')]
    public ?TemporaryUploadedFile $image = null;

    public function save(): void
    {
        $this->validate();

        $post = Post::create([
            'user_id' => auth()->id(),
            'body' => $this->body,
            'image_path' => $this->image?->store('posts', 'public'),
        ]);

        $this->reset('body', 'image');

        $this->dispatch('post-created', postId: $post->id);
    }

    public function removeImage(): void
    {
        $this->reset('image');
    }
};
?>

<div class="card p-4" x-data="{ count: 0 }" x-on:post-created.window="count = 0">
    <form wire:submit="save" class="space-y-3">
        @error('body') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        @error('image') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

        <div class="flex gap-3">
            <x-avatar :user="auth()->user()" :link="false" />

            <div class="flex-1 space-y-2">
                <textarea wire:model="body" rows="2" placeholder="What's happening?"
                          x-on:input="count = $event.target.value.length; $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                          class="w-full resize-none border-0 bg-transparent p-0 text-base text-gray-900 placeholder-gray-400 focus:ring-0 dark:text-white"></textarea>

                @if ($image)
                    <div class="relative">
                        <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="max-h-80 w-full rounded-xl object-cover">
                        <button type="button" wire:click="removeImage" aria-label="Remove image"
                                class="absolute right-2 top-2 grid size-8 place-items-center rounded-full bg-black/60 text-white hover:bg-black/80">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <div wire:loading wire:target="image" class="text-xs text-gray-400">Uploading image…</div>
            </div>
        </div>

        <div class="flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-700">
            <label class="btn-ghost cursor-pointer !px-2.5 text-indigo-500" title="Add photo">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                <input type="file" wire:model="image" accept="image/*" class="hidden">
            </label>

            <div class="flex items-center gap-3">
                <span class="text-sm tabular-nums"
                      :class="{
                          'text-red-500': count > {{ $maxLength }},
                          'text-amber-500': count <= {{ $maxLength }} && count > {{ $maxLength }} * 0.9,
                          'text-gray-400': count <= {{ $maxLength }} * 0.9
                      }"
                      x-text="{{ $maxLength }} - count">{{ $maxLength }}</span>

                <button type="submit" class="btn-primary cursor-pointer disabled:cursor-not-allowed disabled:opacity-40"
                        :disabled="count === 0 || count > {{ $maxLength }}"
                        wire:loading.attr="disabled" wire:target="save,image">
                    Post
                </button>
            </div>
        </div>
    </form>
</div>
