<?php

namespace Karomap\LaravelGIS\DoctrineTypes;

/**
 * @OA\Schema(
 *      schema="Feature",
 *      title="Feature",
 *      required={"type", "geometry"},
 *      @OA\Property(property="type", type="string", default="Feature"),
 *      @OA\Property(
 *          property="geometry",
 *          oneOf={
 *              @OA\Schema(ref="#/components/schemas/Point"),
 *              @OA\Schema(ref="#/components/schemas/MultiPoint"),
 *              @OA\Schema(ref="#/components/schemas/LineString"),
 *              @OA\Schema(ref="#/components/schemas/MultiLineString"),
 *              @OA\Schema(ref="#/components/schemas/Polygon"),
 *              @OA\Schema(ref="#/components/schemas/MultiPolygon"),
 *          }
 *      ),
 *      @OA\Property(property="properties", type="object"),
 * )
 *
 * @OA\Schema(
 *      schema="Coordinate",
 *      type="array",
 *      default="[110, -7]",
 *      minItems=2,
 *      maxItems=4,
 *      @OA\Items(
 *          type="number",
 *          format="float",
 *          description="Format: X,Y[,Z,M] => Longitude,Latitude[,Elevation,Measure]"
 *      ),
 * )
 *
 * @OA\Schema(
 *      schema="Point",
 *      required={"type", "coordinates"},
 *      @OA\Property(property="type", type="string", default="Point"),
 *      @OA\Property(property="coordinates", ref="#components/schemas/Coordinate"),
 * )
 */
class PointType extends GeometryType
{
    public const NAME = 'point';
}
