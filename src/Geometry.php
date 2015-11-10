<?php

namespace Spinen\Geometry;

use geoPHP;

class Geometry
{
    /**
     * @var geoPHP
     */
    protected $geoPhp;

    /**
     * Geometry constructor.
     *
     * @param geoPHP $geoPhp
     */
    public function __construct(geoPHP $geoPhp) {
        $this->geoPhp = $geoPhp;
    }
}
