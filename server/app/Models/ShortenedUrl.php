<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortenedUrl extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'clicks',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'clicks' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the shortened URL.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all clicks for this shortened URL.
     */
    public function urlClicks(): HasMany
    {
        return $this->hasMany(UrlClick::class);
    }

    /**
     * Get recent clicks for analytics (last 30 days).
     */
    public function recentClicks(): HasMany
    {
        return $this->hasMany(UrlClick::class)
            ->where('created_at', '>=', now()->subDays(30))
            ->latest();
    }

    /**
     * Increment the click counter.
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }
}
