<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $studentSns = $t_data["studentSns"];
    
    $snTmp = 0;
    for ($p = 0; $p < count($studentSns); $p++){
        $snTmp = $snTmp . ', ' . $studentSns[$p]["sn"];
    }
    $ts_sql1 = "UPDATE Student
                SET enrollFlag = 0
                WHERE sn IN (".$snTmp.")";
    $t_db->exeUpdate($ts_sql1);
    
    $t_db->close();
?>
