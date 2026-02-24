<?php

namespace App\Filament\Admin\Resources\FormResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Form as FormModel;
use App\Models\FormWebhook;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;

class WebhooksRelationManager extends RelationManager
{
    protected static string $relationship = 'webhooks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->url()
                    ->label('Webhook URL')
                    ->placeholder('https://your-crm.com/webhook'),

                Forms\Components\Select::make('method')
                    ->required()
                    ->options([
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                    ])
                    ->default('POST')
                    ->label('HTTP Method'),

                Forms\Components\KeyValue::make('headers')
                    ->label('Custom Headers')
                    ->keyLabel('Header Name')
                    ->valueLabel('Header Value')
                    ->helperText('Add custom headers like Authorization, Content-Type, etc.')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Only active webhooks will receive data'),

                Forms\Components\Placeholder::make('info')
                    ->label('')
                    ->content('Webhook data will include all form submission fields. Failed webhooks will be retried up to 3 times with exponential backoff.')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'POST' => 'success',
                        'PUT' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

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
                //
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Add Webhook')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('url')
                            ->label('Webhook URL')
                            ->required()
                            ->placeholder('https://your-crm.com/webhook or your-crm.com/webhook')
                            ->helperText('Enter full URL with https:// or just domain - protocol will be added automatically')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state && !preg_match('/^https?:\/\//', $state)) {
                                    $set('url', 'https://' . $state);
                                }
                            }),

                        Forms\Components\Select::make('method')
                            ->label('HTTP Method')
                            ->options([
                                'POST' => 'POST',
                                'PUT' => 'PUT',
                            ])
                            ->default('POST')
                            ->required(),

                        Forms\Components\KeyValue::make('headers')
                            ->label('Custom Headers')
                            ->keyLabel('Header Name')
                            ->valueLabel('Header Value')
                            ->helperText('Add headers like Authorization, Content-Type, etc.')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active webhooks will receive data'),
                    ])
                    ->action(function (array $data) {
                        // Set form_id from the parent record
                        $data['form_id'] = $this->ownerRecord->id;

                        FormWebhook::create($data);

                        Notification::make()
                            ->title('Webhook created successfully!')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Add New Webhook')
                    ->modalSubmitActionLabel('Create Webhook'),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->fillForm(function ($record): array {
                        return [
                            'url' => $record->url,
                            'method' => $record->method,
                            'headers' => $record->headers ?? [],
                            'active' => $record->active,
                        ];
                    })
                    ->form([
                        Forms\Components\TextInput::make('url')
                            ->label('Webhook URL')
                            ->required()
                            ->placeholder('https://your-crm.com/webhook or your-crm.com/webhook')
                            ->helperText('Enter full URL with https:// or just domain - protocol will be added automatically')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state && !preg_match('/^https?:\/\//', $state)) {
                                    $set('url', 'https://' . $state);
                                }
                            }),

                        Forms\Components\Select::make('method')
                            ->label('HTTP Method')
                            ->options([
                                'POST' => 'POST',
                                'PUT' => 'PUT',
                            ])
                            ->required(),

                        Forms\Components\KeyValue::make('headers')
                            ->label('Custom Headers')
                            ->keyLabel('Header Name')
                            ->valueLabel('Header Value')
                            ->helperText('Add headers like Authorization, Content-Type, etc.')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->helperText('Only active webhooks will receive data'),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update($data);

                        Notification::make()
                            ->title('Webhook updated successfully!')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Edit Webhook')
                    ->modalSubmitActionLabel('Update Webhook'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
