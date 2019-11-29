<?php declare(strict_types=1);


namespace App\Controller;

use App\Annotation\Mapping\ParamsMapping;
use App\Model\Entity\Admin;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Session\HttpSession;


/**
 * Class TestController
 *
 * @since 2.0
 *
 * @Controller(prefix="test")
 *
 */
class TestController
{
    /**
     * @RequestMapping(route="test")
     * @return string
     */
    public function test()
    {
        HttpSession::current()->set("hello","hi");
        return 'ok';
    }
    /**
     * @RequestMapping(route="test2")
     * @return string
     */
    public function test2()
    {
        return HttpSession::current()->get("hello");
    }


}