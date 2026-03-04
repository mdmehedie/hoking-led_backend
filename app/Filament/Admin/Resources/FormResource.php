<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FormResource\Pages;
use App\Models\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use App\Filament\Admin\Resources\FormResource\RelationManagers\WebhooksRelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Forms');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create form');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit form');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete form');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view form');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Form Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Builder::make('fields')
                    ->label(__('Form Fields'))
                    ->blocks([
                        Forms\Components\Builder\Block::make('text')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label(__('Label'))
                                    ->required(),
                                Forms\Components\TextInput::make('placeholder')
                                    ->label(__('Placeholder')),
                                Forms\Components\Toggle::make('required')
                                    ->label(__('Required')),
                            ])
                            ->label(__('Text Input')),
                        Forms\Components\Builder\Block::make('email')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label(__('Label'))
                                    ->required(),
                                Forms\Components\TextInput::make('placeholder')
                                    ->label(__('Placeholder')),
                                Forms\Components\Toggle::make('required')
                                    ->label(__('Required')),
                            ])
                            ->label(__('Email Input')),
                        Forms\Components\Builder\Block::make('textarea')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label(__('Label'))
                                    ->required(),
                                Forms\Components\TextInput::make('placeholder')
                                    ->label(__('Placeholder')),
                                Forms\Components\Toggle::make('required')
                                    ->label(__('Required')),
                            ])
                            ->label(__('Textarea')),
                        Forms\Components\Builder\Block::make('select')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label(__('Label'))
                                    ->required(),
                                Forms\Components\Repeater::make('options')
                                    ->label(__('Options'))
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->label(__('Label'))
                                            ->required(),
                                        Forms\Components\TextInput::make('value')
                                            ->label(__('Value'))
                                            ->required(),
                                    ])
                                    ->collapsible(),
                                Forms\Components\Toggle::make('required')
                                    ->label(__('Required')),
                            ])
                            ->label(__('Select Dropdown')),
                    ])
                    ->addActionAlignment('center')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('success_message')
                    ->label(__('Success Message')),
                Forms\Components\Toggle::make('email_notifications')
                    ->label(__('Enable Email Notifications')),
                Forms\Components\TagsInput::make('notification_emails')
                    ->label(__('Notification Emails'))
                    ->placeholder(__('Enter email addresses'))
                    ->visible(fn ($get) => $get('email_notifications')),
                Forms\Components\Toggle::make('store_leads')
                    ->label(__('Store Leads'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Form Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('email_notifications')
                    ->label(__('Email Notifications')),
                Tables\Columns\BooleanColumn::make('store_leads')
                    ->label(__('Store Leads')),
            ])
            ->recordUrl(fn ($record) => static::getUrl('edit', ['record' => $record]))
            ->filters([
                SelectFilter::make('email_notifications')
                    ->label(__('Email Notifications'))
                    ->options([
                        '0' => __('No'),
                        '1' => __('Yes'),
                    ]),
                SelectFilter::make('store_leads')
                    ->label(__('Store Leads'))
                    ->options([
                        '0' => __('No'),
                        '1' => __('Yes'),
                    ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn ($record) => static::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
                \Filament\Actions\DeleteAction::make()
                    ->label(__('Delete')),
            ])
            ->bulkActions([
                BulkAction::make('delete_selected')
                    ->label('Delete Selected')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each->delete();
                        Notification::make()
                            ->success()
                            ->title('Deleted')
                            ->body($count . ' items deleted successfully.')
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            WebhooksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
