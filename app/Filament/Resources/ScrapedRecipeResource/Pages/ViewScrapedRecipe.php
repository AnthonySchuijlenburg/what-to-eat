<?php

namespace App\Filament\Resources\ScrapedRecipeResource\Pages;

use App\Filament\Resources\ScrapedRecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewScrapedRecipe extends ViewRecord
{
    protected static string $resource = ScrapedRecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
