<?php

namespace App\Console\Commands;

use App\Models\Region;
use Illuminate\Console\Command;

class ManageRegions extends Command
{
    protected $signature = 'regions:list {action? : Action to perform (list, create, delete)}';

    protected $description = 'Manage regions for international SEO';

    public function handle()
    {
        $action = $this->argument('action') ?? 'list';

        switch ($action) {
            case 'list':
                $this->listRegions();
                break;
            case 'create':
                $this->createRegion();
                break;
            case 'delete':
                $this->deleteRegion();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: list, create, delete');
                return 1;
        }

        return 0;
    }

    private function listRegions(): void
    {
        $regions = Region::orderBy('sort_order')->get();

        $this->info('Available Regions:');
        $this->info(str_repeat('-', 80));

        foreach ($regions as $region) {
            $status = $region->is_active ? '✓ Active' : '✗ Inactive';
            $default = $region->is_default ? ' (DEFAULT)' : '';
            
            $this->info(sprintf(
                "%-5s %-20s %-10s %-8s %-15s %s",
                $region->code,
                $region->name,
                $region->currency ?? 'N/A',
                $region->language ?? 'N/A',
                $status,
                $default
            ));
        }

        $this->info(str_repeat('-', 80));
        $this->info("Total: {$regions->count()} regions");
    }

    private function createRegion(): void
    {
        $this->info('Create a new region:');
        
        $code = $this->ask('Region code (e.g., us, uk, eu)');
        $name = $this->ask('Region name (e.g., United States)');
        $currency = $this->ask('Currency code (e.g., USD, GBP, EUR)');
        $timezone = $this->ask('Timezone (e.g., America/New_York)');
        $language = $this->ask('Language code (e.g., en-US, en-GB)');
        $active = $this->confirm('Make this region active?', true);
        $default = $this->confirm('Make this the default region?', false);
        $sortOrder = $this->ask('Sort order', '0');

        try {
            Region::create([
                'code' => $code,
                'name' => $name,
                'currency' => $currency,
                'timezone' => $timezone,
                'language' => $language,
                'is_active' => $active,
                'is_default' => $default,
                'sort_order' => (int) $sortOrder,
            ]);

            $this->info("✓ Region '{$code}' created successfully!");
        } catch (\Exception $e) {
            $this->error("✗ Failed to create region: {$e->getMessage()}");
        }
    }

    private function deleteRegion(): void
    {
        $regions = Region::pluck('name', 'code')->toArray();
        
        if (empty($regions)) {
            $this->info('No regions found.');
            return;
        }

        $code = $this->choice('Select region to delete:', array_keys($regions));
        
        if ($this->confirm("Are you sure you want to delete region '{$code}'?")) {
            try {
                Region::where('code', $code)->delete();
                $this->info("✓ Region '{$code}' deleted successfully!");
            } catch (\Exception $e) {
                $this->error("✗ Failed to delete region: {$e->getMessage()}");
            }
        } else {
            $this->info('Operation cancelled.');
        }
    }
}
