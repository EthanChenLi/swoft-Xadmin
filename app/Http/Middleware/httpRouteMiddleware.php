<?php
/**
 * I am what iam
 * Class Descript : http请求全局中间件，解决url访问路径问题 、session生成.
 * User: ehtan
 * Date: 2019-10-24
 * Time: 17:16
 */

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\Cookie;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Http\Session\Handler\RedisHandler;
use Swoft\Http\Session\HttpSession;

/**
 * Class httpRouteMiddleware
 * @package App\Http\Middleware
 * @Bean()
 */
class httpRouteMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Swoft\Exception\SwoftException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //初始话sessionid
        HttpSession::current()->set("SWOFT_SESSION_ID","SWOFT_SESSION_ID");
        //拦截favicon
        $path = $request->getUri()->getPath();
        if ($path === '/favicon.ico') {
            return context()->getResponse()->withStatus(404);
        }
        $response = $handler->handle($request);
        return $response;
    }

}