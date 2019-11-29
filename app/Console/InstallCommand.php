<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-11-28
 * Time: 14:47
 */

namespace App\Console;

use PhpParser\Node\Expr\Clone_;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Db\DB;
use Swoft\Log\Helper\CLog;

/**
 * Class InstallCommand
 * @package App\Console
 *
 * @Command(name="install",coroutine=true,desc="initialize admin")
 */
class InstallCommand
{

    /**
     * @CommandMapping(name="exec",alias="e")
     */
    public function exec(){
        output()->writeln("[info] start install...");
        try{
           $result=  Db::unprepared($this->_getSql());
        }catch (\Exception $e){
            CLog::error($e->getMessage());
        }
        output()->writeln("[info] success");
        $port = config('httpServer.port');
        output()->writeln("[info] access: http:localhost:{$port}/admin/index/index");
        output()->writeln("[info] exec: php bin/swoft ws:start ");
        output()->writeln("[info] username: 'admin' ");
        output()->writeln("[info] password: '123456' ");

    }

    /**
     * get sql
     * @return string
     */
    private function _getSql():string{
        $prefix = config("databases.prefix");
        return "CREATE TABLE `{$prefix}admin` (
          `admin_id` int(11) NOT NULL AUTO_INCREMENT,
          `admin_username` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
          `admin_pwd` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `admin_bs` tinyint(1) DEFAULT '1' COMMENT '0 禁用，1启用',
          `updated_at` datetime DEFAULT NULL,
          `admin_nickname` varchar(64) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
          PRIMARY KEY (`admin_id`),
          UNIQUE KEY `admin_username` (`admin_username`(32)) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='管理员账号';
        BEGIN;
        INSERT INTO `{$prefix}admin` VALUES (1, 'admin', '5b0270efac61ac256e12a2d0f5bf7f73', '2019-11-05 11:37:40', 1, '2019-11-11 16:04:19', '');
        COMMIT;
        CREATE TABLE `{$prefix}admin_post_log` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `uri` varchar(128) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
          `client_ip` varchar(128) COLLATE utf8mb4_bin NOT NULL,
          `request_data` text COLLATE utf8mb4_bin NOT NULL,
          `created_at` datetime DEFAULT NULL COMMENT '创建时间',
          `user_id` int(11) NOT NULL,
          `response_data` text COLLATE utf8mb4_bin NOT NULL,
          `status_code` int(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
        CREATE TABLE `{$prefix}auth_group` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
          `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
          `rules` text NOT NULL COMMENT '规则ID',
          `created_at` datetime DEFAULT NULL COMMENT '创建时间',
          `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
          `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
          PRIMARY KEY (`id`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='角色分组表';
        CREATE TABLE `{$prefix}auth_group_access` (
          `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
          `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
          UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
          KEY `uid` (`uid`) USING BTREE,
          KEY `group_id` (`group_id`) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限分组表';
        CREATE TABLE `{$prefix}auth_rule` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `type` enum('menu','file') NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
          `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
          `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
          `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
          `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
          `created_at` datetime DEFAULT NULL COMMENT '创建时间',
          `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
          `weigh` int(1) NOT NULL DEFAULT '0' COMMENT '权重',
          `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
          `route` varchar(128) NOT NULL DEFAULT '',
          PRIMARY KEY (`id`) USING BTREE,
          KEY `pid` (`pid`) USING BTREE,
          KEY `weigh` (`weigh`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='节点表';
        CREATE TABLE `{$prefix}download` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
          `uid` int(11) NOT NULL,
          `path` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '下载地址',
          `created_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='下载链接表';
        SET FOREIGN_KEY_CHECKS = 1;";
    }


}