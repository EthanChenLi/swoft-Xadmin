<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-11-25
 * Time: 15:50
 */

namespace App\Model\Logic;

use App\Model\Entity\Download;
use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class DownloadLogic
 * @package App\Model\Logic
 *
 * @Bean(name="downloadLogic",scope="Bean::PROTOTYPE")
 */
class DownloadLogic
{
    /**
     * 写入下载日志
     * @param int $uid
     * @param $path
     */
    public function write(string $title, $uid, $path=""){
        return Download::create([
           "title"=>$title,
            "uid"=>$uid,
            'path'=>$path
        ]);
    }


}