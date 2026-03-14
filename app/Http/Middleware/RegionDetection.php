<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Region;
use Symfony\Component\HttpFoundation\Response;

class RegionDetection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $region = $request->route('region');
        
        // Validate that the region exists and is active
        if (!$region || !Region::where('code', $region)->where('is_active', true)->exists()) {
            // If it's a valid locale but not a region, let locale routes handle it
            $supportedLocales = config('app.supported_locales', []);
            if (in_array($region, $supportedLocales)) {
                // This is a locale, not a region - let it pass through to locale routes
                return $next($request);
            }
            
            // Redirect to default region if invalid region/locale
            $defaultRegion = Region::defaultCode();
            $path = $request->path();
            
            // Remove the region prefix from the path
            $pathWithoutRegion = preg_replace('/^[a-z]{2}\//', '', $path);
            
            return redirect($pathWithoutRegion);
        }
        
        // Set region in session for later use
        session(['current_region' => $region]);
        
        // You can also set it in config or app container for controllers to use
        config(['app.current_region' => $region]);
        
        return $next($request);
    }
}
