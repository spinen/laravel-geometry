<?php

namespace Spinen\Geometry\Support;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class GeometryProxy
 *
 * Proxy class to "wrap" the geoPHP classes into class that we can add functionality.
 *
 * @package Spinen\Geometry
 *
 * @method mixed toEwkb() Returns the geometry in EWKB format.
 * @method mixed toEwkt() Returns the geometry in EWKT format.
 * @method mixed toGeoHash() Returns the geometry in GeoHash format.
 * @method mixed toGeoJson() Returns the geometry in GeoJSON format.
 * @method mixed toGeoRss() Returns the geometry in GeoRSS format.
 * @method mixed toGoogleGeocode() Returns the geometry in GoogleGeocode format.
 * @method mixed toGpx() Returns the geometry in GPX format.
 * @method mixed toJson() Returns the geometry in GeoJSON format.
 * @method mixed toKml() Returns the geometry in KML format.
 * @method mixed toWkb() Returns the geometry in WKB format.
 * @method mixed toWkt() Returns the geometry in WKT format.
 */
class GeometryProxy
{
    /**
     * The geometry to proxy.
     *
     * @var
     */
    protected $geometry;

    /**
     * Instance of TypeMapper.
     *
     * @var TypeMapper
     */
    protected $mapper;

    /**
     * Polygon constructor.
     *
     * @param mixed      $geometry
     * @param TypeMapper $mapper
     */
    public function __construct($geometry, TypeMapper $mapper)
    {
        $this->geometry = $geometry;
        $this->mapper = $mapper;
    }

    /**
     * Magic method to allow methods that are not specifically defined.
     *
     * This is where we look to see if the class that we are proxing has the method that is being called, and if it
     * does, then pass the call to the class under proxy.  If a method is defined in our class, then it gets called
     * first, so you can "extend" the classes by defining methods that overwrite the "parent" there.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    function __call($name, $arguments)
    {
        // Sugar to make to<Format>() work
        if (preg_match("/^to([A-Z][A-z]*)/u", $name, $parts) && 0 === count($arguments)) {
            return $this->geometry->out($this->mapper->map($parts[1]));
        }

        // Call the method on the class being proxied
        if (method_exists($this->geometry, $name)) {
            return call_user_func_array([$this->geometry, $name], $arguments);
        }

        throw new RuntimeException(sprintf("Call to undefined method %s::%s().", __CLASS__, $name));
    }
}
