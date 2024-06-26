<?php

namespace Karomap\LaravelGIS\Traits;

use Illuminate\Support\Facades\DB;
use Karomap\LaravelGIS\Casts\GeoJson;

trait GeoModel
{
    /**
     * Temporary geo attributes.
     *
     * @var array
     */
    protected $tmpAttributes = [];

    /**
     * Boot the has event trait for a model.
     *
     * @return void
     */
    public static function bootGeoModel(): void
    {
        static::saving(function ($model) {
            static::savingGeoJson($model);
        });

        static::saved(function ($model) {
            static::savedGeoJson($model);
        });
    }

    /**
     * Convert GeoJSON array to raw query befor saving.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected static function savingGeoJson($model): void
    {
        foreach ($model->getCasts() as $key => $cls) {
            if ($cls == GeoJson::class && is_array($model->$key)) {
                $model->tmpAttributes[$key] = $model->$key;
                $geojson = json_encode($model->$key);
                $model->setAttribute($key, DB::raw("st_geomfromgeojson('$geojson')"));
            }
        }
    }

    /**
     * Restore original GeoJSON array.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected static function savedGeoJson($model): void
    {
        foreach ($model->getCasts() as $key => $cls) {
            if ($cls == GeoJson::class && isset($model->tmpAttributes[$key])) {
                $model->setAttribute($key, $model->tmpAttributes[$key]);
                unset($model->tmpAttributes[$key]);
            }
        }
    }

    /**
     * Convert the model instance to a GeoJSON Feature.
     *
     * @param  string|null $geom Geometry column
     * @return array|null
     */
    public function toGeoJson($geom = null): ?array
    {
        if (!$geom) {
            $geom = $this->getGeomColumn();
        }

        if (!$geom || ($geom && !$this->$geom)) {
            return;
        }

        $attributes = $this->toArray();
        unset($attributes[$geom]);

        return [
            'type' => 'Feature',
            'geometry' => $this->$geom,
            'properties' => $attributes,
        ];
    }

    /**
     * Get default geometry column.
     *
     * @return string|null
     */
    public function getGeomColumn(): ?string
    {
        $geom = null;

        foreach ($this->getCasts() as $key => $cls) {
            if ($cls == GeoJson::class) {
                $geom = $key;
                break;
            }
        }

        return $geom;
    }
}
