<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-11-13
 * Time: 14:45
 */

namespace App\Model\Logic;

use App\Model\Entity\AuthRule;
use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class AuthRuleLogic
 * @package App\Model\Logic
 *
 * @Bean(name="AuthRuleLogic",scope="Bean::PROTOTYPE")
 */
class AuthRuleLogic
{

    /**
     * 获取所有规则父级列表
     * @param array $field
     * @param array $map
     *
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     */
    public function getFatherLists(array $field=["id","type","pid","title","status","icon","created_at"],array $map=[]):array{
        if(empty($map)){
            return AuthRule::all($field)->toArray();
        }else{
            return AuthRule::where($map)->get($field)->toArray();
        }

    }

    /**
     * 批量修改用户状态
     * @param mix $id
     * @return bool
     */
    public function editStatus(array $ids):bool{
        foreach ($ids as $val){
            $auth  = AuthRule::find($val);
            if($auth->getStatus() == 1){
                $auth->setStatus(0);
            }else{
                $auth->setStatus(1);
            }
            $result = $auth->save();
        }
        return $result?true:false;
    }



}