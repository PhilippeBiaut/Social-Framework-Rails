Social-Framework-Rails
======================

A small but complete **social network** built with **Ruby on Rails 8**, styled
with **Tailwind CSS + Flowbite**, and made interactive with **Hotwire (Turbo +
Stimulus)** — no build step, no SPA framework, just HTML over the wire.

> **Also available in Laravel.** The same app, rebuilt on Laravel 13 +
> Livewire 4 + Alpine with the identical Flowbite design system, lives in
> [`laravel/`](laravel/README.md).

![Rails](https://img.shields.io/badge/Rails-8.1-CC0000?logo=rubyonrails&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind-v4-38BDF8?logo=tailwindcss&logoColor=white)
![Flowbite](https://img.shields.io/badge/Flowbite-4-1A56DB)
![Hotwire](https://img.shields.io/badge/Hotwire-Turbo%20%2B%20Stimulus-5cae91)

## Features

- 🔐 **Authentication** — Rails 8's native generator (bcrypt sessions), with a
  custom sign-up flow, password strength meter and show/hide toggle.
- 📝 **Posts** — compose with a live character counter, auto-growing textarea and
  image preview; posted in real time via **Turbo Streams**.
- ❤️ **Likes** — optimistic UI (instant heart + count) reconciled by a Turbo
  Stream from the server.
- 💬 **Comments** — threaded under each post, appended live without a reload.
- 👥 **Social graph** — follow / unfollow, followers & following lists, a
  "For you" feed vs. an "Explore" tab.
- 🧑‍🎨 **Profiles** — gradient header, avatar upload (Active Storage), editable
  bio, client-side tabs (Posts / Likes).
- 🌓 **Dark mode** — class-based, persisted in `localStorage`, no flash on load.
- ♾️ **Infinite scroll** — via native lazy Turbo Frames.
- 🔎 **Live search** — debounced auto-submitting people directory.

## Tech highlights

### Stimulus controllers (`app/javascript/controllers`)

`theme`, `dropdown`, `modal`, `toast`, `char_counter`, `autosize`,
`image_preview`, `tabs`, `clipboard`, `password_visibility`,
`password_strength`, `autosubmit`, `like`, `reveal` — small, composable, and
Turbo-friendly.

### Flowbite + Tailwind v4

Loaded as a Tailwind v4 plugin (`@plugin "flowbite/plugin"` in
`app/assets/tailwind/application.css`). Component utilities (`btn`, `card`,
`input`, …) are defined with the v4 `@utility` API. All interactivity is driven
by Stimulus so it survives Turbo navigations cleanly.

### Data model

```
User ──< Post ──< Comment
 │        └─────< Like >───── User
 └──< Follow (follower / followed, self-referential) >── User
```

## Getting started

```bash
bundle install
npm install                 # pulls in flowbite for the Tailwind plugin
bin/rails db:prepare        # migrate + seed
bin/dev                     # Rails + Tailwind watcher (foreman)
```

Then open http://localhost:3000.

### Demo accounts

Seeded users (password: `password`): `ada@example.com`, `grace@example.com`,
`dhh@example.com`, `linus@example.com`, `yukihiro@example.com`,
`sam@example.com`.

## Tests

```bash
bin/rails test
```

Includes an end-to-end integration smoke test (`test/integration/smoke_test.rb`)
covering sign-up, posting, commenting, liking, following and profile editing.
