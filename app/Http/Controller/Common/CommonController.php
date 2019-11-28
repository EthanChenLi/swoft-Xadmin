<?php
/**
 * I am what iam
 * Class Descript : 公用方法类.
 * User: ehtan
 * Date: 2019-11-06
 * Time: 10:49
 */

namespace App\Http\Controller\Common;

use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Http\Server\Exception\HttpServerException;

/**
 * Class CommonController
 * @package App\Http\Controller\Common
 * @Controller()
 */
class CommonController
{
    /**
     * 上传文件
     * 上传文件name="files"
     *
     * @RequestMapping(route="/common/upload" , method={RequestMethod::POST})
     *
     */
    public function uploads(Request $request, Response $response){
        try{
            $info = $request->getUploadedFiles()['files'];
        }catch (\Exception $e){
            return failWithJsonByApi("上传图片不能为空",[],EXCEPTION_CODE);
        }
        $fileName = $info->getClientFilename();
        $fileNameArr= explode(".",$fileName);
        $fixpre =array_pop($fileNameArr);
        $filePath  = "uploads/".date("Y",time())."-".date("m",time())."-".date("d",time())."/".md5(md5((string)time())).".".$fixpre;
        $info->moveTo("public/".$filePath);
        return successWithJsonByApi(["path"=>(string)$filePath]);
    }

    /**
     * 获取上传资源
     * @RequestMapping(route="/uploads/{dir}/{path}",method={RequestMethod::GET})
     */
    public function getUploadsInfo(string $dir, string $path){
            if(!empty($dir) and !empty($path)){
                $path ="public/uploads/".$dir."/".$path;
                if(file_exists($path)){
                    return \context()->getResponse()->withContent(file_get_contents($path))->withHeader("Content-Type","image/jpeg");
                }
            }
        return \context()->getResponse()->withStatus(NOT_FOUND)->withContent("404 not found");
    }
    

}