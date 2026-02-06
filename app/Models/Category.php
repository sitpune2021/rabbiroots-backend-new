<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'level',
        'icon',
        'image',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_image',
        'is_indexable',
        'is_active',
    ];

        /* ---------------- Relationships ---------------- */

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLevel(Builder $query, string $level)
    {
        return $query->where('level', $level);
    }

    /* ---------------- SEO Accessors ---------------- */

    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title
            ?? "{$this->name} | Buy Online at Best Price";
    }

    public function getSeoDescriptionAttribute(): string
    {
        return $this->meta_description
            ?? "Order {$this->name} online with fast delivery.";
    }

    public function getSeoCanonicalAttribute(): string
    {
        return $this->canonical_url ?? url()->current();
    }

    public function getSeoOgImageAttribute(): string
    {
        return $this->og_image
            ?? asset('images/default-category-og.png');
    }
}
