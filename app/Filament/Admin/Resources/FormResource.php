<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FormResource\Pages;
use App\Models\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Support\Icons\Heroicon;
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Builder::make('fields')
                    ->blocks([
                        Forms\Components\Builder\Block::make('text')
                            ->schema([
                                Forms\Components\TextInput::make('label')->required(),
                                Forms\Components\TextInput::make('placeholder')->label('Placeholder'),
                                Forms\Components\Toggle::make('required'),
                            ])
                            ->label('Text Input'),
                        Forms\Components\Builder\Block::make('email')
                            ->schema([
                                Forms\Components\TextInput::make('label')->required(),
                                Forms\Components\TextInput::make('placeholder')->label('Placeholder'),
                                Forms\Components\Toggle::make('required'),
                            ])
                            ->label('Email Input'),
                        Forms\Components\Builder\Block::make('textarea')
                            ->schema([
                                Forms\Components\TextInput::make('label')->required(),
                                Forms\Components\TextInput::make('placeholder')->label('Placeholder'),
                                Forms\Components\Toggle::make('required'),
                            ])
                            ->label('Textarea'),
                        Forms\Components\Builder\Block::make('select')
                            ->schema([
                                Forms\Components\TextInput::make('label')->required(),
                                Forms\Components\Repeater::make('options')
                                    ->schema([
                                        Forms\Components\TextInput::make('label')->required(),
                                        Forms\Components\TextInput::make('value')->required(),
                                    ])
                                    ->collapsible(),
                                Forms\Components\Toggle::make('required'),
                            ])
                            ->label('Select Dropdown'),
                    ])
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('success_message')
                    ->label('Success Message'),
                Forms\Components\Toggle::make('email_notifications')
                    ->label('Enable Email Notifications'),
                Forms\Components\TagsInput::make('notification_emails')
                    ->label('Notification Emails')
                    ->placeholder('Enter email addresses')
                    ->visible(fn ($get) => $get('email_notifications')),
                Forms\Components\Toggle::make('store_leads')
                    ->label('Store Leads')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('email_notifications'),
                Tables\Columns\BooleanColumn::make('store_leads'),
            ])
            ->recordUrl(fn ($record) => static::getUrl('edit', ['record' => $record]))
            ->filters([
                SelectFilter::make('email_notifications')
                    ->options([
                        '0' => 'No',
                        '1' => 'Yes',
                    ]),
                SelectFilter::make('store_leads')
                    ->options([
                        '0' => 'No',
                        '1' => 'Yes',
                    ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('edit')
                    ->url(fn ($record) => static::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
                \Filament\Actions\DeleteAction::make(),
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
            //
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
