<?php

namespace Modules\Map\Http\Controllers;

use Exception;
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
        $this->clearCache($map);

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
        $this->clearCache($map);

        return response()->json([
            'success' => 'Map successfully updated!',
            'map'     => $map->fresh()->format()->forVue(),
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
        // Deal with single markers.
        $existingMarkers = $map->markers()->pluck('id')->toArray();
        $newMarkers = [];

        foreach ($request->input('markers', []) as $markerData) {
            if (isset($markerData['id'])) {
                $marker = $map->markers()->findOrFail($markerData['id']);

                $marker->update([
                    'latitude'  => $markerData['latitude'] ?? 0,
                    'longitude' => $markerData['longitude'] ?? 0,
                    'address'   => $markerData['address'] ?? null,
                ]);
            } else {
                $marker = $map->markers()->create([
                    'latitude'  => $markerData['latitude'] ?? 0,
                    'longitude' => $markerData['longitude'] ?? 0,
                    'address'   => $markerData['address'] ?? null,
                ]);
            }

            $newMarkers[] = $marker->id;
        }

        // Delete markers that have been removed in frontend.
        $markersToDelete = array_diff($existingMarkers, $newMarkers);
        $map->markers()->whereIn('id', $markersToDelete)->delete();

        // Deal with polygons.
        $existingPolygons = $map->polygons()->pluck('id')->toArray();
        $newPolygons = [];

        foreach ($request->input('polygons', []) as $polygonData) {
            if (isset($polygonData['id'])) {
                $polygon = $map->polygons()->findOrFail($polygonData['id']);
            } else {
                $polygon = $map->polygons()->create([]);
            }

            $newPolygons[] = $polygon->id;

            // Store polygon markers.
            $existingPolygonMarkers = $polygon->markers()->pluck('id')->toArray();
            $newPolygonMarkers = [];

            foreach ((array)$polygonData['markers'] ?? [] as $markerData) {
                if (isset($markerData['id'])) {
                    $polygonMarker = $polygon->markers()->findOrFail($markerData['id']);

                    $polygonMarker->update([
                        'map_id'    => $map->id,
                        'latitude'  => $markerData['latitude'] ?? 0,
                        'longitude' => $markerData['longitude'] ?? 0,
                        'address'   => $markerData['address'] ?? null,
                    ]);
                } else {
                    $polygonMarker = $polygon->markers()->create([
                        'map_id'    => $map->id,
                        'latitude'  => $markerData['latitude'] ?? 0,
                        'longitude' => $markerData['longitude'] ?? 0,
                        'address'   => $markerData['address'] ?? null,
                    ]);
                }

                $newPolygonMarkers[] = $polygonMarker->id;
            }

            // Delete polygon markers.
            $polygonMarkersToDelete = array_diff($existingPolygonMarkers, $newPolygonMarkers);
            $polygon->markers()->whereIn('id', $polygonMarkersToDelete)->delete();
        }

        // Delete polygons.
        $polygonsToDelete = array_diff($existingPolygons, $newPolygons);
        $map->polygons()->whereIn('id', $polygonsToDelete)->delete();
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
            'zoom'       => 'required|integer|min:1|max:20',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',

            'markers.*.latitude'  => 'required|numeric',
            'markers.*.longitude' => 'required|numeric',

            'polygons'           => 'array',
            'polygons.*.markers' => 'array|min:3',

            'polygons.*.markers.*.latitude'  => 'required|numeric',
            'polygons.*.markers.*.longitude' => 'required|numeric',
        ], [
            'polygons.*.markers.min' => 'Each polygon must have at least 3 markers!',
        ]);
    }

    /**
     * Clear cached data.
     *
     * @param \Modules\Map\Models\Map $map
     * @return void
     */
    private function clearCache(Map $map): void
    {
        try {
            cache()->forget('map::markers-' . $map->id);
        } catch (Exception $e) {
            logger()->critical('[module-map :: MapController] Unable to clear cache - ' . $e->getMessage());
        }
    }
}