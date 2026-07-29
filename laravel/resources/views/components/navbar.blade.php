@php($me = auth()->user())

<nav class="sticky top-0 z-40 border-b border-gray-200 bg-white/80 backdrop-blur-md dark:border-gray-700 dark:bg-gray-900/80">
    <div class="mx-auto flex max-w-2xl items-center justify-between gap-2 px-4 py-2.5">
        <a href="{{ route('feed') }}" wire:navigate
           class="flex items-center gap-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">
            <span class="grid size-8 place-items-center rounded-xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white">
                <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 15a6 6 0 1112 0v1a1 1 0 01-1 1H5a1 1 0 01-1-1v-1z"/></svg>
            </span>
            <span class="hidden sm:inline">Social</span>
        </a>

        <div class="flex items-center gap-1">
            <a href="{{ route('feed') }}" wire:navigate class="btn-ghost !px-2.5" title="Feed"
               @if (request()->routeIs('feed')) aria-current="page" @endif>
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
            </a>

            <a href="{{ route('users.index') }}" wire:navigate class="btn-ghost !px-2.5" title="People"
               @if (request()->routeIs('users.index')) aria-current="page" @endif>
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </a>

            <a href="{{ route('feed') }}" wire:navigate class="btn-primary !px-3 !py-1.5" title="New post">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span class="hidden sm:inline">Post</span>
            </a>

            {{-- Theme toggle --}}
            <button type="button" class="btn-ghost !px-2.5" title="Toggle theme" aria-label="Toggle dark mode"
                    x-data="{
                        dark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.dark = !this.dark
                            localStorage.setItem('theme', this.dark ? 'dark' : 'light')
                            document.documentElement.classList.toggle('dark', this.dark)
                        }
                    }"
                    @click="toggle()">
                <svg x-show="!dark" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                <svg x-show="dark" x-cloak class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
            </button>

            {{-- User dropdown --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <x-avatar :user="$me" size="size-8" text="text-xs" :link="false" />
                </button>
                <div x-show="open" x-cloak x-transition.opacity
                     class="absolute right-0 z-50 mt-2 w-52 origin-top-right rounded-xl border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-100 px-4 py-2 dark:border-gray-700">
                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $me->displayName() }}</p>
                        <p class="truncate text-xs text-muted">&#64;{{ $me->username }}</p>
                    </div>
                    <a href="{{ route('users.show', $me) }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">My profile</a>
                    <a href="{{ route('profile.edit') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">Edit profile</a>
                    <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-700">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
