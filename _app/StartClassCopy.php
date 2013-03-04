<?php    
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    //接值 要新增的學年期
    $ts_academicYear0 = $t_data["newAcademicYear"];
    $ts_semester0 = $t_data["newSemester"];
    
    //查詢該年度是否已有資料
    $ts_sql0 = "SELECT COUNT(*) AS ACO
                FROM StartClass
                WHERE academicYear = ".$ts_academicYear0." AND semester = ".$ts_semester0;
    $t_row0 = Fun::readRow($ts_sql0);
    //如果沒有則複製
    if ($t_row0["ACO"] == 0){        
        //根據過來的 sn 讀取要被複製的年度
        $ts_sql5 = "SELECT academicYear, semester
                    FROM AcaSetup
                    WHERE sn = ".$t_data["acaSetupSn"];
        $t_row5 = Fun::readRow($ts_sql5);
        if ($t_row5){
            $ts_academicYear1 = $t_row5["academicYear"];
            $ts_semester1 = $t_row5["semester"];

            //讀取要被複製的年度
            $ts_sql1 = "SELECT a.sn, ".$ts_academicYear0." AS academicYear, ".$ts_semester0." AS semester, a.classLeavesSn, a.classType, a.tuition, a.classH,
                        b.startClassSn, b.cTitles, b.staffSn, '".$_SESSION[Fun::csLoginId]."' AS creator, CURDATE() AS createDate
                        FROM StartClass a, ClassTeacher b
                        WHERE a.sn = b.startClassSn
                        AND a.academicYear = ".$ts_academicYear1." AND a.semester = ".$ts_semester1;
            $ta_row1 = Fun::readRows($ts_sql1);
            if ($ta_row1 != null){                
                for ($r = 0; $r < count($ta_row1); $r++){
                    $t_row1 = $ta_row1[$r];
                    //新增開課檔
                    $ts_sql2 = "INSERT INTO StartClass (academicYear, semester, classLeavesSn, classType, tuition, classH)
                                VALUES ({0}, {1}, {2}, {3}, {4}, {5})";
                    $ts_sql2 = Fun::format($ts_sql2, $t_row1["academicYear"], $t_row1["semester"], $t_row1["classLeavesSn"], $t_row1["classType"], $t_row1["tuition"], $t_row1["classH"]);            
                    $t_db->exeUpdate($ts_sql2);
                
                    //獲得最大開課檔 sn
                    $ts_sql3 = "SELECT MAX(sn) AS MaxSn 
                                FROM StartClass
                                WHERE academicYear = ".$t_row1["academicYear"]." AND semester = ".$t_row1["semester"]."
                                AND classLeavesSn = ".$t_row1["classLeavesSn"];
                    $t_row3 = Fun::readRow($ts_sql3);            
                    if ($t_row3){
                        //新增開課檔帶班老師
                        $ts_sql4 = "INSERT INTO ClassTeacher (startClassSn, cTitles, staffSn, creator, createDate)
                                    VALUES ({0}, '{1}', {2}, '{3}', '{4}')";
                        $ts_sql4 = Fun::format($ts_sql4, $t_row3["MaxSn"], $t_row1["cTitles"], $t_row1["staffSn"], $t_row1["creator"], $t_row1["createDate"]);            
                        $t_db->exeUpdate($ts_sql4);                
                    }
                }        
            }            
        }
    }
    
    $t_db->close();
    
    header('Content-Type:text/plain; charset=utf-8');
    
    echo "1" ;
?>
