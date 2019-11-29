<?php
/**
 * I am what iam
 * Class Descript : http逻辑请求处理类 .
 * User: ehtan
 * Date: 2019-10-30
 * Time: 14:11
 */

namespace App\Http\Traits;


use App\Model\Entity\Admin;
use phpDocumentor\Reflection\Types\Context;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Swoft\Bean\BeanFactory;
use Swoft\Db\DB;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Session\HttpSession;
use Swoft\Task\Task;

/**
 * http空控制器处理
 * Trait HttpBaseTrait
 * @package App\Http\Traits
 */
trait HttpBaseTrait
{

    private $className;

    /**
     *
     * 构建一个逻辑模型
     *  lists
     *  add
     *  edit
     *  del
     *  ourput
     *
     * @param array $className 类名称
     * @param string $actionType action名称
     * @return Response|null
     * @throws \Swoft\Exception\SwoftException
     */
    public function buildLogicModel(array $className ,string $actionType):?Response{
        //1、登录验证
        $authBean =bean('authBean');
        if(!$authBean->isLogin()){
            return redirect("/admin/login/index");
        }
        //2、 auth鉴权
        if(!$authBean->authCheck()){
            if(\context()->getRequest()->isPost() or  \context()->getRequest()->isAjax()){
              return failWithJsonByApi("对不起，您无权访问");
            }else{
                return $this->_fetchView('common/not_auth');
            }
        }

        $this->className = $className;
        switch ($actionType){
            case "lists":
                //列表
                return $this->_lists();
                break;
            case "add":
            case "edit":
                //添加修改
                return $this->_update($actionType);
                break;
            case "del":
                //删除
                return $this->_delete();
                break;
            case 'output':
                //导出到excel
                return $this->_output();
                break;
        }

    }




    /**
     * 获取list分页页面
     * @return Response|null
     * @throws \Exception
     */
    private function _lists():?Response{
        if(empty($this->model)) throw new \Exception("找不到模型对象");
        $params = \context()->getRequest()->input();
        $map =[];
        $keywords =[];
        $pageItemNum=config("defaultPageSize");//每页分页数
        if(!empty($params['params'])){
            $keywords = $params['params'];
            if(!empty($params['params'])){
                //模糊查询
                //like模糊查询-过滤
                $map = $this->_filterMap($params);
            }
        }
        //重写方案，自行处理查询条件
        if(method_exists($this,"__listsKeywords")){
            /**
             * @return array 符合swoft ORM的查询条件
             */
            $map = $this->__listsKeywords($params??[],$map);
            if(!is_array($map))throw new \Exception("错误的返回格式(__listsKeywords)");
        }
        $data=[];
        //是否重写__listsData类,接管查询条件，自己查询内容返回
        if(method_exists($this,"__listsData")){
            /**
             * @return array 查询结果数组
             */
           $data =$this->__listsData($map,$params??[]);
        }else{
           $data = $this->model::where($map)->latest()->forPage(intval($params['page']??1), $pageItemNum)->get()->toArray();
        }
        //获取分页渲染模板
        $count =0;
        if(!empty($data))
            $count = $this->model::where($map)->count();
        $pageItemHtml = pagination($data,$count,$pageItemNum);
        //输出的结果
        $buildDate = [
            "list" =>$data,
            "keywords" =>$keywords,
            "page"=>$pageItemHtml
        ];
        //重写模板渲染，接受查询结果跟分页结果
        if(method_exists($this,"__listsDisplay")) {
            /**
             * @return Response
             */
            return $this->__listsDisplay($buildDate);
        }else{
            return $this->_fetchView("lists",$buildDate);
        }

    }

    /**
     * 添加/修改页面
     *
     *
     * URL:
     *   -添加页面url ：DOMAIN/admin/{controller}/add
     *   - 修改页面url:DOMAIN/admin/{controller}/edit?id={indexId}
     *
     * @param string $actionType
     * @return Response|null
     * @throws \Swoft\Exception\SwoftException
     */
    private function _update(string $actionType):?Response{
        $request= \context()->getRequest();
        //只能用ajax提交
       if($request->isAjax() or $request->isPost()){
           //submit提交
           $requestParams = $request->post();
           //获取实体对象
           $entityName = $this->model;
           $entityObject = new $entityName();
               //新增\修改前置操作  __addBefore  \ __editBefore
               $funcNameByBefore="__{$actionType}Before";
               if(method_exists($this,$funcNameByBefore)) {
                   /** @var array $requestParams */
                   $requestParams = $this->$funcNameByBefore($requestParams);
               }
            if(!empty($requestParams[$entityObject->getKeyName()])){
                //对写入方法进行接管
                if(method_exists($this,"__editUpdateBefore")) {
                    /** @var array $requestParams  @return bool*/
                    $result = $this->__editUpdateBefore($requestParams);
                }else{
                    //更新
                    $editEntityObject =  $this->model::find($requestParams[$entityObject->getKeyName()]);
                    $result=  $editEntityObject->update($requestParams);
                }
            }else{
                //新增
                if(method_exists($this,"__addUpdateBefore")) {
                    /** @var array $requestParams  @return bool*/
                    $result = $this->__addUpdateBefore($requestParams);
                }else{
                    $addEntityObject =$entityObject->new($requestParams);
                    $result = $addEntityObject->save();
                }
            }
               $funcNameByAfter = "__{$actionType}After";
                //新增\修改后置操作  __addAfter  \ __editAfter
               if(method_exists($this,$funcNameByAfter)) {
                   /** @return Response  */
                   return $this->$funcNameByAfter($result);
               }
               if($result){
                   $urlPath  =$this->_class2StringbyApi("lists");

                   return successWithJson("操作成功","{$urlPath}");
               }else{
                   return failWithJson("操作失败");
               }
       }else{
           //display页面展示
            if(!empty($request->get("id")) and $actionType == 'edit'){
                //修改页面
                $indexId = $request->get("id");
                $info = $this->model::find($indexId); //查询id数据
                $info = empty($info)?[]:$info->toArray();
                if(method_exists($this,"__editDisplay")) {
                    /**
                     * @return Response
                     */
                    return $this->__editDisplay($info);
                }else{
                    return $this->_fetchView("info",['info'=>$info]);
                }
            }else{
                //新增页面
                if(method_exists($this,"__addDisplay")) {
                    /**
                     * @return Response
                     */
                    return $this->__addDisplay();
                }else{
                    return $this->_fetchView("info");
                }
            }
       }
    }

    /**
     * 快捷删除
     * url:DOMAIN/admin/{controller}/del/{id}
     *
     * @return Response
     * @throws \Swoft\Exception\SwoftException
     */
    private function _delete():?Response{
        $entityName = $this->model;
        $entityObject = new $entityName();
        $id = \context()->getRequest()->input("id");
        $map[] = ["{$entityObject->getKeyName()}",'=',$id];
        //删除前置操作
        if(method_exists($this,"__delBefore")) {
            /** @return array $map 符合标准ORM条件的数组 $map */
            $map =  $this->__delBefore($map,$id);
        }
        $result = $entityObject::where($map)->delete();
        if(method_exists($this,"__delAfter")){
            /** @return Response */
            return  $this->__delAfter($result);
        }
        return $result?successWithJson("删除成功"):failWithJson("删除失败");
    }


    /**
     * 导出excel任务
     * @return Response
     */
    private function _output():Response{
        $params = \context()->getRequest()->input();
        $map = empty($params)?[]:$this->_filterMap($params);
        if(!property_exists($this,"output"))
            return failWithJson("没有找到导出字段映射属性");
        if(count($this->output) <=0 )
            return failWithJson("output属性定义字段不能为空");
        //是否重写__listsData类,接管查询条件，自己查询内容返回
        if(method_exists($this,"__listsData")){
            $data =$this->__listsData($map,$params??[]);
        }else{
            $data = $this->model::where($map)
                ->latest()
                ->forPage(intval($params['page']??1), config("defaultPageSize")??12)
                ->get()->toArray();
        }
        try{
            $result =Task::async("ExcelOutpub","process",[
                $data,
                $this->output,
                HttpSession::current()->get("USERINFO")['admin_id']??0
            ]);

        }catch (\Exception $e){
            return failWithJson("导出异常");
        }
        return $result?successWithJson("导出任务投递成功,请等待下载"):failWithJson("导出失败");
    }


    /**
     * 路由输出函数
     * @param string $path
     * @param array $data
     * @return Response
     */
    private function _fetchView(string $path,array $data=[]):Response{
        $viewPath  =$this->_class2String($path);
        return fetchView($viewPath,$data??[]);
    }

    /**
     * 返回模板的路径地址
     * @param string $path
     * @return string
     */
    private function _class2String(string $path):string{
        $viewPath ="{$this->className[0]}/";
        if(!empty($this->className[1]))
            $viewPath .= "{$this->className[1]}/";
        $viewPath .="{$path}";
        return $viewPath;
    }

    /**
     * 返回api的uri地址
     * @param string $path
     * @return string
     */
    private function _class2StringbyApi(string $path):string{
        $viewPath ="/admin/{$this->className[0]}";
        if(!empty($this->className[1]))
            $viewPath .= ".{$this->className[1]}";
        $viewPath .="/{$path}";
        return $viewPath;
    }


    /**
     * 将array转成orm条件格式
     * @param array $parasm
     * @return array
     */
    private function _filterMap(array $params):array{
        $map=[];
        if(!empty($params['params']['like'])){
            if(!empty($params['params']['like']['value']) and !empty($params['params']['like']['fields'])){
                $fieldsArr = explode("|",$params['params']['like']['fields']);
                foreach ($fieldsArr as $item){
                    $map[] = ["{$item}","like","%{$params['params']['like']['value']}%",'or'];
                }
            }
        }
        unset($params['params']['like']);
        foreach ($params['params'] as $key=>$val){
            $map[] = ["{$key}","=",trim($val),'or'];
        }
        return $map;
    }

}