<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationLabel = 'Roles';

    public static function getNavigationLabel(): string
    {
        return __('Roles');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('User Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    public static function canAccessResource(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Admin']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create role');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit role');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete role');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view role');
    }

    public static function form(Schema $schema): Schema
    {
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            return $schema;
        }

        return $schema->schema([
            Section::make(__('Role Information'))->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Role Name'))
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->label(__('Description'))
                    ->maxLength(500),
            ]),
            Section::make(__('Permissions'))
                ->description(__('Select permissions for this role'))
                ->schema([
                    Forms\Components\CheckboxList::make('permissions')
                        ->relationship('permissions', 'name')
                        ->columns(3)
                        ->gridDirection('row')
                        ->searchable()
                        ->bulkToggleable()
                        ->helperText(__('Permissions assigned to this role')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Role Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label(__('Permissions')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->label(__('Edit')),
                \Filament\Actions\DeleteAction::make()
                    ->label(__('Delete'))
                    ->requiresConfirmation()
                    ->modalHeading(__('Delete Role'))
                    ->modalDescription(__('Are you sure you want to delete this role? Users assigned to this role will lose their permissions.'))
                    ->modalSubmitActionLabel(__('Yes, delete it')),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected'))
                        ->requiresConfirmation(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
