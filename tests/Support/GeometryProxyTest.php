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

    /**
     * @test
     * @group unit
     */
    public function it_calculates_the_acres_of_a_polygon()
    {
        // Since is it an estimate, this is the margin of error
        $error = 0.01;

        // Our office park
        $json = '{"type":"Polygon","coordinates":[[[-83.737335814824,32.800152813394],[-83.737339484336,32.800608527036],[-83.738309865923,32.800563598199],[-83.73831030949,32.799978351717],[-83.738310952694,32.799127904219],[-83.739853535948,32.799151884925],[-83.739986184774,32.799143007967],[-83.739898851499,32.798918827631],[-83.739877597616,32.798741514606],[-83.739977541009,32.798639242758],[-83.740143740248,32.798546524571],[-83.740166108029,32.798490629605],[-83.739979431602,32.798488650057],[-83.738855137995,32.7984641996],[-83.737063785654,32.798419071462],[-83.736299090827,32.798398751092],[-83.736299027099,32.798411979167],[-83.736151777003,32.798409067995],[-83.736143417056,32.79879316208],[-83.736140340166,32.79909395743],[-83.737256307805,32.799111377933],[-83.737259977211,32.799818917076],[-83.737333140303,32.799820651361],[-83.737335814824,32.800152813394]]]}';

        // Known acres for the polygon above
        $known_acres = 10.59;

        // Upper & lower bonds for the error
        $hi_limit = $known_acres * (1 + $error);
        $low_limit = $known_acres * (1 - $error);

        $this->geometry_mock->shouldReceive('out')
                            ->once()
                            ->with('json')
                            ->andReturn($json);

        $this->mapper_mock->shouldReceive('map')
                          ->once()
                          ->with('Json')
                          ->andReturn('json');

        $acres = $this->geometry_proxy->acres;

        // Counter clock-wise, so negative
        $this->assertTrue($acres < 0);

        // Just get the acres rounded to 2 places
        $acres = round(abs($acres), 2);

        // Is the amount within the margin
        $this->assertTrue(($low_limit <= $acres) && ($acres <= $hi_limit));
    }
}

function method_exists($object, $method_name)
{
    if ('proxiedMethod' === $method_name) {
        return true;
    }

    if ('getAcres' === $method_name) {
        return true;
    }

    if ('getSquareMeters' === $method_name) {
        return true;
    }
}
