<?php
/**
 * Created by PhpStorm.
 * User: Leonardo
 * Date: 08/04/2016
 * Time: 03:17 PM
 */

namespace Kolovious\MeliSocialite\Facade;

use Illuminate\Support\Facades\Facade;


class Meli extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Kolovious\MeliSocialite\MeliManager';
    }
}