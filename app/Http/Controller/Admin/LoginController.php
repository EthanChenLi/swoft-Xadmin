<?php
/**
 * I am what iam
 * Class Descript : 登录控制器 .
 * User: ehtan
 * Date: 2019-10-28
 * Time: 10:16
 */

namespace App\Http\Controller\Admin;

use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Session\HttpSession;

/**
 * Class LoginController
 * @package App\Http\Controller\Admin
 *
 * @Controller(prefix="/admin/login")
 */
class LoginController
{

    /**
     * 登录页
     * @RequestMapping(route="index")
     */
    public function index(){
       return fetchView("login/login");

    }

    /**
     * 登录提交处理
     * @RequestMapping("check")
     */
    public function check(Request $request):Response{
        $param = $request->getPost();
        $adminId = bean("adminLogic")->loginCheck($param['username'],$param['password']);
        if($adminId){
            //登录成功
            HttpSession::current()->set("USERINFO",[
                'admin_id'=>$adminId,
                'admin_username'=>$param['username'],
            ]);

            if(!HttpSession::current()->has("USERINFO"))  return failWithJson("登录异常");

            $this->_writelog($param);

            return successWithJson("登录成功","/admin/index/index");
        }else{
            $this->_writelog($param);
            return failWithJson("账号或密码错误");
        }
    }

    private function _writelog($param){
        \bean("postLogLogic")->createPostLog([
            'username'=>$param['username'],
            "password"=>"******"
        ],["账号或密码错误"]);
    }


}