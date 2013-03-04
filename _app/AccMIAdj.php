<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $AccMIAdjSns = $t_data["AccMIAdjSns"];
    
    //$k = 0;
    $ts_sql = "";
    $ts_ual = " UNION ALL ";
    
    for ($p = 0; $p < count($AccMIAdjSns); $p++){
        if ($p == count($AccMIAdjSns)-1){
           $ts_ual = "";
        }
        $ts_sql = $ts_sql . "SELECT ".$AccMIAdjSns[$p]["accMIncomeSn"].", ".$AccMIAdjSns[$p]["amounts"].", ".$AccMIAdjSns[$p]["cAmounts"].", ".$_SESSION["usersSn"].", CURRENT_TIMESTAMP()" . $ts_ual;
        //$k = $k + 1;
    }
    
    $ts_sql1 = "INSERT INTO AccMIAdj(accMIncomeSn, amounts, cAmounts, userSn, cDate) " . $ts_sql;
    $tn_row = $t_db->exeUpdate($ts_sql1);
    
    $t_db->close();
    /*
    header('Content-Type:text/plain; charset=utf-8');
    
    if ($tn_row == 1){
        echo $k;
    }else{
        echo 0;
    }
     */
?>