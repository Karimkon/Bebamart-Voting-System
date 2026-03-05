<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'title',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'category',
        'order',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'order' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Get the parent mediable model.
     */
    public function mediable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include images.
     */
    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    /**
     * Scope a query to only include videos.
     */
    public function scopeVideos($query)
    {
        return $query->where('file_type', 'video');
    }

    /**
     * Scope a query to only include featured media.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to order by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the full URL for the media file
     */
    public function getUrlAttribute()
    {
        return asset($this->file_path);
    }

    /**
     * Get human readable file size
     */
    public function getHumanFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if media is an image
     */
    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    /**
     * Check if media is a video
     */
    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }
}
