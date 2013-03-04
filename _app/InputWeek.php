<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $weekData = $t_data["weekData"];
    
    $ts_sql = "";
    $ts_ual = " UNION ALL ";
    
    for ($p = 0; $p < count($weekData); $p++){
        if ($p == count($weekData)-1){
           $ts_ual = "";
        }
        $ts_sql = $ts_sql . "SELECT ".$_SESSION["academicYear"].", 
                                    ".$_SESSION["semester"].", 
                                    '".$weekData[$p]["sDay"]."', 
                                    '".$weekData[$p]["eDay"]."', 
                                    '".$_SESSION[Fun::csLoginId]."', 
                                    CURRENT_TIMESTAMP()" . $ts_ual;
        
    }
    
    $ts_sql1 = "INSERT INTO YearEdu(academicYear, semester, sDate, eDate, creator, createDate) " . $ts_sql;
    $tn_row = $t_db->exeUpdate($ts_sql1);
    
    $t_db->close();
    
    header('Content-Type:text/plain; charset=utf-8');

    echo $tn_row;

/*
    require_once(dirname(__FILE__)."/../_code/Fun.php");
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $sDate = $t_data["sDate"];
    $eDate = $t_data["eDate"];

    $ts_sql = "CALL sp_weekList('2009/8/17', '2010/2/5')";
    $ta_row = $t_db->readRows($ts_sql);
    
    $t_db->close();

    echo Fun::json($ta_row);
*/
?>