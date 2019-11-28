<?php
/**
 * I am what iam
 * Class Descript :  菜单规则
 * User: ehtan
 * Date: 2019-10-24
 * Time: 16:05
 */

namespace App\Http\Controller\Admin\Auth;

use App\Http\Middleware\authCheckMiddleware;

use App\Http\Traits\HttpBaseTrait;
use App\Model\Entity\Admin;
use App\Model\Entity\AuthRule;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Redis\Redis;


/**
 * Class IndexController
 * @package App\Http\Controller\Admin\Auth
 *
 * @Controller(prefix="/admin/auth.rule")
 * @Middleware(authCheckMiddleware::class)
 */
class RuleController
{
    use HttpBaseTrait;
    protected $model=AuthRule::class;

    /**
     * 添加规则前置方法
     * @return Response
     * @throws \Swoft\Db\Exception\DbException
     */
    public function __addDisplay():Response{
       return $this->_responseDisplay();
    }

    /**
     * 修改规则前置方法
     * @return Response
     * @throws \Swoft\Db\Exception\DbException
     */
    public function __editDisplay($info):Response{
        return $this->_responseDisplay(['info'=>$info]);
    }

    /**
     * 返回前置模板
     * @param array $data
     * @return Response
     * @throws \Swoft\Db\Exception\DbException
     */
    private function _responseDisplay(array $data=[]):Response{
        $data = array_merge($data,[
            "glist"=> list2Tree(\bean("AuthRuleLogic")->getFatherLists()), //获取一级菜单
        ]);
        return fetchView("auth/rule/info",$data);
    }

    /**
     * @RequestMapping("del")
     * 重写删除方法
     * @return array|mixed|string|Response
     */
    public function del(){
        $id = \context()->getRequest()->input("id");
        if(!is_array($id)) $id=[$id];
        $iscount = $this->model::whereIn('pid',$id)->count();

        if($iscount >=1){
            return failWithJson("请先删除下级节点");
        }
        $where[] = ["id",'=',$id];
        $result =$this->model::where($where)->delete();
        if($result){
            return successWithJson("操作成功");
        }else{
            return failWithJson("操作失败");
        }

    }




    /**
     * 重组lists数据
     * @param array $map
     * @param array $params
     * @return mixed
     */
    public function __listsData(array $map,array $params){
        $list =\bean("Commonlogic")->getPageList(
            $this->model,
            ["id","type","pid","title","status","icon","created_at","route","weigh"],
            $map,intval($params['page']??1),
            "weigh"
        )->toArray();
        $list =list2Tree($list);
        return $list;
    }


    /**
     * @RequestMapping(route="icon")
     */
    public function icon(){
        return fetchView('auth/rule/icon');
    }



    /**
     * 停用规则
     * @param Request $request
     * @param Response $response
     * @return Response
     *
     * @RequestMapping(route="rule_stop")
     */
    public function memberStop(Request $request,Response $response){
        $id =$request->input("id");
        if(empty($id)) return failWithJson("id不能为空");
        if(!is_array($id))$id = [$id];
         if(bean("AuthRuleLogic")->editStatus($id)){
             return successWithJson("修改成功");
         }else{
             return failWithJson("修改失败");
         }
    }





}