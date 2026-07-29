<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeedPaginationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression: the feed pages on a descending `id` cursor, so its ordering
     * must be by id too. Ordering by created_at while cursoring on id repeated
     * or skipped posts whenever the two disagreed (e.g. backdated records).
     */
    public function test_cursor_pagination_walks_every_post_exactly_once(): void
    {
        $author = User::factory()->create();
        $reader = User::factory()->create();

        $posts = Post::factory()->count(25)->for($author)->create();
        // Backdate in the opposite order of insertion to force the two orders apart.
        $posts->each(fn (Post $post, int $i) => $post->forceFill([
            'created_at' => now()->subHours($i),
        ])->saveQuietly());

        $this->actingAs($reader);

        $component = Livewire::test('feed')->set('tab', 'explore');

        $seen = $component->get('postIds');
        while ($component->get('hasMore')) {
            $component->call('loadMore');
            $seen = $component->get('postIds');
        }

        $expected = Post::pluck('id')->sort()->values()->all();

        $this->assertCount(count($expected), $seen, 'expected no duplicated or skipped posts');
        $this->assertSame($expected, collect($seen)->sort()->values()->all());
        $this->assertSame(collect($seen)->sortDesc()->values()->all(), $seen, 'feed should stay newest-first across pages');
    }

    public function test_the_feed_only_shows_followed_authors_and_myself(): void
    {
        $me = User::factory()->create();
        $followed = User::factory()->create();
        $stranger = User::factory()->create();

        $me->follow($followed);

        $mine = Post::factory()->for($me)->create();
        $theirs = Post::factory()->for($followed)->create();
        $unrelated = Post::factory()->for($stranger)->create();

        $this->actingAs($me);

        $ids = Livewire::test('feed')->get('postIds');

        $this->assertContains($mine->id, $ids);
        $this->assertContains($theirs->id, $ids);
        $this->assertNotContains($unrelated->id, $ids);
    }
}
