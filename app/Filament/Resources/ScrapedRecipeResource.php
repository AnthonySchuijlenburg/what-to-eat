<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScrapedRecipeResource\Pages;
use App\Models\ScrapedRecipe;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
                Forms\Components\DateTimePicker::make('last_modified_at'),
                Forms\Components\Textarea::make('content')
                    ->rows(16)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->state(function (ScrapedRecipe $record): string {
                        if ($record->scraped_at === null && $record->processed_at === null) {
                            return 'Unscraped';
                        }

                        if (
                            new Carbon($record->scraped_at) >= new Carbon('01-01-2029')
                        ) {
                            return 'Not found';
                        }

                        if (
                            new Carbon($record->scraped_at) <= new Carbon('01-01-2029')
                            && new Carbon($record->processed_at) >= new Carbon('01-01-2030')
                        ) {
                            return 'Error processing';
                        }

                        if ($record->processed_at === null) {
                            return 'Unprocessed';
                        }

                        return 'Processed';
                    })
                    ->color(function (ScrapedRecipe $record): string {
                        if ($record->scraped_at === null && $record->processed_at === null) {
                            return 'warning';
                        }

                        if (
                            new Carbon($record->scraped_at) >= new Carbon('01-01-2029')
                        ) {
                            return 'danger';
                        }

                        if (
                            new Carbon($record->scraped_at) <= new Carbon('01-01-2029')
                            && new Carbon($record->processed_at) >= new Carbon('01-01-2030')
                        ) {
                            return 'danger';
                        }

                        if ($record->processed_at === null) {
                            return 'warning';
                        }

                        return 'success';
                    }),
                TextColumn::make('scraped_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('scraped_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('last_modified_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('source')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('scraped_at', 'desc')
            ->filters([
                Filter::make('scraped_at')
                    ->form([
                        DatePicker::make('scraped_from'),
                        DatePicker::make('scraped_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scraped_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scraped_at', '>=', $date),
                            )
                            ->when(
                                $data['scraped_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scraped_at', '<=', $date),
                            );
                    }),
                Filter::make('processed_at')
                    ->form([
                        DatePicker::make('processed_from'),
                        DatePicker::make('processed_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['processed_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('processed_at', '>=', $date),
                            )
                            ->when(
                                $data['processed_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('processed_at', '<=', $date),
                            );
                    }),
                Filter::make('last_modified_at')
                    ->form([
                        DatePicker::make('last_modified_from'),
                        DatePicker::make('last_modified_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['last_modified_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_modified_at', '>=', $date),
                            )
                            ->when(
                                $data['last_modified_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_modified_at', '<=', $date),
                            );
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
