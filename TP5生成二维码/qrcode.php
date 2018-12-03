<?php
 /*生成二维码  不生成图片文件  直接显示图片
     *text  二维码所带的信息
     *size  尺寸
     *  */
    static public function createCode($text='http://www.baidu.com',$size=8)
    {
        header('Content-Type: image/png');
        include_once EXTEND_PATH.'phpqrcode/phpqrcode.php';
        $a=new \QRcode();
        $a::png($text,FALSE,4,$size);
        exit;
    }
    /*生成桌位二维码  生成图片文件  桌号用
    *text  二维码所带的信息
    *size  尺寸
    *  */
    static public function createTableCode($text='http://www.baidu.com',$size=8,$sid=2,$tid=5)
    {
        //header('Content-Type: image/png');
        include_once EXTEND_PATH.'phpqrcode/phpqrcode.php';
        $a=new \QRcode();
        $filename='sid'.$sid.'tid'.$tid.'.jpg';
        $path = ROOT_PATH."/public/static/tablecode"; //图片输出路径
        $filename=$path.'/'.$filename;
        $a::png($text,$filename,4,$size);
        //exit;
    }