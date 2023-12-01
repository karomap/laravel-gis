<?php

namespace Karomap\LaravelGIS\Factories;

use GeoIO\CRS;
use GeoIO\Dimension;
use GeoIO\GeoJSON\Factory;

class GeoJSONFactory extends Factory
{
    public function createMultiPolygon(
        Dimension $dimension,
        ?int $srid,
        iterable $polygons,
    ): array {
        /**
         * @var iterable<array{coordinates: array}> $polygons
         */
        $geometry = [
            'type' => 'MultiPolygon',
            'coordinates' => $this->geometriesToCoordinates($polygons),
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    /**
     * @param iterable<array{coordinates: array}> $geometries
     */
    private function geometriesToCoordinates(iterable $geometries): array
    {
        $coordinates = [];

        foreach ($geometries as $geometry) {
            $coordinates[] = $geometry['coordinates'];
        }

        return $coordinates;
    }

    private function crs(int $srid): array
    {
        return [
            'type' => 'name',
            'properties' => [
                'name' => CRS\srid_to_urn($srid),
            ],
        ];
    }
}
