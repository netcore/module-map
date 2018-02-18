<?php

namespace Modules\Map\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MapPolygon extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_map__map_polygons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'map_id',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Map polygon belongs to the map.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    /**
     * Map polygon has many markers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function markers(): HasMany
    {
        return $this->hasMany(MapMarker::class);
    }
}