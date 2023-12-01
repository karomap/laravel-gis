<?php

namespace Karomap\LaravelGIS\DoctrineTypes;

/**
 * @OA\Schema(
 *      schema="Polygon",
 *      required={"type", "coordinates"},
 *      @OA\Property(property="type", type="string", default="Polygon"),
 *      @OA\Property(property="coordinates", type="array", minItems=4, @OA\Items(ref="#components/schemas/Coordinate")),
 * )
 */
class PolygonType extends GeometryType
{
    public const NAME = 'polygon';
}
