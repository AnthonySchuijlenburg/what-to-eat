<?php

namespace App\Models;

use Database\Factories\ScrapedRecipeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Model|static updateOrCreate(array $attributes, array $values = [])
 */
class ScrapedRecipe extends Model
{
    /** @use HasFactory<ScrapedRecipeFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'source',
        'content',
        'scraped_at',
        'processed_at',
        'last_modified_at',
    ];
}
