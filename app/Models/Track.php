<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Track extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title', 'artist', 'url', 'player', 'player_track_id', 'player_thumbnail_url', 'category_id', 'user_id', 'week_id'];


    /**
     * Get the user who shared this track.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
                    ->withDefault();
    }

    /**
     * Get the track likes.
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')
                    ->withPivot('liked_at');
    }

    /**
     * Get the track likes.
     */
    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class);
    }

    /**
     * Get current week tracks.
     */
    public function scopeCurrentWeek(Builder $query): Builder
    {
        return $query->whereRelation('week', 'year', date('Y'))
            ->whereRelation('week', 'week_number', date('W'));
    }

    /**
     * Get tracks ranking.
     */
    public function scopeRanking(Builder $query): Builder
    {
        return $query->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->orderBy('created_at', 'asc');
    }

    protected static $categories = [
        1 => 'Soul',
        2 => 'Ambient',
        3 => 'Pop',
        4 => 'Rap',
        5 => 'Funk',
        6 => 'Rock',
        7 => 'Reggae / Dub',
        8 => 'Techno',
        9 => 'Electro'
    ];

    public function getCategoryAttribute()
    {
        return self::$categories[$this->category_id] ?? 'Unknown';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
