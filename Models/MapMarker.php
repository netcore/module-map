<?php

namespace Modules\Map\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapMarker extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_map__map_markers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'map_id',
        'map_polygon_id',
        'latitude',
        'longitude',
        'address',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Map marker belongs to the map.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    /**
     * Map marker belongs to the map polygon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function polygon(): BelongsTo
    {
        return $this->belongsTo(MapPolygon::class);
    }
}