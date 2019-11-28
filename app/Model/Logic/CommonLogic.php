<?php
/**
 * I am what iam
 * Class Descript :全局通用模型逻辑实现类 .
 * User: ehtan
 * Date: 2019-11-05
 * Time: 10:14
 */

namespace App\Model\Logic;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Db\DB;

/**
 * Class CommonLogic
 * @package App\Model\Logic
 * @Bean(name="Commonlogic", scope="Bean::PROTOTYPE")
 */
class CommonLogic
{

    /**
     * 分页排序
     * @param string $entityName
     * @param array $fields
     * @param array $map
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public function getPageList(string $entityName,array $fields=['*'], array $map=[],int $page=1,string $order=""){
       $obj = $entityName::where($map)->forPage($page, config("defaultPageSize"));
        if($order != ""){
            $list = $obj->orderBy("{$order}","desc");
        }else{
            $list = $obj->latest();
        }
        return $list->get($fields);
    }


}