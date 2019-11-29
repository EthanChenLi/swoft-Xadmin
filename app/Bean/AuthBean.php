<?php
/**
 * I am what iam
 * Class Descript : 鉴权bean .
 * User: ehtan
 * Date: 2019-11-26
 * Time: 15:23
 */

namespace App\Bean;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Http\Session\HttpSession;
use Swoft\Redis\Pool;

/**
 * Class AuthBean
 * @package App\Bean
 *
 * @Bean(name="authBean",scope="Bean::PROTOTYPE")
 */
class AuthBean
{
    /**
     * @Inject()
     * @var Pool
     */
    private $redis;

    /**
     * 无须鉴权列表
     * @var array
     */
    private $NOT_AUTH_URI=[
      "/admin/index/index",
      '/admin/index/welcome'
    ];

    /**
     * 鉴权
     */
    public function authCheck():bool{
        $userinfo = HttpSession::current()->get("USERINFO");
        if(\bean('adminLogic')->isSuper($userinfo['admin_username']))return true;
        //当前访问的URI
        $uri = \context()->getRequest()->getUri()->getPath();

        //获取rules的路由地址
        $menuList = $this->redis->get(config("cache_keys.CACHE_MENU_LIST"));
        if(empty($menuList)){
            $menuList = \bean("AuthGroupLogic")->getRulesRouteByUid($userinfo['admin_id']);
            $this->redis->set(config("cache_keys.CACHE_MENU_LIST"),$menuList);
        }
        if(in_array(strtolower($uri),array_merge($this->NOT_AUTH_URI,$menuList))){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 查看登录状态
     * @return bool
     */
    public function isLogin():bool{
        return !empty(HttpSession::current()->get("USERINFO")['admin_id'])?true:false;
    }

}