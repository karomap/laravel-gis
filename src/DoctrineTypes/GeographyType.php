<?php

namespace Karomap\LaravelGIS\DoctrineTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class GeographyType extends GeometryType
{
    public const NAME = 'geography';

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return "ST_GeogFromText('$sqlExpr')";
    }
}
