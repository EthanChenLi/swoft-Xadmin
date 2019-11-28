<?php
/**
 * I am what iam
 * Class Descript :  角色管理
 * User: ehtan
 * Date: 2019-10-24
 * Time: 16:05
 */

namespace App\Http\Controller\Admin\Auth;

use App\Http\Middleware\authCheckMiddleware;

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
 * @Controller(prefix="/admin/auth.group")
 * @Middleware(authCheckMiddleware::class)
 */
class GroupController
{

    use HttpBaseTrait;
    protected $model=AuthGroup::class;

    /**
     * lists渲染
     * @param array $data
     * @return Response
     */
    public function __listsDisplay(array $data):Response{
        $data['list']=list2Tree($data['list']);
        return fetchView("auth/group/lists",$data);
    }



    /**
     * 添加显示前置方法
     * @return Response
     */
    public function __addDisplay():Response{
        //获取权限规则节点
        $ruleList = \bean("AuthRuleLogic")->getFatherLists(["id","pid","title"]);
        $list = list2Tree($ruleList);
        $data = $this->_getLists($list);
        return fetchView("auth/group/info",$data);
    }

    /**
     * 修改前置方法
     * @param array $info
     * @return Response
     */
    public function __editDisplay(array $info):Response{
        //获取该节点下的权限规则
        $ruleList = \bean("AuthRuleLogic")->getFatherLists(["id","pid","title"]);
        $rules = $this->model::find($info['id'],['rules'])->toArray();
        if(!empty($rules['rules'])){
            $filter_rules=explode(",",$rules['rules']);
            $ruleArr = array_filter($filter_rules);
            //获取全部节点
            foreach ($ruleList as $key=>$item){
                if(!in_array($item['id'],$ruleArr)){
                    unset($ruleList[$key]);
                }
            }
            $list = list2Tree($ruleList,"id","pid",'children',0,$filter_rules);
        }else{
            //全部节点 不过滤
            $list = list2Tree($ruleList);
        }
        $data = $this->_getLists($list);
        $data = array_merge(['info'=>$info],$data);
        return fetchView("auth/group/info",$data);
    }






    /**
     * 前置方法获取数组
     * @param array $ruleList 规则权限列表
     * @return array
     */
    private function _getLists(array $ruleList):array{
        //获取分组节点
        $groupList =  list2Tree($this->model::all(["id","pid","name"])->toArray()??[]);
         return [
             'tree'          =>  json_encode($ruleList),
             "group_list"    =>  $groupList
         ];
    }




    /**
     * 添加前置方法
     * @param array $requests
     * @return array
     */
    public function __addBefore(array $requests):array{
        $requests['rules'] =$this->_tree2lists($requests);
        unset($requests['trees']);
        return $requests;
    }

    /**
     * 修改前置方法
     * @param array $requestParams
     * @return array
     */
    public function __editBefore(array $requestParams):array{
        $requestParams['rules'] =$this->_tree2lists($requestParams);
        unset($requestParams['trees']);
        return $requestParams;
    }

    //树形菜单转字符串入库
    private function _tree2lists(array $requests):string {
        $tree="";
        if(!empty($requests['trees'])){
            //重组数组
            foreach ($requests['trees'] as $item){
                $tree.=$item.',';
            }
            $tree= substr($tree,0,-1);
        }
        return $tree;
    }

    /**
     * 获取父级下的权限规则
     * @RequestMapping(route="get_pid")
     */
    public function get_pid(){
       $pid = \context()->getRequest()->get("pid");
       //当前ID 用于勾选当前修改ID选中的菜单
       $this_id = \context()->getRequest()->get("this_id");

        $ruleList = \bean("AuthRuleLogic")->getFatherLists(["id","pid","title"]);
       if(intval($pid) > 0){
           //获取父级以下节点
           $rules = $this->model::find($pid,['rules'])->toArray();
           //过滤不包含父级节点的规则
           if(!empty($rules['rules'])){
               $ruleArr = array_filter(explode(",",$rules['rules']));
               //获取全部节点
               foreach ($ruleList as $key=>$item){
                   if(!in_array($item['id'],$ruleArr)){
                       unset($ruleList[$key]);
                   }
               }

           }
       }

       //选中勾选菜单
        if(!empty($this_id) or $this_id != 0){
            $rules = $this->model::find($this_id,['rules'])->toArray();
            if(!empty($rules['rules'])){
                $filter_rules=explode(",",$rules['rules']);
                $list = list2Tree($ruleList,"id","pid",'children',0,$filter_rules);
            }else{
                $list =list2Tree($ruleList);
            }
        }else{
            $list =list2Tree($ruleList);
        }
       return successWithJsonByApi($list);
    }



    /**
     * 停用用户组
     * @param Request $request
     * @param Response $response
     * @return Response
     *
     * @RequestMapping(route="group_stop")
     */
    public function memberStop(Request $request,Response $response){
        $id =$request->input("id");
        if(empty($id)) return failWithJson("id不能为空");
        return \bean("AuthGroupLogic")->editStatus($id)?
            successWithJson("修改成功"):
            failWithJson("修改失败");
    }


    public function _del(){
        $id = \context()->getRequest()->input("id");
        if(!is_array($id)) $id=[$id];
        $iscount = $this->model::whereIn('pid',$id)->count();

        if($iscount >=1){
            return failWithJson("请先删除下级节点");
        }
        $where[] = ["id",'=',$id];

        $result =$this->model::where($where)->delete();
        if($result)
            return successWithJson("操作成功");
        else
            return failWithJson("操作失败");
    }

}