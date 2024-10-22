<?php

namespace App\Util;

class ListUtil
{

    public static function splitListByLimit(array $list, int $limit)
    {
        return array_chunk($list, $limit);
    }
}
