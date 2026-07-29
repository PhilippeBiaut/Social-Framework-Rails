<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regression: the profile used to render every post an author had ever written,
 * which took seconds once that was a few thousand rows.
 */
class ProfilePaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_profile_renders_one_page_of_posts(): void
    {
        $author = User::factory()->create(['username' => 'author']);
        Post::factory()->count(Post::PER_PAGE + 5)->for($author)->create();
        $this->actingAs(User::factory()->create());

        Livewire::test('profile', ['user' => $author])
            ->assertViewHas('posts', fn ($posts) => $posts->count() === Post::PER_PAGE)
            ->assertSet('hasMore', true);
    }

    public function test_load_more_grows_the_page(): void
    {
        $author = User::factory()->create(['username' => 'author']);
        Post::factory()->count(Post::PER_PAGE + 3)->for($author)->create();
        $this->actingAs(User::factory()->create());

        Livewire::test('profile', ['user' => $author])
            ->call('loadMore')
            ->assertViewHas('posts', fn ($posts) => $posts->count() === Post::PER_PAGE + 3)
            ->assertSet('hasMore', false);
    }

    public function test_switching_tab_resets_the_page(): void
    {
        $author = User::factory()->create(['username' => 'author']);
        Post::factory()->count(Post::PER_PAGE + 5)->for($author)->create();
        $this->actingAs(User::factory()->create());

        Livewire::test('profile', ['user' => $author])
            ->call('loadMore')
            ->assertSet('shown', Post::PER_PAGE * 2)
            ->set('tab', 'likes')
            ->assertSet('shown', Post::PER_PAGE);
    }

    public function test_the_likes_tab_is_paged_too(): void
    {
        $author = User::factory()->create(['username' => 'author']);
        Post::factory()->count(Post::PER_PAGE + 2)->for($author)->create()
            ->each(fn (Post $post) => $post->likes()->create(['user_id' => $author->id]));
        $this->actingAs(User::factory()->create());

        Livewire::test('profile', ['user' => $author])
            ->set('tab', 'likes')
            ->assertViewHas('posts', fn ($posts) => $posts->count() === Post::PER_PAGE);
    }
}
