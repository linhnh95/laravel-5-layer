<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 1/21/2020
 * Time: 2:24 PM
 */

namespace App\Common\Helpers;

use Illuminate\Support\Facades\Redis;
use Predis\PredisException;

class RedisHelper
{
    /**
     * @return bool
     */
    public static function healthCheck()
    {
        try {
            $ping = Redis::ping();
            if ($ping) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
