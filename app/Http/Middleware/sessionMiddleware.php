<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-11-28
 * Time: 10:35
 */

namespace App\Http\Middleware;




use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\Cookie;

/**
 * Class sessionMiddleware
 * @package App\Http\Middleware
 * @Bean()
 */
class sessionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //生成sessionid
        $response = $handler->handle($request);
        $info =$request->getCookieParams();
        if(empty($info['SESSIONID'])){
            //设置一个新的sessionid
            $hash = md5(time().rand(1111,99999999));
            $cookie = new Cookie();
            $cookie->setName("SESSIONID");
            $cookie->setValue((string)$hash);
            $cookie->setPath("/");
            $response->setCookie("SESSIONID",$cookie);
        }
        return $response;
    }


}