<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function getDetail(Request $request)
    {
        $name = $request->query('name');

        // ✅ Missing or empty → 400
        if (is_null($name) || trim((string)$name) === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad Request'
            ], 400)->header('Access-Control-Allow-Origin', '*');
        }

        // ✅ Non-string → 422
        if (!is_string($name)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unprocessable Entity'
            ], 422)->header('Access-Control-Allow-Origin', '*');
        }

        $name = trim($name);

        // 🚀 Call external API with error handling
        try {
            $response = Http::get('https://api.genderize.io', [
                'name' => $name
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'External API error'
                ], 500)->header('Access-Control-Allow-Origin', '*');
            }

            $data = $response->json();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'External API error'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }

        $gender = $data['gender'] ?? null;
        $sample_size = $data['count'] ?? 0;
        $probability = $data['probability'] ?? 0;

        // ❌ Genderize edge case: no prediction available
        if ($gender === null || $sample_size === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No prediction available for the provided name'
            ], 422)->header('Access-Control-Allow-Origin', '*');
        }

        // ✅ Confidence logic: true only if BOTH probability >= 0.7 AND sample_size >= 100
        $is_confident = ($probability >= 0.7 && $sample_size >= 100);

        // ✅ Generate current UTC timestamp in ISO 8601 format
        $processed_at = Carbon::now('UTC')->toIso8601String();

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $name,
                'gender' => $gender,
                'probability' => (float) $probability,
                'sample_size' => (int) $sample_size,
                'is_confident' => $is_confident,
                'processed_at' => $processed_at
            ]
        ], 200)->header('Access-Control-Allow-Origin', '*');
    }
}