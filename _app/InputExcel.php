<?php    

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();
    /*
    Fun2::hasSession();
    
    $ts_app = isset($t_data["app"]) ? $t_data["app"] : "";
    $ts_right = self::checkRight($t_data, $ts_app);
    if ($ts_right == "")
        return "";                
    */    
    $t_db = new DB();

    $excelFile = $t_data["excelFile"];
    $inputExcelType = $t_data["inputExcelType"];
    $creator = $_SESSION[Fun::csLoginId];
    $startClassSn = $t_data["startClassSn"];

    $i = 0;
    
    //學生
    $stuNo = 0;
    $cName = 0;
    $eName = 0;
    $gender = 0;
    $birthDate = 0;
    $idNo = 0;
    $address = 0;
    
    //教職員
    $empNo = 0;
    //$cName = 0;
    //$eName = 0;
    //$gender = 0;
    $deptSn = 0;
    $titlesSn = 0;
    //$birthDate = 0;
    //$idNo = 0;
    $conTel = 0;
    //$address = 0;
    //$eMail = 0;
    $takeOfficeDate = 0;
    $birthplace = 0;
    $maritalStatus = 0;
    $bloodType = 0;
    $stature = 0;
    $weight = 0;
    $urgentName = 0;
    $urgentTel = 0;
    $dutyFlag = 0;
    
    //家長
    //$cName = 0;
    //$gender = 0;
    //$birthDate = 0;
    //$idNo = 0;
    //$conTel = 0;
    //$address = 0;
    $eMail = 0;
    $eduExtent = 0;
    $company = 0;
    
    /*
    $excelFile = "InputExcel_1_10_1330674769981.xls";
    $inputExcelType = "1";
    $creator = "Admin";
    */
    
    //判斷是否有值
    if (trim($excelFile) != "" && trim($inputExcelType) != ""){
        
        ini_set("memory_limit","1024M");  //設定php可使用的最大記憶體量，如果你的 Excel 只是幾十KB的話，可以將這個值設小一點。
        require_once(dirname(__FILE__)."/../PHPExcel/Classes/PHPExcel/IOFactory.php");
    
        $objPHPExcel = PHPExcel_IOFactory::createReader('Excel5'); //設定為舊 Excel 版本相容
        $objPHPExcel = PHPExcel_IOFactory::load("../dbUpLoadFiles/TeachersFolder/".$excelFile);  //設定要讀取的檔案
        $objWorksheet = $objPHPExcel->getSheet(0);  //設定讀取第一個頁面
        $array_out[] = null; //建立一個空 array

        header('Content-Type:text/plain; charset=utf-8');
    
        foreach ($objWorksheet->getRowIterator() as $row_key => $row){  //開始做 row 迴圈
            $cellIterator = $row->getCellIterator();     //抓取這一行的 cell 資訊
            $cellIterator->setIterateOnlyExistingCells(false);  //讀入整行的cells,如果為空就回傳 null
            foreach ($cellIterator as $cell_key => $cell){  //做 cell 迴圈                
                if ($row_key==1){
                    switch ($inputExcelType){
                    case "1":
                        switch ($cell->getValue()){
                        case "stuNo":
                            $stuNo = $cell_key;
                            break;
                        case "cName":
                            $cName = $cell_key;
                            break;
                        case "eName":
                            $eName = $cell_key;
                            break;
                        case "gender":
                            $gender = $cell_key;
                            break;
                        case "birthDate":
                            $birthDate = $cell_key;
                            break;
                        case "idNo":
                            $idNo = $cell_key;
                            break;
                        case "address":
                            $address = $cell_key;
                            break;
                        }
                        break;
                    case "2":
                        switch ($cell->getValue()){
                        case "empNo":
                            $empNo = $cell_key;
                            break;
                        case "cName":
                            $cName = $cell_key;
                            break;
                        case "eName":
                            $eName = $cell_key;
                            break;
                        case "deptSn":
                            $deptSn = $cell_key;
                            break;
                        case "titlesSn":
                            $titlesSn = $cell_key;
                            break;
                        case "gender":
                            $gender = $cell_key;
                            break;
                        case "birthDate":
                            $birthDate = $cell_key;
                            break;
                        case "idNo":
                            $idNo = $cell_key;
                            break;
                        case "conTel":
                            $conTel = $cell_key;
                            break;
                        case "address":
                            $address = $cell_key;
                            break;
                        case "eMail":
                            $eMail = $cell_key;
                            break;
                        case "takeOfficeDate":
                            $takeOfficeDate = $cell_key;
                            break;
                        case "birthplace":
                            $birthplace = $cell_key;
                            break;
                        case "maritalStatus":
                            $maritalStatus = $cell_key;
                            break;
                        case "bloodType":
                            $bloodType = $cell_key;
                            break;
                        case "stature":
                            $stature = $cell_key;
                            break;
                        case "weight":
                            $weight = $cell_key;
                            break;
                        case "urgentName":
                            $urgentName = $cell_key;
                            break;
                        case "urgentTel":
                            $urgentTel = $cell_key;
                            break;
                        case "dutyFlag":
                            $dutyFlag = $cell_key;
                            break;
                        }
                        break;
                    case "3":
                        switch ($cell->getValue()){
                        case "cName":
                            $cName = $cell_key;
                            break;
                        case "gender":
                            $gender = $cell_key;
                            break;
                        case "birthDate":
                            $birthDate = $cell_key;
                            break;
                        case "idNo":
                            $idNo = $cell_key;
                            break;
                        case "conTel":
                            $conTel = $cell_key;
                            break;
                        case "address":
                            $address = $cell_key;
                            break;
                        case "eMail":
                            $eMail = $cell_key;
                            break;
                        case "eduExtent":
                            $eduExtent = $cell_key;
                            break;
                        case "company":
                            $company = $cell_key;
                            break;
                        }
                        break;
                    }
                }
                $array_out[$row_key][$cell_key] = $cell->getValue().''; //將每一個row 的 cell 寫入 array
                //$array_out[$cell->getValue()][$row_key] = $cell->getValue().''; //將每一個row 的 cell 寫入 array
                //上面會看到最後有 ." 是為了要強破 array 寫入的是字串，不然到時候 array 裡存的會是 object
            }
            
            if ($row_key!=1){

                switch ($inputExcelType){
                case "1":
                    if (trim($startClassSn) != ""){
                        $ts_sql = "INSERT INTO Student(stuNo, cName, eName, gender, birthDate, idNo, address, enrollFlag, creator, createDate) 
                                VALUES('".$array_out[$row_key][$stuNo]."', '".$array_out[$row_key][$cName]."', '".$array_out[$row_key][$eName]."',
                                '".$array_out[$row_key][$gender]."', STR_TO_DATE('".$array_out[$row_key][$birthDate]."', '%Y.%m.%d'), 
                                '".$array_out[$row_key][$idNo]."', '".$array_out[$row_key][$address]."', 1, '".$creator."', CURRENT_TIMESTAMP())";
                        $tn_row = $t_db->exeUpdate($ts_sql);
                        if ($tn_row>=0){
                            $ts_sql1 = "SELECT MAX(sn) AS MaxSn 
                                        FROM Student
                                        WHERE stuNo = '".$array_out[$row_key][$stuNo]."'";
                            $t_row1 = Fun::readRow($ts_sql1);
                            if ($t_row1){
                                $ts_sql2 = "INSERT INTO StudentStatus(startClassSn, studentSn) 
                                            VALUES(".$startClassSn.", ".$t_row1["MaxSn"].")";
                                $t_db->exeUpdate($ts_sql2);
                            }
                            $i++;
                        }
                    }
                    
                    break;
                case "2":    
                        $ts_sql = "INSERT INTO Staff(
                                empNo, cName, eName, 
                                deptSn, titlesSn,
                                gender, birthDate, idNo, 
                                conTel, address, eMail, takeOfficeDate, 
                                birthplace, maritalStatus, bloodType, 
                                stature, weight, urgentName, 
                                urgentTel, dutyFlag, creator, createDate) VALUES(
                                '".$array_out[$row_key][$empNo]."', '".$array_out[$row_key][$cName]."', '".$array_out[$row_key][$eName]."',
                                '".$array_out[$row_key][$deptSn]."', '".$array_out[$row_key][$titlesSn]."',
                                '".$array_out[$row_key][$gender]."', STR_TO_DATE('".$array_out[$row_key][$birthDate]."', '%Y.%m.%d'), '".$array_out[$row_key][$idNo]."',
                                '".$array_out[$row_key][$conTel]."', '".$array_out[$row_key][$address]."', '".$array_out[$row_key][$eMail]."', STR_TO_DATE('".$array_out[$row_key][$takeOfficeDate]."', '%Y.%m.%d'), 
                                '".$array_out[$row_key][$birthplace]."', '".$array_out[$row_key][$maritalStatus]."', '".$array_out[$row_key][$bloodType]."', 
                                ".$array_out[$row_key][$stature].", ".$array_out[$row_key][$weight].", '".$array_out[$row_key][$urgentName]."', 
                                '".$array_out[$row_key][$urgentTel]."', ".$array_out[$row_key][$dutyFlag].", '".$creator."', CURRENT_TIMESTAMP())";
                        $tn_row = $t_db->exeUpdate($ts_sql);
                        if ($tn_row>=0){
                            $i++;   
                        }
                    break;
                case "3":                
                        $ts_sql = "INSERT INTO Parent(
                                cName, gender, birthDate, 
                                idNo, conTel, address, 
                                eMail, eduExtent, company, 
                                creator, createDate) VALUES(
                                '".$array_out[$row_key][$cName]."', '".$array_out[$row_key][$gender]."', STR_TO_DATE('".$array_out[$row_key][$birthDate]."', '%Y.%m.%d'), 
                                '".$array_out[$row_key][$idNo]."', '".$array_out[$row_key][$conTel]."', '".$array_out[$row_key][$address]."', 
                                '".$array_out[$row_key][$eMail]."', '".$array_out[$row_key][$eduExtent]."', '".$array_out[$row_key][$company]."', 
                                '".$creator."', CURRENT_TIMESTAMP())";
                        $tn_row = $t_db->exeUpdate($ts_sql);
                        if ($tn_row>=0){
                            $i++;   
                        }
                    break;
                }                

            }            
        }
        
        $t_db->close();
        
        if($i==0){
            echo "匯入失敗！請檢查 上傳之檔案欄位資料格式 是否 依照範例檔案格式 填寫。" ;
        }else{
            echo "匯入成功！共計匯入 ". --$row_key . " 筆，成功 " . $i ." 筆。" ;   
        }        
    }else{
        echo "匯入失敗！請重新執行匯入檔案功能。" ;
    }    
?>