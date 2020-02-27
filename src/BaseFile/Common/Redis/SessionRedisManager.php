<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 1/21/2020
 * Time: 9:04 PM
 */

namespace App\Common\Redis;


use App\Common\RedisHelper;
use Illuminate\Session\SessionManager;

class SessionRedisManager extends SessionManager
{
    /**
     * @return \Illuminate\Session\Store
     */
    protected function createRedisDriver()
    {
        try {
            if(RedisHelper::healthCheck()){
                return parent::createRedisDriver(); // TODO: Change the autogenerated stub
            }else{
                return parent::createDatabaseDriver();
            }
        } catch (\Exception $e) {
            return parent::createDatabaseDriver();
        }
    }

    /**
     * @return \Illuminate\Session\Store
     */
    protected function createDatabaseDriver()
    {
        try {
            if(RedisHelper::healthCheck()){
                return parent::createRedisDriver(); // TODO: Change the autogenerated stub
            }else{
                return parent::createDatabaseDriver();
            }
        } catch (\Exception $e) {
            return parent::createDatabaseDriver();
        }
    }
}
