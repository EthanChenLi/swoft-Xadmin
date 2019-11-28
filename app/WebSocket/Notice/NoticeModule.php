<?php
/**
 * I am what iam
 * Class Descript : 站内消息推送websocket模型.
 * User: ehtan
 * Date: 2019-11-18
 * Time: 14:22
 */

namespace App\WebSocket\Notice;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoole\WebSocket\Server;
use Swoft\WebSocket\Server\Annotation\Mapping\OnClose;
use Swoft\WebSocket\Server\Annotation\Mapping\OnHandshake;
use Swoft\WebSocket\Server\Annotation\Mapping\OnMessage;
use Swoft\WebSocket\Server\Annotation\Mapping\OnOpen;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use Swoole\WebSocket\Frame;

/**
 *
 * @WsModule("/notice")
 */
class NoticeModule
{

    /**
     * 在这里你可以验证握手的请求信息
     * 校验链接合法性
     * @OnHandshake()
     * @param Request $request
     * @param Response $response
     * @return array [bool, $response]
     */
    public function checkHandshake(Request $request, Response $response): array{
         $params =$request->get();
         $uid = $params['uid']??"";
         $rand = $params['rand']??"";
         $hashGet = $params['hash']??"";
         $hash =md5($uid.$rand.config("wsServer.auth_hash"));
         if($hashGet != $hash){
             //链接不合法
             return [false, $response];
         }else{
             return [true, $response];
         }
    }

    /**
     * 建立连接
     * @OnOpen()
     */
    public function OnOpen(Request $request,int $fd){
        server()->push($fd,json_encode([
            'event'=>"connect",
            "msg"=>"connect successful！"
        ]));
        //用户ID跟FD绑定
        $uid = context()->getRequest()->get("uid");
        bean('ws')->bindUid($uid,$fd);
    }


    /**
     * 消息处理
     * @OnMessage()
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server,Frame $frame){}

    /**
     * On connection closed
     * - you can do something. eg. record log
     *
     * @OnClose()
     * @param Server $server
     * @param int    $fd
     */
    public function onClose(Server $server, int $fd):void{}



}