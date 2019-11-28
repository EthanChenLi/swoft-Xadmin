<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-10-28
 * Time: 14:51
 */

namespace App\Model\Logic;


use App\Model\Entity\Admin;
use App\Model\Entity\AuthGroupAccess;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Db\DB;

/**
 * Class AdminLogic
 * @package App\Model\Logic
 * @Bean(name="adminLogic", scope="Bean::PROTOTYPE")
 */
class AdminLogic
{

    const ADMINISTRATOR_NAME = "admin"; //超级管理员账号名称

    public function isSuper(string $name){
        return self::ADMINISTRATOR_NAME == $name?true:false;
    }

    /**
     * 登录验证
     * @param string $username
     * @param string $pwd
     * @return bool|mixed
     * @throws book|\Swoft\Db\Exception\DbException
     */
    public function loginCheck(string $username,string $pwd){
        $info = Admin::where("admin_username",$username)->first();
        if(empty($info)) return false;
        if($info['admin_bs'] == 0) return false;
        if($info['admin_pwd'] == md5($pwd.config("admin.password_sale"))){
            return $info['admin_id'];
        }else{
            return false;
        }
    }

    /**
     * 验证密码
     * @param int $id 主键id
     * @param string $password 密码明文
     * @return bool
     */
    public function checkPwd(int $id, string $password):bool {
        $info = Admin::find($id);
        if(empty($info)) return false;
        if(md5($password.config("admin.password_sale")) == $info->getAdminPwd()){
           return true;
        }else{
            return false;
        }
    }

    /**
     * 修改密码
     * @param int $id
     * @param string $password
     * @return bool
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Db\Exception\DbException
     */
    public function editPwd(int $id,string $password):bool {
        $pwd =md5($password.config("admin.password_sale"));
        $admin  = Admin::find($id);
        $admin->setAdminPwd($pwd);
        $result = $admin->save();
        return $result?true:false;
    }

    /**
     * 批量修改用户状态
     * @param mix $id
     * @return bool
     */
    public function editStatus(array $ids):bool{
        foreach ($ids as $val){
            $admin  = Admin::find($val);
            if($admin->getAdminUsername() == self::ADMINISTRATOR_NAME) return false;
            if($admin->getAdminBs() == 1){
                $admin->setAdminBs(0);
            }else{
                $admin->setAdminBs(1);
            }
            $result = $admin->save();
        }
        return $result?true:false;
    }


    /**
     * 密码加盐
     * @param string $pwd
     * @return string
     */
    public function saleToPwd(string $pwd):string{
        return  md5($pwd.config("admin.password_sale"));
    }

    /**
     * 插入用户-角色组到数据库
     * @param array $adminParams
     * @param int $group_id
     *
     * @return bool
     */
    public function createAdminAccess(array $adminParams,int $group_id):bool{
        //开启事务
        DB::beginTransaction();
        try{
            //插入admin数据
           $admin = Admin::new($adminParams);
           $admin->save();
           $uid = $admin->getAdminId();

           $result =AuthGroupAccess::create([
              'uid'=>$uid,
               'group_id'=>$group_id
           ]);
            Db::commit();
        }catch (\Exception $e){
            $result=false;
            p($e->getMessage());
            DB::rollBack();
        }
       return $result?true:false;
    }

    /**
     * 获取分页
     * @param array $map
     * @param int $page
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     */
    public function getlist(array $map,int $page=1):array{
        return DB::table("admin as ad")
            ->leftJoin("auth_group_access as agr","ad.admin_id","=","agr.uid")
            ->leftJoin("auth_group as ag","ag.id","=","agr.group_id")
            ->forPage($page, config("defaultPageSize"))
            ->get(['ad.*',"ag.name as group_name"])->toArray();
    }

}