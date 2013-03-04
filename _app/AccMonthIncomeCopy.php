<?php
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();

    $classLeavesSn = $t_data["classLeavesSn"];
    $cYearA = $t_data["cYearA"];
    $cMonthA = $t_data["cMonthA"];
    $cYearB = $t_data["cYearB"];
    $cMonthB = $t_data["cMonthB"];
    $k = 0;

    $ts_sql = "SELECT COUNT(*) AS existCount
                FROM AccMIncomeStu
                WHERE (classLeavesSn = ".$classLeavesSn.") AND 
                (cYear = ".$cYearB.") AND (cMonth = ".$cMonthB.")";
    $ta_row = Fun::readRow($ts_sql);
    if ($ta_row["existCount"]==0){
        $ts_sql0 = "SELECT a.*
                    FROM AccMIncomeStu a INNER JOIN
                    Student b ON a.studentSn = b.sn
                    WHERE (b.enrollFlag = 1) AND (b.deleFlag = 0) AND (a.classLeavesSn = ".$classLeavesSn.") AND 
                    (a.cYear = ".$cYearA.") AND (a.cMonth = ".$cMonthA.")";
        $ta_row0 = Fun::readRows($ts_sql0);
        if ($ta_row0 != null){

            $ser_sql = "SELECT CASE WHEN MAX(RIGHT(serialNo, 13)) IS NULL THEN CONCAT(CURDATE() + 0, '00001') ELSE MAX(RIGHT(serialNo, 13)) +1 END AS serialNo
                        FROM AccMIncomeStu
                        WHERE MID(serialNo, 2, 8) = CURDATE() + 0";
            $ser_row = Fun::readRow($ser_sql);
            if ($ser_row != null){
                $serialNo = $ser_row["serialNo"];

                for ($x = 0; $x < count($ta_row0); $x++){
                    $t_row0 = $ta_row0[$x];

                    $k = $k + 1;
                    $serialNo = $serialNo + 1;

                    $ts_sql1 = "INSERT INTO AccMIncomeStu (serialNo, classLeavesSn, studentSn, cYear, cMonth, creator, createDate)
                                SELECT 'M".$serialNo."', ".$t_row0["classLeavesSn"].", ".$t_row0["studentSn"].", ".$cYearB.", ".$cMonthB.", '".$_SESSION[Fun::csLoginId]."', CURRENT_TIMESTAMP()";
                    $t_db->exeUpdate($ts_sql1);

                    $ts_sql2 = "SELECT sn AS accMIncomeStuSn 
                                FROM AccMIncomeStu
                                WHERE serialNo = 'M".$serialNo."'";
                    $ta_row2 = Fun::readRow($ts_sql2);
                    if ($ta_row2 != null){
                        $ts_sql3 = "INSERT INTO AccMIncome (accMIncomeStuSn, accIocSn, amounts, creator, createDate)
                                    SELECT ".$ta_row2["accMIncomeStuSn"].", accIocSn, amounts, '".$_SESSION[Fun::csLoginId]."', CURRENT_TIMESTAMP()
                                    FROM AccMIncome
                                    WHERE accMIncomeStuSn = ".$t_row0["sn"]."";
                        $t_db->exeUpdate($ts_sql3);
                    }
                }
            }
        }
        echo $k ;
    }else{
        echo -1 ;
    }
    
    $t_db->close();
    
    //header('Content-Type:text/plain; charset=utf-8');    
    
?>