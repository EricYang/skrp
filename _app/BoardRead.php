<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $boardSns = $t_data["boardSns"];
    
    $k = 0;
    $ts_sql = "";
    $ts_ual = " UNION ALL ";
    
    for ($p = 0; $p < count($boardSns); $p++){
        if ($p == count($boardSns)-1){
           $ts_ual = "";
        }
        $ts_sql = $ts_sql . "SELECT ".$boardSns[$p]["sn"].", ".$_SESSION["userType"].", ".$_SESSION["usersSn"].", CURRENT_TIMESTAMP()" . $ts_ual;
        $k = $k + 1;
    }
    
    $ts_sql1 = "INSERT INTO BoardUE(boardSn, userType, userSn, dDate) " . $ts_sql;
    $tn_row = $t_db->exeUpdate($ts_sql1);
    
    $t_db->close();
    
    header('Content-Type:text/plain; charset=utf-8');
    
    if ($tn_row == 1){
        echo $k;
    }else{
        echo 0;
    }
?>
