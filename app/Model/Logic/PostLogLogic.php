<?php
/**
 * I am what iam
 * Class Descript : 日志逻辑.
 * User: ehtan
 * Date: 2019-11-06
 * Time: 16:11
 */

namespace App\Model\Logic;

use App\Model\Entity\AdminPostLog;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Session\HttpSession;

/**
 * Class PostLogLogic
 * @package App\Model\Logic
 * @Bean(name="postLogLogic", scope="Bean::PROTOTYPE")
 */
class PostLogLogic
{

    /**
     * 写入日志记录
     * @param array $request_data
     * @param string $id
     * @param string $uri
     */
    public function createPostLog(array $request_data=[],array $response_data=[]){
        //after
        $request  = \context()->getRequest();
        $response = \context()->getResponse();

        AdminPostLog::create([
            "uri"=> $request->getUri()->getPath(),
            "client_ip" =>$request->getServerParams()['remote_addr'],
            "request_data"=> json_encode(empty($request_data)?$request->getParsedBody():$request_data),
            "user_id"=>HttpSession::current()->get("USERINFO")['admin_id']??"",
            "response_data" =>json_encode(empty($response_data)?$response->getData():$response_data),
            "status_code" =>$response->getStatusCode()??""
        ]);
    }





}