<?php
/**
 * I am what iam
 * Class Descript : REDIS session存储.
 * User: ehtan
 * Date: 2019-10-30
 * Time: 10:39
 */

namespace App\Bean\cache;


use Swoft\Redis\Redis;

class RedisCache implements ICache
{
    /**
     * redis过期时间
     * @var int $_expire
     */
    private $expire = 0;

    public function __construct($expire)
    {
        if(is_numeric($expire) or intval($expire) > 0){
            $this->expire = $expire;
        }
    }

    public function init(){}

    /**
     * @param string $key
     * @param array $val
     * @param int|null $expire
     * @return bool
     */
    public function set(string $key, array $val,int $exp = 0): bool
    {
        $expire = $exp<=0?$this->expire:$exp;
        if($expire <= 0){
            return Redis::set($key,json_encode($val));
        }else{
            return Redis::set($key,json_encode($val),$expire);
        }
    }

    /**
     * @param string $key
     * @return array
     */
    public function get(string $key): array
    {
        $data = Redis::get($key);
        if(empty($data)){
            return [];
        }else{
            return json_decode($data,true);
        }
    }

    /**
     * 暂未实现
     * @return array
     */
    public function getAll(): array {
        return [];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function del(string $key): bool
    {
       return Redis::del($key)?true:false;
    }


}