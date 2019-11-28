<?php declare(strict_types=1);

namespace App\Exception\Handler;


use const APP_DEBUG;
use function get_class;
use ReflectionException;
use function sprintf;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Error\Annotation\Mapping\ExceptionHandler;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Exception\Handler\AbstractHttpErrorHandler;
use Swoft\Http\Server\Exception\HttpServerException;
use Swoft\Http\Session\HttpSession;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Throwable;

/**
 * Class HttpExceptionHandler
 *
 * @ExceptionHandler(\Throwable::class)
 */
class HttpExceptionHandler extends AbstractHttpErrorHandler
{

    /**
     * @param Throwable $e
     * @param Response $response
     * @return Response
     * @throws Throwable
     * @throws \Swoft\Exception\SwoftException
     */
    public function handle(Throwable $e, Response $response): Response
    {

        //处理是否存在空控制逻辑
        $emptyLogicResult = $this->_emptyLogicCheck();
        if(null != $emptyLogicResult){
            return $emptyLogicResult; //有控制器逻辑直接返回Response对象
        }

        // Log
        Log::error($e->getMessage());
        CLog::error($e->getMessage());
        CLog::error($e->getFile());

        // Debug is false
        if (!APP_DEBUG) {
            //弹出404页面
            return fetchView("common/error");
        }
        $data = [
            'code'  => $e->getCode(),
            'error' => sprintf('(%s) %s', get_class($e), $e->getMessage()),
            'file'  => sprintf('At %s line %d', $e->getFile(), $e->getLine()),
            'trace' => $e->getTraceAsString(),
        ];
        // Debug is true
        return $response->withData($data)->withHeader("Content-Type","application/json");
    }

    /**
     * 空控制器处理
     * @throws \Swoft\Exception\SwoftException
     */
    private function _emptyLogicCheck():?Response{
        //空逻辑处理
        $uriPath = context()->getRequest()->getUri()->getPath();
        //提取uri信息
        $uriArray = array_values(
                        array_filter(
                            explode("/",$uriPath)
                        )
                    );
        //支持分组url
        if(strpos($uriArray[1],'.') >= 1){
            //xxx/xxx.xxx/xxx
            $classGroup =   array_values(array_filter(explode(".",$uriArray[1])));
            $className = [
                strtolower($classGroup[0]),
                strtolower($classGroup[1]),
            ];
            $classNameSpace ="\\App\\Http\\Controller\\Admin\\".ucfirst(strtolower($classGroup[0]))."\\".ucfirst(strtolower($classGroup[1]))."Controller";
        }else{
            //xxx/xxx/xxx
            $className =[strtolower($uriArray[1])];
            $classNameSpace ="\\App\\Http\\Controller\\Admin\\".ucfirst(strtolower($uriArray[1]))."Controller";
        }
        if(!class_exists($classNameSpace)) return null;
        //类名称
        try{
            //action名称
            $actionName= strtolower($uriArray[2]);
            if(!in_array($actionName,["lists","add", "edit", "del", "output"]))return null;
            $class = new $classNameSpace();
            return $class->buildLogicModel($className,(string)$actionName); //构建模型
        }catch (\Exception $e){
            return \context()->getResponse()->withStatus(NOT_FOUND)->withContent("404 NOT FOUND");
        }

    }



}
