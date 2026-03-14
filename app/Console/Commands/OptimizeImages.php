<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:optimize-images 
                            {--disk=public : The storage disk to optimize}
                            {--path= : Specific path to optimize}
                            {--force : Force re-optimization of existing images}
                            {--dry-run : Show what would be optimized without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize images and generate WebP/AVIF versions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = $this->option('disk');
        $path = $this->option('path') ?? '';
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        $this->info("Starting image optimization for disk: {$disk}");
        if ($path) {
            $this->info("Path: {$path}");
        }

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No actual optimization will be performed");
        }

        $storage = Storage::disk($disk);
        $images = $storage->allFiles($path);

        $imageFiles = $this->filterImageFiles($images, $storage, $force);
        
        if (empty($imageFiles)) {
            $this->info("No images found to optimize.");
            return;
        }

        $this->info("Found " . count($imageFiles) . " images to process");

        $progressBar = $this->output->createProgressBar(count($imageFiles));
        $progressBar->start();

        $optimized = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($imageFiles as $imagePath) {
            try {
                if ($dryRun) {
                    $this->line("Would optimize: {$imagePath}");
                    $optimized++;
                } else {
                    if (ImageOptimizer::optimizeImage($imagePath, $disk)) {
                        $optimized++;
                        $this->line("✓ Optimized: {$imagePath}", null, 'info');
                    } else {
                        $failed++;
                        $this->line("✗ Failed: {$imagePath}", null, 'error');
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                $this->line("✗ Error: {$imagePath} - " . $e->getMessage(), null, 'error');
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // Show summary
        $this->info("Optimization completed!");
        $this->line("Optimized: {$optimized}");
        $this->line("Failed: {$failed}");
        $this->line("Skipped: {$skipped}");

        if (!$dryRun && $optimized > 0) {
            $this->info("Generated WebP and AVIF versions for {$optimized} images");
            
            // Show storage usage
            $this->showStorageUsage($disk);
        }
    }

    /**
     * Filter image files that need optimization.
     */
    private function filterImageFiles($files, $storage, $force = false)
    {
        $imageFiles = [];
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

        foreach ($files as $file) {
            // Skip directories and non-image files
            if ($storage->isDirectory($file)) {
                continue;
            }

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if (!in_array($extension, $imageExtensions)) {
                continue;
            }

            // Skip if already optimized and not forcing
            if (!$force && $this->isAlreadyOptimized($file, $storage)) {
                continue;
            }

            $imageFiles[] = $file;
        }

        return $imageFiles;
    }

    /**
     * Check if image is already optimized.
     */
    private function isAlreadyOptimized($imagePath, $storage)
    {
        $webpPath = ImageOptimizer::getWebPPath($imagePath);
        $avifPath = ImageOptimizer::getAVIFPath($imagePath);
        
        return $storage->exists($webpPath) && $storage->exists($avifPath);
    }

    /**
     * Show storage usage information.
     */
    private function showStorageUsage($disk)
    {
        $this->newLine();
        $this->info("Storage Usage:");

        try {
            $storage = Storage::disk($disk);
            
            if ($disk === 'local' || $disk === 'public') {
                $path = storage_path("app/{$disk}");
                if (is_dir($path)) {
                    $totalSize = $this->getDirectorySize($path);
                    $this->line("Total size: " . $this->formatBytes($totalSize));
                }
            } elseif ($disk === 'redis') {
                $info = $storage->connection()->info('memory');
                $usedMemory = $info['used_memory'] ?? 0;
                $this->line("Redis memory usage: " . $this->formatBytes($usedMemory));
            }
        } catch (\Exception $e) {
            $this->warn("Could not determine storage usage: " . $e->getMessage());
        }
    }

    /**
     * Get directory size.
     */
    private function getDirectorySize($path)
    {
        $totalSize = 0;
        $files = glob(rtrim($path, '/') . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
            } elseif (is_dir($file)) {
                $totalSize += $this->getDirectorySize($file);
            }
        }

        return $totalSize;
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
