<?php
/**
 * I am what iam
 * Class Descript :  后台首页.
 * User: ehtan
 * Date: 2019-10-24
 * Time: 16:05
 */

namespace App\Http\Controller\Admin;

use App\Http\Middleware\authCheckMiddleware;
use App\Model\Entity\AuthRule;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\Middlewares;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Http\Session\HttpSession;
use Swoft\Redis\Redis;


/**
 * Class IndexController
 * @package App\Http\Controller\Admin
 *
 * @Controller(prefix="/admin/index")
 * @Middlewares({
 *    @Middleware(authCheckMiddleware::class),
 * })
 *
 */
class IndexController
{


    /**
     * 后台首页
     * @RequestMapping(route="index",method={RequestMethod::GET})
     */
    public function index(Request $request){
        //创建ws hash
        $userinfo =HttpSession::current()->get("USERINFO");
        $time = time();
        $wsParams = ['uid'=>$userinfo['admin_id'], "rand"=>$time,"hash"=>md5($userinfo['admin_id'].$time.config("wsServer.auth_hash"))];


        $ws_url="ws://".$request->getUri()->getHost().":".$request->getUri()->getPort().config("wsServer.uri.notice")."/?".http_build_query($wsParams);

        //获取菜单list
        $menuList=  (\bean("AuthRuleLogic")->getFatherLists(
            ["id","type","pid","title","icon","route"],
            [
                ['status',"=",'1']
            ])); //获取一级菜单

        if(!bean("adminLogic")->isSuper($userinfo['admin_username'])){
            //过滤用户权限菜单
            $ruleArr =\bean('AuthGroupLogic')->getUserRulesByUserid($userinfo['admin_id']);
            foreach ($menuList as $key=>$item){
                if(!in_array($item['id'],$ruleArr)){
                    unset($menuList[$key]);
                }
            }
        }

        return fetchView("index/index",[
            "admin_username"=>$userinfo['admin_username'],
            "ws_url"=>$ws_url,
            'menu'=>list2Tree($menuList)
        ]);

    }

    /**
     * 欢迎页
     * @RequestMapping(route="welcome",method={RequestMethod::GET})
     */
    public function welcome(){
        return fetchView("index/welcome",[
            'userinfo' => HttpSession::current()->get('USERINFO')
        ]);
    }

    /**
     * 登出
     * @RequestMapping(route="logout")
     */
    public function logout():Response{
        HttpSession::current()->delete("USERINFO");
        return redirect("/admin/login/index");
    }



}