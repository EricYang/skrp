<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    //接值 要新增的資料
    $filesManaSn = $t_data["filesManaSn"];    
    
    $ts_sql = "INSERT INTO FilesUsed(filesManaSn, userType, userSn, dDate)
                VALUES (".$filesManaSn.", ".$_SESSION["userType"].", ".$_SESSION["usersSn"].", CURRENT_TIMESTAMP())";
    $tn_row = $t_db->exeUpdate($ts_sql);
    
    header('Content-Type:text/plain; charset=utf-8');
    
    $t_db->close();
    
    if ($tn_row>=1){
        echo "1";
    }else{
        echo "0";
    }    
?>
