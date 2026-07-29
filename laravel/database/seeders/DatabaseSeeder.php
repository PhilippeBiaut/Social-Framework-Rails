<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Demo data. Every write is idempotent, so re-seeding is a no-op instead of
     * a way to duplicate the whole timeline.
     */
    public function run(): void
    {
        $people = [
            ['username' => 'ada', 'name' => 'Ada Lovelace', 'bio' => 'Writing the first algorithm, one loop at a time. 💻'],
            ['username' => 'grace', 'name' => 'Grace Hopper', 'bio' => 'Debugging since before it was cool. 🦟'],
            ['username' => 'linus', 'name' => 'Linus T.', 'bio' => 'Just for fun. Kernel enthusiast.'],
            ['username' => 'dhh', 'name' => 'David H.', 'bio' => 'Convention over configuration. Rails forever. 🚀'],
            ['username' => 'yukihiro', 'name' => 'Yukihiro M.', 'bio' => 'Optimizing for developer happiness.'],
            ['username' => 'taylor', 'name' => 'Taylor O.', 'bio' => 'Elegant code for artisans. Laravel. ✨'],
        ];

        $users = collect($people)->mapWithKeys(fn (array $attrs) => [
            $attrs['username'] => User::firstOrCreate(
                ['username' => $attrs['username']],
                [
                    'name' => $attrs['name'],
                    'bio' => $attrs['bio'],
                    'email' => $attrs['username'].'@example.com',
                    'password' => 'password',
                ]
            ),
        ]);

        foreach ([
            ['ada', 'grace'], ['ada', 'dhh'], ['ada', 'taylor'],
            ['grace', 'ada'], ['grace', 'linus'],
            ['linus', 'yukihiro'], ['linus', 'dhh'],
            ['dhh', 'taylor'], ['dhh', 'yukihiro'], ['dhh', 'ada'],
            ['yukihiro', 'dhh'], ['yukihiro', 'taylor'],
            ['taylor', 'dhh'], ['taylor', 'ada'], ['taylor', 'grace'],
        ] as [$follower, $followed]) {
            $users[$follower]->follow($users[$followed]);
        }

        $timeline = [
            ['ada', 'Hello world 👋 Excited to join this little corner of the internet.'],
            ['grace', "Reminder: it's easier to ask forgiveness than permission. Ship it."],
            ['dhh', 'Just refactored a controller down to 6 lines. Convention over configuration is undefeated.'],
            ['taylor', "Livewire + Alpine = server-driven UI without writing an API. Try it, you'll love it."],
            ['yukihiro', 'A programming language should feel natural. Ruby was designed for humans first.'],
            ['linus', 'Talk is cheap. Show me the code.'],
            ['ada', 'Spent the afternoon reading about Livewire. Reactive UI without a single line of custom JS 🤯'],
            ['dhh', 'Dark mode toggle in 10 lines of Alpine. The web platform is good, actually.'],
            ['grace', "The most dangerous phrase is 'we've always done it this way'."],
            ['taylor', 'Small components, small views, small everything. Composition scales.'],
        ];

        // Insert oldest first so ids grow with time, the way real traffic writes
        // them. The feed pages on an id cursor, so backdating in reverse would
        // make ordering by id disagree with ordering by created_at.
        $total = count($timeline);
        $posts = collect($timeline)->reverse()->values()
            ->map(function (array $entry, int $i) use ($users, $total) {
                [$username, $body] = $entry;

                $post = Post::firstOrCreate([
                    'user_id' => $users[$username]->id,
                    'body' => $body,
                ]);

                $post->forceFill(['created_at' => now()->subHours($total - 1 - $i)])->saveQuietly();

                return $post;
            })
            ->reverse()->values();

        // Deterministic spread of likes (no rand) so re-seeding stays a no-op.
        $roster = $users->values();
        $posts->each(function (Post $post, int $i) use ($roster) {
            foreach (range(0, $i % 4) as $offset) {
                $user = $roster[($i + $offset) % $roster->count()];
                $post->likes()->firstOrCreate(['user_id' => $user->id]);
            }
        });

        foreach ([
            ['grace', 0, 'Welcome aboard! 🎉'],
            ['dhh', 0, 'Great to have you here.'],
            ['ada', 3, 'This is exactly what I was looking for. Thanks Taylor!'],
            ['linus', 2, 'Six lines? Rookie numbers.'],
        ] as [$username, $index, $body]) {
            Comment::firstOrCreate([
                'user_id' => $users[$username]->id,
                'post_id' => $posts[$index]->id,
                'body' => $body,
            ]);
        }

        $this->command?->info(sprintf(
            'Done! %d users, %d posts, %d comments, %d likes.',
            User::count(), Post::count(), Comment::count(), Like::count()
        ));
        $this->command?->info(
            'Sign in with any of: '.$users->keys()->map(fn (string $u) => $u.'@example.com')->implode(', ').' (password: "password")'
        );
    }
}
