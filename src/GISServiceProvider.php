<?php

namespace Karomap\LaravelGIS;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Karomap\LaravelGIS\DoctrineTypes\GeographyType;
use Karomap\LaravelGIS\DoctrineTypes\GeometryType;
use Karomap\LaravelGIS\DoctrineTypes\LineStringType;
use Karomap\LaravelGIS\DoctrineTypes\MultiLineStringType;
use Karomap\LaravelGIS\DoctrineTypes\MultiPointType;
use Karomap\LaravelGIS\DoctrineTypes\MultiPolygonType;
use Karomap\LaravelGIS\DoctrineTypes\PointType;
use Karomap\LaravelGIS\DoctrineTypes\PolygonType;

class GISServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/add_postgis_extension.php' => $this->getMigrationFileName('add_postgis_extension.php'),
        ], 'gis-migrations');
    }

    public function register()
    {
        try {
            if (in_array(DB::getDriverName(), ['mysql', 'pgsql'])) {
                $customDoctrineTypes = [
                    GeographyType::class,
                    GeometryType::class,
                    PointType::class,
                    MultiPointType::class,
                    LineStringType::class,
                    MultiLineStringType::class,
                    PolygonType::class,
                    MultiPolygonType::class,
                ];

                $doctrinePlatform = DB::getDoctrineConnection()->getDatabasePlatform();

                foreach ($customDoctrineTypes as $typeClass) {
                    DB::connection()->registerDoctrineType($typeClass, $typeClass::NAME, $typeClass::NAME);
                    $doctrinePlatform->registerDoctrineTypeMapping($typeClass::NAME, $typeClass::NAME);
                }
            }
        } catch (\Throwable $th) {
            logger()->error($th);
        }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName($migrationFileName, $index = 0): string
    {
        $timestamp = date('Y_m_d_Hi').Str::padLeft((string) $index, 2, '0');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
