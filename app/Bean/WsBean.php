<?php
/**
 * I am what iam
 * Class Descript : websocket一些操作.
 * User: ehtan
 * Date: 2019-11-22
 * Time: 14:43
 */

namespace App\Bean;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Redis\Pool;

/**
 * Class WsBean
 * @package App\Bean
 *
 * @Bean(name="ws")
 */
class WsBean
{


    const WEBSOCKET_REDIS_KEY = "WEBSOCKET_UID_FD";

    /**
     * @Inject()
     * @var Pool
     */
    private $redis;

    /**
     * FD和UID绑定
     * @param int $user_id
     * @param int $fd
     */
    public function bindUid(int $user_id,int $fd){
        $this->redis->hSet(WsBean::WEBSOCKET_REDIS_KEY,(string)$user_id,(string)$fd);
    }

    /**
     * 通过uid获取fd
     * @param int $user_id
     * @return int
     */
    public function getFdByUid(int $user_id):int{
        return $this->redis->hGet(WsBean::WEBSOCKET_REDIS_KEY,(string)$user_id)??0;
    }


}