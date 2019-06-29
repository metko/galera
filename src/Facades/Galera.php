<?php

namespace Metko\Galera\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Metko\Activiko\Skeleton\SkeletonClass
 */
class Galera extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Galera';
    }
}
