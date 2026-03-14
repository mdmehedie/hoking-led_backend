<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Cache;

class AnalyticsSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.analytics-settings';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Analytics Settings';

    public function getHeading(): string
    {
        return 'Analytics Settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Google Analytics 4')
                    ->description('Configure GA4 API integration for advanced analytics data.')
                    ->schema([
                        Forms\Components\TextInput::make('services.ga4.property_id')
                            ->label('Property ID')
                            ->placeholder('properties/123456789')
                            ->helperText('Your GA4 Property ID from Google Analytics'),
                        Forms\Components\TextInput::make('services.ga4.credentials_path')
                            ->label('Credentials Path')
                            ->placeholder('storage/app/ga4-credentials.json')
                            ->helperText('Path to your GA4 service account credentials file'),
                        Forms\Components\Toggle::make('analytics.ga4_enabled')
                            ->label('Enable GA4 Integration')
                            ->default(false)
                            ->helperText('Fetch data from Google Analytics API'),
                    ]),

                Section::make('Heatmap & Session Recording')
                    ->description('Integrate third-party heatmap and session recording tools.')
                    ->schema([
                        Forms\Components\Select::make('analytics.heatmap_provider')
                            ->label('Provider')
                            ->options([
                                'hotjar' => 'Hotjar',
                                'clarity' => 'Microsoft Clarity',
                                'fullstory' => 'FullStory',
                                'none' => 'None'
                            ])
                            ->default('none')
                            ->helperText('Choose your heatmap provider'),
                        Forms\Components\TextInput::make('analytics.hotjar_id')
                            ->label('Hotjar Site ID')
                            ->placeholder('123456')
                            ->helperText('Your Hotjar site ID')
                            ->visible(fn ($get) => $get('analytics.heatmap_provider') === 'hotjar'),
                        Forms\Components\TextInput::make('analytics.clarity_id')
                            ->label('Clarity Project ID')
                            ->placeholder('abcdefg')
                            ->helperText('Your Microsoft Clarity project ID')
                            ->visible(fn ($get) => $get('analytics.heatmap_provider') === 'clarity'),
                    ]),

                Section::make('Custom Event Tracking')
                    ->description('Configure custom event tracking behavior.')
                    ->schema([
                        Forms\Components\Toggle::make('analytics.track_page_views')
                            ->label('Track Page Views')
                            ->default(true)
                            ->helperText('Automatically track page views'),
                        Forms\Components\Toggle::make('analytics.track_clicks')
                            ->label('Track Clicks')
                            ->default(true)
                            ->helperText('Track button and link clicks'),
                        Forms\Components\Toggle::make('analytics.track_forms')
                            ->label('Track Form Submissions')
                            ->default(true)
                            ->helperText('Track form submissions'),
                        Forms\Components\Toggle::make('analytics.track_scrolling')
                            ->label('Track Scrolling')
                            ->default(true)
                            ->helperText('Track scroll depth milestones'),
                        Forms\Components\Toggle::make('analytics.track_core_web_vitals')
                            ->label('Track Core Web Vitals')
                            ->default(true)
                            ->helperText('Track LCP, CLS, and INP metrics'),
                        Forms\Components\Toggle::make('analytics.debug_mode')
                            ->label('Debug Mode')
                            ->default(false)
                            ->helperText('Enable console logging for debugging'),
                    ]),

                Section::make('Performance Monitoring')
                    ->description('Configure Core Web Vitals monitoring.')
                    ->schema([
                        Forms\Components\Select::make('analytics.vitals_provider')
                            ->label('Monitoring Provider')
                            ->options([
                                'pagespeed' => 'Google PageSpeed Insights API',
                                'crux' => 'Chrome UX Report (CrUX)',
                                'custom' => 'Custom Tracking Only'
                            ])
                            ->default('custom')
                            ->helperText('Choose how to monitor Core Web Vitals'),
                        Forms\Components\TextInput::make('analytics.pagespeed_api_key')
                            ->label('PageSpeed API Key')
                            ->placeholder('Your Google API key')
                            ->helperText('Google PageSpeed Insights API key')
                            ->visible(fn ($get) => $get('analytics.vitals_provider') === 'pagespeed'),
                        Forms\Components\TextInput::make('analytics.monitoring_urls')
                            ->label('Monitor URLs')
                            ->placeholder('/, /about, /contact')
                            ->helperText('Comma-separated list of URLs to monitor')
                            ->default('/'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Save to settings (assuming you have a settings system)
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    setting("{$key}.{$subKey}", $subValue);
                }
            } else {
                setting($key, $value);
            }
        }

        // Clear relevant caches
        Cache::tags(['ga4_analytics'])->flush();
        Cache::tags(['analytics_settings'])->flush();

        $this->notify('success', 'Analytics settings saved successfully.');
    }
}
