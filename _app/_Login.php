<?php

    //require_once(dirname(__FILE__) . "/../_code/Fun.php"); 
    //require_once(dirname(__FILE__) . "/../_code/Fun2.php"); 
    require_once(dirname(__FILE__) . "/Fun.php"); 
    require_once(dirname(__FILE__) . "/Fun2.php"); 

            
    //init
    //Fun::init();
    
    session_start();
    
    
    //define("tsc_loginError", "loginError");     //for flex side 多國語 
    const tsc_loginError = "loginError";

    
    $t_data = Fun::decode(); 
    $ts_sql;
    $ts_loginId = "";
    $ts_agentId;
    $ts_error = "";         //for send root
    $ts_errorCode = "";     //for send root
    $ts_fun = $t_data["fun"];
    $t_row;
    $t_user = array();
    $tb_pwd = true;
    $ts_extInfo = "";  //userid,password 登入失敗時, 會將此資訊寫入log
    switch ($ts_fun)
    {
        case "Login":   //errorCode 的對應文字在 _Login.src
            $t_data["data"] = "LoginUser";
            //$ts_dbPwd = "";
            $ts_db = "";
            $t_user = array();
            $tb_autoLogin = (Fun2::csAutoLoginId != "");
            if ($tb_autoLogin){
                $ts_loginId = Fun2::csAutoLoginId;
                $t_data[Fun::csLoginId] = $ts_loginId;
                //$t_user[Fun::csLoginId] = $ts_loginId;
                //$t_user[Fun::csUserId] = $ts_loginId;
                //$t_user[Fun::csUserName] = $ts_loginId;
            }else{
                //$t_data["data"] = "LoginUser";
                $ts_loginId = $t_data[Fun::csLoginId];
                $ts_extInfo = '(' . $ts_loginId . '/' . $t_data[Fun::csPwd] . ')';
            }
            
            //$ts_dbPwd = "";
            $ts_db = "";
            $ts_sql = Fun2::_getSql($t_data, $ts_db);
            if ($ts_sql == ""){
                //$ts_error = "Not auto login mode, need id/pwd !";
                $ts_error = "LoginUser did not Exist in Fun2, Please check !!";
                //lab_exit($ts_loginId, $ts_fun, $ts_error);
                //return;
                goto lab_exit;
            }else if(strlen($ts_sql) < 20){     //如果傳回的字串長度小於20(自行定義), 系統會認定為錯誤代碼, 必須在 src檔案中定義這個錯誤代碼
                //echo Fun::jsonData("errorCode", $ts_sql);
                $ts_errorCode = $ts_sql;
                goto lab_exit;
                //return;
            }
            
            $t_row = Fun::readRow($ts_sql);
            if ($t_row == null)
            {
                $ts_errorCode = tsc_loginError;
                //lab_exit($ts_loginId, $ts_fun, $ts_error);
                //return;
                goto lab_exit;
            }

            
            //check password if need
            if ($tb_autoLogin){
                $tb_pwd = false;
            }else{
                
                //temp change !!
                //if (ts_dbPwd != "" && ts_dbPwd != Fun::md5(t_data[Fun::csPwd].ToString()))
                //if ($ts_dbPwd != "" && $ts_dbPwd != $t_data[Fun::csPwd]){
                $ts_dbPwd = ($t_row[Fun::csPwd] != null) ? $t_row[Fun::csPwd] : "";
                if ($ts_dbPwd == '' && $t_data[Fun::csPwd] == ''){
                    $tb_pwd = false;
                }else if ($ts_dbPwd != md5($t_data[Fun::csPwd])){      //md5 加密
                    $ts_errorCode = tsc_loginError;
                    //lab_exit($ts_loginId, $ts_fun, $ts_error);
                    //return;
                    goto lab_exit;
                }else{
                    /*
                    //=== 2012-5-12c Malcom add begin ===
                    //檢查是否超過系統使用期限(Setting table)
                    $t_today = strtotime(Fun::today());
                    $t_set = Fun::readRow('select startDate, endDate from Setting order by compId limit 0,1');
                    if (strtotime($t_set['startDate']) > $t_today || strtotime($t_set['endDate']) < $t_today){
                        $ts_errorCode = "overSysDate";
                        goto lab_exit;                        
                        
                    //如果沒有設定起迄, 則不限制 !!
                    }else if (strtotime($t_row['startDate']) == 0 && strtotime($t_row['endDate']) == 0){
                        //do nothing
                        
                    //檢查是否超過用戶使用期限(_User table)
                    }else if (strtotime($t_row['startDate']) > $t_today || strtotime($t_row['endDate']) < $t_today){
                        $ts_errorCode = "overUserDate";
                        goto lab_exit;                        
                    }
                    //=== 2012-5-12c Malcom add end ===
                    */
                    
                    $tb_pwd = true;
                }
                
                //$tb_pwd = ($ts_dbPwd != "");
                //$tb_pwd = ($t_data[Fun::csPwd] != "");
            }

            //case of ok            
            //ts_hasPwd = (ts_dbPwd != "") ? "Y" : "N";
            $ts_agentId = (isset($t_data[Fun::csAgentId])) ? $t_data[Fun::csAgentId] : "";
            if ($ts_agentId == "")
            {
                $t_user[Fun::csLoginId] = $ts_loginId;
                $t_user[Fun::csUserId] = $t_row[Fun::csUserId];
                $t_user[Fun::csUserName] = $t_row[Fun::csUserName];
                $t_user[Fun::csAgentId] = "";
                //t_user[Fun::csIdno] = t_row[Fun::csIdno].ToString();
            }
            else
            {
                $t_user[Fun::csLoginId] = $ts_agentId;     //被代理的人變成使用者
                $t_user[Fun::csUserId] = $ts_agentId;
                $t_user[Fun::csAgentId] = $ts_loginId;     //代理人
                //t_user[Fun::csIdno] = t_row[Fun::csIdno].ToString();
                
                //get user name for agent
                $t_data[Fun::csLoginId] = $ts_agentId;
                $ts_sql = Fun2::_getSql($t_data, $ts_db);
                $t_row = Fun::readRow($ts_sql);                            
                $t_user[Fun::csUserName] = $t_row[Fun::csUserName] . "(代)";
            }                        
            
            //set session                
            //$ts_error = Fun::setSession($t_user, "", $tb_pwd);                        
            $ts_error = Fun::setSession($t_user, 0, $tb_pwd);                        
            break;            
        
        case "Relogin":     //errorCode 的對應文字在 _Fun.src
            //relogin 時傳入的用戶資料是經過轉換的(考慮代理人)
            //$ts_loginTime = $t_data[Fun::csLoginTime];
            $tn_loginTime = $t_data[Fun::csLoginTime];
            //ts_loginId = t_data[Fun::csLoginId].ToString();
            //$ts_agentId = ($t_data[Fun::csAgentId] == null) ? "" : $t_data[Fun::csAgentId];
            $ts_agentId = (isset($t_data[Fun::csAgentId])) ? "" : $t_data[Fun::csAgentId];
            $ts_loginId = ($ts_agentId != "") ? $ts_agentId : $t_data[Fun::csLoginId];
            /*
            $ts_sql = "select userId, hasPwd from _UserLogin " .
                "where userId = '" . $ts_loginId . "' " .
                "and ip = '" . Fun::getIP() . "' " .
                "and dbo.fnDTtoStr(loginDate) = '" . $ts_loginTime . "'";
             */
            $ts_sql = "select userId from _UserLogin2 where userId='{0}' and loginTime={1}";
            $ts_sql = Fun::format($ts_sql, $ts_loginId, $tn_loginTime);
            //"and convert(char, loginDate, 120) = '" + ts_loginTime + "'";

            $t_user = Fun::readRow(Fun::removeDbo($ts_sql));

            if ($t_user == null || $t_user["userId"] != $ts_loginId)   //考慮 sql injection
            {
                //$ts_error = tsc_loginError;
                //lab_exit($ts_loginId, $ts_fun, $ts_error);
                $ts_errorCode = "noLoginRow";  //必須重新登入, 文字內容參考 see _Fun.src noLoginRow
                goto lab_exit;
                //return;
            }
                            
            //t_user = (JsonObject)JsonConvert.Import(t_user["loginInfo"].ToString());
            $tb_pwd = ($t_user["hasPwd"] == "1");
            //
            $t_user = array();
            $t_user[Fun::csLoginId] = $t_data[Fun::csLoginId];
            $t_user[Fun::csUserId] = $t_data[Fun::csUserId];
            $t_user[Fun::csUserName] = $t_data[Fun::csUserName];
            $t_user[Fun::csAgentId] = $ts_agentId;

            $ts_error = Fun::setSession($t_user, $tn_loginTime, $tb_pwd);
            break;
            
        default:
            //tb_sysError = true;
            $ts_error = "Wrong Action in _Login.ashx (" . $ts_fun . ")";
            break;
    }

    //return;
    goto lab_exit;
    
    /*
    if (ts_error != "")
        goto lab_exit;
            
    if (ts_fun == "Relogin")
        p_hc.Response.Write(Fun::jsonData(Fun::csKey, Fun::encode(p_hc.Session[Fun::csKey].ToString(), false)));   //key 值經過加密(old key)!
        
    return;
    */
        
    
lab_exit:
    //log user action.
    Fun::utf8();
    if ($ts_error != ''){
        $ts_error .= $ts_extInfo;
        Fun::logAct($ts_fun, $ts_error);
        echo Fun::jsonError($ts_error);
        
    }else if ($ts_errorCode != ''){
        $ts_error = $ts_errorCode . $ts_extInfo ;
        Fun::logAct($ts_fun, $ts_error);
        echo Fun::jsonData("errorCode", $ts_errorCode);
        
    }else{        
        //call Fun2.afterLogin()
        Fun::logAct($ts_fun, $ts_loginId);
        Fun2::afterLogin();
        
        if ($ts_fun == "Relogin"){
            echo Fun::jsonData(Fun::csKey, Fun::encode($_SESSION[Fun::csKey], true));   //key 值經過加密(old key)!
        }else{
            echo "_"; 
        }        
    }    
//}

?>