<?php

use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Edit profile')] class extends Component
{
    use WithFileUploads;

    public int $maxBio = 280;

    public string $username = '';

    public string $name = '';

    public string $bio = '';

    public ?TemporaryUploadedFile $avatar = null;

    public function mount(): void
    {
        $me = auth()->user();

        $this->username = $me->username;
        $this->name = (string) $me->name;
        $this->bio = (string) $me->bio;
    }

    public function save(): void
    {
        $me = auth()->user();

        $validated = $this->validate([
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_]+$/', Rule::unique('users', 'username')->ignore($me->id)],
            'name' => ['nullable', 'string', 'max:60'],
            'bio' => ['nullable', 'string', 'max:280'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpeg,jpg,webp,gif', 'max:5120'],
        ], [
            'username.regex' => 'The username may only contain lowercase letters, numbers and underscores.',
        ]);

        if ($this->avatar) {
            $validated['avatar_path'] = $this->avatar->store('avatars', 'public');
        }
        unset($validated['avatar']);

        $me->update($validated);

        session()->flash('status', 'Profile updated.');

        $this->redirectRoute('users.show', $me->fresh(), navigate: true);
    }
};
?>

<div>
    <h1 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Edit profile</h1>

    <form wire:submit="save" class="card space-y-5 p-5" x-data="{ count: {{ mb_strlen($bio) }} }">
        @if ($errors->any())
            <div class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/40 dark:text-red-300">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <div>
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" alt="New avatar" class="size-20 rounded-full object-cover">
                @else
                    <x-avatar :user="auth()->user()" size="size-20" text="text-xl" :link="false" :ring="false" />
                @endif
            </div>
            <label class="btn-outline cursor-pointer">
                Change photo
                <input type="file" wire:model="avatar" accept="image/*" class="hidden">
            </label>
            <span wire:loading wire:target="avatar" class="text-xs text-gray-400">Uploading…</span>
        </div>

        <div>
            <label for="username" class="label">Username</label>
            <div class="flex">
                <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 px-3 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300">&#64;</span>
                <input id="username" wire:model="username" autocomplete="off" class="input !rounded-l-none">
            </div>
        </div>

        <div>
            <label for="name" class="label">Display name</label>
            <input id="name" wire:model="name" placeholder="Your name" class="input">
        </div>

        <div>
            <label for="bio" class="label">Bio</label>
            <textarea id="bio" wire:model="bio" rows="3" placeholder="Tell people about yourself"
                      x-on:input="count = $event.target.value.length"
                      class="input resize-none"></textarea>
            <p class="mt-1 text-right text-xs text-gray-400">
                <span x-text="{{ $maxBio }} - count">{{ $maxBio - mb_strlen($bio) }}</span> left
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary cursor-pointer">Save changes</button>
            <a href="{{ route('users.show', auth()->user()) }}" wire:navigate class="btn-ghost">Cancel</a>
        </div>
    </form>
</div>
