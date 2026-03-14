<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\CaseStudy;
use App\Models\News;
use App\Models\Page;
use App\Models\Locale;
use App\Models\Region;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap {--region= : Generate sitemap for specific region}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap with hreflang and region support';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $region = $this->option('region');
        $supportedLocales = Locale::activeCodes();
        $activeRegions = Region::activeCodes();

        if ($region) {
            // Generate region-specific sitemap
            $this->generateRegionSitemap($region, $supportedLocales);
        } else {
            // Generate main sitemap with hreflang
            $this->generateMainSitemap($supportedLocales, $activeRegions);
            
            // Generate region-specific sitemaps
            foreach ($activeRegions as $regionCode) {
                $this->generateRegionSitemap($regionCode, $supportedLocales);
            }
            
            // Generate sitemap index
            $this->generateSitemapIndex($activeRegions);
        }

        $this->info('Sitemap generated successfully!');
    }

    private function generateMainSitemap(array $supportedLocales, array $activeRegions): void
    {
        $sitemap = Sitemap::create();

        // Add products with hreflang
        foreach (Product::where('status', 'published')->get() as $product) {
            $url = Url::create($product->getUrl())
                ->setLastModificationDate($product->updated_at);
            
            // Add hreflang alternates
            foreach ($product->getAlternates() as $alternate) {
                $url->addAlternate($alternate['url'], $alternate['locale']);
            }
            
            $sitemap->add($url);
        }

        // Add categories
        foreach (Category::all() as $category) {
            $sitemap->add(Url::create($category->getUrl())->setLastModificationDate($category->updated_at));
        }

        // Add blogs with hreflang
        foreach (Blog::where('status', 'published')->get() as $blog) {
            $url = Url::create($blog->getUrl())
                ->setLastModificationDate($blog->updated_at);
            
            foreach ($blog->getAlternates() as $alternate) {
                $url->addAlternate($alternate['url'], $alternate['locale']);
            }
            
            $sitemap->add($url);
        }

        // Add case studies with hreflang
        foreach (CaseStudy::where('status', 'published')->get() as $caseStudy) {
            $url = Url::create($caseStudy->getUrl())
                ->setLastModificationDate($caseStudy->updated_at);
            
            foreach ($caseStudy->getAlternates() as $alternate) {
                $url->addAlternate($alternate['url'], $alternate['locale']);
            }
            
            $sitemap->add($url);
        }

        // Add news with hreflang
        foreach (News::where('status', 'published')->get() as $news) {
            $url = Url::create($news->getUrl())
                ->setLastModificationDate($news->updated_at);
            
            foreach ($news->getAlternates() as $alternate) {
                $url->addAlternate($alternate['url'], $alternate['locale']);
            }
            
            $sitemap->add($url);
        }

        // Add pages with hreflang (including region-specific pages)
        foreach (Page::where('status', 'published')->get() as $page) {
            $url = Url::create($page->getUrl())
                ->setLastModificationDate($page->updated_at);
            
            foreach ($page->getAlternates() as $alternate) {
                $url->addAlternate($alternate['url'], $alternate['locale']);
            }
            
            $sitemap->add($url);
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }

    private function generateRegionSitemap(string $regionCode, array $supportedLocales): void
    {
        $sitemap = Sitemap::create();
        $region = Region::where('code', $regionCode)->first();

        // Add region-specific products
        $productIds = $region ? $region->products()->pluck('products.id') : collect([]);
        foreach (Product::whereIn('id', $productIds)->where('status', 'published')->get() as $product) {
            $url = Url::create($product->getUrl())
                ->setLastModificationDate($product->updated_at);
            
            foreach ($product->getAlternates() as $alternate) {
                $url->addAlternate($alternate['url'], $alternate['locale']);
            }
            
            $sitemap->add($url);
        }

        // Add region-specific pages
        $pageIds = $region ? $region->pages()->pluck('pages.id') : collect([]);
        foreach (Page::whereIn('id', $pageIds)->where('status', 'published')->get() as $page) {
            $url = Url::create($page->getUrl())
                ->setLastModificationDate($page->updated_at);
            
            foreach ($page->getAlternates() as $alternate) {
                $url->addAlternate($alternate['url'], $alternate['locale']);
            }
            
            $sitemap->add($url);
        }

        $sitemap->writeToFile(public_path("sitemap-{$regionCode}.xml"));
    }

    private function generateSitemapIndex(array $activeRegions): void
    {
        $index = Sitemap::create();
        
        // Add main sitemap
        $index->add(Url::create(url('/sitemap.xml')));
        
        // Add region-specific sitemaps
        foreach ($activeRegions as $regionCode) {
            $index->add(Url::create(url("/sitemap-{$regionCode}.xml")));
        }
        
        $index->writeToFile(public_path('sitemap-index.xml'));
    }
}
