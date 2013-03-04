<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    //接值 要新增的資料
    $sn = $t_data["sn"];
    $userSn = $t_data["userSn"];
    $loginPw = $t_data["loginPw"];    
    
    $ts_sql = "UPDATE Account
                SET loginPw = '".$loginPw."'
                WHERE sn = ".$sn." AND userSn = ".$userSn;
    $tn_row = $t_db->exeUpdate($ts_sql);
    
    header('Content-Type:text/plain; charset=utf-8');
    
    $t_db->close();
    
    if ($tn_row>=1){
        echo "1";
    }else{
        echo "0";
    }    
?>
