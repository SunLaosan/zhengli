<?php
/*事务*/
$error = array();
        // 启动事务
        Db::startTrans();
        if(in_array(false,$error))
        {
            // 回滚事务
            Db::rollback();
        }
        // 提交事务
        Db::commit();