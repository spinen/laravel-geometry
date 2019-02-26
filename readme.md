# SPINEN's Laravel Geometry

[![Latest Stable Version](https://poser.pugx.org/spinen/laravel-geometry/v/stable)](https://packagist.org/packages/spinen/laravel-geometry)
[![Total Downloads](https://poser.pugx.org/spinen/laravel-geometry/downloads)](https://packagist.org/packages/spinen/laravel-geometry)
[![Latest Unstable Version](https://poser.pugx.org/spinen/laravel-geometry/v/unstable)](https://packagist.org/packages/spinen/laravel-geometry)
[![License](https://poser.pugx.org/spinen/laravel-geometry/license)](https://packagist.org/packages/spinen/laravel-geometry)

Wrapper over the geoPHP Class to make it integrate with Laravel better.

## Build Status

| Branch | Status | Coverage | Code Quality |
| ------ | :----: | :------: | :----------: |
| Develop | [![Build Status](https://travis-ci.org/spinen/laravel-geometry.svg?branch=develop)](https://travis-ci.org/spinen/laravel-geometry) | [![Code Coverage](https://scrutinizer-ci.com/g/spinen/laravel-geometry/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/spinen/laravel-geometry/?branch=develop) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spinen/laravel-geometry/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/spinen/laravel-geometry/?branch=develop) |
| Master | [![Build Status](https://travis-ci.org/spinen/laravel-geometry.svg?branch=master)](https://travis-ci.org/spinen/laravel-geometry) | [![Code Coverage](https://scrutinizer-ci.com/g/spinen/laravel-geometry/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/spinen/laravel-geometry/?branch=develop) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spinen/laravel-geometry/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spinen/laravel-geometry/?branch=master) |

## Prerequisite

#### NOTE: If you need to use < php7.2, please stay with version 1.x

Aside from Laravel >= 5.5, there is 1 package that is required.

* [phayes/geophp](https://github.com/phayes/geoPHP)

## Install

Install Geometry:

```bash
    $ composer require spinen/laravel-geometry
```

The package uses the auto registration feature

## Using the package

The Geometry Class exposes parseType methods where "Type" is StudlyCase of the geometry type that geoPHP supports.  Here is a full list...

* parseEwkb($geometry)
* parseEwkt($geometry)
* parseGeoHash($geometry)
* parseGeoJson($geometry)
* parseGeoRss($geometry)
* parseGoogleGeocode($geometry)
* parseGpx($geometry)
* parseJson($geometry)
* parseKml($geometry)
* parseWkb($geometry)
* parseWkt($geometry)

The geometries are wrapped in a `Spinen\Geometry\Geometries` namespace with a little sugar to be able to do

* toEwkb()
* toEwkt()
* toGeoHash()
* toGeoJson()
* toGeoRss()
* toGoogleGeocode()
* toGpx()
* toJson()
* toKml()
* toWkb()
* toWkt()

In addition to the above export methods, we have added a ```toArray``` that gives an array from the toJson method.  For convenience, we have exposed all of the properties of the geometry through a getter, so you have direct access to the property without having ask for the keys in the array.
 
## Area of the polygon
 
We are estimating the area in meters squared & acres.  We expect the estimation to be within 1%, so it is not very accurate.  We essentially refactored a js method that Mapbox has in their [geojson-area package](https://github.com/mapbox/geojson-area/blob/v0.2.1/index.js#L55) .  You get the area by calling the ```getAcres``` or ```getSquareMeters```.  There is a shortcut to them as properties, so you can read the "acres" or "square_meters" property.
