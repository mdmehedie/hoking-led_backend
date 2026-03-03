<?php

use App\Models\Product;
use App\Models\Translation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!class_exists(Product::class) || !DB::getSchemaBuilder()->hasTable('products')) {
            return;
        }

        if (!DB::getSchemaBuilder()->hasTable('translations')) {
            return;
        }

        Product::query()->chunkById(100, function ($products): void {
            foreach ($products as $product) {
                $raw = $product->getRawOriginal('detailed_description');

                if (!is_string($raw) || $raw === '') {
                    continue;
                }

                $decoded = json_decode($raw, true);
                if (!is_array($decoded)) {
                    continue;
                }

                foreach ($decoded as $locale => $value) {
                    if (!is_string($locale)) {
                        continue;
                    }

                    Translation::query()->updateOrCreate(
                        [
                            'translatable_type' => Product::class,
                            'translatable_id' => $product->id,
                            'locale' => $locale,
                            'attribute' => 'detailed_description',
                        ],
                        [
                            'value' => is_string($value) ? $value : (is_null($value) ? null : json_encode($value)),
                        ]
                    );
                }

                if (isset($decoded['en']) && is_string($decoded['en'])) {
                    DB::table('products')->where('id', $product->id)->update([
                        'detailed_description' => $decoded['en'],
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Irreversible
    }
};
