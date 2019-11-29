<?php
/**
 * I am what iam
 * Class Descript : 基于文件存储的cache. 文件目录请自行清理
 * User: ehtan
 * Date: 2019-10-29
 * Time: 17:52
 */

namespace App\Bean\cache;


use Swoft\Log\Helper\CLog;

class FileCache implements ICache
{

    /**
     * 文件存储路径
     * @var $_dir
     */
    private $_dir;

    public function __construct($dir)
    {
        $this->_dir = $dir;
    }

    /**
     * 初始化
     */
    public function init(){}

    /**
     * 设置存储文件
     * @param string $key
     * @param array $val
     * @param int $expire 过期时间
     * @return bool
     */
    public function set(string $key, array $val,int $expire=0): bool
    {
        if($expire >= 0){
            //TODO
            $val['expire_time_out']= time()+$expire;
        }
        $path = ROOT_PATH.$this->_dir;
        if(!file_exists($path)){
            Directory($path);
            chmod($path,0777);
        }
        $fileName =$path."/".$key;
        try{
            $result = file_put_contents($fileName,serialize($val));
        }catch (\Exception $e){
            CLog::error($e->getMessage());
        }

        return $result?true:false;
    }

    public function get(string $key): array
    {
        $fileName =ROOT_PATH.$this->_dir."/".$key;
        if(file_exists($fileName)){
            try{
                $data = file_get_contents($fileName);
            }catch (\Exception $e){
                CLog::error($e->getMessage());
            }
            return unserialize($data);
        }else{
            return [];
        }

    }

    public function getAll(): array
    {
       return [];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function del(string $key): bool
    {
        try{
            $result = unlink(ROOT_PATH.$this->_dir."/".$key);
        }catch (\Exception $e){
            return false;
        }
        return $result?true:false;
    }


}