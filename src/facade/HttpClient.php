<?php

declare(strict_types=1);

namespace ibibicloud\facade;

use think\Facade;

class HttpClient extends Facade
{
    protected static function getFacadeClass(): string
    {
    	return 'ibibicloud\HttpClient';
    }
}