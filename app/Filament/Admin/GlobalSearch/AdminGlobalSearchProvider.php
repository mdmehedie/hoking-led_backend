<?php

namespace App\Filament\Admin\GlobalSearch;

use App\Filament\Admin\Resources\NewsletterSubscriptionResource;
use App\Filament\Admin\Resources\RoleResource;
use App\Filament\Admin\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\Providers\DefaultGlobalSearchProvider;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class AdminGlobalSearchProvider implements GlobalSearchProvider
{
    /**
     * Resources excluded from the "Navigation" quick-link results,
     * even if they're otherwise visible in the sidebar.
     *
     * @var array<class-string>
     */
    protected array $excludedFromNavigationLinks = [
        NewsletterSubscriptionResource::class,
        RoleResource::class,
        UserResource::class,
    ];

    public function getResults(string $query): ?GlobalSearchResults
    {
        $builder = GlobalSearchResults::make();

        $navigationResults = $this->getNavigationLinks()
            ->filter(fn (array $link) => str_contains(strtolower($link['label']), strtolower($query)))
            ->map(fn (array $link) => new GlobalSearchResult($link['label'], $link['url']))
            ->values();

        if ($navigationResults->isNotEmpty()) {
            $builder->category(__('Navigation'), $navigationResults->all());
        }

        $defaultResults = app(DefaultGlobalSearchProvider::class)->getResults($query);

        foreach ($defaultResults?->getCategories() ?? [] as $name => $results) {
            $builder->category($name, $results);
        }

        return $builder;
    }

    protected function getNavigationLinks(): Collection
    {
        $resourceLinks = collect(Filament::getResources())
            ->reject(fn (string $resource) => in_array($resource, $this->excludedFromNavigationLinks))
            ->filter(fn (string $resource) => $resource::shouldRegisterNavigation() && $resource::canViewAny())
            ->map(fn (string $resource) => [
                'label' => $resource::getNavigationLabel(),
                'url' => $resource::getUrl(),
            ]);

        $pageLinks = collect(Filament::getPages())
            ->filter(fn (string $page) => is_subclass_of($page, Page::class) && $page::shouldRegisterNavigation() && $page::canAccess())
            ->map(fn (string $page) => [
                'label' => $page::getNavigationLabel(),
                'url' => $page::getUrl(),
            ]);

        return $resourceLinks->merge($pageLinks);
    }
}
