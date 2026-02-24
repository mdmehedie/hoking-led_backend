<?php

namespace App\Observers;

use App\Models\Product;
use App\Jobs\PublishToSocialMedia;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     * Check if the product was just published and dispatch social media job
     */
    public function updated(Product $product): void
    {
        // Check if the product was just published (status changed to 'published')
        if ($product->wasChanged('status') && $product->status === 'published') {
            Log::info('Product published, dispatching social media job', [
                'product_id' => $product->id,
                'title' => $product->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($product, 'product');
        }
    }

    /**
     * Handle the Product "created" event.
     * Check if the product is created as published and dispatch social media job
     */
    public function created(Product $product): void
    {
        // Check if the product was created as published
        if ($product->status === 'published') {
            Log::info('Product created as published, dispatching social media job', [
                'product_id' => $product->id,
                'title' => $product->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($product, 'product');
        }
    }
}
