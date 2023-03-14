<?php

namespace Spinen\Geometry;

use Exception;
use Geometry as GlobalGeometry;
use geoPHP;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use RuntimeException;
use Spinen\Geometry\Support\GeometryProxy;
use Spinen\Geometry\Support\TypeMapper;

/**
 * Class Geometry
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
     */
    protected ?Application $app;

    /**
     * Instance of geoPHP.
     */
    protected geoPHP $geoPhp;

    /**
     * Instance of TypeMapper.
     */
    protected TypeMapper $mapper;

    /**
     * Geometry constructor.
     */
    public function __construct(geoPHP $geoPhp, TypeMapper $mapper, ?Application $app = null)
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
     * @throws RuntimeException
     */
    public function __call(string $name, array $arguments): mixed
    {
        // Sugar to make parse<Format>() work
        if (preg_match('/^parse([A-Z][A-z]*)/u', $name, $parts) && 1 === count($arguments)) {
            return $this->parse($arguments[0], $parts[1]);
        }

        throw new RuntimeException(sprintf('Call to undefined method %s::%s().', __CLASS__, $name));
    }

    /**
     * Build the name to the proxy geometry class.
     *
     * @throws InvalidArgumentException|RuntimeException
     */
    public function buildGeometryClassName(?GlobalGeometry $geometry): string
    {
        if (is_null($geometry)) {
            throw new InvalidArgumentException('The geometry object cannot be null when building the name to the proxy class.');
        }

        $class = __NAMESPACE__.'\Geometries\\'.get_class($geometry);

        if (class_exists($class)) {
            return $class;
        }

        throw new RuntimeException(sprintf('There proxy class [%s] is not defined.', $class));
    }

    /**
     * Call geoPHP to load the data.
     *
     * @throws InvalidArgumentException|Exception
     */
    protected function loadGeometry(object|string $data, ?string $type): GlobalGeometry
    {
        $geometry = is_null($type)
            ? $this->geoPhp->load($data)
            : $this->geoPhp->load($data, $this->mapper->map($type));

        if (! $geometry) {
            throw new InvalidArgumentException('Could not parse the supplied data.');
        }

        return $geometry;
    }

    /**
     * Pass the data to geoPHP to convert to the correct geometry type.
     *
     * @throws InvalidArgumentException|Exception
     */
    public function parse(object|string $data, ?string $type = null): GeometryProxy
    {
        $geometry = $this->loadGeometry($data, $type);

        if (is_null($geometry)) {
            throw new InvalidArgumentException('Could not parse the supplied data.');
        }

        $geometry_class = $this->buildGeometryClassName($geometry);

        // If running in Laravel, then use the IoC
        return is_null($this->app)
            ? new $geometry_class($geometry, $this->mapper)
            : $this->app->make($geometry_class, [$geometry, $this->mapper]);
    }
}
