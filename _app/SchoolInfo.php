<?php    

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();
        
    $t_db = new DB();

    $sn = $t_data["sn"];    
    $schoolName = $t_data["schoolName"];
    $fileName = $t_data["fileName"];
    $webSiteUrl = $t_data["webSiteUrl"];
    $conTel = $t_data["conTel"];
    $address = $t_data["address"];
    $reviser = $t_data["reviser"];
    
    if (trim($sn) != ""){
        $ts_sql = "UPDATE ManaInfo.SchoolD 
                    SET schoolName = '".$schoolName."', fileName = '".$fileName."', 
                        webSiteUrl = '".$webSiteUrl."', conTel = '".$conTel."', address = '".$address."',
                        reviser = '".$reviser."', reviseDate = CURRENT_TIMESTAMP()
                    WHERE sn = ".$sn;
        $tn_row = $t_db->exeUpdate($ts_sql);
    
        $t_db->close();
        
        header('Content-Type:text/plain; charset=utf-8');
        if ($tn_row>=1){
            echo "1";
        }else{
            echo "0";
        }  
    }  
?>