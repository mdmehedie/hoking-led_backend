<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactSubmissionResource\Form\ContactSubmissionForm;
use App\Filament\Admin\Resources\ContactSubmissionResource\Table\ContactSubmissionTable;
use App\Filament\Admin\Resources\ContactSubmissionResource\Pages;
use App\Models\ContactSubmission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Contact Inbox';

    protected static ?string $slug = 'contact-submissions';

    protected static ?int $navigationSort = 9;

    public static function getModelLabel(): string
    {
        return 'Contact Submission';
    }

    public static function getNavigationLabel(): string
    {
        return __('Contact Inbox');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Marketing');
    }

    public static function form(Schema $schema): Schema
    {
        return ContactSubmissionForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactSubmissionTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactSubmissions::route('/'),
            'view' => Pages\ViewContactSubmission::route('/{record}'),
        ];
    }
}
