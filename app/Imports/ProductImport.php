<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading
{
    private $imported = 0;
    private $errors = [];

    public function model(array $row)
    {
        $slug = $row['slug'] ?? Str::slug($row['title']);
        $category = Category::where('name', $row['category'])->orWhere('slug', $row['category'])->first();
        $product = Product::where('slug', $slug)->first();

        $data = [
            'title' => $row['title'],
            'slug' => $slug,
            'short_description' => $row['short_description'] ?? null,
            'detailed_description' => $row['detailed_description'] ?? null,
            'status' => $row['status'] ?? 'draft',
            'published_at' => isset($row['published_at']) ? Carbon::parse($row['published_at']) : null,
            'technical_specs' => isset($row['technical_specs']) ? json_decode($row['technical_specs'], true) : [],
            'tags' => isset($row['tags']) ? explode(',', $row['tags']) : [],
            'category_id' => $category->id ?? null,
        ];

        if ($product) {
            $product->fill($data);
            $product->save();
            $this->imported++;
            return null;
        } else {
            $this->imported++;
            return new Product($data);
        }
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,published,archived',
            'category' => 'nullable|string',
            'published_at' => 'nullable|date',
            'technical_specs' => 'nullable|string',
            'tags' => 'nullable|string',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getImportedCount()
    {
        return $this->imported;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
