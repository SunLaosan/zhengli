<?php
/**
 * Created by PhpStorm.
 * User: 孙洪飞
 * Date: 2018/7/6 0006
 * Time: 10:18
 */

namespace app\index\service;

/**
 * 小程序登录Login方法code换取敏感数据(openID和session_key) 请求数据后的解密数据过程
 * 将wecharencrypted文件夹放入public同级的extend中
 * Class XcxDecrypt
 * @package app\index\service
 */
class XcxDecrypt
{
    private $appid;
    private $secret;
    private $code;

    /**
     * 构造函数
     * XcxDecrypt constructor.
     * @param $appid
     * @param $secret
     * @param $code Login返回的code
     */
    public function __construct($appid, $secret, $code)
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->code = $code;
    }

    /**
     * 换取敏感信息 处理用户表 成功返回用户信息 失败false
     * @return bool
     */
    public function getInfo()
    {
        //请求api  换取敏感数据
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $this->appid . "&secret=" . $this->secret . "&js_code=" . $this->code . "&grant_type=authorization_code";
        $apiData = file_get_contents($url);
        $array=json_decode($apiData,true);
        //此时$array请求成功返回openid session_key失败包含errcode

        //判断请求是否成功
        if(!isset($array['errcode']))
        {

            //判断该用户是否存在
            $count = "判断用户是否存在的方法";

            if(!$count)//不存在
            {
                /**添加用户的处理方法,存储openID,session_key**/
            }else{
                /**修改登录信息等的方法 修改session_key**/
            }
        }
        //请求失败
        return false;
    }

    /**
     *请求数据的解密操作
     * @param $encryptedData   接收到的加密数据
     * @param $iv   向量
     * @param $session_key  code换取回的session_key
     * @return bool
     */
    public function dataDecrypt($encryptedData, $iv, $session_key)
    {
        include_once EXTEND_PATH.'wecharencrypted/wxBizDataCrypt.php';

        $pc = new \WXBizDataCrypt($this->appid, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        if ($errCode == 0) {//解密成功
            $array = json_decode($data,true);
            /**处理解密后的数据**/

        }
        return false;
    }
}