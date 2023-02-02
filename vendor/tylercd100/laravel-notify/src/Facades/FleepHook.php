<?php

namespace Tylercd100\Notify\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\Filesystem\Filesystem
 */
class FleepHook extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'notify-fleephook';
    }
}