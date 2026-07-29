<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SocialFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_sign_in(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_a_visitor_can_register_and_lands_on_the_feed(): void
    {
        $response = $this->post(route('register'), [
            'email' => 'newbie@example.com',
            'username' => 'newbie',
            'name' => 'New Bie',
            'password' => 'sup3r-secret-passw0rd',
            'password_confirmation' => 'sup3r-secret-passw0rd',
        ]);

        $response->assertRedirect(route('feed'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['username' => 'newbie']);
    }

    public function test_registration_rejects_an_invalid_username(): void
    {
        $this->post(route('register'), [
            'email' => 'nope@example.com',
            'username' => 'Not Valid!',
            'password' => 'sup3r-secret-passw0rd',
            'password_confirmation' => 'sup3r-secret-passw0rd',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_a_user_can_publish_a_post_from_the_composer(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('post-composer')
            ->set('body', 'My very first post!')
            ->call('save')
            ->assertDispatched('post-created');

        $this->assertDatabaseHas('posts', ['body' => 'My very first post!']);
    }

    public function test_the_composer_rejects_an_empty_post(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('post-composer')
            ->set('body', '')
            ->call('save')
            ->assertHasErrors('body');

        $this->assertSame(0, Post::count());
    }

    public function test_liking_a_post_is_a_toggle(): void
    {
        $me = User::factory()->create();
        $post = Post::factory()->create();
        $this->actingAs($me);

        Livewire::test('post-card', ['post' => $post])->call('toggleLike');
        $this->assertDatabaseHas('likes', ['user_id' => $me->id, 'post_id' => $post->id]);

        Livewire::test('post-card', ['post' => $post->fresh()])->call('toggleLike');
        $this->assertSame(0, Like::count());
    }

    public function test_a_user_can_comment_and_delete_their_own_comment(): void
    {
        $me = User::factory()->create();
        $post = Post::factory()->create();
        $this->actingAs($me);

        Livewire::test('comment-section', ['post' => $post])
            ->set('body', 'Nice one')
            ->call('addComment');

        $comment = Comment::firstOrFail();
        $this->assertSame('Nice one', $comment->body);

        Livewire::test('comment-section', ['post' => $post])
            ->call('deleteComment', $comment->id);

        $this->assertSame(0, Comment::count());
    }

    public function test_a_user_cannot_delete_someone_elses_post(): void
    {
        $post = Post::factory()->create();
        $this->actingAs(User::factory()->create());

        Livewire::test('post-card', ['post' => $post])
            ->call('delete')
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_following_is_a_toggle_and_shapes_the_feed(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $theirPost = Post::factory()->for($other)->create();
        $this->actingAs($me);

        // Not following yet: their post is absent from "For you".
        Livewire::test('feed')->assertDontSee($theirPost->body);

        Livewire::test('follow-button', ['user' => $other])->call('toggle');
        $this->assertTrue($me->fresh()->isFollowing($other));

        Livewire::test('feed')->assertSee($theirPost->body);

        Livewire::test('follow-button', ['user' => $other])->call('toggle');
        $this->assertFalse($me->fresh()->isFollowing($other));
    }

    public function test_a_user_cannot_follow_themselves(): void
    {
        $me = User::factory()->create();

        $me->follow($me);

        $this->assertSame(0, $me->following()->count());
    }

    public function test_profile_and_connection_pages_render(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        Post::factory()->for($other)->create();
        $me->follow($other);
        $this->actingAs($me);

        $this->get(route('users.show', $other))->assertOk()->assertSee($other->username);
        $this->get(route('users.followers', $other))->assertOk()->assertSee($me->username);
        $this->get(route('users.following', $me))->assertOk()->assertSee($other->username);
        $this->get(route('users.index'))->assertOk();
    }

    public function test_a_user_can_update_their_profile(): void
    {
        $me = User::factory()->create(['username' => 'before']);
        $this->actingAs($me);

        Livewire::test('profile-edit')
            ->set('username', 'after')
            ->set('bio', 'Updated bio')
            ->call('save');

        $me->refresh();
        $this->assertSame('after', $me->username);
        $this->assertSame('Updated bio', $me->bio);
    }

    public function test_people_search_escapes_like_wildcards(): void
    {
        $this->actingAs(User::factory()->create());
        $match = User::factory()->create(['username' => 'findme', 'name' => 'Find Me']);

        // A bare "%" must match nothing rather than acting as a wildcard.
        Livewire::test('people')
            ->set('q', '%')
            ->assertDontSee($match->username);

        Livewire::test('people')
            ->set('q', 'findme')
            ->assertSee($match->username);
    }
}
