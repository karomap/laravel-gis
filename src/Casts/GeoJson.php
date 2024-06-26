<?php

namespace Karomap\LaravelGIS\Casts;

use GeoIO\WKB\Parser\Parser as WKBParser;
use GeoIO\WKT\Parser\Parser as WKTParser;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Karomap\LaravelGIS\Factories\GeoJSONFactory;

class GeoJson implements CastsAttributes
{
    private $factory;
    private $wkbParser;
    private $wktParser;

    public function __construct()
    {
        $this->factory = new GeoJSONFactory();
        $this->wkbParser = new WKBParser($this->factory);
        $this->wktParser = new WKTParser($this->factory);
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string                              $key
     * @param  mixed                               $value
     * @param  array                               $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (!$value || is_array($value)) {
            return $value;
        }

        if (!ctype_print($value) || ctype_xdigit($value)) {
            try {
                $geojson = $this->wkbParser->parse($value);
                $model->setAttribute($key, $geojson);

                return $geojson;
            } catch (\Throwable $th) {
                logger()->error($th);
            }
        }

        try {
            $geojson = json_decode($value, true);
            if (!$geojson) {
                $geojson = $this->wktParser->parse($value);
            }
            $model->setAttribute($key, $geojson);

            return $geojson;
        } catch (\Throwable $th) {
            logger()->error($th);
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string                              $key
     * @param  mixed                               $value
     * @param  array                               $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return [$key => $value];
    }
}
