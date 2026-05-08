<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Blog;
use App\Models\CaseStudy;
use App\Models\News;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class ApiFrontendSearchController extends ApiBaseController
{
    /**
     * Search across different models.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        $type = $request->get('type'); // product, blog, news, case-study, page

        if (empty($query)) {
            return $this->okResponse(['results' => []], __('Search query is empty'));
        }

        $results = collect();

        // Define search configurations
        $searchConfigs = [
            'product' => [
                'model' => Product::class,
                'title_attr' => 'title',
                'excerpt_attr' => 'short_description',
                'image_attr' => 'main_image',
            ],
            'blog' => [
                'model' => Blog::class,
                'title_attr' => 'title',
                'excerpt_attr' => 'excerpt',
                'image_attr' => 'image_path',
            ],
            'news' => [
                'model' => News::class,
                'title_attr' => 'title',
                'excerpt_attr' => 'excerpt',
                'image_attr' => 'image_path',
            ],
            'case-study' => [
                'model' => CaseStudy::class,
                'title_attr' => 'title',
                'excerpt_attr' => 'excerpt',
                'image_attr' => 'image_path',
            ],
            'page' => [
                'model' => Page::class,
                'title_attr' => 'title',
                'excerpt_attr' => 'excerpt',
                'image_attr' => 'image_path',
            ],
        ];

        // Filter configs if type is specified
        if ($type && isset($searchConfigs[$type])) {
            $configsToSearch = [$type => $searchConfigs[$type]];
        } else {
            $configsToSearch = $searchConfigs;
        }

        foreach ($configsToSearch as $key => $config) {
            $items = $config['model']::published()
                ->where(function (Builder $q) use ($query, $config) {
                    $q->where($config['title_attr'], 'like', "%{$query}%")
                        ->orWhere($config['excerpt_attr'], 'like', "%{$query}%")
                        ->orWhereHas('translations', function (Builder $t) use ($query) {
                            $t->where('attribute', 'title')
                                ->where('value', 'like', "%{$query}%");
                        });
                })
                ->limit(10)
                ->get();

            foreach ($items as $item) {
                $results->push([
                    'type' => $key,
                    'title' => $item->{$config['title_attr']},
                    'slug' => $item->slug,
                    'excerpt' => $item->{$config['excerpt_attr']},
                    'image' => $item->{$config['image_attr']} ? Storage::disk('public')->url($item->{$config['image_attr']}) : null,
                    'published_at' => $item->published_at,
                ]);
            }
        }

        $finalResults = $results->sortByDesc('published_at')->values();

        return $this->okResponse([
            'results' => $finalResults,
        ], __('Search results retrieved successfully'));
    }
}
