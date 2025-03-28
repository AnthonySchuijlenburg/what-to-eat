<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers\IngredientsRelationManager;
use App\Filament\Resources\RecipeResource\RelationManagers\RecipeResultsRelationManager;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationGroup = 'Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_url')
                    ->image()
                    ->required()
                    ->visibility('public')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('serves')
                    ->required(),
                Forms\Components\TextInput::make('preparation_time')
                    ->required(),
                Forms\Components\TextInput::make('course')
                    ->required(),
                Forms\Components\TextInput::make('nutritional_value')
                    ->required(),
                Forms\Components\TextInput::make('source_url')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('steps')
                    ->simple(
                        TextInput::make('step')->required(),
                    )
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ingredients_count')
                    ->counts('ingredients')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serves')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('preparation_time')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('course')
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('image_url'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('preparation_time')
                    ->multiple()
                    ->options(
                        function () {
                            $options = Recipe::select('preparation_time')
                                ->distinct()
                                ->get()
                                ->toArray();

                            $options = array_map(fn ($item) => [$item['preparation_time'] => ucfirst($item['preparation_time'])], $options);

                            return array_merge(...$options);
                        }),
                SelectFilter::make('course')
                    ->multiple()
                    ->options(
                        function () {
                            $options = Recipe::select('course')
                                ->distinct()
                                ->get()
                                ->toArray();

                            $options = array_map(fn ($item) => [$item['course'] => ucfirst($item['course'])], $options);

                            return array_merge(...$options);
                        }),
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
            IngredientsRelationManager::class,
            RecipeResultsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
            'view' => Pages\ViewRecipe::route('/{record}'),
        ];
    }
}
