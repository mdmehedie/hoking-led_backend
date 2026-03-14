<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RegionResource\Pages;
use App\Models\Region;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::GlobeAmericas;

    protected static ?string $navigationLabel = 'Regions';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Region Settings Tabs')->tabs([
                    Tab::make(__('Region Information'))->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Region Code')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->helperText('e.g., us, uk, eu'),
                        Forms\Components\TextInput::make('name')
                            ->label('Region Name')
                            ->required()
                            ->maxLength(100)
                            ->helperText('e.g., United States, United Kingdom'),
                        Forms\Components\TextInput::make('currency')
                            ->label('Currency')
                            ->maxLength(3)
                            ->helperText('e.g., USD, GBP, EUR'),
                        Forms\Components\TextInput::make('timezone')
                            ->label('Timezone')
                            ->helperText('e.g., America/New_York'),
                        Forms\Components\TextInput::make('language')
                            ->label('Language')
                            ->maxLength(10)
                            ->helperText('e.g., en, en-US'),
                    ])->columns(2),
                    Tab::make(__('Settings'))->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Enable this region for users'),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Default Region')
                            ->default(false)
                            ->helperText('Set as default region for new users'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order in which regions appear'),
                    ])->columns(3),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('language')
                    ->label('Language')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
                Tables\Columns\ToggleColumn::make('is_default')
                    ->label('Default'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                Tables\Filters\SelectFilter::make('is_default')
                    ->label('Default')
                    ->options([
                        '1' => 'Default',
                        '0' => 'Not Default',
                    ]),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->url(fn ($record) => route('filament.admin.resources.regions.edit', $record))
                    ->icon('heroicon-o-pencil'),
                Action::make('delete')
                    ->label('Delete')
                    ->action(fn ($record) => $record->delete())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                BulkAction::make('toggle_active')
                    ->label('Toggle Active Status')
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                        });
                        Notification::make()
                            ->success()
                            ->title('Status Updated')
                            ->body($count . ' regions updated successfully.')
                            ->send();
                    })
                    ->icon('heroicon-o-power'),
                BulkAction::make('change_sort_order')
                    ->label('Change Sort Order')
                    ->form([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $count = $records->count();
                        $records->each->update(['sort_order' => $data['sort_order']]);
                        Notification::make()
                            ->success()
                            ->title('Sort Order Updated')
                            ->body($count . ' regions updated.')
                            ->send();
                    })
                    ->icon('heroicon-o-arrow-path'),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
