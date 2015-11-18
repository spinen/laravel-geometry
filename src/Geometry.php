<?php

namespace Spinen\Geometry;

use Exception;
use geoPHP;
use Illuminate\Contracts\Foundation\Application;
use RuntimeException;
use Spinen\Geometry\Support\TypeMapper;

/**
 * Class Geometry
 *
 * @package Spinen\Geometry
 *
 * @method mixed parseEwkb(mixed $data) Parses data into EWKB format.
 * @method mixed parseEwkt(mixed $data) Parses data into EWKT format.
 * @method mixed parseGeoHash(mixed $data) Parses data into GeoHash format.
 * @method mixed parseGeoJson(mixed $data) Parses data into GeoJSON format.
 * @method mixed parseGeoRss(mixed $data) Parses data into GeoRSS format.
 * @method mixed parseGoogleGeocode(mixed $data) Parses data into GoogleGeocode format.
 * @method mixed parseGpx(mixed $data) Parses data into GPX format.
 * @method mixed parseJson(mixed $data) Parses data into GeoJSON format.
 * @method mixed parseKml(mixed $data) Parses data into KML format.
 * @method mixed parseWkb(mixed $data) Parses data into WKB format.
 * @method mixed parseWkt(mixed $data) Parses data into WKT format.
 */
class Geometry
{
    /**
     * The Laravel application.
     *
     * @var Application|null
     */
    protected $app;

    /**
     * Instance of geoPHP.
     *
     * @var geoPHP
     */
    protected $geoPhp;

    /**
     * Instance of TypeMapper.
     *
     * @var TypeMapper
     */
    protected $mapper;

    /**
     * Geometry constructor.
     *
     * @param geoPHP           $geoPhp
     * @param TypeMapper       $mapper
     * @param Application|null $app
     */
    public function __construct(geoPHP $geoPhp, TypeMapper $mapper, $app = null)
    {
        $this->geoPhp = $geoPhp;
        $this->mapper = $mapper;
        $this->app = $app;
    }

    /**
     * Magic method to allow methods that are not specifically defined.
     *
     * Allow parseStudlyCaseOfType i.e. parseWkt or parseGeoJson to be called & mapped to the load method.
     *
     * @param string $name Name of the undefined method
     * @param array  $arguments
     *
     * @return bool|\GeometryCollection|mixed
     * @throws RuntimeException
     */
    public function __call($name, $arguments)
    {
        // Sugar to make parse<Format>() work
        if (preg_match("/^parse([A-Z][A-z]*)/u", $name, $parts) && 1 === count($arguments)) {
            return $this->parse($arguments[0], $parts[1]);
        }

        throw new RuntimeException(sprintf("Call to undefined method %s::%s().", __CLASS__, $name));
    }

    /**
     * Build the name to the proxy geometry class.
     *
     * @param $geometry
     *
     * @return string
     */
    protected function buildGeometryClassName($geometry)
    {
        return __NAMESPACE__ . '\Geometries\\' . get_class($geometry);
    }

    /**
     * @param string|object $data
     * @param string|null   $type
     *
     * @return bool|\GeometryCollection|mixed
     * @throws Exception
     */
    protected function loadGeometry($data, $type)
    {
        if (is_null($type)) {
            return $this->geoPhp->load($data);
        }

        return $this->geoPhp->load($data, $this->mapper->map($type));
    }

    /**
     * Pass the data to geoPHP to convert to the correct geometry type.
     *
     * @param string $data
     * @param string $type
     *
     * @return bool|\GeometryCollection|mixed
     * @throws Exception
     */
    public function parse($data, $type = null)
    {
        $geometry = $this->loadGeometry($data, $type);

        $geometry_class = $this->buildGeometryClassName($geometry);

        // If running in Laravel, then use the IoC
        if (!is_null($this->app)) {
            return $this->app->make($geometry_class, [$geometry, $this->mapper]);
        }

        return new $geometry_class($geometry, $this->mapper);
    }
}
