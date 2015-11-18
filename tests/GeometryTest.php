<?php

namespace Spinen\Geometry;

use geoPHP;
use Illuminate\Contracts\Foundation\Application as Laravel;
use InvalidArgumentException;
use Mockery;
use RuntimeException;
use Spinen\Geometry\Geometries\LineString;
use Spinen\Geometry\Geometries\MultiLineString;
use Spinen\Geometry\Geometries\MultiPoint;
use Spinen\Geometry\Geometries\MultiPolygon;
use Spinen\Geometry\Geometries\Point;
use Spinen\Geometry\Geometries\Polygon;
use Spinen\Geometry\Support\TypeMapper;

class GeometryTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    protected $geo_php_mock;

    /**
     * @var Geometry
     */
    protected $geometry;

    /**
     * @var Mockery\Mock
     */
    protected $mapper_mock;

    public function setUp()
    {
        parent::setUp();

        $this->setUpMocks();

        $this->geometry = new Geometry($this->geo_php_mock, $this->mapper_mock);
    }

    protected function setUpMocks()
    {
        $this->geo_php_mock = Mockery::Mock(geoPHP::class);
        $this->mapper_mock = Mockery::mock(TypeMapper::class);
    }

    /**
     * @test
     * @group unit
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Geometry::class, $this->geometry);
    }

    /**
     * @test
     * @group unit
     */
    public function it_calls_load_on_geoPHP_with_the_correct_type_for_the_dynamic_parse_methods()
    {
        $types = [
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

        $polygon = new \Polygon();

        foreach ($types as $method => $type) {
            $this->geo_php_mock->shouldReceive('load')
                               ->once()
                               ->withArgs([
                                   'data',
                                   $type,
                               ])
                               ->andReturn($polygon);

            $this->mapper_mock->shouldReceive('map')
                              ->once()
                              ->with($method)
                              ->andReturn($type);

            $this->geometry->{'parse' . $method}('data');
        }
    }

    /**
     * @test
     * @group unit
     */
    public function it_uses_laravel_to_resolve_classes_if_was_provided()
    {
        $laravel_mock = Mockery::mock(Laravel::class);

        $this->geometry = new Geometry($this->geo_php_mock, $this->mapper_mock, $laravel_mock);

        $polygon = new \Polygon();

        $this->geo_php_mock->shouldReceive('load')
                           ->once()
                           ->withArgs([
                               'data',
                               'wkt',
                           ])
                           ->andReturn($polygon);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Wkt')
                          ->andReturn('wkt');

        $laravel_mock->shouldReceive('make')
                     ->once()
                     ->withArgs([
                         'Spinen\Geometry\Geometries\Polygon',
                         [
                             $polygon,
                             $this->mapper_mock,
                         ],
                     ])
                     ->andReturn('geometry');

        $this->geometry->parseWkt('data');
    }

    /**
     * @test
     * @group unit
     */
    public function it_returns_the_wrapped_linestring_class_for_geoPHPs_linestring()
    {
        $geometry = new \LineString();

        $this->geo_php_mock->shouldReceive('load')
                           ->once()
                           ->withArgs([
                               'data',
                               'wkt',
                           ])
                           ->andReturn($geometry);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Wkt')
                          ->andReturn('wkt');

        $this->assertInstanceOf(LineString::class, $this->geometry->parseWkt('data'));
    }

    /**
     * @test
     * @group unit
     */
    public function it_returns_the_wrapped_multilinestring_class_for_geoPHPs_multilinestring()
    {
        $geometry = new \MultiLineString();

        $this->geo_php_mock->shouldReceive('load')
                           ->once()
                           ->withArgs([
                               'data',
                               'wkt',
                           ])
                           ->andReturn($geometry);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Wkt')
                          ->andReturn('wkt');

        $this->assertInstanceOf(MultiLineString::class, $this->geometry->parseWkt('data'));
    }

    /**
     * @test
     * @group unit
     */
    public function it_returns_the_wrapped_multipoint_class_for_geoPHPs_multipoint()
    {
        $geometry = new \MultiPoint();

        $this->geo_php_mock->shouldReceive('load')
                           ->once()
                           ->withArgs([
                               'data',
                               'wkt',
                           ])
                           ->andReturn($geometry);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Wkt')
                          ->andReturn('wkt');

        $this->assertInstanceOf(MultiPoint::class, $this->geometry->parseWkt('data'));
    }

    /**
     * @test
     * @group unit
     */
    public function it_returns_the_wrapped_multipolygon_class_for_geoPHPs_multipolygon()
    {
        $geometry = new \MultiPolygon();

        $this->geo_php_mock->shouldReceive('load')
                           ->once()
                           ->withArgs([
                               'data',
                               'wkt',
                           ])
                           ->andReturn($geometry);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Wkt')
                          ->andReturn('wkt');

        $this->assertInstanceOf(MultiPolygon::class, $this->geometry->parseWkt('data'));
    }

    /**
     * @test
     * @group unit
     */
    public function it_returns_the_wrapped_polygon_class_for_geoPHPs_polygon()
    {
        $geometry = new \Polygon();

        $this->geo_php_mock->shouldReceive('load')
                           ->once()
                           ->withArgs([
                               'data',
                               'wkt',
                           ])
                           ->andReturn($geometry);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Wkt')
                          ->andReturn('wkt');

        $this->assertInstanceOf(Polygon::class, $this->geometry->parseWkt('data'));
    }

    /**
     * @test
     * @group unit
     */
    public function it_returns_the_wrapped_point_class_for_geoPHPs_point()
    {
        $geometry = new \Point();

        $this->geo_php_mock->shouldReceive('load')
                           ->once()
                           ->withArgs([
                               'data',
                               'wkt',
                           ])
                           ->andReturn($geometry);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Wkt')
                          ->andReturn('wkt');

        $this->assertInstanceOf(Point::class, $this->geometry->parseWkt('data'));
    }

    /**
     * @test
     * @group                    unit
     * @expectedException RuntimeException
     * @expectedExceptionMessage Call to undefined method Spinen\Geometry\Geometry::invalidMethod().
     */
    public function it_raises_exception_for_undefined_method()
    {
        $this->geometry->invalidMethod('data');
    }

    /**
     * @test
     * @group unit
     * @expectedException InvalidArgumentException
     */
    public function it_raises_exception_when_the_data_cannot_be_converted()
    {
        $this->geo_php_mock->shouldReceive('load')
                           ->once()
                           ->withArgs([
                               'invalid',
                               'wkt',
                           ])
                           ->andReturnNull();

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Wkt')
                          ->andReturn('wkt');

        $this->geometry->parseWkt('invalid');
    }

    /**
     * @test
     * @group unit
     * @expectedException InvalidArgumentException
     */
    public function it_raises_exception_when_building_name_to_proxy_class_for_null_geometry()
    {
        $this->geometry->buildGeometryClassName(null);
    }

    /**
     * @test
     * @group unit
     * @expectedException RuntimeException
     */
    public function it_raises_exception_when_building_name_to_proxy_class_that_does_not_exist()
    {
        $this->geometry->buildGeometryClassName($this->geometry);
    }
}
