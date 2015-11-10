<?php

namespace Spinen\Geometry\Stubs;

use Spinen\Geometry\GeometryFacade;

/**
 * Class GeometryFacadeStub
 *
 * @package Spinen\Geometry\Stubs
 */
class GeometryFacadeStub extends GeometryFacade
{
    /**
     * Expose the protected function to allow it to be tested.
     *
     * @return string
     */
    public function publicGetFacadeAccessor()
    {
        return $this->getFacadeAccessor();
    }
}
