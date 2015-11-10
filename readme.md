# SPINEN's Laravel Geometry

[![Latest Stable Version](https://poser.pugx.org/spinen/laravel-geometry/v/stable)](https://packagist.org/packages/spinen/laravel-geometry)
[![Total Downloads](https://poser.pugx.org/spinen/laravel-geometry/downloads)](https://packagist.org/packages/spinen/laravel-geometry)
[![Latest Unstable Version](https://poser.pugx.org/spinen/laravel-geometry/v/unstable)](https://packagist.org/packages/spinen/laravel-geometry)
[![Dependency Status](https://www.versioneye.com/php/spinen:laravel-geometry/0.1.1/badge.svg)](https://www.versioneye.com/php/spinen:laravel-geometry/0.1.1)
[![License](https://poser.pugx.org/spinen/laravel-geometry/license)](https://packagist.org/packages/spinen/laravel-geometry)

Wrapper over the geoPHP Class to make it integrate with Laravel better.

## Build Status

| Branch | Status | Coverage | Code Quality |
| ------ | :----: | :------: | :----------: |
| Develop | [![Build Status](https://travis-ci.org/spinen/laravel-geometry.svg?branch=develop)](https://travis-ci.org/spinen/laravel-geometry) | [![Coverage Status](https://coveralls.io/repos/spinen/laravel-geometry/badge.svg?branch=develop&service=github)](https://coveralls.io/github/spinen/laravel-geometry?branch=develop) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spinen/laravel-geometry/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/spinen/laravel-geometry/?branch=develop) |
| Master | [![Build Status](https://travis-ci.org/spinen/laravel-geometry.svg?branch=master)](https://travis-ci.org/spinen/laravel-geometry) | [![Coverage Status](https://coveralls.io/repos/spinen/laravel-geometry/badge.svg?branch=master&service=github)](https://coveralls.io/github/spinen/laravel-geometry?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spinen/laravel-geometry/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spinen/laravel-geometry/?branch=master) |

## Prerequisite

* [phayes/geophp](https://github.com/phayes/geoPHP)

## Install

Install Garbage Man:

```bash
    $ composer require spinen/laravel-geometry
```

Add the Service Provider to `config/app.php`:

```php
    'providers' => [
        // ...
        Spinen\Geometry\GeometryServiceProvider::class,
    ];
```
