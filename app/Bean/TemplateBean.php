<?php
/**
 * I am what iam
 * Class Descript : tp模板引擎初始化
 * User: ehtan
 * Date: 2019-11-01
 * Time: 16:42
 */

namespace App\Bean;

use Swoft\Bean\Annotation\Mapping\Bean;
use think\Template;

/**
 * Class TemplateBean
 * @package App\Bean
 * @Bean(name="template" ,scope="Bean::PROTOTYPE")
 */
class TemplateBean
{
    private $_template;

    public function __construct()
    {
        $this->_template = new Template([
            // 模板文件目录
            'view_path'   => ROOT_PATH.'/resource/views/',
            // 模板编译缓存目录（可写）
            'cache_path'  => ROOT_PATH.'/runtime/template/',
            // 模板文件后缀
            'view_suffix' => 'html',
        ]);
    }


    public function getTemplate(){
        return $this->_template;
    }

}