<?php

namespace App\Filament\Resources\RecipeResultResource\Pages;

use App\Filament\Resources\RecipeResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRecipeResult extends ViewRecord
{
    protected static string $resource = RecipeResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
