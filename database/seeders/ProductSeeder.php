<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Locale;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocale = Locale::defaultCode() ?? 'en';

        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Software', 'slug' => 'software'],
            ['name' => 'Hardware', 'slug' => 'hardware'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate($categoryData);
        }

        $electronicsCategory = Category::where('slug', 'electronics')->first();
        $softwareCategory = Category::where('slug', 'software')->first();
        $hardwareCategory = Category::where('slug', 'hardware')->first();
        $accessoriesCategory = Category::where('slug', 'accessories')->first();

        $products = [
            [
                'slug' => 'smart-wireless-headphones-pro',
                'title' => 'Smart Wireless Headphones Pro',
                'short_description' => 'Premium noise-cancelling wireless headphones with exceptional sound quality.',
                'status' => 'published',
                'category_id' => $electronicsCategory->id,
                'is_featured' => true,
                'technical_specs' => [
                    ['key' => 'Battery Life', 'value' => '30 hours'],
                    ['key' => 'Connectivity', 'value' => 'Bluetooth 5.0'],
                    ['key' => 'Weight', 'value' => '250g'],
                    ['key' => 'Frequency Response', 'value' => '20Hz - 20kHz']
                ],
                'tags' => ['wireless', 'noise-cancelling', 'bluetooth', 'premium'],
                'meta_title' => 'Smart Wireless Headphones Pro - Premium Audio Experience',
                'meta_description' => 'Discover premium wireless headphones with active noise cancellation and 30-hour battery life.',
                'downloads' => ['product-brochure.pdf', 'technical-specs.pdf'],
                '_detailed_description' => [
                    ['title' => 'Experience Premium Audio', 'description' => 'Featuring advanced noise-cancellation technology, 30-hour battery life, and superior comfort for all-day wear.', 'image' => null],
                ],
                '_features' => [['feature' => 'Active Noise Cancellation'], ['feature' => '30-hour battery life'], ['feature' => 'Premium leather cushions'], ['feature' => 'Bluetooth 5.0'], ['feature' => 'Built-in voice assistant']],
                '_video_embeds' => [
                    ['type' => 'embed', 'title' => 'Product Demo', 'url' => 'https://www.youtube.com/watch?v=demo123']
                ],
            ],
            [
                'slug' => 'professional-laptop-stand',
                'title' => 'Professional Laptop Stand',
                'short_description' => 'Ergonomic aluminum laptop stand for improved posture and cooling.',
                'status' => 'published',
                'category_id' => $accessoriesCategory->id,
                'is_featured' => false,
                'technical_specs' => [
                    ['key' => 'Material', 'value' => 'Aluminum Alloy'],
                    ['key' => 'Adjustable Angle', 'value' => '0-45 degrees'],
                    ['key' => 'Weight Capacity', 'value' => '10kg'],
                    ['key' => 'Compatibility', 'value' => '10-17 inch laptops']
                ],
                'tags' => ['ergonomic', 'aluminum', 'adjustable', 'cooling'],
                'meta_title' => 'Professional Laptop Stand - Ergonomic Aluminum Design',
                'meta_description' => 'Ergonomic aluminum laptop stand for better posture and laptop cooling.',
                'downloads' => ['product-brochure.pdf', 'technical-specs.pdf'],
                '_detailed_description' => [
                    ['title' => 'Transform Your Workspace', 'description' => 'Made from premium aluminum, this stand provides optimal ergonomics and improved airflow for your laptop.', 'image' => null],
                ],
                '_features' => [['feature' => 'Improves posture'], ['feature' => 'Enhances laptop cooling'], ['feature' => 'Adjustable height and angle'], ['feature' => 'Sleek, modern design'], ['feature' => 'Compatible with all laptop sizes']],
            ],
            [
                'slug' => 'cloud-management-software-suite',
                'title' => 'Cloud Management Software Suite',
                'short_description' => 'Comprehensive cloud management solution for businesses of all sizes.',
                'status' => 'published',
                'category_id' => $softwareCategory->id,
                'is_featured' => true,
                'technical_specs' => [
                    ['key' => 'Platform', 'value' => 'Web-based'],
                    ['key' => 'Cloud Support', 'value' => 'AWS, Azure, Google Cloud'],
                    ['key' => 'Users', 'value' => 'Unlimited'],
                    ['key' => 'Support', 'value' => '24/7']
                ],
                'tags' => ['cloud', 'management', 'enterprise', 'monitoring'],
                'meta_title' => 'Cloud Management Software Suite - Enterprise Solution',
                'meta_description' => 'Comprehensive cloud management solution for businesses with multi-cloud support.',
                'downloads' => ['product-brochure.pdf', 'technical-specs.pdf'],
                '_detailed_description' => [
                    ['title' => 'Streamline Your Operations', 'description' => 'Designed for efficiency, security, and scalability.', 'image' => null],
                ],
                '_features' => [['feature' => 'Multi-cloud support'], ['feature' => 'Real-time monitoring'], ['feature' => 'Automated backups'], ['feature' => 'Advanced security features'], ['feature' => '24/7 technical support']],
            ],
            [
                'slug' => 'mechanical-gaming-keyboard-rgb',
                'title' => 'Mechanical Gaming Keyboard RGB',
                'short_description' => 'High-performance mechanical gaming keyboard with customizable RGB lighting.',
                'status' => 'published',
                'category_id' => $hardwareCategory->id,
                'is_featured' => true,
                'technical_specs' => [
                    ['key' => 'Switch Type', 'value' => 'Mechanical Blue'],
                    ['key' => 'Connectivity', 'value' => 'USB-C, Bluetooth 5.0'],
                    ['key' => 'Battery Life', 'value' => '40 hours (wireless)'],
                    ['key' => 'Key Rollover', 'value' => 'N-Key']
                ],
                'tags' => ['gaming', 'mechanical', 'rgb', 'wireless'],
                'meta_title' => 'Mechanical Gaming Keyboard RGB - Pro Gaming Experience',
                'meta_description' => 'High-performance mechanical gaming keyboard with RGB lighting and programmable keys.',
                'downloads' => ['product-brochure.pdf', 'technical-specs.pdf'],
                '_detailed_description' => [
                    ['title' => 'Elevate Your Gaming', 'description' => 'Featuring premium mechanical switches, customizable RGB lighting, and programmable keys.', 'image' => null],
                ],
                '_features' => [['feature' => '50M+ keystroke lifespan'], ['feature' => 'Full RGB backlighting'], ['feature' => 'Programmable keys and macros'], ['feature' => 'Anti-ghosting and N-key rollover'], ['feature' => 'Durable aluminum frame']],
                '_video_embeds' => [
                    ['type' => 'embed', 'title' => 'Gaming Demo', 'url' => 'https://www.youtube.com/watch?v=gaming456']
                ],
            ],
            [
                'slug' => 'wireless-charging-pad',
                'title' => 'Wireless Charging Pad',
                'short_description' => 'Fast wireless charging pad for all Qi-enabled devices.',
                'status' => 'published',
                'category_id' => $accessoriesCategory->id,
                'is_featured' => false,
                'technical_specs' => [
                    ['key' => 'Charging Power', 'value' => '15W'],
                    ['key' => 'Compatibility', 'value' => 'Qi-enabled devices'],
                    ['key' => 'Input', 'value' => 'USB-C PD'],
                    ['key' => 'Safety', 'value' => 'Overcharge protection']
                ],
                'tags' => ['wireless', 'charging', 'qi', 'fast-charging'],
                'meta_title' => 'Wireless Charging Pad - 15W Fast Charging',
                'meta_description' => 'Fast 15W wireless charging pad compatible with all Qi-enabled devices.',
                'downloads' => ['product-brochure.pdf', 'technical-specs.pdf'],
                '_detailed_description' => [
                    ['title' => 'Charge Effortlessly', 'description' => 'Compatible with all Qi-enabled smartphones and devices.', 'image' => null],
                ],
                '_features' => [['feature' => '15W fast charging'], ['feature' => 'Qi-certified'], ['feature' => 'LED charging indicator'], ['feature' => 'Anti-slip surface'], ['feature' => 'Compact and portable design']],
            ],
        ];

        foreach ($products as $productData) {
            $insertData = $productData;
            $detailedDesc = $insertData['_detailed_description'] ?? null;
            $features = $insertData['_features'] ?? null;
            $videoEmbeds = $insertData['_video_embeds'] ?? null;
            unset($insertData['_detailed_description'], $insertData['_features'], $insertData['_video_embeds']);

            $product = Product::firstOrCreate(
                ['slug' => $insertData['slug']],
                $insertData
            );

            // Set translatable attributes
            foreach (['title', 'short_description', 'meta_title', 'meta_description'] as $field) {
                if (isset($productData[$field])) {
                    $product->setTranslation($field, $defaultLocale, $productData[$field]);
                }
            }
            if ($detailedDesc) {
                $product->setTranslation('detailed_description', $defaultLocale, $detailedDesc);
            }
            if ($features) {
                $product->setTranslation('features', $defaultLocale, $features);
            }
            if ($videoEmbeds) {
                $product->setTranslation('video_embeds', $defaultLocale, $videoEmbeds);
            }
            $product->save();
        }

        $this->command->info('Products seeded successfully!');
    }
}
