<?php

namespace Spinen\Geometry\Geometries;

use Geometry as GlobalGeometry;
use Mockery;
use Spinen\Geometry\Support\TypeMapper;
use Spinen\Geometry\TestCase;

class PointTest extends TestCase
{
    /**
     * @var Point
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

        $this->geometry = new Point($this->geometry_mock, $this->mapper_mock);
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
        $this->assertInstanceOf(Point::class, $this->geometry);
    }
}
