<?php

namespace Spinen\Geometry;

use geoPHP;
use Mockery;
use RuntimeException;

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

    public function setUp()
    {
        parent::setUp();

        $this->setUpMocks();

        $this->geometry = new Geometry($this->geo_php_mock);
    }

    protected function setUpMocks()
    {
        $this->geo_php_mock = Mockery::Mock(geoPHP::class);
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

        foreach ($types as $method => $type) {
            $this->geo_php_mock->shouldReceive('load')
                               ->once()
                               ->withArgs([
                                   'data',
                                   $type,
                               ])
                               ->andReturn('geometry');

            $this->geometry->{'parse' . $method}('data');
        }
    }

    /**
     * @test
     * @group unit
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
     * @expectedExceptionMessage Unknown geometry type of [InvalidType] was provided.
     */
    public function it_raises_exception_for_undefined_parse_type()
    {
        $this->geometry->parseInvalidType('data');
    }
}


