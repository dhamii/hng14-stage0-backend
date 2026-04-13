<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function getDetail(Request $request){
        $name = request('name');
        $is_number = is_numeric($name);
        if ($name == null){
            return response()->json([
                'status' => 'error',
                'message' => "Bad Request"
            ], 400)->header('Access-Control-Allow-Origin', '*');
        }
        if ($is_number){
            return response()->json([
                'status' => 'error',
                'message' => 'No prediction available for the provided name'
            ])->header('Access-Control-Allow-Origin', '*');
        }
        $response = Http::get('https://api.genderize.io/', [
            'name' => $name 
        ])->json();
        $responseCount = $response['count'];//which will later be referred to as sample size
        $gender = $response['gender'];
        if ($gender == null || $responseCount == 0){
            return response()->json([
                'status' => 'error',
                'message' => 'No prediction available for the provided name'
            ])->header('Access-Control-Allow-Origin', '*');
        }
        $responseProbability = $response['probability'];
        if($responseProbability >= 0.7 && $responseCount >= 100){
            $is_confident = true;
        }
        else{
            $is_confident = false;
        }
        $time = now()->toIso8601String();
        return response()->json([
          'status' => 'success',
          'data' => [
            'name' => $name,
            'gender' => $gender,
            'probability'=> $responseProbability,
            'sample_size' => $responseCount,
            'is_confident' => $is_confident,
            'processed_at' => $time
          ]  
        ])->header('Access-Control-Allow-Origin', '*');
    }
}
