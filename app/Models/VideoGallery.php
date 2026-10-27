<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VideoGallery extends Model
{
    use HasTranslations;

    protected $table = 'video_gallery';

    protected $fillable = ['title', 'video_url', 'thumbnail', 'sort_order', 'is_active'];

    protected $casts = [
        'title'      => 'array',
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    /** YouTube linkindən video ID (watch?v=, youtu.be/, shorts/, embed/) */
    public function youtubeId(): ?string
    {
        $pattern = '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~';

        return preg_match($pattern, (string) $this->video_url, $m) ? $m[1] : null;
    }

    /** Saytda <iframe> üçün link */
    public function embedUrl(): ?string
    {
        if ($id = $this->youtubeId()) {
            return "https://www.youtube-nocookie.com/embed/{$id}";
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', (string) $this->video_url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }

        return null;
    }

    /** Önizləmə şəkli: yüklənibsə o, yoxdursa YouTube-un şəkli */
    public function thumbnailUrl(): ?string
    {
        if ($this->thumbnail) {
            return Storage::disk('public')->url($this->thumbnail);
        }

        if ($id = $this->youtubeId()) {
            return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
        }

        return null;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }
}
