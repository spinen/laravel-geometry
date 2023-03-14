<?php

namespace Spinen\Geometry\Stubs;

use Spinen\Geometry\GeometryFacade;

/**
 * Class GeometryFacadeStub
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
