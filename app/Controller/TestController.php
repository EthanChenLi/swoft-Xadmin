<?php declare(strict_types=1);


namespace App\Controller;

use App\Model\Entity\Admin;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;

use Swoft\Task\Task;

/**
 * Class TestController
 *
 * @since 2.0
 *
 * @Controller(prefix="test")
 */
class TestController
{
    /**
     * @RequestMapping(route="test")
     *
     * @return string
     */
    public function test()
    {

    }


}