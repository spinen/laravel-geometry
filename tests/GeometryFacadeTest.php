<?php

namespace Spinen\Geometry;

use Mockery;
use Spinen\Geometry\Stubs\GeometryFacadeStub;

class GeometryFacadeTest extends TestCase
{
    /**
     * @var GeometryFacade
     */
    protected $facade;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpMocks();

        $this->facade = new GeometryFacade();
    }

    private function setUpMocks()
    {
        //
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(GeometryFacade::class, $this->facade);
    }

    /**
     * @test
     */
    public function it_returns_the_correct_aliase_for_the_facade()
    {
        $facade_stub = new GeometryFacadeStub();

        $this->assertEquals('geometry', $facade_stub->publicGetFacadeAccessor());
    }
}


