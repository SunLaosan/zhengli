<?php
/**
 * Created by PhpStorm.
 * User: 孙洪飞
 * Date: 2017/10/25 0025
 * Time: 下午 8:11
 */

namespace app\admin\service;
use think\Controller;
class CommonService extends Controller
{

    /*检查参数
     *@param    post         POST方式接到的值
     *@param    paramName    需要的参数名组成的数组
     *@return   string/bool
     * */
    static function checkParam($post,$paramName,$arr)
    {
        foreach ($post as $key => $value)
        {
            if(!in_array($key,$paramName))
            {
                return '参数名有误'.$arr[$key];
            }
            if (strlen($value)== 0)
            {
                return '('.$arr[$key].')不能为空';
            }
        }
        foreach ($paramName as $key1 => $value1)
        {
            if(!isset($post[$value1]))
            {
                return '缺少参数'.$arr[$value1];
            }
        }
        return true;
    }

    /*ajax返回信息及数据
     *@param    code 状态码0/1
     *@param    msg  信息或数据
     *@return   json
     *   */
    static function returnData($code,$msg)
    {
        $data=[
            'code'  =>  $code,
            'msg'   =>  $msg
        ];
        return json($data);
    }
    /*ajax返回jsonp  解决跨域问题
    *@param    code 状态码0/1
    *@param    msg  信息或数据
    *@return   jsonp
    *   */
    static function returnJsonp($code,$msg)
    {
        $data=[
            'code'  =>  $code,
            'msg'   =>  $msg
        ];
        return jsonp($data);
    }

    /**原生增删改的结果返回 
     * [returnNative description]
     * @param  [type] $r   [description]    结果
     * @param  [type] $url [description]    成功后的跳转路径
     * @param  [type] $msg [description]    错误提示信息
     * @return [type]      [description]
     */
    static function returnNative($r,$url,$msg='操作失败')
    {
        if($r)
        {
            return self::redirect($url);
        }else{
            return self::error($msg);
        }
    }
    /**ajax返回json
     * [returnAjax description]
     * @param  [type] $r       [description]    结果
     * @param  [type] $success [description]    成功信息
     * @param  [type] $error   [description]    失败信息
     * @return [type]          [description]
     */
    static function returnAjax($r,$success,$error)
    {
        if($r)
        {
            return self::returnData(1,$success);
        }else{
            return self::returnData(0,$error);
        }
    }
    /**ajax返回jsonp
     * [returnAjaxp description]
     * @param  [type] $r       [description]    结果
     * @param  [type] $success [description]    成功信息
     * @param  [type] $error   [description]    失败信息
     * @return [type]          [description]
     */
    static function returnAjaxp($r,$success,$error)
    {
        if($r)
        {
            return self::returnJsonp(1,$success);
        }else{
            return self::returnJsonp(0,$error);
        }
    }
    /*
     * 判断重复
     *
     * */
    static function checkRepeat($table,$where,$wheres='1=1')
    {
        $r=db($table)->where($where)->where($wheres)->count();
        return $r;
    }

    /**修改数据
     * @param $table
     * @param $where两个where条件配合使用 一个数组 一个字符串
     * @param $wheres
     * @param $data
     * @return int|string
     */
    static function valueUpdate($table,$where,$data,$wheres='1=1')
    {
        $r=db($table)->where($where)->where($wheres)->update($data);
        return $r;
    }

    /**
     * 查询单条数据
     * @param $table
     * @param $where
     * @param string $wheres
     * @return array|false|\PDOStatement|string|\think\Model
     */
    static function valueFind($table, $where, $wheres = '1=1', $field = '*')
    {
        $r = db($table)->where($where)->where($wheres)->field($field)->find();
        return $r;
    }

    /**
     * 查询某个值
     * @param $table
     * @param $where
     * @param $param
     * @param string $wheres
     * @return mixed
     */
    static function selectValue($table, $where, $param, $wheres = '1=1')
    {
        $r = db($table)->where($where)->where($wheres)->value($param);
        return $r;
    }

    /**
     * 返回结果的封装判断
     * @param $r
     * @return \think\response\Json
     */
    static function returnJudge($r, $content, $remarks = '无')
    {
        if($r){
            //LogService::log($content, $remarks);
            return self::returnData(1,'操作成功');
        }else{
            return self::returnData(0,'操作失败');
        }
    }

    /**
     * 添加数据
     * @param $table
     * @param $data
     * @return int|string
     */
    static function valueInsert($table, $data)
    {
        //判断传入的是一维数组还是二维数组
        if (count($data) == count($data,1)) {
            $r = db($table)->insertGetId($data);
        } else {
            $r = db($table)->insertAll($data);
        }

        return $r;
    }

    /**
     * 删除数据 真删除
     * @param $table
     * @param $where
     * @param string $wheres
     * @return int
     */
    static public function delValue($table, $where, $wheres = '1=1')
    {
        $r = db($table)->where($where)->where($wheres)->delete();

        return $r;
    }
}