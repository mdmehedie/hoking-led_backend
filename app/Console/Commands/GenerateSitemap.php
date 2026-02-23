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

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap for products, categories, and content';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = Sitemap::create();

        // Add products
        foreach (Product::all() as $product) {
            $sitemap->add(Url::create($product->getUrl())->setLastModificationDate($product->updated_at));
        }

        // Add categories
        foreach (Category::all() as $category) {
            $sitemap->add(Url::create($category->getUrl())->setLastModificationDate($category->updated_at));
        }

        // Add blogs
        foreach (Blog::all() as $blog) {
            $sitemap->add(Url::create($blog->getUrl())->setLastModificationDate($blog->updated_at));
        }

        // Add case studies
        foreach (CaseStudy::all() as $caseStudy) {
            $sitemap->add(Url::create($caseStudy->getUrl())->setLastModificationDate($caseStudy->updated_at));
        }

        // Add news
        foreach (News::all() as $news) {
            $sitemap->add(Url::create($news->getUrl())->setLastModificationDate($news->updated_at));
        }

        // Add pages
        foreach (Page::all() as $page) {
            $sitemap->add(Url::create($page->getUrl())->setLastModificationDate($page->updated_at));
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
