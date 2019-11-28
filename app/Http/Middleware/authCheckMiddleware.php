<?php
/**
 * I am what iam
 * Class Descript : 登录验证中间件 .
 * User: ehtan
 * Date: 2019-10-24
 * Time: 17:50
 */

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Http\Session\HttpSession;

/**
 * Class loginMiddleware
 * @package App\Http\Middleware
 * @Bean()
 */
class authCheckMiddleware implements MiddlewareInterface
{
    private $userinfo;

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Swoft\Exception\SwoftException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //验证登录状态
        if(\bean("authBean")->isLogin()){
            //鉴权
            if(\bean('authBean')->authCheck()){
                return $handler->handle($request);
            }else{
                return fetchView("common/not_auth");
            }
        }else{
            //重定向到登录页面
            return redirect("/admin/login/index");
        }
    }




}