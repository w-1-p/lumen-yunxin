<?php

namespace W1p\LumenYunxin;

use Illuminate\Support\Facades\Facade;

class YunXin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'YunXin';
    }
}
