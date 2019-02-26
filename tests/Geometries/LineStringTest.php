<?php

namespace Spinen\Geometry\Geometries;

use Mockery;
use Spinen\Geometry\Support\TypeMapper;
use Spinen\Geometry\TestCase;

class LineStringTest extends TestCase
{
    /**
     * @var LineString
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

        $this->geometry = new LineString($this->geometry_mock, $this->mapper_mock);
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
        $this->assertInstanceOf(LineString::class, $this->geometry);
    }
}
