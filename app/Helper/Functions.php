<?php
use \Swoft\Http\Message\Response;


define("SUCCESS_CODE",200);
define("FAIL_CODE",400);
define("EXCEPTION_CODE",500);
define("REDIRECT_CODE",302);
define("NOT_FOUND",404);

/**
 * 用json方式响应
 *
 * @return Response
 */
function reponseWithJson(array $param): Response{
    return context()->getResponse()->withData($param)->withHeader("Content-Type","application/json");
}

/**
 * vdump的包装方法
 * @param array $data
 *
 * @return void
 */
function p($data): void{
    vdump($data);
}


/**
 * 请求成功响应- api
 * @param array $data
 * @param string $desctipt
 * @param int $code
 * @return Response
 */
function successWithJsonByApi(array $data=[],string $desctipt="success",int $code=SUCCESS_CODE){
    return reponseWithJson([
        "code"=>$code,
        "data"=>$data,
        "message"=>$desctipt
    ]);
}

/**
 * 请求失败响应-api
 * @param string $desctipt
 * @param array $data
 * @param int $code
 * @return Response
 */
function failWithJsonByApi(string $desctipt="success",array $data=[],int $code=FAIL_CODE){
    return reponseWithJson([
        "code"=>$code,
        "data"=>$data,
        "message"=>$desctipt
    ]);
}


/**
 * 成功返回的格式，包装方法-后台用
 * @param string $descript
 * @param string $url
 * @param array $data
 * @return Response
 */
function successWithJson(string $descript = "success", string $url="", array $data=[]):Response{
    return reponseWithJson([
       "code"=>SUCCESS_CODE,
       "data"=>$data,
       "msg"=>$descript,
       "url"=>$url,
    ]);
}

/**
 * 失败返回json格式 包装方法-后台用
 * @param string $descript
 * @param string $url
 * @param array $data
 *
 * @return Response
 */
function failWithJson(string $descript, string $url="",array $data=[]):Response{
    return reponseWithJson([
        "code"=>FAIL_CODE,
        "data"=>$data,
        "msg"=>$descript,
        "url" =>$url
    ]);
}

/**
 * 302重定向
 * @param string $route
 * @return Response
 * @throws \Swoft\Exception\SwoftException
 */
function redirect(string $route):Response{
    return context()->getResponse()->redirect($route);
    //return context()->getResponse()->withHeader('Location',"{$route}")->withStatus(REDIRECT_CODE);
}

/**
 * 获取template对象
 * @return \think\Template
 */
function template(){
    return \bean("template")->getTemplate();
}


/**
 * 输出渲染模板
 * @param string $path 模板路径
 * @param array $param 模板参数
 * @return Response
 */
function fetchView(string $path,array $param=[]):Response{

    $html =  \bean("template")->getTemplate()->fetch($path,$param);
    return \context()->getResponse()->withContent($html);
}

/**
 * 分页样式
 * @param array $data 当前的查询数组
 * @param int $count 列表总数
 * @param int $pageNum
 * @return string
 */
function pagination(array $data,int $count,int $pageNum):string{
    $page = new \App\Http\Extend\html\Pagination($data,$count,$pageNum,['style' => 1,'simple'=>false,'allCounts'=>true,'nowAllPage'=>true]);
    return $page->render();
}

function errorPage(string $msg):Response{
    return \context()->getResponse()->withContent($msg)->withStatus(EXCEPTION_CODE);
}


function list2Tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0,array $filter_data=[]) {


    $tree = [];
    if (is_array($list)) {
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[ $data[ $pk ] ] = &$list[ $key ];
        }

        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[ $pid ];
            if ($root == $parentId) {
                $tree[ $data[ $pk ] ] = &$list[ $key ];
                $tree[ $data[ $pk ] ]['field'] ="trees[]";

            } else {
                if (isset($refer[$parentId])) {


                    $parent = &$refer[ $parentId ];
                    $parent[ $child ][ $data[ $pk ] ] = &$list[ $key ];
                    $parent[ $child ][ $data[ $pk ] ]['field'] ="trees[]";
                    //判断选中状态
                    if(!empty($filter_data)){
                         in_array($data[ $pk ],$filter_data)?$parent[ $child ][ $data[ $pk ] ]['checked'] =true:null;
                    }
                    $parent[ $child ] = array_values($parent[ $child ]);
                }
            }
        }
    }
    return $tree;
}

/**
 * 补全表名前缀
 * @param string $table
 */
function getTableName(string $table):string{
    return config("databases.prefix").$table;
}


/**
 * 构建拼接uri
 * @param $uri
 */
function getOutputUri(){
    $query= \context()->getRequest()->getUriQuery();
    if(!empty($query)){
        return "output?".$query;
    }else{
        return 'output';
    }
}

/**
 * 创建目录
 * @param $dir string 目录路径
 * @return bool
 */
function Directory( $dir ){
    return  is_dir ( $dir ) or Directory(dirname( $dir )) and  mkdir ( $dir , 0777);
}