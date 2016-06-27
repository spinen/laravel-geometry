<?php

namespace Spinen\Geometry\Support;

use Mockery;
use Spinen\Geometry\TestCase;

class GeometryProxyTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    protected $geometry_mock;

    /**
     * @var GeometryProxy
     */
    protected $geometry_proxy;

    /**
     * @var Mockery\Mock
     */
    protected $mapper_mock;

    public function setUp()
    {
        parent::setUp();

        $this->setUpMocks();

        $this->geometry_proxy = new GeometryProxy($this->geometry_mock, $this->mapper_mock);
    }

    private function setUpMocks()
    {
        $this->geometry_mock = Mockery::mock(StdClass::class);

        $this->mapper_mock = Mockery::mock(TypeMapper::class);
    }

    /**
     * @test
     * @group unit
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(GeometryProxy::class, $this->geometry_proxy);
    }

    /**
     * @test
     * @group unit
     */
    public function it_calls_count_on_geoPHP_geometry_with_the_correct_type_for_the_dynamic_to_methods()
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
            $this->geometry_mock->shouldReceive('out')
                                ->once()
                                ->with($type)
                                ->andReturn('converted geomerty');

            $this->mapper_mock->shouldReceive('map')
                              ->once()
                              ->with($method)
                              ->andReturn($type);

            $this->geometry_proxy->{'to' . $method}();
        }
    }

    /**
     * @test
     * @group
     */
    public function it_returns_the_results_of_a_method_on_the_geometry_if_method_is_not_on_proxy()
    {
        $results = 'results';

        $this->geometry_mock->shouldReceive('proxiedMethod')
                            ->once()
                            ->withNoArgs()
                            ->andReturn($results);

        $this->assertEquals($results, $this->geometry_proxy->proxiedMethod());
    }

    /**
     * @test
     * @group                    unit
     * @expectedException RuntimeException
     * @expectedExceptionMessage Call to undefined method Spinen\Geometry\Support\GeometryProxy::invalidMethod().
     */
    public function it_raises_exception_for_undefined_method()
    {
        $this->geometry_proxy->invalidMethod();
    }

    /**
     * @test
     * @group unit
     */
    public function it_returns_the_json_when_casted_as_a_string()
    {
        $json = '{"first":"value","second":"another"}';

        $this->geometry_mock->shouldReceive('out')
                            ->once()
                            ->with('json')
                            ->andReturn($json);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Json')
                          ->andReturn('json');

        $this->assertEquals($json, (string)$this->geometry_proxy);
    }

    /**
     * @test
     * @group unit
     */
    public function it_returns_a_cached_array()
    {
        $this->geometry_mock->shouldReceive('out')
                            ->once()
                            ->with('json')
                            ->andReturn('{}');

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Json')
                          ->andReturn('json');

        $this->assertTrue(is_array($this->geometry_proxy->toArray()));

        // Make sure that is returns the cache since the mocks only return once
        $this->assertTrue(is_array($this->geometry_proxy->toArray()));
    }

    /**
     * @test
     * @group unit
     */
    public function it_exposes_the_geometry_properties_as_its_own()
    {
        $this->geometry_mock->shouldReceive('out')
                            ->once()
                            ->with('json')
                            ->andReturn('{"first":"value","second":"another"}');

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Json')
                          ->andReturn('json');

        $this->assertEquals('value', $this->geometry_proxy->first);

        $this->assertEquals('another', $this->geometry_proxy->second);
    }

    /**
     * @test
     * @group unit
     * @expectedException RuntimeException
     */
    public function it_raises_exception_getting_non_existing_property()
    {
        $this->geometry_mock->shouldReceive('out')
                            ->once()
                            ->with('json')
                            ->andReturn('{"first":"value","second":"another"}');

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Json')
                          ->andReturn('json');

        $this->geometry_proxy->missing;
    }
}

function method_exists($object, $method_name)
{
    if ('proxiedMethod' === $method_name) {
        return true;
    }
}
