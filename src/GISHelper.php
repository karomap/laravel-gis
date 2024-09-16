<?php

namespace Karomap\LaravelGIS;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GISHelper
{
    /**
     * Get table columns.
     *
     * @param  string $table
     * @param  bool   $withType
     * @return array
     */
    public static function getColumns(string $table, $withType = true)
    {
        if ($withType) {
            return array_map(function ($col) {
                return $col['type_name'];
            }, Schema::getColumns($table));
        }

        return Schema::getColumnListing($table);
    }

    /**
     * Get GeoJSON.
     *
     * @param  string   $table
     * @param  string   $geomColumn
     * @param  \Closure $callback
     * @return string
     */
    public static function getGeoJson($table, $geomColumn, $callback = null)
    {
        $db_driver = DB::getDriverName();

        if ($db_driver == 'pgsql') {
            $result = DB::table($table, 't')
                    ->selectRaw("json_build_object(
                            'type', 'FeatureCollection',
                            'features', coalesce(json_agg(st_asgeojson(t.*, '$geomColumn')::json), '[]'::json)
                        ) as geojson")
                    ->whereNotNull($geomColumn)
                    ->when($callback, $callback)
                    ->first();

            return $result->geojson;
        } elseif ($db_driver == 'mysql') {
            $columns = array_diff(self::getColumns($table, false), [$geomColumn]);
            $columns[] = DB::raw("ST_AsGeoJSON($geomColumn) as geometry");
            $result = DB::table($table)->whereNotNull($geomColumn)->get($columns);
            $features = [];

            foreach ($result as $record) {
                if ($record->geometry) {
                    $features[] = [
                        'type' => 'Feature',
                        'geometry' => json_decode($record->geometry, true),
                        'properties' => Arr::except((array) $record, ['geometry']),
                    ];
                }
            }

            $geojson = [
                'type' => 'FeatureCollection',
                'features' => $features,
            ];

            return json_encode($geojson);
        } else {
            throw new \Exception('Unsupported database driver.');
        }
    }

    /**
     * Get single GeoJSON feature.
     *
     * @param  string        $table
     * @param  string        $geomColumn
     * @param  \Closure|null $callback
     * @return array|void
     */
    public static function getGeoJsonFeature(string $table, $geomColumn = 'geom', ?\Closure $callback = null)
    {
        $columns = Arr::except(self::getColumns($table, false), [$geomColumn]);
        $columns[] = DB::raw("ST_AsGeoJSON($geomColumn) as geom");

        $record = DB::table($table)->when($callback, $callback)->select($columns)->first();

        if ($record) {
            return [
                'type' => 'Feature',
                'geometry' => $record->geom ? json_decode($record->geom, true) : null,
                'properties' => Arr::except((array) $record, ['geom']),
            ];
        }
    }
}
