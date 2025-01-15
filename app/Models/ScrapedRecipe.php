<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ScrapedRecipe extends Model
{
    use HasUuids;

    protected $fillable = [
        'source',
        'content',
        'scraped_at',
        'processed_at',
    ];
}
