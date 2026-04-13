<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        ], 400)
        ->header('Access-Control-Allow-Origin', '*');
    }

    // ✅ Non-string → 422
    if (!is_string($name)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unprocessable Entity'
        ], 422)
        ->header('Access-Control-Allow-Origin', '*');
    }

    $name = trim($name);

    // 🚀 Call API
    $response = Http::get('https://api.genderize.io', [
        'name' => $name
    ])->json();

    $gender = $response['gender'] ?? null;
    $sample_size = $response['count'] ?? 0;
    $probability = $response['probability'] ?? 0;

    // ❌ Genderize edge case
    if ($gender === null || $sample_size == 0) {
        return response()->json([
            'status' => 'error',
            'message' => 'No prediction available for the provided name'
        ], 400)
        ->header('Access-Control-Allow-Origin', '*');
    }

    // ✅ confidence logic
    $is_confident = ($probability >= 0.7 && $sample_size >= 100);

    return response()->json([
        'status' => 'success',
        'data' => [
            'name' => $name,
            'gender' => $gender,
            'probability' => (float) $probability,
            'sample_size' => (int) $sample_size,
            'is_confident' => $is_confident,
            'processed_at' => now()->utc()->toIso8601String()
        ]
    ])
    ->header('Access-Control-Allow-Origin', '*');
}
}