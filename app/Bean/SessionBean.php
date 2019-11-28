<?php
/**
 * I am what iam
 * Class Descript : session存储驱动 .
 * User: ehtan
 * Date: 2019-10-29
 * Time: 14:35
 */

namespace App\Bean;

use App\Bean\cache\ICache;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoole\Table;

/**
 * Class SessionBean
 * @package App
 *
 * @Bean(name="session")
 */
class SessionBean
{

    /**
     * session对象
     * @var $_session
     */
    private $_session;


    /**
     * 初始化session对象
     * SessionBean constructor.
     */
    public function __construct()
    {
        $type = ucfirst(strtolower(env("SESSION_MEMORY_TYPE")));
        $sessionName ="\\App\\Bean\\cache\\{$type}Cache";
        $this->_session = new $sessionName(env("SESSION_OPTION_".strtoupper($type)));
        $this->_session->init(); //初始化
    }


    /**
     * 设置一个session
     * @param string $key
     * @param array $val
     * @return bool
     * @throws \Swoft\Exception\SwoftException
     */
    public function setSession(string $key,array $val):bool{
        $cookie = \context()->getRequest()->getCookieParams();
        if(empty($cookie['SESSIONID'])) return false;
        return $this->_session->set($cookie['SESSIONID']."_".$key,$val);
    }

    /**
     * 获取session信息
     * @param string $key
     * @return array
     * @throws \Swoft\Exception\SwoftException
     */
    public function getSession(string $key):array{
        $cookie = \context()->getRequest()->getCookieParams();
        if(empty($cookie['SESSIONID'])) return [];
        return $this->_session->get($cookie['SESSIONID']."_".$key);
    }


    /**
     * 获取所有session数据
     * @return array
     */
    public function getAll():array {
        return $this->_session->getAll();
    }

    /**
     * 删除session
     * @param string $key
     * @return book
     * @throws \Swoft\Exception\SwoftException
     */
    public function delSession(string $key):bool {
        $cookie = \context()->getRequest()->getCookieParams();
        return $this->_session->del($cookie['SESSIONID']."_".$key);
    }

    /**
     * 获取sessionid
     * @return string
     */
    public function getSessionId():string{
        return \context()->getRequest()->getCookieParams()['SESSIONID'];
    }

}