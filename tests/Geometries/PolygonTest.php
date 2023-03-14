<?php

namespace Spinen\Geometry\Geometries;

use Geometry as GlobalGeometry;
use Mockery;
use Spinen\Geometry\Support\TypeMapper;
use Spinen\Geometry\TestCase;

class PolygonTest extends TestCase
{
    /**
     * @var Polygon
     */
    protected $geometry;

    /**
     * @var Mockery\Mock
     */
    protected $geometry_mock;

    /**
     * @var Mockery\Mock
     */
    protected $mapper_mock;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpMocks();

        $this->geometry = new Polygon($this->geometry_mock, $this->mapper_mock);
    }

    private function setUpMocks()
    {
        $this->geometry_mock = Mockery::mock(GlobalGeometry::class);

        $this->mapper_mock = Mockery::mock(TypeMapper::class);
    }

    /**
     * @test
     *
     * @group unit
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Polygon::class, $this->geometry);
    }
}
