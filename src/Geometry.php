<?php

namespace Spinen\Geometry;

use Exception;
use geoPHP;
use InvalidArgumentException;
use RuntimeException;

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
     * Instance of geoPHP.
     *
     * @var geoPHP
     */
    protected $geoPhp;

    /**
     * Supported geometry types.
     *
     * @var array
     */
    protected $types = [
        'Ewkb'          => 'ewkb',
        'Ewkt'          => 'ewkt',
        'GeoHash'       => 'geohash',
        'GeoJson'       => 'geojson',
        'GeoRss'        => 'georss',
        'GoogleGeocode' => 'google_geocode',
        'Gpx'           => 'gpx',
        'Json'          => 'json',
        'Kml'           => 'kml',
        'Wkb'           => 'wkb',
        'Wkt'           => 'wkt',
    ];

    /**
     * Geometry constructor.
     *
     * @param geoPHP $geoPhp
     */
    public function __construct(geoPHP $geoPhp)
    {
        $this->geoPhp = $geoPhp;
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
        if (preg_match("/parse(.+)/u", $name, $parts) && 1 === count($arguments)) {
            return $this->parse($arguments[0], $parts[1]);
        }

        throw new RuntimeException(sprintf("Call to undefined method %s::%s().", __CLASS__, $name));
    }

    /**
     * StudlyCase of the method name.
     *
     * Look it up in the types to make sure that it is defined & map it to the string that geoPHP expects.
     *
     * @param string $type
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function convertType($type)
    {
        if (in_array($type, array_keys($this->types))) {
            return $this->types[$type];
        }

        throw new InvalidArgumentException(sprintf("Unknown geometry type of [%s] was provided.", $type));
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
    protected function parse($data, $type)
    {
        return $this->geoPhp->load($data, $this->convertType($type));
    }
}
