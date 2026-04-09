<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsletterSubscriptionResource\Form\NewsletterSubscriptionForm;
use App\Filament\Admin\Resources\NewsletterSubscriptionResource\Table\NewsletterSubscriptionTable;
use App\Filament\Admin\Resources\NewsletterSubscriptionResource\Pages;
use App\Models\NewsletterSubscription;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class NewsletterSubscriptionResource extends Resource
{
    protected static ?string $model = NewsletterSubscription::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Newsletter';

    protected static ?string $slug = 'newsletter-subscriptions';


    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): ?string
    {
        return 'Marketing';
    }

    public static function form(Schema $schema): Schema
    {
        return NewsletterSubscriptionForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsletterSubscriptionTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscriptions::route('/'),
            'view' => Pages\ViewNewsletterSubscription::route('/{record}'),
        ];
    }
}
