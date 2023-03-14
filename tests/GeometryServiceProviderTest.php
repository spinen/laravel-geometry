<?php

namespace Spinen\Geometry;

use ArrayAccess as Application;
use geoPHP;
use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Support\ServiceProvider;
use Mockery;
use Spinen\Geometry\Support\TypeMapper;

class GeometryServiceProviderTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    protected $application_mock;

    /**
     * @var Mockery\Mock
     */
    protected $events_mock;

    /**
     * @var Mockery\Mock
     */
    protected $geo_php_mock;

    /**
     * @var Mockery\Mock
     */
    protected $geometry_mock;

    /**
     * @var Mockery\Mock
     */
    protected $mapper_mock;

    /**
     * @var ServiceProvider
     */
    protected $service_provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpMocks();

        $this->service_provider = new GeometryServiceProvider($this->application_mock);
    }

    private function setUpMocks()
    {
        $this->events_mock = Mockery::mock(Events::class);
        $this->events_mock->shouldReceive('listen')
                          ->withAnyArgs()
                          ->andReturnNull();

        $this->application_mock = Mockery::mock(Application::class);
        $this->application_mock->shouldReceive('offsetGet')
                               ->zeroOrMoreTimes()
                               ->with('events')
                               ->andReturn($this->events_mock);

        $this->geometry_mock = Mockery::mock(Geometry::class);

        $this->geo_php_mock = Mockery::mock(geoPHP::class);

        $this->mapper_mock = Mockery::mock(TypeMapper::class);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(GeometryServiceProvider::class, $this->service_provider);
    }

    /**
     * @test
     */
    public function it_registers_the_geometry_mock()
    {
        $this->application_mock->shouldReceive('make')
                               ->once()
                               ->withAnyArgs([
                                   Geometry::class,
                                   [
                                       $this->geo_php_mock,
                                       $this->mapper_mock,
                                       $this->application_mock,
                                   ],
                               ])
                               ->andReturn($this->geometry_mock);

        $this->application_mock->shouldReceive('singleton')
                               ->once()
                               ->withArgs([
                                   'geometry',
                                   Mockery::on(function ($closure) {
                                       $this->assertInstanceOf(Geometry::class, $closure($this->application_mock));

                                       return true;
                                   }),
                               ])
                               ->andReturnNull();

        $this->assertNull($this->service_provider->register());
    }

    /**
     * @test
     */
    public function it_boots_the_service()
    {
        $this->assertNull($this->service_provider->boot());
        // NOTE: It would be nice to verify that the config got set.
    }
}

function config_path($file)
{
    return 'path/to/config/'.$file;
}
