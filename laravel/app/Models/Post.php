<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['user_id', 'body', 'image_path'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->oldest();
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * `id` breaks ties so the order is total: the feed pages on an `id` cursor,
     * which only yields stable pages if the ordering is deterministic.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * Everything the post card touches, so rendering a list costs a fixed
     * number of queries instead of one per row.
     */
    public function scopeWithCardData(Builder $query): Builder
    {
        return $query->with(['user', 'likes'])->withCount(['likes', 'comments']);
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // Reads the preloaded association when the caller eager-loaded `likes`
        // (the feed does), instead of firing one EXISTS query per post.
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }
}
