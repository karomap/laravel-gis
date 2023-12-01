<?php

namespace Karomap\LaravelGIS\DoctrineTypes;

/**
 * @OA\Schema(
 *      schema="LineString",
 *      required={"type", "coordinates"},
 *      @OA\Property(property="type", type="string", default="LineString"),
 *      @OA\Property(property="coordinates", type="array", minItems=2, @OA\Items(ref="#components/schemas/Coordinate")),
 * )
 */
class LineStringType extends GeometryType
{
    public const NAME = 'lineString';
}
