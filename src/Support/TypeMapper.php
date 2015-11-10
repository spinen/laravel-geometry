<?php

namespace Spinen\Geometry\Support;

/**
 * Class TypeMapper
 *
 * Convert the types that geoPHP supports in more predictable names.
 *
 * @package Spinen\Geometry\Support
 */
class TypeMapper
{
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
     * StudlyCase of the method name.
     *
     * Look it up in the types to make sure that it is defined & map it to the string that geoPHP expects.
     *
     * @param string $type
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function map($type)
    {
        if (in_array($type, array_keys($this->types))) {
            return $this->types[$type];
        }

        throw new InvalidArgumentException(sprintf("Unknown geometry type of [%s] was provided.", $type));
    }
}
