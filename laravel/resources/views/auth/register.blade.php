<x-layouts.app title="Join Social">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <span class="mx-auto mb-3 grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white">
                <svg class="size-7" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 15a6 6 0 1112 0v1a1 1 0 01-1 1H5a1 1 0 01-1-1v-1z"/></svg>
            </span>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create your account</h1>
            <p class="text-sm text-muted">Join the conversation.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="card space-y-4 p-6"
              x-data="{
                  show: false,
                  password: '',
                  get score() {
                      let s = 0
                      if (this.password.length >= 8) s++
                      if (this.password.length >= 12) s++
                      if (/[A-Z]/.test(this.password) && /[a-z]/.test(this.password)) s++
                      if (/\d/.test(this.password)) s++
                      if (/[^A-Za-z0-9]/.test(this.password)) s++
                      return Math.min(s, 5)
                  },
                  get level() {
                      return [
                          { label: 'Too weak', color: 'bg-red-500', width: '20%' },
                          { label: 'Weak', color: 'bg-orange-500', width: '40%' },
                          { label: 'Fair', color: 'bg-yellow-500', width: '60%' },
                          { label: 'Good', color: 'bg-lime-500', width: '80%' },
                          { label: 'Strong', color: 'bg-green-500', width: '100%' },
                      ][Math.max(0, this.score - 1)]
                  }
              }">
            @csrf

            @if ($errors->any())
                <div role="alert" class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" required autofocus autocomplete="email"
                       value="{{ old('email') }}" placeholder="you@example.com" @error('email') aria-invalid="true" @enderror class="input">
            </div>

            <div>
                <label for="username" class="label">Username</label>
                <div class="flex">
                    <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 px-3 text-sm text-gray-600 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300">&#64;</span>
                    <input id="username" name="username" required autocomplete="off"
                           value="{{ old('username') }}" placeholder="yourname" @error('username') aria-invalid="true" @enderror class="input !rounded-l-none">
                </div>
            </div>

            <div>
                <label for="name" class="label">Display name</label>
                <input id="name" name="name" value="{{ old('name') }}" placeholder="Optional" @error('name') aria-invalid="true" @enderror class="input">
            </div>

            <div>
                <label for="password" class="label">Password</label>
                <div class="relative">
                    <input id="password" name="password" required autocomplete="new-password" maxlength="72" @error('password') aria-invalid="true" @enderror
                           class="input pr-10" x-model="password" :type="show ? 'text' : 'password'">
                    <button type="button" @click="show = !show" tabindex="-1"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-gray-600 dark:hover:text-gray-200"
                            aria-label="Toggle password visibility">
                        <svg x-show="!show" class="size-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="show" x-cloak class="size-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"/></svg>
                    </button>
                </div>
                <div class="mt-2">
                    <div class="h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-600">
                        <div class="h-1.5 rounded-full transition-all duration-300"
                             :class="password ? level.color : 'bg-gray-200 dark:bg-gray-600'"
                             :style="`width: ${password ? level.width : '0%'}`"></div>
                    </div>
                    <p class="mt-1 text-xs text-muted" x-text="password ? level.label : ''"></p>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="label">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       autocomplete="new-password" maxlength="72" placeholder="Repeat your password" @error('password_confirmation') aria-invalid="true" @enderror class="input">
            </div>

            <button type="submit" class="btn-primary w-full cursor-pointer">Create account</button>
        </form>

        <p class="mt-5 text-center text-sm text-muted">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Sign in</a>
        </p>
    </div>
</x-layouts.app>
