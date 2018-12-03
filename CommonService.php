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

}