<?php

use App\Models\Post;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Feed')] class extends Component
{
    public const PER_PAGE = 10;

    #[Url]
    public string $tab = 'following';

    /** Ids already rendered; the cursor is the smallest of them. */
    public array $postIds = [];

    public bool $hasMore = false;

    public function mount(): void
    {
        $this->loadPage(reset: true);
    }

    public function updatedTab(): void
    {
        $this->loadPage(reset: true);
    }

    public function loadMore(): void
    {
        $this->loadPage();
    }

    #[On('post-created')]
    public function prependPost(int $postId): void
    {
        array_unshift($this->postIds, $postId);
    }

    #[On('post-deleted')]
    public function removePost(int $postId): void
    {
        $this->postIds = array_values(array_diff($this->postIds, [$postId]));
    }

    /**
     * Paging is done on a descending id cursor, so the query must order by id
     * too: ordering by created_at while cursoring on id would repeat or skip
     * rows whenever the two disagree.
     */
    private function loadPage(bool $reset = false): void
    {
        if ($reset) {
            $this->postIds = [];
        }

        $query = $this->tab === 'explore'
            ? Post::query()
            : auth()->user()->feedQuery();

        if ($cursor = $this->cursor()) {
            $query->where('id', '<', $cursor);
        }

        $ids = $query->orderByDesc('id')->limit(self::PER_PAGE + 1)->pluck('id')->all();

        $this->hasMore = count($ids) > self::PER_PAGE;
        $this->postIds = [...$this->postIds, ...array_slice($ids, 0, self::PER_PAGE)];
    }

    private function cursor(): ?int
    {
        return $this->postIds === [] ? null : min($this->postIds);
    }

    /** @return Collection<int, Post> */
    public function posts(): Collection
    {
        if ($this->postIds === []) {
            return collect();
        }

        return Post::withCardData()
            ->whereIn('id', $this->postIds)
            ->orderByDesc('id')
            ->get();
    }

    public function with(): array
    {
        return ['posts' => $this->posts()];
    }
};
?>

<div>
    <h1 class="sr-only">Your feed</h1>

    <livewire:post-composer />

    {{-- Feed tabs --}}
    <div class="mt-6 flex rounded-xl border border-gray-200 bg-white p-1 text-sm font-medium dark:border-gray-700 dark:bg-gray-800" role="tablist">
        @foreach (['following' => 'For you', 'explore' => 'Explore'] as $value => $label)
            <button type="button" wire:click="$set('tab', '{{ $value }}')" role="tab"
                    aria-selected="{{ $tab === $value ? 'true' : 'false' }}"
                    @class([
                        'flex-1 rounded-lg px-3 py-1.5 text-center transition',
                        'bg-indigo-600 text-white shadow' => $tab === $value,
                        'text-muted hover:text-gray-900 dark:hover:text-white' => $tab !== $value,
                    ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- aria-relevant=additions keeps this to "a new post arrived" rather than
         re-reading the whole timeline on every update. --}}
    <div class="mt-4 space-y-4" aria-live="polite" aria-relevant="additions">
        @forelse ($posts as $post)
            <livewire:post-card :post="$post" :key="'post-'.$post->id" />
        @empty
            <x-empty-feed :tab="$tab" />
        @endforelse

        @if ($hasMore)
            {{-- Auto-loads when the sentinel scrolls into view; a spinner only,
                 to match the Rails port's lazy frame. --}}
            <div x-data x-intersect.margin.300px="$wire.loadMore()" class="flex justify-center py-6">
                <svg class="size-6 animate-spin text-indigo-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        @endif
    </div>
</div>
