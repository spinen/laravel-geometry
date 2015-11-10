<?php

namespace Spinen\Geometry;

use Illuminate\Support\Facades\Facade;

class GeometryFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'geometry';
    }
}
