<?php

namespace Modules\Map\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Map\Models\Map;

class ApiController extends Controller
{
    /**
     * Get the markers for frontend.
     *
     * @param \Modules\Map\Models\Map $map
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMarkers(Map $map): JsonResponse
    {
        try {
            $markers = cache()->rememberForever('map::markers-' . $map->id, function () use ($map) {
                return $this->getMarkersForResponse($map);
            });
        } catch (Exception $e) {
            logger()->critical('[module-map :: ApiController] Something is wrong with caching - ' . $e->getMessage());
            $markers = [];
        }

        return response()->json($markers);
    }

    /**
     * Format markers for frontend.
     *
     * @param \Modules\Map\Models\Map $map
     * @return array
     */
    private function getMarkersForResponse(Map $map): array
    {
        $data = [
            'zoom'      => (int)$map->zoom,
            'center'    => [
                'lat' => (float)$map->latitude,
                'lng' => (float)$map->longitude,
            ],
            'locations' => [],
            'coverage'  => [],
        ];

        foreach ($map->markers as $marker) {
            $data['locations'][] = [
                'id'  => (int)$marker->id,
                'lat' => (float)$marker->latitude,
                'lng' => (float)$marker->longitude,
            ];
        }

        foreach ($map->polygons as $polygon) {
            $polygonData = [];

            foreach ($polygon->markers as $marker) {
                $polygonData[] = [
                    'id'  => (float)$marker->id,
                    'lat' => (float)$marker->latitude,
                    'lng' => (float)$marker->longitude,
                ];
            }

            $data['coverage'][] = $polygonData;
        }

        return $data;
    }
}