<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    //判斷 ieFlag, 如果為 I 則 Insert, E 則 Update    
    $ts_ieFlag = $t_data["ieFlag"];    
    switch ($ts_ieFlag){
    case "I":        
        $ts_sql = "insert into BabyCheck(academicYear, semester, classLeavesSn, studentSn, cDate, attendFlag) values('{0}','{1}','{2}','{3}','{4}','{5}')";
        $ts_sql = Fun::format($ts_sql, $_SESSION["academicYear"], $_SESSION["semester"], $t_data["classLeavesSn"], $t_data["studentSn"], $t_data["cDate"], $t_data["attendFlag"]);
        break;
    case "E":
        $ts_sql = "UPDATE BabyCheck SET attendFlag = '{0}' WHERE sn = '{1}'";
        $ts_sql = Fun::format($ts_sql, $t_data["attendFlag"], $t_data["sn"]);
        break;
    }
    $t_db->exeUpdate($ts_sql);
    
    $t_db->close();
    
    header('Content-Type:text/plain; charset=utf-8');
    
    echo "1" ;
?>
