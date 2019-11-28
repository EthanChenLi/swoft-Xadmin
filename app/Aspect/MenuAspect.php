<?php
/**
 * I am what iam
 * Class Descript : 对rule操作后删除缓存.
 * User: ehtan
 * Date: 2019-11-28
 * Time: 11:50
 */

namespace App\Aspect;

use App\Http\Controller\Admin\Auth\RuleController;
use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\PointBean;
use Swoft\Redis\Redis;

/**
 * Class MenuAspect
 * @package App\Aspect
 *
 * @Aspect(order=1)
 * @PointBean(include={
 *   "App\Http\Controller\Admin\Auth\RuleController",
 *     })
 */
class MenuAspect
{

    /**
     * @After()
     */
    public function after(){
        //清楚缓存
        $this->_clearCache();
    }

    /**
     * 清除缓存
     */
    private function _clearCache(){
        Redis::del(config("cache_keys.CACHE_MENU_LIST"));
    }
}