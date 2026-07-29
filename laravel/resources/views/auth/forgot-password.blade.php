<x-layouts.app title="Reset password">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Forgot your password?</h1>
            <p class="text-sm text-muted">We'll email you a reset link.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="card space-y-4 p-6">
            @csrf

            @if ($errors->any())
                <div role="alert" class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" required autofocus autocomplete="username"
                       value="{{ old('email') }}" placeholder="you@example.com" @error('email') aria-invalid="true" @enderror class="input">
            </div>

            <button type="submit" class="btn-primary w-full cursor-pointer">Email reset instructions</button>
        </form>

        <p class="mt-5 text-center text-sm text-muted">
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Back to sign in</a>
        </p>
    </div>
</x-layouts.app>
