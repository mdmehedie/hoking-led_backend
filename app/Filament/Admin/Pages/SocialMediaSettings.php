<?php

namespace App\Filament\Admin\Pages;

use App\Models\SocialAccount;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class SocialMediaSettings extends Page implements \Filament\Tables\Contracts\HasTable
{
    use InteractsWithTable;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-share';

    protected string $view = 'filament.admin.pages.social-media-settings';

    protected static ?string $title = 'Social Media Settings';

    protected static ?string $navigationLabel = 'Social Media';

    public static function getNavigationLabel(): string
    {
        return __('Social Media');
    }

    public function getTitle(): string
    {
        return __('Social Media Settings');
    }

    protected static ?int $navigationSort = 99;

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('manage socialmedia');
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => SocialAccount::all())
            ->columns([
                TextColumn::make('platform')
                    ->label('Platform'),
                TextColumn::make('account_name')
                    ->label('Account Name'),
                TextColumn::make('is_active')
                    ->label('Active')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
                TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('platform')
                    ->label('Platform')
                    ->options([
                        'facebook' => 'Facebook',
                        'twitter' => 'Twitter',
                        'linkedin' => 'LinkedIn',
                    ]),
            ])
            ->searchable()
            ->actions([
                EditAction::make('edit')
                    ->form([
                        Select::make('platform')
                            ->options([
                                'facebook' => 'Facebook',
                                'twitter' => 'Twitter (X)',
                                'linkedin' => 'LinkedIn',
                            ])
                            ->required(),

                        TextInput::make('account_name')
                            ->label('Account Name')
                            ->placeholder('e.g., Company Page, Main Account')
                            ->required(),

                        \Filament\Forms\Components\KeyValue::make('credentials')
                            ->label('API Credentials')
                            ->keyLabel('Credential Name')
                            ->valueLabel('Credential Value')
                            ->helperText(function ($get) {
                                $platform = $get('platform');
                                return match($platform) {
                                    'facebook' => 'Required: app_id, app_secret, access_token, page_id',
                                    'twitter' => 'Required: api_key, api_secret, access_token, access_token_secret',
                                    'linkedin' => 'Required: client_id, client_secret, access_token, organization_id (optional)',
                                    default => 'Enter the required API credentials for this platform',
                                };
                            }),

                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Active'),
                    ]),
                DeleteAction::make('delete'),
            ])
            ->bulkActions([
                BulkAction::make('delete_selected')
                    ->label('Delete Selected')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $count = $records->count();
                        $records->each->delete();
                        Notification::make()
                            ->title('Deleted')
                            ->body($count . ' items deleted successfully.')
                            ->success()
                            ->send();
                    }),
                BulkAction::make('activate')
                    ->label('Activate Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $records->each->update(['is_active' => true]);
                        Notification::make()
                            ->title('Accounts activated')
                            ->body('Selected accounts have been activated.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                BulkAction::make('deactivate')
                    ->label('Deactivate Selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $records->each->update(['is_active' => false]);
                        Notification::make()
                            ->title('Accounts deactivated')
                            ->body('Selected accounts have been deactivated.')
                            ->warning()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->emptyStateHeading('No social media accounts configured')
            ->emptyStateDescription('Add your social media accounts to enable automatic posting when content is published.')
            ->emptyStateIcon('heroicon-o-share');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('Add Account')
                ->form([
                    Select::make('platform')
                        ->options([
                            'facebook' => 'Facebook',
                            'twitter' => 'Twitter (X)',
                            'linkedin' => 'LinkedIn',
                        ])
                        ->required()
                        ->live(),

                    TextInput::make('account_name')
                        ->label('Account Name')
                        ->placeholder('e.g., Company Page, Main Account')
                        ->required(),

                    \Filament\Forms\Components\KeyValue::make('credentials')
                        ->label('API Credentials')
                        ->keyLabel('Credential Name')
                        ->valueLabel('Credential Value')
                        ->helperText(function ($get) {
                            $platform = $get('platform');
                            return match($platform) {
                                'facebook' => 'Required: app_id, app_secret, access_token, page_id',
                                'twitter' => 'Required: api_key, api_secret, access_token, access_token_secret',
                                'linkedin' => 'Required: client_id, client_secret, access_token, organization_id (optional)',
                                default => 'Enter the required API credentials for this platform',
                            };
                        }),

                    \Filament\Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    SocialAccount::create($data);

                    Notification::make()
                        ->title('Social media account added successfully!')
                        ->success()
                        ->send();
                })
                ->modalHeading('Add Social Media Account'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            '' => 'Social Media Settings',
            url()->current() => 'List',
        ];
    }
}