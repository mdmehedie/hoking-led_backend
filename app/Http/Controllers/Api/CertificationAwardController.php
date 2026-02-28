<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertificationAward;
use Illuminate\Http\Request;

class CertificationAwardController extends Controller
{
    public function index(Request $request)
    {
        $query = CertificationAward::where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('date_awarded', 'desc');

        if ($request->has('year') && $request->year) {
            $query->whereYear('date_awarded', $request->year);
        }

        $certifications = $query->get();

        return response()->json([
            'data' => $certifications->map(function ($certification) {
                return [
                    'id' => $certification->id,
                    'title' => $certification->title,
                    'slug' => $certification->slug,
                    'issuing_organization' => $certification->issuing_organization,
                    'date_awarded' => $certification->date_awarded?->format('Y-m-d'),
                    'description' => $certification->description,
                    'image' => $certification->image,
                    'sort_order' => $certification->sort_order,
                    'created_at' => $certification->created_at,
                    'updated_at' => $certification->updated_at,
                ];
            })
        ]);
    }
}
