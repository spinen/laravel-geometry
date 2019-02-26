<?php

namespace Spinen\Geometry\Support;

use InvalidArgumentException;
use Mockery;
use Spinen\Geometry\TestCase;

class TypeMapperTest extends TestCase
{
    /**
     * @var TypeMapper
     */
    protected $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mapper = new TypeMapper();
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(TypeMapper::class, $this->mapper);
    }

    /**
     * @test
     */
    public function it_returns_the_expected_types()
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
            $this->assertEquals($type, $this->mapper->map($method));
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_for_undefined_parse_type()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown geometry type of [InvalidType] was provided.');

        $this->mapper->map('InvalidType');
    }
}
