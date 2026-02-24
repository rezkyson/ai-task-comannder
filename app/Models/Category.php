<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
    ];

    /**
     * Get all tasks for this category.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
