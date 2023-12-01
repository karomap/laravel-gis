<?php

namespace Karomap\LaravelGIS\DoctrineTypes;

/**
 * @OA\Schema(
 *      schema="MultiPoint",
 *      required={"type", "coordinates"},
 *      type="object",
 *      @OA\Property(property="type", type="string", default="MultiPoint"),
 *      @OA\Property(property="coordinates", type="array", @OA\Items(ref="#/components/schemas/Coordinate")),
 * )
 */
class MultiPointType extends GeometryType
{
    public const NAME = 'multiPoint';
}
