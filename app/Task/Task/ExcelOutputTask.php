<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-11-22
 * Time: 14:06
 */

namespace App\Task\Task;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Swoft\Log\Helper\CLog;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;

/**
 * Class ExcelOutTask
 * @package App\Task\Task
 *
 * @Task(name="ExcelOutpub")
 */
class ExcelOutputTask
{

    /**
     * 标题字段
     * @var array
     */
    private $_fields = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

    /**
     *
     * @var Spreadsheet
     */
    private $sheet;

    /**
     * @TaskMapping()
     * @param array $data 数据数组
     * @param array $fields 字段数组
     * @param integer $uid 用户id
     * @return array
     */
    public function process(array $data,array $fields,int $uid):array{


        $fieldsValue =  array_column($fields,0); //获取需要打印的字段
        $fieldsTitle  = array_column($fields,1); //获取字段名称

        $path ="";
        try{
            $spreadsheet = new Spreadsheet();
            $this->sheet = $spreadsheet->getActiveSheet();
                $this->_makeTitleLine($fieldsTitle);
                $this->_makeDataLine($fieldsValue, $data, $fields);
                $writer = new Xlsx($spreadsheet);
                $path = md5(time().rand(111,999)).'.xlsx';
                $dir = ROOT_PATH."/public/uploads/excel/";
                if(!file_exists($dir)){
                    Directory($dir);
                    chmod($dir,0777);
                }
                $writer->save($dir.$path);
        }catch (\Exception $e){
           CLog::error($e->getMessage());
        }
        return [
            'event'=>'output',
            'data'=>[
                'uid' =>$uid,
                'path'=>"/uploads/excel/".$path
            ]
        ];
    }

    /**
     * 构建标题字段
     * @param $fields
     */
    private function _makeTitleLine(array $fields){
        $line = 0;
        foreach ($fields as $val) {
            $this->sheet->setCellValue($this->_fields[$line++].'1', $val);
        }
    }


    /**
     * 构建数据列
     * @param array $fieldsVal 需要打印的字段
     * @param array $data      要打印的数组
     * @param array $fields    用户构建的数组
     */
    private function _makeDataLine(array $fieldsVal, array $data, array $fields){
        $rowCount = 1;
        foreach ($data as $rows) {
            $i = 0;
            $rowCount++; //从A1开始
            foreach ($rows as $key=>$val){
                if(in_array($key,$fieldsVal)){
                    $line = $this->_fields[$i++];
                    $this->sheet->setCellValue($line.$rowCount, $this->_makeCondition($key,$val,$fields));
                }
            }
        }
    }


    private function _makeCondition($key,$val, array $fields){
        foreach ($fields as $field) {
            if(!empty($field[2]) and $key == $field[0]){
                if($val >= 1 or $val == true){
                    $val = $field[2][0];
                }else{
                    $val = $field[2][1];
                }
            }
        }
        return $val;
    }


}