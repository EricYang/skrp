<?php    

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();        
    $t_db = new DB();

    $sn = $t_data["sn"];
    $classLeavesSn = $t_data["classLeavesSn"];
    
    //mySql 竟然不能 下 DELETE 原 Table WHERE sn IN 原 Table, 需要拆解分段執行
    if (trim($sn) != "" && trim($classLeavesSn) != ""){     
        $ts_sql0 = "SELECT c.sn AS sn
                    FROM LearnPro a, LearnProPT b, LearnProTG c
                    WHERE a.sn = b.learnProSn AND b.sn = c.learnProPTSn
                    AND a.sn = '".$sn."' AND c.classLeavesSn != '".$classLeavesSn."'
                    AND c.academicYear = ".$_SESSION["academicYear"]." AND c.semester = ".$_SESSION["semester"];
        $ta_row0 = Fun::readRows($ts_sql0);
        if ($ta_row0 != null){
            $snTmp = 0;
            for ($r = 0; $r < count($ta_row0); $r++){
                $snTmp = $snTmp . ', ' . $ta_row0[$r]["sn"];
            }
            
            $ts_sql1 = "DELETE FROM LearnProTG
                        WHERE sn IN (".$snTmp.")";
            $t_db->exeUpdate($ts_sql1);
        }
        $t_db->close();
    }  
?>