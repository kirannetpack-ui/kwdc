<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function search(Request $request)
    {
        $data = $request->validate(['q' => 'required|string|min:3|max:255']);
        $query = trim($data['q']);
        $features = $this->lookup('api', ['q' => $query, 'limit' => 5, 'lat' => 27.7172, 'lon' => 85.3240, 'lang' => 'en']);

        return response()->json($this->locations($features));
    }

    public function reverse(Request $request)
    {
        $data = $request->validate(['lat' => 'required|numeric|between:-90,90', 'lon' => 'required|numeric|between:-180,180']);
        $features = $this->lookup('reverse', ['lat' => (float) $data['lat'], 'lon' => (float) $data['lon'], 'lang' => 'en']);
        $locations = $this->locations($features);

        return response()->json($locations[0] ?? ['display_name' => '', 'address' => []]);
    }

    public function route(Request $request)
    {
        $data = $request->validate([
            'points' => 'required|array|min:2|max:15',
            'points.*.lat' => 'required|numeric|between:-90,90',
            'points.*.lng' => 'required|numeric|between:-180,180',
        ]);
        $coordinates = collect($data['points'])->map(fn ($point) => (float) $point['lng'].','.(float) $point['lat'])->implode(';');
        $base = rtrim(config('maps.router_url'), '/');
        $result = $this->cachedRequest($base.'/route/v1/driving/'.$coordinates, ['overview' => 'false']);
        $route = $result['routes'][0] ?? null;
        abort_unless(($result['code'] ?? null) === 'Ok' && is_numeric($route['distance'] ?? null), 422, 'No road route found. Check the selected locations.');

        return response()->json(['distance_km' => round($route['distance'] / 1000, 2), 'duration_minutes' => (int) ceil(($route['duration'] ?? 0) / 60)]);
    }

    private function lookup(string $path, array $parameters): array
    {
        $base = rtrim(config('maps.geocoder_url'), '/');
        $result = $this->cachedRequest($base.'/'.$path.'/', $parameters);
        abort_unless(is_array($result['features'] ?? null), 503, 'Location search is unavailable. Try again shortly.');

        return $result['features'];
    }

    private function cachedRequest(string $url, array $parameters): array
    {
        $key = 'maps:'.hash('sha256', $url.json_encode($parameters));
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        // Bound aggregate traffic across users as well as per-user route throttling.
        abort_unless(Cache::add('maps:provider-request', true, 1), 429, 'Location service is busy. Please retry shortly.');
        try {
            $response = Http::acceptJson()->withUserAgent('KTM-WDC/1.0 ('.config('app.url').')')
                ->timeout(config('maps.timeout'))->connectTimeout(3)->get($url, $parameters);
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            abort(503, 'Location service is unavailable. Try again shortly.');
        }
        abort_unless($response->successful() && is_array($response->json()), 503, 'Location service is unavailable. Try again shortly.');
        $result = $response->json();
        Cache::put($key, $result, config('maps.cache_seconds'));

        return $result;
    }

    private function locations(array $features): array
    {
        return collect($features)->filter(function ($feature) {
            $point = $feature['geometry']['coordinates'] ?? [];
            return isset($point[0], $point[1]) && is_numeric($point[0]) && is_numeric($point[1])
                && abs($point[0]) <= 180 && abs($point[1]) <= 90;
        })->map(function ($feature) {
            $properties = $feature['properties'] ?? [];
            $name = collect(['name', 'street', 'city', 'district', 'state', 'country'])
                ->map(fn ($key) => $properties[$key] ?? null)->filter()->unique()->implode(', ');
            return [
                'lat' => $feature['geometry']['coordinates'][1],
                'lon' => $feature['geometry']['coordinates'][0],
                'display_name' => $name,
                'address' => ['road' => $properties['street'] ?? '', 'city' => $properties['city'] ?? '', 'state' => $properties['state'] ?? '', 'country' => $properties['country'] ?? ''],
            ];
        })->values()->all();
    }
}
