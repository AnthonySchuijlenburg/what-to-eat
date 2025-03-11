<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeResult extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeResultFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'url',
        'status_code',
        'result',
    ];
}
