<?php

namespace ibibicloud\facade;

use think\Facade;

class HttpClient extends Facade
{
    protected static function getFacadeClass()
    {
    	return 'ibibicloud\HttpClient';
    }
}