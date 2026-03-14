<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AppSetting;

class TestAppSetting extends Command
{
    protected $signature = 'test:appsetting';
    protected $description = 'Test AppSetting data and translations';

    public function handle()
    {
        $setting = AppSetting::with('translations')->first();
        
        if (!$setting) {
            $this->error('No AppSetting found');
            return 1;
        }
        
        $this->info('AppSetting ID: ' . $setting->id);
        $this->info('Translations count: ' . $setting->translations->count());
        
        foreach ($setting->translations as $trans) {
            $this->line("Locale: {$trans->locale}, Attribute: {$trans->attribute}, Value: {$trans->value}");
        }
        
        $this->info('Direct app_name: ' . ($setting->app_name ?? 'null'));
        $this->info('Direct company_name: ' . ($setting->company_name ?? 'null'));
        $this->info('Direct about: ' . ($setting->about ?? 'null'));
        
        // Test dotted notation
        $this->info('Testing dotted notation:');
        $this->info('app_name.en: ' . $setting->getAttribute('app_name.en'));
        $this->info('company_name.en: ' . $setting->getAttribute('company_name.en'));
        $this->info('about.en: ' . $setting->getAttribute('about.en'));
        
        return 0;
    }
}
