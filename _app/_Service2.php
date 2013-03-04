<?php

    //require_once(dirname(__FILE__) . "/../_code/Fun.php"); 
    require_once(dirname(__FILE__) . "/Fun.php"); 


    $t_data = Fun::decode(false);
    session_start();

    $ts_result = "";
    $ts_fun = ($t_data["fun"] === null) ? "" : $t_data["fun"];
    
    //log act
    Fun::logAct('_Service2(' . $ts_fun . ')', '');
    
    switch ($ts_fun)
    {
        case "CheckSession":    //檢查 session 是否存在
            $ts_result = Fun2::hasSession() ? "Y" : "N";
            goto lab_exit;
            
        case "GetLang":         //傳回多國語資料
            //check
            if (!isset($t_data["file"])){
                goto lab_exit;
            }

            $t_code = Fun::jsonByCodeFile($t_data["file"] . ".src");
            $ts_result = ($t_code === null) ? "" : Fun::json($t_code);
            goto lab_exit;
            
        case "AgentId":       //查詢代理人是否存在(登入畫面)
            $ts_sql = "select userSeq from _User where userId='{0}' and agented=1";
            $ts_sql = Fun::format($ts_sql, $t_data["agentId"]);
            $t_row = Fun::readRow($ts_sql);
            $ts_result = ($t_row != null) ? 1 : 0;
            goto lab_exit;
            
        case "ForgetPwd":     //在登入畫面執行忘記密碼功能
            //get user row
            $t_db = new DB();
            $ts_sql = "select userSeq, userName from _User where userId='{0}' and email='{1}'";
            $ts_sql  = Fun::format($ts_sql, $t_data["loginId"], $t_data["email"]);
            $t_user = $t_db->readRow($ts_sql);
            if ($t_user == null){
                $t_db->close();
                $ts_result = "-1";
                goto lab_exit;
            }
                        
            //update new random pwd with md5
            $ts_newPwd = randomPwd(6);
            $ts_md5Pwd = md5($ts_newPwd);
            $ts_sql = "update _User set md5Pwd='{0}' where userSeq={1}";
            $ts_sql = Fun::format($ts_sql, $ts_md5Pwd, $t_user["userSeq"]);
            $t_db->exeUpdate($ts_sql);
            
            //send email
            $t_dw;
            $t_dw["userName"] = $t_user["userName"];
            $t_dw["now"] = Fun::now();
            $t_dw["newPwd"] = $ts_newPwd;
            $ts_mail = Fun::localeFile("ForgetPwd.mail");
            $ts_body = Fun::replaceStrArray(Fun::fileToStr($ts_mail), $t_dw, true);
            $ts_result = (Fun::sendMail("忘記密碼申請(Forget Password)", $ts_body, $t_data["email"]) == 1) ? "1" : "0";
            break;
            
        case "FileExist":     //檔案是否存在, 傳回 1/0
            if ($t_data["file"] == null){
                $ts_result = "0";
            }else{
                $ts_file = Fun::$sDirRoot . $t_data["file"];
                $ts_result = (file_exists(Fun::$sDirRoot . $t_data["file"])) ? "1" : "0";
            }
            goto lab_exit;
            
    }

    lab_exit:
        //header('Content-Type:text/plain; charset=utf-8');
        Fun::utf8();
        echo $ts_result;        
      
  
    //產生隨機字串 for 密碼          
    function randomPwd($pn_len) { 

        $ts_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
        srand((double)microtime()*1000000); 
        
        $i = 0; 
        $ts_pwd = '' ; 
        while ($i < $pn_len) { 
            $tn_num = rand() % 33; 
            $ts_tmp = substr($ts_list, $tn_num, 1); 
            $ts_pwd = $ts_pwd . $ts_tmp; 
            $i++; 
        } 

        return $ts_pwd; 
    }         

?>