<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CaseStudyResource\Pages;
use App\Models\CaseStudy;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;

    protected static ?string $navigationLabel = 'Case Studies';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('General')->schema([
                    TextInput::make('title')
                        ->afterStateUpdated(function ($state, callable $set, $context) {
                            $record = $context['record'] ?? null;
                            if ($record === null) {
                                $set('slug', static::generateUniqueSlug($state, $record?->id));
                            }
                        })
                        ->live()
                        ->required(),
                    TextInput::make('slug')
                        ->unique(ignoreRecord: true)
                        ->required(),
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'review' => 'Review',
                            'published' => 'Published',
                        ])
                        ->required(),
                    Hidden::make('published_at')
                        ->default(now()),
                    Hidden::make('author_id')
                        ->default(auth()->id()),
                ]),
                Section::make('Content')->schema([
                    Textarea::make('excerpt'),
                    RichEditor::make('content')
                        ->required(),
                ]),
                Section::make('Media')->schema([
                    FileUpload::make('featured_image')
                        ->image()
                        ->directory('case-studies'),
                ]),
                Section::make('SEO')->schema([
                    TextInput::make('meta_title'),
                    Textarea::make('meta_description'),
                    Textarea::make('meta_keywords'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('status'),
                TextColumn::make('author.name'),
                TextColumn::make('published_at'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Review',
                        'published' => 'Published',
                    ]),
            ])
            ->searchable()
            ->actions([
                \Filament\Actions\Action::make('send_to_review')
                    ->label('Send to Review')
                    ->icon('heroicon-o-arrow-right')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(fn ($record) => $record->update(['status' => 'review'])),
                \Filament\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status === 'review')
                    ->action(fn ($record) => $record->update(['status' => 'published', 'published_at' => now()])),
                \Filament\Actions\Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn ($record) => $record->status === 'published')
                    ->action(fn ($record) => $record->update(['status' => 'draft'])),
                \Filament\Actions\EditAction::make(),
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
                BulkAction::make('change_status')
                    ->label('Change Status')
                    ->form([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'review' => 'Review',
                                'published' => 'Published',
                            ])
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each->update(['status' => $data['status']]);
                        Notification::make()
                            ->success()
                            ->title('Status Updated')
                            ->body('Selected items have been updated to ' . $data['status'] . '.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-cog'),
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
            'index' => Pages\ListCaseStudies::route('/'),
            'create' => Pages\CreateCaseStudy::route('/create'),
            'edit' => Pages\EditCaseStudy::route('/{record}/edit'),
        ];
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'case_studies';
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
