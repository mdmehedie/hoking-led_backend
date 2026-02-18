<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with('category')->get();
    }

    public function headings(): array
    {
        return [
            'Title',
            'Slug',
            'Short Description',
            'Detailed Description',
            'Status',
            'Published At',
            'Technical Specs',
            'Tags',
            'Category',
            'Category Slug',
        ];
    }

    public function map($product): array
    {
        $tags = is_array($product->tags) ? array_filter($product->tags, 'is_scalar') : [];
        $specs = is_array($product->technical_specs) ? $product->technical_specs : [];

        return [
            $product->title,
            $product->slug,
            $product->short_description,
            $product->detailed_description,
            $product->status,
            $product->published_at?->format('Y-m-d H:i:s'),
            json_encode($specs),
            implode(',', $tags),
            $product->category?->name,
            $product->category?->slug,
        ];
    }
}
