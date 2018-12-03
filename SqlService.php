<?php
/**
 * Created by PhpStorm.
 * User: 孙洪飞
 * Date: 2017/10/25 0025
 * Time: 下午 8:11
 */

namespace app\admin\service;
use think\Controller;
class SqlService extends Controller
{

    /**判断参数值重复
     * [checkRepeat description]
     * @param  [type] $table  [description]     表名
     * @param  [type] $where  [description]     where条件,传入数组或字符串
     * @param  string $wheres [description]     where条件,和$where配合使用,一个传入数组,一个传入字符串
     * @return [type]         [description]     
     */
    static function checkRepeat($table,$where,$wheres='1=1')
    {
        $r=db($table)->where($where)->where($wheres)->count();
        return $r;
    }
    static function 
}