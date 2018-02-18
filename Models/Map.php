<?php

namespace Modules\Map\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Map\PassThroughs\Format;

class Map extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_map__maps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier',
        'zoom',
        'latitude',
        'longitude',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'markers',
        'polygons.markers',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Map has many polygons.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function polygons(): HasMany
    {
        return $this->hasMany(MapPolygon::class);
    }

    /**
     * Map has many markers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function markers(): HasMany
    {
        return $this->hasMany(MapMarker::class)->whereNull('map_polygon_id');
    }

    /** -------------------- PassThroughs -------------------- */

    /**
     * Formatter pass-through.
     *
     * @return \Modules\Map\PassThroughs\Format
     */
    public function format(): Format
    {
        return new Format($this);
    }
}