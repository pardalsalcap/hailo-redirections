<?php

namespace Pardalsalcap\HailoRedirections\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pardalsalcap\HailoRedirections\HailoRedirections
 */
class HailoRedirections extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Pardalsalcap\HailoRedirections\HailoRedirections::class;
    }
}
