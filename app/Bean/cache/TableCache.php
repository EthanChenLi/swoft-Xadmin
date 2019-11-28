<?php
/**
 * I am what iam
 * Class Descript :  基于内存共享的cache. 服务重启后会回收所有内存
 * User: ehtan
 * Date: 2019-10-29
 * Time: 17:42
 */

namespace App\Bean\cache;


use Swoole\Table;

class TableCache implements ICache
{

    /**
     * swoole 内存区域
     * @var Table
     *
     */
    private $table;

    /**
     * 初始化配置
     * @var array $_options
     */
    private $_options =[
      "size" =>1024
    ];

    /**
     * TableSession constructor.
     * @param int $size 内存大小
     */
    public function __construct(int $size)
    {
        $this->_options['size'] = $size;
    }

    /**
     * 初始化table
     * cloumn :
     *      value : json , length=64 格式化数据
     *      ctime : int ,length=16  创建时间
     */
    public function init()
    {
        $this->table = new Table($this->_options['size']);
        $this->table->column('value', Table::TYPE_STRING, 64); //值
        $this->table->column('ctime', Table::TYPE_INT, 16); //创建时间
        $this->table->create();
    }

    /**
     * 设置session
     * @param string $key 绑定session_id的key
     * @param array $val
     * @return bool
     */
    public function set(string $key, array $val): bool
    {
        return $this->table->set($key,[
            'value'=>json_encode($val),
            'ctime'=>time()
        ]);
    }

    /**
     * 获取session
     * @param string $key 绑定session_id的key
     * @return array
     */
    public function get(string $key): array
    {
        if($this->table->exist($key)){
            return json_decode($this->table->get($key)['value'],true);
        }else{
            return [];
        }
    }


    /**
     * 获取所有
     * @return array
     */
    public function getAll(): array
    {
        $list =[];
        foreach($this->table as $row)
        {
            $list[]=$row;
        }
        return $list;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function del(string $key): bool
    {
        return $this->table->del($key)?true:false;
    }

}