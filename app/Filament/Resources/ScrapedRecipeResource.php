<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScrapedRecipeResource\Pages;
use App\Models\ScrapedRecipe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScrapedRecipeResource extends Resource
{
    protected static ?string $model = ScrapedRecipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('source')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('scraped_at'),
                Forms\Components\DateTimePicker::make('processed_at'),
                Forms\Components\Textarea::make('content')
                    ->rows(16)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scraped_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('scraped_at', 'desc')
            ->filters([
                Filter::make('scraped_at')->query(fn (Builder $query): Builder => $query->where('scraped_at', '!=', null)),
                Filter::make('processed_at')->query(fn (Builder $query): Builder => $query->where('processed_at', '!=', null)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScrapedRecipes::route('/'),
            'view' => Pages\ViewScrapedRecipe::route('/{record}'),
        ];
    }
}
