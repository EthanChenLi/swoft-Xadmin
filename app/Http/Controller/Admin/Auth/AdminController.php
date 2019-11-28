<?php
/**
 * I am what iam
 * Class Descript :  管理员管理.
 * User: ehtan
 * Date: 2019-10-24
 * Time: 16:05
 */

namespace App\Http\Controller\Admin\Auth;

use App\Http\Middleware\authCheckMiddleware;
use App\Http\Middleware\testMiddleware;
use App\Http\Traits\HttpBaseTrait;
use App\Model\Entity\Admin;
use App\Model\Entity\AuthGroup;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\Middlewares;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;


/**
 * Class IndexController
 * @package App\Http\Controller\Admin\Auth
 *
 * @Controller(prefix="/admin/auth.admin")
 * @Middleware(authCheckMiddleware::class)
 *
 */
class AdminController
{

    use HttpBaseTrait;
    protected $model=Admin::class;


    /**
     * 导出到excel设置
     * @var array
     */
    protected $output=[
      ["admin_id","id"],
      ["admin_username","用户名"],
      ["created_at","创建时间"],
      ["admin_bs","状态",['启用','禁用']],
      ["admin_nickname","昵称"],
      ["group_name","所属组"],
    ];

    /**
     * @RequestMapping("test")
     */
    public function test(){

        return 'ok';
    }

    /**
     * list重写数据处理方法
     * @param array $map
     * @param array $params
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     */
    public function __listsData(array $map,array $params=[]):array{
        //join查询
        return \bean("adminLogic")->getlist($map);
    }


    /**
     * 修改密码
     * @RequestMapping(route="edit_pwd")
     */
    public function editPwd(Request $request,Response $response){
        $param = $request->input();
        if(empty($param['id'])) return errorPage("无效的id");

        if($request->isAjax() or $request->isPost()){
            if(!\bean("adminLogic")->checkPwd($param['id'],$param['old_pwd']))
                return failWithJson("原密码错误");
          //修改密码
            $result  = \bean("adminLogic")->editPwd($param['id'],$param['pass']);
            return $result?successWithJson("修改成功"):failWithJson("修改失败");
        }else{
            return fetchView("auth/admin/edit_pwd");
        }
    }


    /**
     * 停用账号
     * @param Request $request
     * @param Response $response
     * @return Response
     *
     * @RequestMapping(route="member_stop")
     */
    public function memberStop(Request $request,Response $response){
        $id =$request->input("id");
        if(empty($id)) return failWithJson("id不能为空");
        if(!is_array($id))$id = [$id];
        return \bean("adminLogic")->editStatus($id)?successWithJson("修改成功"):failWithJson("修改失败");
    }


    /**
     * 添加前置方法
     * @param array $request
     * @return array
     */
    public function __addBefore(array $request):array{
        $request['admin_pwd'] = \bean("adminLogic")->saleToPwd($request['admin_pwd']);
        return $request;
    }


    /**
     * 添加数据处理方法
     * @param array $request
     * @return bool
     */
    public function __addUpdateBefore(array $request):bool{
        return \bean("adminLogic")->createAdminAccess($request,intval($request['group_id']));
    }


    /**
     * 添加显示前置方法
     * @return Response
     * @throws \Swoft\Db\Exception\DbException
     */
    public function __addDisplay():Response{
        //获取角色组列表
        $groupList = list2Tree(AuthGroup::all(["id","pid","name"])->toArray()) ;
        return fetchView("auth/admin/info",[
            "group_list" =>$groupList
        ]);
    }




}