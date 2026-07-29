<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'username', 'email', 'password', 'bio', 'avatar_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Profiles live at /users/{username}. */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // --- Relationships ------------------------------------------------------

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'likes')->withTimestamps();
    }

    /** People this user follows. */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
            ->withTimestamps();
    }

    /** People who follow this user. */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
            ->withTimestamps();
    }

    // --- Social helpers -----------------------------------------------------

    public function follow(User $user): void
    {
        if ($user->is($this)) {
            return;
        }

        $this->following()->syncWithoutDetaching([$user->id]);
    }

    public function unfollow(User $user): void
    {
        $this->following()->detach($user->id);
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->whereKey($user->id)->exists();
    }

    /**
     * Posts from the people I follow, plus my own. Callers add ordering, eager
     * loading and pagination so the feed can be sliced without duplicating this.
     */
    public function feedQuery()
    {
        return Post::whereIn('user_id', [...$this->following()->pluck('users.id')->all(), $this->id]);
    }

    // --- Presentation -------------------------------------------------------

    public function displayName(): string
    {
        return $this->name ?: $this->username;
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->displayName())) ?: [];

        return mb_strtoupper(implode('', array_map(
            static fn (string $word): string => mb_substr($word, 0, 1),
            array_slice(array_filter($words), 0, 2)
        )));
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null;
    }
}
