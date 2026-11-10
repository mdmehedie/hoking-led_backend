<?php

namespace App\Filament\Admin\Resources\ProductResource\Form;

use App\Filament\Forms\Components\TableBuilder;
use App\Helpers\UploadFileName;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Illuminate\Http\UploadedFile;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Product Tabs')->tabs([
                Tab::make(__('General Information'))->schema(self::generalSchema()),
                Tab::make(__('Product Details'))->schema(self::productDetailsSchema()),
                Tab::make(__('Media'))->schema(self::mediaSchema()),
                Tab::make(__('Technical Specifications'))->schema(self::techSpecsSchema()),
                Tab::make(__('Tags & Relations'))->schema(self::tagsSchema()),
                Tab::make(__('SEO'))->schema(self::seoSchema()),
            ])->columnSpanFull(),
        ]);
    }

    private static function generalSchema(): array
    {
        return [
            Select::make('category_id')
                ->relationship('category', 'name')
                ->label(__('Category'))
                ->nullable(),
            TextInput::make('order_column')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0)
                    ->helperText(__('Lower numbers appear first')),
            Select::make('status')
                ->label(__('Status'))
                ->options([
                    'draft' => __('Draft'),
                    'published' => __('Published'),
                    'archived' => __('Archived'),
                ])
                ->required(),
            Hidden::make('published_at')->default(now()),
//            Toggle::make('is_featured')->label(__('Featured Product')),
            Toggle::make('is_top')->label(__('Top Product')),
        ];
    }

    private static function productDetailsSchema(): array
    {
        return [
            TextInput::make('title')
                ->label(__('Title'))
                ->live()
                ->required()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if (blank($get('slug'))) {
                        $set('slug', static::generateUniqueSlug($state, null));
                    }
                }),
            TextInput::make('slug')
                ->label(__('Slug'))
                ->unique(ignoreRecord: true)
                ->required()
                ->regex('/^[a-z0-9-]+$/')
                ->helperText(__('Only lowercase letters, numbers, and hyphens are allowed. Spaces are not permitted.'))
                ->live(debounce: 300)
                ->afterStateUpdated(function ($state, callable $set) {
                    $slug = strtolower(str_replace(' ', '-', $state));
                    $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
                    $slug = preg_replace('/-+/', '-', $slug);
                    $slug = trim($slug, '-');
                    $set('slug', $slug);
                }),
            RichEditor::make('short_description')
                ->label(__('Short Description')),
            self::detailedDescriptionRepeater(),
            self::featuresRepeater(),
            self::videoEmbedsRepeater(),
        ];
    }

    private static function detailedDescriptionRepeater(): Repeater
    {
        return Repeater::make('detailed_description')
            ->label(__('Detailed Description'))
            ->schema([
                Select::make('layout')
                    ->label(__('Layout Style'))
                    ->options([
                        'stacked' => __('Vertical Stack (Default)'),
                        'image_left_50_50' => __('Side-by-Side (Image on Left, 50/50 Split)'),
                        'image_right_50_50' => __('Side-by-Side (Image on Right, 50/50 Split)'),
                        'image_left_text_wide' => __('Side-by-Side (Image on Left, Wide Text)'),
                        'image_right_text_wide' => __('Side-by-Side (Image on Right, Wide Text)'),
                    ])
                    ->default('stacked')
                    ->live()
                    ->required()
                    ->columnSpanFull()
                    ->helperText(function (callable $get) {
                        $layout = $get('layout') ?: 'stacked';
                        $previewLabel = __('Layout Preview:');
                        
                        $sizeGuide = match ($layout) {
                            'stacked' => __('Recommended Image Size: 800px - 1200px width (Aspect Ratio: 16:9 or auto)'),
                            'image_left_50_50', 'image_right_50_50' => __('Recommended Image Size: 500px - 800px width (Aspect Ratio: 1:1 square or 4:3 landscape)'),
                            'image_left_text_wide', 'image_right_text_wide' => __('Recommended Image Size: 400px - 600px width (Aspect Ratio: 1:1 square or 4:3 landscape)'),
                            default => '',
                        };

                        $skeleton = match ($layout) {
                            'image_left_50_50' => '
                                <div style="display: flex; align-items: stretch; gap: 16px; border: 1px solid rgba(148, 163, 184, 0.3); padding: 16px; border-radius: 12px; max-width: 500px; background: rgba(148, 163, 184, 0.05); margin-top: 10px;">
                                    <div style="width: 50%; height: 90px; background: rgba(59, 130, 246, 0.15); border: 1px dashed rgba(59, 130, 246, 0.4); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #3b82f6; font-weight: bold; flex-shrink: 0;">Image</div>
                                    <div style="width: 50%; border: 1px solid rgba(148, 163, 184, 0.2); padding: 10px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center; text-align: left;">
                                        <div style="font-size: 14px; font-weight: bold; opacity: 0.9; margin-bottom: 4px;">Title</div>
                                        <div style="font-size: 11px; line-height: 1.3; opacity: 0.6;">Text Content (50%)</div>
                                    </div>
                                </div>
                            ',
                            'image_right_50_50' => '
                                <div style="display: flex; align-items: stretch; gap: 16px; border: 1px solid rgba(148, 163, 184, 0.3); padding: 16px; border-radius: 12px; max-width: 500px; background: rgba(148, 163, 184, 0.05); margin-top: 10px;">
                                    <div style="width: 50%; border: 1px solid rgba(148, 163, 184, 0.2); padding: 10px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center; text-align: left;">
                                        <div style="font-size: 14px; font-weight: bold; opacity: 0.9; margin-bottom: 4px;">Title</div>
                                        <div style="font-size: 11px; line-height: 1.3; opacity: 0.6;">Text Content (50%)</div>
                                    </div>
                                    <div style="width: 50%; height: 90px; background: rgba(59, 130, 246, 0.15); border: 1px dashed rgba(59, 130, 246, 0.4); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #3b82f6; font-weight: bold; flex-shrink: 0;">Image</div>
                                </div>
                            ',
                            'image_left_text_wide' => '
                                <div style="display: flex; align-items: stretch; gap: 16px; border: 1px solid rgba(148, 163, 184, 0.3); padding: 16px; border-radius: 12px; max-width: 500px; background: rgba(148, 163, 184, 0.05); margin-top: 10px;">
                                    <div style="width: 35%; height: 90px; background: rgba(59, 130, 246, 0.15); border: 1px dashed rgba(59, 130, 246, 0.4); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #3b82f6; font-weight: bold; flex-shrink: 0;">Image</div>
                                    <div style="width: 65%; border: 1px solid rgba(148, 163, 184, 0.2); padding: 10px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center; text-align: left;">
                                        <div style="font-size: 14px; font-weight: bold; opacity: 0.9; margin-bottom: 4px;">Title</div>
                                        <div style="font-size: 11px; line-height: 1.3; opacity: 0.6;">Wide Text Content (60%)</div>
                                    </div>
                                </div>
                            ',
                            'image_right_text_wide' => '
                                <div style="display: flex; align-items: stretch; gap: 16px; border: 1px solid rgba(148, 163, 184, 0.3); padding: 16px; border-radius: 12px; max-width: 500px; background: rgba(148, 163, 184, 0.05); margin-top: 10px;">
                                    <div style="width: 65%; border: 1px solid rgba(148, 163, 184, 0.2); padding: 10px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center; text-align: left;">
                                        <div style="font-size: 14px; font-weight: bold; opacity: 0.9; margin-bottom: 4px;">Title</div>
                                        <div style="font-size: 11px; line-height: 1.3; opacity: 0.6;">Wide Text Content (60%)</div>
                                    </div>
                                    <div style="width: 35%; height: 90px; background: rgba(59, 130, 246, 0.15); border: 1px dashed rgba(59, 130, 246, 0.4); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #3b82f6; font-weight: bold; flex-shrink: 0;">Image</div>
                                </div>
                            ',
                            'stacked' => '
                                <div style="border: 1px solid rgba(148, 163, 184, 0.3); padding: 16px; border-radius: 12px; max-width: 500px; background: rgba(148, 163, 184, 0.05); margin-top: 10px;">
                                    <div style="border: 1px solid rgba(148, 163, 184, 0.2); padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 12px;">
                                        <div style="font-size: 14px; font-weight: bold; opacity: 0.9; margin-bottom: 4px;">Title</div>
                                        <div style="font-size: 11px; line-height: 1.3; opacity: 0.6;">Text stacked above the image.</div>
                                    </div>
                                    <div style="width: 100%; height: 80px; background: rgba(59, 130, 246, 0.15); border: 1px dashed rgba(59, 130, 246, 0.4); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #3b82f6; font-weight: bold;">Image</div>
                                </div>
                            ',
                            default => '',
                        };
                        
                        return new HtmlString('
                            <div style="margin-bottom: 8px;">
                                <div style="font-size: 0.75rem; font-weight: 600; color: #64748b;">' . $previewLabel . '</div>
                                <div style="font-size: 0.75rem; color: #eab308; font-weight: 600; margin-top: 2px;">' . $sizeGuide . '</div>
                            </div>
                            ' . $skeleton
                        );
                    }),
                TextInput::make('title')
                    ->label(__('Title'))
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->maxLength(1000)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label(__('Image'))
                    ->image()
                    ->disk('public')
                    ->directory('products/descriptions')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => UploadFileName::make($file))
                    ->columnSpanFull()
                    ->helperText(function (callable $get) {
                        $layout = $get('layout') ?: 'stacked';
                        return match ($layout) {
                            'stacked' => __('Recommended Image Size: 800px - 1200px width (Aspect Ratio: 16:9 landscape or original).'),
                            'image_left_50_50', 'image_right_50_50' => __('Recommended Image Size: 500px - 800px width (Aspect Ratio: 1:1 square or 4:3 landscape).'),
                            'image_left_text_wide', 'image_right_text_wide' => __('Recommended Image Size: 400px - 600px width (Aspect Ratio: 1:1 square or 4:3 landscape).'),
                            default => __('Select a layout to see recommended dimensions.'),
                        };
                    }),
            ])
            ->columns(1)
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ->createItemButtonLabel(__('Add item'))
            ->columnSpanFull();
    }

    private static function featuresRepeater(): Repeater
    {
        return Repeater::make('features')
            ->label(__('Key Features'))
            ->schema([
                TextInput::make('feature')
                    ->label(__('Feature'))
                    ->maxLength(255)
                    ->placeholder(__('Add a feature')),
            ])
            ->createItemButtonLabel(__('Add item'))
            ->columnSpanFull()
            ->default([])
            ->formatStateUsing(fn ($state) => static::formatFeaturesState($state))
            ->dehydrateStateUsing(fn ($state) => static::dehydrateFeaturesState($state));
    }

    private static function videoEmbedsRepeater(): Repeater
    {
        return Repeater::make('video_embeds')
            ->label(__('Video Embeds'))
            ->schema([
                Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'embed' => __('Embed URL'),
                        'file' => __('Self-hosted File'),
                    ])
                    ->default('embed')
                    ->live()
                    ->required(),
                TextInput::make('title')
                    ->label(__('Title'))
                    ->hidden(fn ($state, callable $get) => $get('type') === 'file'),
                TextInput::make('url')
                    ->label(__('URL'))
                    ->url()
                    ->rules(['regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/'])
                    ->hidden(fn ($state, callable $get) => $get('type') === 'file'),
                FileUpload::make('video_file')
                    ->label(__('Video File'))
                    ->disk('public')
                    ->directory('products/videos')
                    ->visibility('public')
                    ->hidden(fn ($state, callable $get) => $get('type') === 'embed')
                    ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => UploadFileName::make($file)),
            ])
            ->collapsible()
            ->columnSpanFull();
    }

    private static function mediaSchema(): array
    {
        $keepOriginal = fn (UploadedFile $file) => UploadFileName::make($file);

        return [
            FileUpload::make('main_image')
                ->label(__('Main Image'))
                ->image()
                ->disk('public')
                ->directory('products/main')
                ->visibility('public')
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1'])
                ->getUploadedFileNameForStorageUsing($keepOriginal)
                ->helperText(__('Recommended size: 800px x 800px (1:1 square aspect ratio) for card display and gallery slides.')),
            FileUpload::make('gallery')
                ->label(__('Gallery'))
                ->multiple()
                ->image()
                ->disk('public')
                ->directory('products/gallery')
                ->visibility('public')
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1'])
                ->getUploadedFileNameForStorageUsing($keepOriginal)
                ->helperText(__('Recommended size: 800px x 600px (4:3 aspect ratio) or 800px x 800px (1:1 square). Keep image sizes consistent.')),
            FileUpload::make('downloads')
                ->label(__('Downloads'))
                ->multiple()
                ->disk('public')
                ->directory('products/downloads')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal),
        ];
    }

    private static function techSpecsSchema(): array
    {
        return [
            TableBuilder::make('technical_specs')
                ->label(__('Technical Specifications Table'))
                ->initialRows(3)
                ->initialColumns(2),
        ];
    }

    private static function tagsSchema(): array
    {
        return [
            TagsInput::make('tags')
                ->label(__('Tags')),
            Select::make('related_products')
                ->label(__('Related Products'))
                ->multiple()
                ->relationship('relatedProducts', 'title')
                ->searchable()
                ->preload(),
        ];
    }

    private static function seoSchema(): array
    {
        return [
            TextInput::make('meta_title')
                ->label(__('Meta Title')),
            Textarea::make('meta_description')
                ->label(__('Meta Description')),
            TagsInput::make('meta_keywords')
                ->separator(',')
                ->label(__('Meta Keywords')),
            TextInput::make('canonical_url')
                ->label(__('Canonical URL')),
        ];
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'products';
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    /**
     * Format features state for display in repeater.
     */
    private static function formatFeaturesState($state): array
    {
        if (blank($state) || !is_array($state) || count($state) === 0) {
            return [['feature' => '']];
        }

        foreach ($state as $key => $item) {
            if (is_array($item) && isset($item['feature']['feature'])) {
                $state[$key] = ['feature' => ''];
            }
        }

        $firstItem = reset($state);
        if (!is_array($firstItem) || !isset($firstItem['feature'])) {
            return collect($state)->map(fn ($f) => ['feature' => $f])->all();
        }

        return $state;
    }

    /**
     * Dehydrate features state for storage.
     */
    private static function dehydrateFeaturesState($state): array
    {
        if (!is_array($state)) {
            return $state;
        }

        return collect($state)
            ->filter(fn ($item) => !empty($item['feature']))
            ->pluck('feature')
            ->values()
            ->all();
    }
}
