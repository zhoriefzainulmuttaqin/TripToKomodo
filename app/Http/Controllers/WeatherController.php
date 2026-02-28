<?php

namespace App\Http\Controllers;

use App\Services\LabuanBajoWeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function labuanBajo(Request $request, LabuanBajoWeatherService $service): JsonResponse
    {
        $force = $request->boolean('force');

        try {
            $data = $service->get($force);
        } catch (\Throwable) {
            return response()->json([
                'ok' => false,
                'message' => 'Cuaca sementara tidak tersedia. Coba lagi beberapa saat.',
            ], 503);
        }

        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }
}
