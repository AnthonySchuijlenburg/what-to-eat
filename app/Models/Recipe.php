<?php

namespace App\Models;

use Database\Factories\RecipeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    /** @use HasFactory<RecipeFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'steps',
        'serves',
        'preparation_time',
        'course',
        'nutritional_value',
        'image_url',
    ];

    protected $casts = [
        'steps' => 'array',
        'variable_size' => 'boolean',
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
