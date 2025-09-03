<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Http;
use Illuminate\Http\Request;

class OngkirController extends Controller
{
    public function getAreas(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => env('BITESHIP_API_KEY'),
            'Content-Type' => 'application/json',
        ])->get('https://api.biteship.com/v1/maps/areas', [
            'countries' => 'ID',
            'input' => $request->input('q'),
            'type' => 'single',
        ]);

        return $response->json();
    }

    public function cekOngkir(Request $request)
    {
        $validated = $request->validate([
            'origin_area_id' => 'required|string',
            'destination_area_id' => 'required|string',
            'couriers' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.value' => 'required|integer|min:0',
            'items.*.length' => 'required|integer|min:1',
            'items.*.width' => 'required|integer|min:1',
            'items.*.height' => 'required|integer|min:1',
            'items.*.weight' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $response = Http::withHeaders([
            'Authorization' => env('BITESHIP_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.biteship.com/v1/rates/couriers', [
            'origin_area_id' => $validated['origin_area_id'],
            'destination_area_id' => $validated['destination_area_id'],
            'couriers' => $validated['couriers'],
            'items' => $validated['items'],
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'message' => 'Gagal mendapatkan ongkir',
            'errors' => $response->json(),
        ], $response->status());
    }
}
