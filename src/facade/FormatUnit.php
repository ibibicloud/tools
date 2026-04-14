<?php

declare(strict_types=1);

namespace ibibicloud\facade;

use think\Facade;

class FormatUnit extends Facade
{
    protected static function getFacadeClass(): string
    {
    	return 'ibibicloud\FormatUnit';
    }
}