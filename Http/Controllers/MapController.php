<?php

namespace Modules\Map\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Map\Models\Map;

class MapController extends Controller
{
    use ValidatesRequests;

    /**
     * Display a listing of the maps.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $maps = Map::all();

        return view('map::index', compact('maps'));
    }

    /**
     * Display map create form.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('map::form');
    }

    /**
     * Store map in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->validateRequest($request);

        $map = Map::create(
            $request->only('identifier', 'zoom', 'latitude', 'longitude')
        );

        $this->storeMarkersAndPolygons($map, $request);

        return response()->json([
            'redirect' => route('map::maps.edit', $map),
            'success'  => 'Map successfully created!',
        ]);
    }

    /**
     * Display map edit form.
     *
     * @param \Modules\Map\Models\Map $map
     * @return \Illuminate\View\View
     */
    public function edit(Map $map): View
    {
        return view('map::form', [
            'map' => $map,
        ]);
    }

    /**
     * Update map in database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Map\Models\Map $map
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Map $map): JsonResponse
    {
        $this->validateRequest($request, $map);

        $map->update(
            $request->only('identifier', 'zoom', 'latitude', 'longitude')
        );

        $this->storeMarkersAndPolygons($map, $request);

        return response()->json([
            'success' => 'Map successfully updated!',
        ]);
    }

    /**
     * Delete existing data and store new one.
     *
     * @param \Modules\Map\Models\Map $map
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function storeMarkersAndPolygons(Map $map, Request $request): void
    {
        $map->markers()->delete();
        $map->polygons()->delete();

        foreach ($request->input('markers', []) as $marker) {
            $map->markers()->create([
                'latitude'  => $marker['latitude'] ?? 0,
                'longitude' => $marker['longitude'] ?? 0,
                'address'   => $marker['address'] ?? null,
            ]);
        }

        foreach ($request->input('polygons', []) as $polygon) {
            $polygonInstance = $map->polygons()->create([]);

            foreach ((array)$polygon['markers'] ?? [] as $marker) {
                $polygonInstance->markers()->create([
                    'map_id'    => $map->id,
                    'latitude'  => $marker['latitude'] ?? 0,
                    'longitude' => $marker['longitude'] ?? 0,
                    'address'   => $marker['address'] ?? null,
                ]);
            }
        }
    }

    /**
     * Validate form request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Map\Models\Map|null $map
     * @return void
     */
    private function validateRequest(Request $request, Map $map = null): void
    {
        $this->validate($request, [
            'identifier' => ['required', Rule::unique('netcore_map__maps', 'identifier')->ignore($map->id ?? 0)],
            'zoom'       => 'required|min:1|max:20',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',

            'markers.*.latitude'  => 'required|numeric',
            'markers.*.longitude' => 'required|numeric',

            'polygons.*.markers.*.latitude'  => 'required|numeric',
            'polygons.*.markers.*.longitude' => 'required|numeric',
        ]);
    }
}
