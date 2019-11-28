<?php
/**
 * I am what iam
 * Class Descript : .
 * User: ehtan
 * Date: 2019-10-29
 * Time: 17:38
 */

namespace App\Bean\cache;


Interface ICache
{
    public function init();
    public function set(string $key,array $val):bool;
    public function get(string $key):array;
    public function getAll():array;
    public function del(string $key):bool;
}