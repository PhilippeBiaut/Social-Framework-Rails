<x-layouts.app title="Sign in">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <span class="mx-auto mb-3 grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white">
                <svg class="size-7" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 15a6 6 0 1112 0v1a1 1 0 01-1 1H5a1 1 0 01-1-1v-1z"/></svg>
            </span>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back</h1>
            <p class="text-sm text-muted">Sign in to continue.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="card space-y-4 p-6" x-data="{ show: false }">
            @csrf

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" required autofocus autocomplete="username"
                       value="{{ old('email') }}" placeholder="you@example.com" class="input">
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="label !mb-0">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-indigo-600 hover:underline dark:text-indigo-400">Forgot password?</a>
                </div>
                <div class="relative mt-2">
                    <input id="password" name="password" required autocomplete="current-password" maxlength="72"
                           placeholder="Your password" class="input pr-10"
                           :type="show ? 'text' : 'password'">
                    <button type="button" @click="show = !show" tabindex="-1"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-gray-600 dark:hover:text-gray-200"
                            aria-label="Toggle password visibility">
                        <svg x-show="!show" class="size-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="show" x-cloak class="size-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"/></svg>
                    </button>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <input type="checkbox" name="remember" value="1"
                       class="size-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                Remember me
            </label>

            <button type="submit" class="btn-primary w-full cursor-pointer">Sign in</button>
        </form>

        <p class="mt-5 text-center text-sm text-muted">
            New here?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Create an account</a>
        </p>
    </div>
</x-layouts.app>
