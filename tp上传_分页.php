<?php
function upload($name)
    {
        $file=request()->file($name);

        if (empty($file)) {
            return false;
        }else{
            $path=ROOT_PATH.'public'.DS.'static'.DS.'admin'.DS.'file';
            $info=$file->move($path);
            if ($info) {
                $url=$info->getSaveName();
                //转斜杠
                $url=str_replace("\\","/",$url);
                return $url;
            }else{
                return false;
            }
        }
    }
?>
/**分页**/
<link rel="stylesheet" type="text/css" href="bootstrap.min.css" />
//分页样式
<style>
 /*分页样式*/  
    .pagination{text-align:center;margin-top:20px;margin-bottom: 20px;}  
    .pagination li{margin:0px 0px; border:1px solid #e6e6e6;padding: 3px 8px;display: inline-block;color:#337AB7 }  
    .pagination .active{background-color:#337AB7;color: white}  
    .pagination .disabled{color: red;}  
</style>
//追加分页css文件
<script>
    function addCss()
    {
        $("<link>") .attr({ rel: "stylesheet", type: "text/css", href: "__STYLE__bootstrap.min.css"}) .appendTo("head"); 
    }
</script>