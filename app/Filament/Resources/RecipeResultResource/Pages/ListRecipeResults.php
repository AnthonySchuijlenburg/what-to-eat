<?php

namespace App\Filament\Resources\RecipeResultResource\Pages;

use App\Filament\Resources\RecipeResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecipeResults extends ListRecords
{
    protected static string $resource = RecipeResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
