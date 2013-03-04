<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $arpData = $t_data["arpData"];
    
    for ($p = 0; $p < count($arpData); $p++){
        $ts_sql1 = "SELECT SUM(amounts) AS amounts
                    FROM AccRequestPaid
                    WHERE accRequestPaymentSn = ".$arpData[$p]["accRequestPaymentSn"];
        $t_row1 = Fun::readRow($ts_sql1);
        if ($t_row1){
            $ts_sql2 = "UPDATE AccRequestPayment
                        SET amounts = ".$t_row1["amounts"]."
                        WHERE sn = ".$arpData[$p]["accRequestPaymentSn"];
            $t_db->exeUpdate($ts_sql2);
        }
    }
    
    $t_db->close();
?>
