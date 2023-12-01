<?php

namespace Karomap\LaravelGIS\DoctrineTypes;

/**
 * @OA\Schema(
 *      schema="MultiPolygon",
 *      required={"type", "coordinates"},
 *      type="object",
 *      @OA\Property(property="type", type="string", default="MultiPolygon"),
 *      @OA\Property(
 *          property="coordinates",
 *          type="array",
 *          @OA\Items(
 *              type="array",
 *              minItems=4,
 *              @OA\Items(ref="#components/schemas/Coordinate")
 *          )
 *      ),
 * )
 */
class MultiPolygonType extends GeometryType
{
    public const NAME = 'multiPolygon';
}
