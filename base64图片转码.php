<?php
/**
 * 
 */


/**base64转换为图片并保存
 * [baseToImage description]
 * @param  [type] $base64 [base64文件]
 * @param  [type] $src    [文件保存路径]
 * @return [type]         [bool]
 */
function baseToImage($base64,$src)
    {
    	$imageName = date("YmdHis",time()).rand(1111,9999).'.png';
    	$imageName = md5($imageName);

        if (strstr($base64,",")){
            $base64 = explode(',',$base64);
            $base64 = $base64[1];
        }

        $path = $src . date("Ymd",time());

        if (!is_dir($path)){ //判断目录是否存在 不存在就创建
            mkdir($path,0777,true);
        }

        $imageSrc=  $path."/". $imageName;  //图
        $r = file_put_contents($imageSrc, base64_decode($image));//返回的是字节数
        if (!$r) {
            return false;
        }else{
            return true;
        }
    }