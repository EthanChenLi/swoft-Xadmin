<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-11-06
 * Time: 16:45
 */

namespace App\Aspect;


use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\PointBean;

/**
 * 日志aop
 * Class LogAspect
 * @package App\Aspect
 *
 * @Aspect(order=1)
 *
 * @PointBean(include={
 *     "App\Http\Controller\Admin\IndexController",
 *     "App\Http\Controller\Admin\AdminController"
 *     })
 *
 */
class LogAspect
{

    /**
     * @After()
     */
    public function after(){

        if(strtolower(\context()->getRequest()->getMethod()) == 'post'){
            \bean("postLogLogic")->createPostLog();
        }
    }

}