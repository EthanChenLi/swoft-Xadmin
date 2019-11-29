<?php
/**
 * I am what iam
 * Class Descript : 下载列表 .
 * User: ehtan
 * Date: 2019-11-25
 * Time: 16:03
 */

namespace App\Http\Controller\Admin;

use App\Http\Traits\HttpBaseTrait;
use App\Model\Entity\Download;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\Middlewares;
use App\Http\Middleware\authCheckMiddleware;
use Swoft\Http\Session\HttpSession;

/**
 * Class DownloadController
 * @package App\Http\Controller\Admin
 * @Controller(prefix="/admin/download")
 * @Middleware(authCheckMiddleware::class)
 */
class DownloadController
{
    use HttpBaseTrait;
    protected $model = Download::class;

    public function __listsKeywords(array $param,array $map):array{
        return array_merge($map,[
           'uid'=>HttpSession::current()->get("USERINFO")['admin_id']
        ]);
    }


}