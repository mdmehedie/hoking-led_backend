<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AnalyticsController extends Controller
{
    /**
     * Track custom analytics event
     */
    public function trackEvent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:255',
            'page' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:500',
            'parameters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid data provided',
                'details' => $validator->errors(),
            ], 422);
        }

        try {
            $event = AnalyticsEvent::create([
                'event_name' => $request->input('event_name'),
                'page' => $request->input('page'),
                'url' => $request->input('url'),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'parameters' => $request->input('parameters', []),
                'user_id' => Auth::check() ? Auth::id() : null,
                'event_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'event_id' => $event->id,
                'message' => 'Event tracked successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to track event',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get analytics statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        $totalEvents = AnalyticsEvent::betweenDates($startDate, $endDate)->count();
        $uniquePages = AnalyticsEvent::betweenDates($startDate, $endDate)
            ->whereNotNull('page')
            ->distinct('page')
            ->count();
        $uniqueUsers = AnalyticsEvent::betweenDates($startDate, $endDate)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count();

        // Top events
        $topEvents = AnalyticsEvent::betweenDates($startDate, $endDate)
            ->selectRaw('event_name, COUNT(*) as count')
            ->groupBy('event_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Top pages
        $topPages = AnalyticsEvent::betweenDates($startDate, $endDate)
            ->whereNotNull('page')
            ->selectRaw('page, COUNT(*) as count')
            ->groupBy('page')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Device breakdown
        $deviceStats = AnalyticsEvent::betweenDates($startDate, $endDate)
            ->selectRaw('
                SUM(CASE WHEN user_agent LIKE "%Mobile%" OR user_agent LIKE "%Android%" OR user_agent LIKE "%iPhone%" OR user_agent LIKE "%iPad%" THEN 1 ELSE 0 END) as mobile,
                SUM(CASE WHEN user_agent NOT LIKE "%Mobile%" AND user_agent NOT LIKE "%Android%" AND user_agent NOT LIKE "%iPhone%" AND user_agent NOT LIKE "%iPad%" THEN 1 ELSE 0 END) as desktop
            ')
            ->first();

        return response()->json([
            'total_events' => $totalEvents,
            'unique_pages' => $uniquePages,
            'unique_users' => $uniqueUsers,
            'top_events' => $topEvents,
            'top_pages' => $topPages,
            'device_stats' => [
                'mobile' => $deviceStats->mobile ?? 0,
                'desktop' => $deviceStats->desktop ?? 0,
            ],
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Get funnel analytics
     */
    public function getFunnelData(Request $request): JsonResponse
    {
        $funnelSteps = $request->input('steps', []);
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        if (empty($funnelSteps)) {
            return response()->json([
                'success' => false,
                'error' => 'No funnel steps provided',
            ], 422);
        }

        $funnelData = [];
        $previousStepUsers = null;

        foreach ($funnelSteps as $index => $step) {
            $stepEvents = AnalyticsEvent::betweenDates($startDate, $endDate)
                ->byName($step['event_name'])
                ->when(isset($step['page']), function ($query) use ($step) {
                    return $query->byPage($step['page']);
                })
                ->when(isset($step['parameters']), function ($query) use ($step) {
                    foreach ($step['parameters'] as $key => $value) {
                        $query->where("parameters->{$key}", $value);
                    }
                    return $query;
                })
                ->get();

            $uniqueUsers = $stepEvents->whereNotNull('user_id')->unique('user_id')->count();
            $totalEvents = $stepEvents->count();

            $conversionRate = $previousStepUsers !== null 
                ? round(($uniqueUsers / $previousStepUsers) * 100, 2)
                : 100;

            $funnelData[] = [
                'step' => $index + 1,
                'name' => $step['event_name'],
                'page' => $step['page'] ?? null,
                'unique_users' => $uniqueUsers,
                'total_events' => $totalEvents,
                'conversion_rate' => $conversionRate,
                'drop_off_rate' => 100 - $conversionRate,
            ];

            $previousStepUsers = $uniqueUsers;
        }

        return response()->json([
            'success' => true,
            'funnel_data' => $funnelData,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
