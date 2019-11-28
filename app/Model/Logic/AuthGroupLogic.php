<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-11-13
 * Time: 14:45
 */

namespace App\Model\Logic;

use App\Model\Entity\AuthGroup;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Db\DB;

/**
 * Class AuthRuleLogic
 * @package App\Model\Logic
 *
 * @Bean(name="AuthGroupLogic",scope="Bean::PROTOTYPE")
 */
class AuthGroupLogic
{

    /**
     * 批量修改用户状态
     * @param int $id
     * @return bool
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Db\Exception\DbException
     */
    public function editStatus(int $id):bool{
        $group = AuthGroup::find($id);
        if($group->getStatus() == 1){
            $group->setStatus(0);
        }else{
            $group->setStatus(1);
        }
        $result=$group->save();
        return $result?true:false;
    }

    /**
     * 获取用户所属组的rule节点
     * @param int $user_id
     * @return array
     */
    public function getUserRulesByUserid(int $user_id):array{
       $info= DB::table("auth_group_access")
           ->leftJoin("auth_group","auth_group_access.group_id","=","auth_group.id")
           ->where('auth_group_access.uid',"=",$user_id)
           ->select("rules")
           ->first();
        if(!empty($info['rules'])){
            return array_filter(explode(",",$info['rules']));
        }
       return [];
    }


    /**
     * 根据用户id获取权限列表
     * @param int $user_id
     */
    public function getRulesRouteByUid(int $user_id){
        $ruleId = $this->getUserRulesByUserid($user_id);
        $list = DB::table('auth_rule')->whereIn('id', $ruleId)->get(['route']);
        if(empty($list)){
            return [];
        }else{
            $newlists =[];
            foreach ($list as $item) {
                $newlists[]=strtolower($item['route']);
            }
            return $newlists;
        }

    }

}