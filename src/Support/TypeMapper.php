<?php

namespace Spinen\Geometry\Support;

use InvalidArgumentException;

/**
 * Class TypeMapper
 *
 * Convert the types that geoPHP supports in more predictable names.
 */
class TypeMapper
{
    /**
     * Supported geometry types.
     */
    protected array $types = [
        'Ewkb' => 'ewkb',
        'Ewkt' => 'ewkt',
        'GeoHash' => 'geohash',
        'GeoJson' => 'geojson',
        'GeoRss' => 'georss',
        'GoogleGeocode' => 'google_geocode',
        'Gpx' => 'gpx',
        'Json' => 'json',
        'Kml' => 'kml',
        'Wkb' => 'wkb',
        'Wkt' => 'wkt',
    ];

    /**
     * StudlyCase of the method name.
     *
     * Look it up in the types to make sure that it is defined & map it to the string that geoPHP expects.
     *
     * @throws InvalidArgumentException
     */
    public function map(string $type): string
    {
        if (in_array($type, array_keys($this->types))) {
            return $this->types[$type];
        }

        throw new InvalidArgumentException(sprintf('Unknown geometry type of [%s] was provided.', $type));
    }
}
