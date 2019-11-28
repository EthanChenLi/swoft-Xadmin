<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Task\Listener;

use function context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Http\Session\HttpSession;
use Swoft\Log\Helper\CLog;
use Swoft\Task\TaskEvent;

/**
 * Class FinishListener
 *
 * @since 2.0
 *
 * @Listener(event=TaskEvent::FINISH)
 */
class FinishListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * event :
     *    |- output 导出execl
     */
    public function handle(EventInterface $event): void
    {
        $taskData = json_decode(context()->getTaskData(),true);
        if(!empty($taskData)){
           switch ($taskData['result']['event']){
               case 'output':
                   $this->_outputEvent($taskData['result']['data']);
                   break;
           }
        }
    }


    /**
     * 导出execl
     * @param array $data
     */
    private function _outputEvent(array $data){
       $fd =  \bean("ws")->getFdByUid($data['uid']);
       //写入到数据库
        bean('downloadLogic')->write("导出到excel", $data['uid'],$data['path']);
       if($fd >0 or !empty($fd)){
           //推送到ws
           server()->push($fd,json_encode([
               'event'=>"output",
               "path"=>$data['path']
           ]));

       }
    }

}
