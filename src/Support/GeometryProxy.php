<?php

namespace Spinen\Geometry\Support;

use Geometry as GlobalGeometry;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class GeometryProxy
 *
 * Proxy class to "wrap" the geoPHP classes into class that we can add functionality.
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
 *
 * @property float acres The acres with in +/-1%.
 * @property array coordinates The points that define the shape.
 * @property float square_meters The square meters with in +/-1%.
 */
class GeometryProxy
{
    /**
     * Cache the area to not have to loop through the calculations each time that it is needed.
     */
    protected ?float $cached_area = null;

    /**
     * The geometry to proxy.
     */
    protected GlobalGeometry $geometry;

    /**
     * Cached array version of the geometry.
     */
    protected ?array $geometry_array = null;

    /**
     * Instance of TypeMapper.
     */
    protected TypeMapper $mapper;

    /**
     * Polygon constructor.
     */
    public function __construct(GlobalGeometry $geometry, TypeMapper $mapper)
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
     * @throws InvalidArgumentException
     */
    public function __call(string $name, array $arguments)
    {
        // Sugar to make to<Format>() work
        if (preg_match('/^to([A-Z][A-z]*)/u', $name, $parts) && 0 === count($arguments)) {
            return $this->geometry->out($this->mapper->map($parts[1]));
        }

        // Call the method on the class being proxied
        if (method_exists($this->geometry, $name)) {
            return call_user_func_array(
                [$this->geometry, $name], array_map([$this, 'exposeRawIfAvailable'], $arguments)
            );
        }

        throw new RuntimeException(sprintf('Call to undefined method %s::%s().', __CLASS__, $name));
    }

    /**
     * Expose the getters
     */
    public function __get(string $name)
    {
        // Properties on the geometry
        if (isset($this->toArray()[$name])) {
            return $this->toArray()[$name];
        }

        // Shortcut to the getters
        if (method_exists($this, 'get'.Str::studly($name))) {
            return $this->{'get'.Str::studly($name)}();
        }

        throw new RuntimeException(sprintf('Undefined property: %s', $name));
    }

    /**
     * If using the object as a string, just return the json.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Figure out what index to use in the ringArea calculation
     */
    private function determineCoordinateIndices(int $index, int $length): array
    {
        // i = N-2
        if ($index === ($length - 2)) {
            return [$length - 2, $length - 1, 0];
        }

        // i = N-1
        if ($index === ($length - 1)) {
            return [$length - 1, 0, 1];
        }

        // i = 0 to N-3
        return [$index, $index + 1, $index + 2];
    }

    /**
     * If the object passed in has a getRawGeometry, call it
     */
    protected function exposeRawIfAvailable($argument)
    {
        return ((\is_string($argument) || \is_object($argument)) && method_exists($argument, 'getRawGeometry'))
            ? $argument->getRawGeometry()
            : $argument;
    }

    /**
     * Calculate the acres
     */
    public function getAcres(): float
    {
        return $this->square_meters * 0.000247105381;
    }

    /**
     * Expose the underlying Geometry object
     */
    public function getRawGeometry(): GlobalGeometry
    {
        return $this->geometry;
    }

    /**
     * Calculate the square meters
     */
    public function getSquareMeters(): float
    {
        if (! is_null($this->cached_area)) {
            return $this->cached_area;
        }

        $this->cached_area = 0.0;

        foreach ($this->coordinates as $coordinate) {
            $this->cached_area += $this->ringArea($coordinate);
        }

        return $this->cached_area;
    }

    /**
     * Convert degrees to radians
     *
     * I know that there is a built in function, but I read that it was very slow & to use this.
     */
    private function radians(float|int $degrees): float
    {
        return $degrees * M_PI / 180;
    }

    /**
     * Estimate the area of a ring
     *
     * Calculate the approximate area of the polygon were it projected onto
     *     the earth.  Note that this area will be positive if ring is oriented
     *     clockwise, otherwise it will be negative.
     *
     * Reference:
     * Robert. G. Chamberlain and William H. Duquette, "Some Algorithms for
     *     Polygons on a Sphere", JPL Publication 07-03, Jet Propulsion
     *     Laboratory, Pasadena, CA, June 2007 http://trs-new.jpl.nasa.gov/dspace/handle/2014/40409
     *
     * @see https://github.com/mapbox/geojson-area/blob/master/index.js#L55
     */
    public function ringArea($coordinates): float
    {
        $area = 0.0;

        $length = count($coordinates);

        if ($length <= 2) {
            return $area;
        }

        for ($i = 0; $i < $length; $i++) {
            [$lower_index, $middle_index, $upper_index] = $this->determineCoordinateIndices($i, $length);

            $point1 = $coordinates[$lower_index];
            $point2 = $coordinates[$middle_index];
            $point3 = $coordinates[$upper_index];

            $area += ($this->radians($point3[0]) - $this->radians($point1[0])) * sin($this->radians($point2[1]));
        }

        return $area * 6378137 * 6378137 / 2;
    }

    /**
     * Build array of the object
     *
     * Cache the result, so that we don't decode it on every call.
     */
    public function toArray(): array
    {
        if (is_null($this->geometry_array)) {
            $this->geometry_array = (array) json_decode($this->toJson(), true);
        }

        return $this->geometry_array;
    }
}
