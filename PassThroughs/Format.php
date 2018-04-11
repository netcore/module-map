<?php

namespace Modules\Map\PassThroughs;

use Illuminate\Support\Collection;
use Modules\Map\Models\Map;

class Format extends PassThrough
{
    /**
     * @var \Modules\Map\Models\Map
     */
    protected $map;

    /**
     * Format constructor.
     *
     * @param \Modules\Map\Models\Map $map
     */
    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    /**
     * Format model for VueJS.
     *
     * @return \Illuminate\Support\Collection
     */
    public function forVue(): Collection
    {
        $data = [
            'identifier' => $this->map->identifier,
            'zoom'       => $this->map->zoom,
            'latitude'   => (float)$this->map->latitude,
            'longitude'  => (float)$this->map->longitude,
            'markers'    => [],
            'polygons'   => [],
        ];

        foreach ($this->map->markers as $marker) {
            $data['markers'][] = [
                'key'       => str_random(10),
                'id'        => (int)$marker->id,
                'latitude'  => (float)$marker->latitude,
                'longitude' => (float)$marker->longitude,
                'address'   => $marker->address,
            ];
        }

        foreach ($this->map->polygons as $polygon) {
            $polygonArray = [
                'id'      => $polygon->id,
                'key'     => str_random(10),
                'markers' => [],
            ];

            foreach ($polygon->markers as $marker) {
                $polygonArray['markers'][] = [
                    'key'       => str_random(10),
                    'id'        => (int)$marker->id,
                    'latitude'  => (float)$marker->latitude,
                    'longitude' => (float)$marker->longitude,
                    'address'   => $marker->address,
                ];
            }

            $data['polygons'][] = $polygonArray;
        }

        return collect($data);
    }
}