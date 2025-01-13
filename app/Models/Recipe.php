<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'steps',
        'variable_size',
        'serves',
        'source',
    ];

    protected $casts = [
        'steps' => 'array',
        'variable_size' => 'boolean',
    ];
}
