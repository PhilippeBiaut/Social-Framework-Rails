Social Framework — Laravel edition
==================================

The same social network as the Rails app at the repository root, rebuilt on
**Laravel 13** with **Livewire 4 + Alpine** and the same **Tailwind CSS v4 +
Flowbite** design system.

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4-4E56A6)
![Tailwind](https://img.shields.io/badge/Tailwind-v4-38BDF8?logo=tailwindcss&logoColor=white)
![Flowbite](https://img.shields.io/badge/Flowbite-4-1A56DB)

## Features

Feature parity with the Rails version:

- 🔐 **Authentication** — hand-rolled register / login / logout on Laravel's
  session guard, with throttling, "remember me", a password strength meter and
  show/hide toggle, plus a full password-reset flow (queued mail notification).
- 📝 **Posts** — composer with live character counter, auto-growing textarea and
  image upload preview, published without a page reload.
- ❤️ **Likes** — optimistic UI (Alpine flips the heart instantly) reconciled by
  the Livewire round-trip.
- 💬 **Comments** — added and removed live under each post.
- 👥 **Social graph** — follow / unfollow, followers & following lists, a
  "For you" feed vs. an "Explore" tab.
- 🧑‍🎨 **Profiles** — gradient header, avatar upload, editable bio, Posts/Likes tabs.
- 🌓 **Dark mode** — class-based, persisted in `localStorage`, no flash on load.
- ♾️ **Infinite scroll** — `x-intersect` sentinel calling back into Livewire.
- 🔎 **Live search** — debounced `wire:model.live` people directory.

## How it maps to the Rails version

| Concern | Rails | Laravel |
|---|---|---|
| Interactivity | Turbo Streams + 14 Stimulus controllers | Livewire components + Alpine |
| Templates | ERB partials | Blade + Livewire single-file components |
| ORM | Active Record | Eloquent |
| Uploads | Active Storage | `Storage` disk `public` |
| Styling | Tailwind v4 + Flowbite | *identical* |

### Livewire 4 single-file components

Components live in `resources/views/components/` and put their class and
markup in one file:

```php
<?php
use Livewire\Component;

new class extends Component { /* state + actions */ };
?>

<div>{{-- markup --}}</div>
```

`feed`, `post-composer`, `post-card`, `comment-section`, `follow-button`,
`people`, `profile`, `profile-edit`, `connections`, `post-page`.

Pure-presentation pieces (`avatar`, `navbar`, `flash`, `user-row`,
`timestamp`, `empty-feed`) are plain Blade components in the same directory.

### Data model

```
User ──< Post ──< Comment
 │        └─────< Like >───── User
 └──< follows (follower_id / followed_id, self-referential) >── User
```

## Getting started

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm run build          # or: npm run dev
php artisan serve
```

Then open http://localhost:8000.

### Demo accounts

Seeded users (password: `password`): `ada@example.com`, `grace@example.com`,
`dhh@example.com`, `linus@example.com`, `yukihiro@example.com`,
`taylor@example.com`.

## Tests

```bash
php artisan test
./vendor/bin/pint --test    # code style
```

22 feature tests cover registration, posting, commenting, liking, following,
profile editing, search escaping and the password-reset flow — plus a regression
test pinning the feed's cursor pagination (ordering must match the cursor
column, or pages repeat rows).

## Kept in step with the Rails app

Both ports serve the same URLs (`/login`, `/register`, `/forgot-password`,
`/people`, `/settings/profile`, `/users/{username}`), the same seed data and the
same relative-time strings, so the two can be diffed screen by screen. With
identical data and matching fonts, 13 of 17 screens render pixel-identical and
the rest differ by under 0.1%.
