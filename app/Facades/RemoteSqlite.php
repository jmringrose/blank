<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void use()
 * @method static void useLocal()
 * @method static string current()
 * @method static bool isRemote()
 *
 * @see \App\Providers\AppServiceProvider
 */
class RemoteSqlite extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'db.sqlite.remote';
    }
}
