<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    //接值 要新增的開班檔
    $ts_oriStartClassSn = $t_data["oriStartClassSn"];
    $ts_nowStartClassSn = $t_data["nowStartClassSn"];
    
    $ts_sql0 = "INSERT INTO StudentStatus(startClassSn, studentSn)
                SELECT ".$ts_nowStartClassSn." AS startClassSn, studentSn
                FROM StudentStatus
                WHERE startClassSn = ".$ts_oriStartClassSn;
    $t_db->exeUpdate($ts_sql0); 
    
    $t_db->close();
    
    header('Content-Type:text/plain; charset=utf-8');
    
    echo "1" ;
?>
