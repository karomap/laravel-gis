<?php

namespace Karomap\LaravelGIS\DoctrineTypes;

/**
 * @OA\Schema(
 *      schema="MultiLineString",
 *      required={"type", "coordinates"},
 *      type="object",
 *      @OA\Property(property="type", type="string", default="MultiLineString"),
 *      @OA\Property(
 *          property="coordinates",
 *          type="array",
 *          @OA\Items(
 *              type="array",
 *              minItems=2,
 *              @OA\Items(ref="#/components/schemas/Coordinate"))
 *          )
 *      ),
 * )
 */
class MultiLineStringType extends GeometryType
{
    public const NAME = 'multiLineString';
}
