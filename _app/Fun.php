<?PHP

//session_start();

require_once(dirname(__FILE__) . "/../Config.php"); 
require_once(dirname(__FILE__) . "/Fun2.php"); 
require_once(dirname(__FILE__) . "/DB.php"); 

error_reporting(E_ALL);
//error_reporting(0);

class Fun{

    
    //=== constant begin ===
    //db type    
    const cnMSSql = 1;
    const cnMySql = 2;
    const cnOracle = 3;

    //��r�ɸ̪��S��r��
    const csRemark = "#";   //����
    const csVar = '$';      //�ܼ�, ������޸� for php
    
    //user, dept, 
    const csUserId = "userId";
    const csLoginId = "loginId";
    const csUserName = "userName";
    const csPwd = "pwd";
    const csAgentId = "agentId";
    const csDeptId = "deptId";
    const csDeptIds = "deptIds";
    //const csFOwners = "fOwners";
    //const csFOwnerTypes = "fOwnerTypes";
    const csFOwners = "aFOwner";
    const csFOwnerTypes = "aFOwnerType";
    const csFDepts = "aFDept";
    
    //others
    //const csFun = "_fun";
    //const csApp = "_app";
    //const csOpenAllMenu = "openAllMenu";
    const csAutoLogin = "autoLogin";
    const csAppList = "appList";
    const csLogin = "login";
    const csDefTable = "defaultTable";
    const csTable = "table";
    const csColName = "colName";
    const csProgRange = "progRange";
    const csOrRangeCond = "orRangeCond";
    const csHtmlCarrier = "<br>";
    const csTextCarrier = "\r\n";     //is standard for c# !!
    const csLoginTime = "loginTime";
    const csMain = "_Main";            //�D�e�� app id
    const csMenu = "menu";             //main menu
    const csSessId = "sessId";             //main menu
    //=== constant end ===
    
    //add by Louis 20120420 - start
    const csImportType = "importType";  
    const csImportFile = "importFile";
    const csImportExts = "txt";
    const csLogDuplicate = "logDup";
    const csDbChar = "utf8";  //Database�y�t 
    //add by Louis 20120420 - end
    
    
    //=== public property begin ===
    public static $bConcat;
    public static $sApp;        //application id
    public static $sLang = "";
    
    //directory
    public static $sDirRoot;
    public static $sDirLog;
    public static $sDirTemp;
    public static $sDirUpload;
    public static $sDirLocale;
    public static $sDirInf;
    
    //other
    //public static $nMaxRow;
    //public static $nRptMaxRow;
    //=== public property end ===
    
    
    //constructor    
    //�]�w�ܼ�
    //public function __construct(){
    public static function init(){
        self::$bConcat = (Fun2::cnDBType == self::cnMySql);    
        self::$sDirRoot = str_replace("_app", "", getcwd());
        self::$sDirLocale = self::$sDirRoot . "_locale/";
        self::$sDirInf = self::$sDirRoot . "_inf/";
        //
        self::$sDirTemp = self::$sDirRoot . "temp/";
        self::$sDirLog = self::$sDirRoot . "log/";
        self::$sDirUpload = self::$sDirRoot . "upload/";
        //$nMaxRow = Config::maxRow;

        //make directory            
        if (!is_dir(self::$sDirTemp))
            mkdir(self::$sDirTemp, 0700);
            
        if (!is_dir(self::$sDirLog))
            mkdir(self::$sDirLog, 0700);
        
        if (!is_dir(self::$sDirUpload))
            mkdir(self::$sDirUpload, 0700);   
            
        //initial Fun2
        Fun2::init();                     
    }

    
    /**
     * �Ǧ^ decode object, �p�G���ۭq�� php, �i�����I�s�����
     * ps_errCode (by reference) ���~��, �t�η|�]�w�o���ܼ�
     * write error log and return null
    */
    public static function getInput(&$ps_errCode){
        //initial 
        $ps_errCode = '';
        
        //decode
        $t_data = Fun::decode();
        
        //init session
        //�ϥ� chrome/firefox �W���ɮ׮�, session Id �|�]��, �������s�ǰe sessionId ����,
        //��ݵ{���|���s���J�o�� session ����ƨ�D��
        if (isset($t_data["sessId"])){      //upload byte array ��, �|�ǤJ�o���ܼ�
            session_id($t_data["sessId"]);  //�����b session_start()���e���� session
        }        
        session_start();
  
        //check session      
        //$ts_error = '';
        if (!Fun2::hasSession()){
            //$ts_error = 'err1';
            $ps_errCode = 'S01';
            goto lab_error;
        }
        
        //check app right, �Ҧ��I�s�����ǤJ app �M fun
        if (!isset($t_data["app"]) || !isset($t_data["fun"])){
            //$ts_error = 'err2';
            $ps_errCode = 'S02';
            goto lab_error;            
        }
        
        $ts_right = self::checkRight($t_data, $t_data["app"]);
        if ($ts_right == ""){
            //$ts_error = 'err3';
            $ps_errCode = 'S03';
            goto lab_error;                       
        }
        
        //�g�J _right
        $t_data['_right'] = $ts_right;
        return $t_data;
        
        
    lab_error:
        //log error
        return null;
    }
    
    
    //method
    public static function service($pb_encrypt){
            
        //init
        //self::init();
        
        //return json_decode(str_replace("\\", "", $_REQUEST["data"]));        
        
        /*
        //���B�z�S�����p
        $t_data = self::decode();
        if (isset($t_data["sessId"])){    //upload��, �|�ǤJ�o���ܼ�
            //self::logFile("se2=" . $t_data["sessId"]);
            session_id($t_data["sessId"]);  //�b session_start()���e���� session
            //session_end();
        //}else{
        //   self::logFile("no session var");            
        }
        
        session_start();
        */
        $ts_errCode = '';
        $t_data = self::getInput($ts_errCode);      //call by reference
        if ($ts_errCode != ''){
            return self::jsonError($ts_errCode);
        }else if ($t_data == null){
            return '';
        }
        
        //$ts_fun = $_REQUEST["fun"];
        $ts_app = $t_data["app"];
        $ts_fun = isset($t_data["fun"]) ? $t_data["fun"] : "" ;
        $ts_right = $t_data['_right'];
        //$tb_hasSession = Fun2::hasSession();
        switch ($ts_fun)
        {
            //move to _Service2.php
            //case "CheckSession":    //�ˬd session �O�_�s�b
            //    return ($tb_hasSession) ? "Y" : "N";

            /*
            case "GetLang":         //�Ǧ^�h��y���
                //check
                //$t_data = self::decode($_REQUEST["data"], $pb_encrypt);
                if (!isset($t_data["file"]))
                    return "";

                $t_code = self::jsonByCodeFile($t_data["file"] . ".src");
                //return ($t_code === null) ? "" : json_encode($t_code);
                
                return ($t_code === null) ? "" : self::json($t_code);
            */
            
            case "getMain":            //called by �D�e��
                //if (!$tb_hasSession){
                //    return self::jsonError("Please Login System First ! (_Main)", false);
                //}else{        
                    //�ǰe�ϥΪ̸�ƤΥ\����e��
                        //csKey => self::encode(p_hc.Session[self::csKey, 1);  //�o�����|���R�Akey�[�K !!
                    $t_row = array();
                    $t_row[self::csLoginId] = $_SESSION[self::csLoginId];
                    $t_row[self::csLoginTime] = $_SESSION[self::csLoginTime];
                    $t_row[self::csUserId] = $_SESSION[self::csUserId];
                    $t_row[self::csUserName] = $_SESSION[self::csUserName];
                    $t_row[self::csDeptId] = $_SESSION[self::csDeptId];
                    $t_row[self::csDeptIds] = $_SESSION[self::csDeptIds];
                    $t_row[self::csMenu] = $_SESSION[self::csMenu];
                    //$t_row[self::csMenu] = "";
                    $t_row[self::csAutoLogin] = (Fun2::csAutoLoginId != "") ? "Y" : "N";
                    //$t_row[self::csOpenAllMenu] = (Fun2::cbOpenAllMenu && Fun2::csAutoLoginId != "") ? "Y" : "N";
                    $t_row[self::csSessId] = session_id();   //�O��session

                    //clear menu session
                    $_SESSION[self::csMenu] = null;

                    //�_�h flex �L�k decode !!
                    //return json_encode($t_data);
                    //$ts_data = self::json($t_row);                    
                    //$ts_data = str_replace(array('"[',']"'), array('[',']'), $ts_data);
                    return str_replace(array('"[',']"'), array('[',']'), self::json($t_row));
                //}
        }


        //�ˬd session
        //if (!tb_hasSession)
        //    return self::jsonError("S01");


        //�ѽX
        //$ts_data = str_replace("\\", "", $_REQUEST["data"]);
        /*
        if ($pb_encrypt){
            $ts_data = Fun2::decrypt($ts_data);
            //if (t_data === null)
            //    return "";
        }
        */
        
        //$t_data = array();
        //$t_data = self::decode(str_replace("\\", "", $_REQUEST["data"]), $pb_encrypt);
        
        //$tt="tt";
        
        /*
        //�ˬd�v��
        $ts_app = isset($t_data["app"]) ? $t_data["app"] : "";
        $ts_right = self::checkRight($t_data, $ts_app);
        if ($ts_right == "")
            return "";
        */
        
        //check input "data" element
        $ts_error = "";
        $ts_inf = isset($t_data["inf"]) ? $t_data["inf"] : $ts_app;


        $ts_result = "";
        switch ($ts_fun)
        {
            case "Json":        //return json string format.
            case "Var":         //get server variables with string format.
            case "UpdateDB":
            case "SetSession":
            case "SQL":         //return sql query result of json array string.
                if (!isset($t_data["data"]) || $t_data["data"] == null){                    
                    $t_data["data"] = $ts_fun;
                }

                $ts_db = "";
                $ts_result = Fun2::_getSql($t_data, $ts_db);
                
                //�Ǧ^�Ŧr�ꪺ���p
                if ($ts_result == "")
                {
                    switch ($ts_fun)
                    {
                        case "SetSession":
                        case "UpdateDB":
                        case "Var":
                            return "";
                        default:
                            $ts_error = "Fun2::_getSql() Return Empty for [data] = " . $t_data["data"] . (isset($t_data["type"]) ? " and [type] = " . $t_data["type"] : "") . " !";
                            goto lab_error;
                    }
                }
                
                //if (ts_result == "")
                if ($ts_fun == "SetSession"){
                    return "";
                    
                }else if ($ts_fun == "UpdateDB"){
                    //2012-1-17
                    if (strpos(strtolower($ts_result), "insert ")){
                        return self::insertIdent($ts_result);                        
                    }else{
                        return self::exeUpdate($ts_result);                        
                    }
                    
                }else if ($ts_fun == "Var"){
                    $ts_result = self::jsonData("data", $ts_result);
                    
                }else if ($ts_fun == "SQL"){
                    $ta_row;
                    if (!isset($t_data["isJson"]) || $t_data["isJson"] == "1"){
                        $ta_row = self::readRowsByDB($ts_db, $ts_result);
                    }else{
                        //temp remark
                        //$ta_row = self::readArray($ts_db, $ts_result);
                    }

                    $ts_result = ($ta_row === null) ? "" : self::json($ta_row);
                }
                break;

            case "QueryList":   //return query result for list window with inf. 
            case "Excel":       //output excel file with inf.
                //int tn_funRange = Convert.ToInt32(t_data["range"]);
                //$ts_list = (isset($t_data["list"])) ? $t_data["list"] : "list";
                $tn_range = intval(substr($ts_right, 1, 1));   //CRUDP: inquiry !
                if ($ts_fun == "QueryList")
                {
                    $ts_result = self::queryList(true, $ts_inf, $t_data, $tn_range);
                }
                else
                {
                    //$ts_table = ($t_data["table"] != null) ? $t_data["table"] : $ts_inf;
                    //$ts_row1 = ($t_data["row1"] != null) ? $t_data["row1"] : "";
                    $ts_result = self::queryList(false, $ts_inf, $t_data, $tn_range);
                }
                break;
                
            case "QueryEdit":   //return query result for edit window with inf.
                //$ts_result = self::queryEdit($ts_inf, $t_data["data"]);
                $ts_result = self::queryEdit($ts_inf, $t_data["data"], $t_data["send"]);
                break;

            case "GenExcel3":
                break;
                
            case "C":   //create, same as client side is_fun variables
            case "U":   //update
            case "D":   //delete
                //ts_result = self::updateDB(ts_fun, ts_inf, ts_item, null);
                $ts_result = self::updateDB($t_data, null);
                //$ts_result = self::updateDB($t_data, null, $t_data["send"]);
                break;
            case "Config":   //get flow jobs
                $ts_result = self::config($t_data["fid"]);
                break;

            /*
            case "readFlowJobs":   //get flow jobs
                ts_result = Fun2::readFlowJobs(ts_app, t_data);
                break;

            case "Mail":        //send mail
                ts_result = self::sendMailByFile(t_data["template"], t_data);
                if (ts_result != "")
                    ts_result = self::jsonError(ts_result);

                break;
            */

            case "Upload":        //save upload file
                //�����P Flex �t�X !!
                $tc_file = "_file";
                $tc_filePath = "_filePath";
                
                /*
                //firefox �L�kŪ�� session, �ҥH���ˬd�v�� !!
                $ts_filePath = ($t_data["fileName"] != null) ? $t_data["fileName"] : System.IO.Path.GetFileName(p_hc.Request.Files[0].FileName);
                $ts_file = self::sDirUpload + $ts_filePath;
                if (File.Exists(ts_file))
                {
                    //rename old file if exists.
                    $ts_newFile = self::sDirUpload + self::getFileStem(ts_filePath) + "_" + self::genFileTail() + "." + self::getFileExt(ts_filePath);
                    File.Move(ts_file, ts_newFile);
                }

                //save file
                p_hc.Request.Files[0].SaveAs(ts_file);
                */
                //�]�v�������Y, ��b�U���� SWITCH �|���}������
                //�Ψ䥦���q������W��
                //$_SERVER['DOCUMENT_ROOT'] >> ����t�Ϊ��W�h�ؿ�
                
                //$t_data["file"] >> �ɮצs���m
                //$t_data["filePath"] >> ����ɦW
                $ts_tmpFile = $_FILES['file']['tmp_name'];  //�o�̤��O�� _file !!
                if ($t_data[$tc_filePath] != null){
                    $ts_file = $t_data[$tc_filePath];        
                    $ts_dir = dirname(self::$sDirRoot . $ts_file);             
                    if (!file_exists($ts_dir)){
                        mkdir($ts_dir, 0755, true);    //recursive mkdir
                    }
                }else{
                    $ts_file = self::$sDirUpload . basename($ts_tmpFile);
                }
                
                $ts_file = self::$sDirRoot . $ts_file;
                

                //rename old file to file name with today
                //$uploadfile = $uploaddir . basename($t_data["file"]);                
                
                //@copy($temploadfile , $uploadfile);                
                $tb_ok = move_uploaded_file($ts_tmpFile , $ts_file);
                
                //echo "";
                break;
                
            case "UploadByteArray":     //�W�� byteArray �ɮ�       
                //save file
                $tc_filePath = "_filePath";
                $ts_file = $t_data[$tc_filePath];        
                $ts_dir = dirname(self::$sDirRoot . $ts_file);             
                if (!file_exists($ts_dir)){
                    mkdir($ts_dir, 0755, true);    //recursive mkdir
                }
                $ts_file = self::$sDirRoot . $ts_file;    
                $t_file = fopen($ts_file, 'wb');
                fwrite($t_file, $GLOBALS['HTTP_RAW_POST_DATA']);
                fclose($t_file);
                break;
                
            //add by Louis 20120420 - start
            case "ImportDB":
                $ta_data = self::importDB($t_data);
                $ts_result = ($ta_data === null) ? "" : self::json($ta_data); 
                break;
            //add by Louis 20120420 - end

            default:
                $ts_error = "[fun] Parameter = " . $ts_fun . " is Wrong in _Service app !";
                goto lab_error;
                //return;                
        }

        //response menu info and clear session
        return $ts_result;
    //}
        
    lab_error:
        //send error email to root
        //self::sendRoot("E", ts_error);

        return self::jsonSysError($ts_error);        
        
    }

    
    //add by Louis 20120420 - start
    /**
    * �N�HTab(\t)���j�C�������쪺text�ɶפJ��database�A�@��text�ɶפJ�@��table
    */
    public static function importDB($p_data) {
        $ta_data = array();
        $ts_type = isset($p_data["type"]) ? $p_data["type"] : "" ;
        
        switch($ts_type) {
            case "UploadFile":
                $ts_impType = isset($p_data["impType"]) ? $p_data["impType"] : "";
                $ts_logDup = isset($p_data["logDup"]) ? $p_data["logDup"] : false;
                $ts_tempFile = $_FILES['file']['tmp_name'];
                $ts_fileName = $_FILES['file']['name'];
                $tn_dot = strrpos($ts_fileName,'.');
                $ts_fileExts = strtolower(substr($ts_fileName, $tn_dot));
                             
                $i = 0; 
                $ts_prefix = self::$sDirTemp .  date("YmdHis"); 
                do {
                    $i++;
                    $ts_filePath = $ts_prefix . (($i < 10)? '0'.$i:$i) . $ts_fileExts;
                } while(file_exists($ts_filePath));
                
                $tb_ok = move_uploaded_file($ts_tempFile, $ts_filePath);

                $_SESSION[self::csImportType] = $ts_impType;
                $_SESSION[self::csImportFile] = $ts_filePath;
                $_SESSION[self::csLogDuplicate] = $ts_logDup;
                break;
            case "ImportData":
                $ts_impType = (isset($_SESSION[self::csImportType]))? $_SESSION[self::csImportType] : "";
                $ts_filePath = (isset($_SESSION[self::csImportFile]))? $_SESSION[self::csImportFile] : "";
                $tb_logDup = (isset($_SESSION[self::csLogDuplicate]))? $_SESSION[self::csLogDuplicate] : false;
                if ($ts_impType == "" || $ts_filePath == "") {
                    $ta_data['msg'] = "Can not find the session data!!";
                    break;                  
                }
                
                $ts_file = basename($ts_filePath);
                $tn_dot = strrpos($ts_file,'.');
                $ts_fileName = substr($ts_file, 0, $tn_dot);
                $ts_fileExts = substr($ts_file, $tn_dot + 1);
                
                if ($ts_fileExts != self::csImportExts) {
                    $ta_data['msg'] = "Accept only .csv file type!!";
                    break;
                }
                $ts_impFile = self::$sDirImport . $ts_impType . ".imp";
                if (!file_exists($ts_impFile)) {
                    $ta_data['msg'] = "The file ". $ts_impType . ".imp does not exist!!";
                    break;
                }
                
                $t_inf = self::infToJson($ts_impFile);
                if ($t_inf === null) {
                    $ta_data['msg'] = "Some error occurred while reading the file ". $ts_impType . ".imp !!";
                    break;
                }
                
                $t_imp = $t_inf['imp'];
                if (isset($t_imp['field'])) {
                    $tn_field = intval($t_imp['field']);    //txt�ɨC��(��)�������
                } else {
                    $ta_data['msg'] = "Unbound variable 'field' in the file ". $ts_impType . ".imp !!";
                    break;
                }
                
                $ts_char = isset($t_imp['char']) ? $t_imp['char'] : "big5";         //txt�ɤ�r�s�X�榡
                $ts_colSep = isset($t_imp['colSep']) ? $t_imp['colSep'] : "\t";     //�����j�Ÿ�
                $ts_conSep = isset($t_imp['conSep']) ? $t_imp['conSep'] : "\"";     //���e���j�Ÿ�
                $ts_escSign = isset($t_imp['escSign']) ? $t_imp['escSign'] : "\\";  //�S��r������Ÿ�
                $ts_chkSql = isset($t_imp['chkSql']) ? $t_imp['chkSql'] : "";       //�ˬd���и�ƪ�SQL
                $ts_insSql = isset($t_imp['insSql']) ? $t_imp['insSql'] : "";       //�פJ��ƪ�SQL
                $ta_dataType = isset($t_imp['dataType']) ? $t_imp['dataType'] : ""; //txt�ɨC��(��)�����줺�e���A
                $ta_default = isset($t_imp['default']) ? $t_imp['default'] : "";    //txt����줺�e���ťծɪ��w�]��
                $ta_escape = isset($t_imp['escape']) ? $t_imp['escape'] : "";       //������r���~�ݭn�S�O�ഫ���r��(�r��)
                
                if (!file_exists($ts_filePath)) {
                    $ta_data['msg'] = "The import data file does not exist!!";
                    break;
                }
                
                $tn_total = 0;
                $tn_valid = 0;
                $tn_invalid = 0;
                $tn_duplicate = 0;
                $tn_success = 0;
                $tn_fail = 0;
                
                $t_db = new DB();
                if (!$t_db){
                   $ta_data['msg'] = "Cannot create database connection!!";
                   break;   
                }                
                
                $t_file = fopen($ts_filePath, "r");
                while (!feof($t_file)) { 
                    $ts_line = trim(fgets($t_file));
                    if (self::isEmpty(trim($ts_line)))
                        continue; 
                        
                    $tn_total++;
                    $ta_colArray = explode($ts_colSep,$ts_line);
                    $tn_len = count($ta_colArray);
                    if ($tn_len != $tn_field) { //������ƻPfield�]�w���ūh���L�ĸ��
                        $tn_invalid++;
                        $ts_error = "Line ".$tn_total." - Number of columns did not match the variable 'field'!!";
                        self::importLog($ts_fileName, $ts_error, true);
                    }
                    
                    for ($i = 0; $i < $tn_len; $i++) {
                        if (self::isEmpty($ta_colArray[$i])) {
                            $ta_colArray[$i] = NULL;
                            if (isset($ta_default[$i]) && !self::isEmpty($ta_default[$i])) {
                                if (((isset($ta_dataType[$i]) && $ta_dataType[$i] == 'D') || (isset($ta_dataType[$i]) && $ta_dataType[$i] == 'DT')) && $ta_default[$i] == "date")
                                    $ta_colArray[$i] = ($ta_dataType[$i] == 'D') ? self::today : self::now;
                                else
                                    $ta_colArray[$i] = $ta_default[$i];
                            } else if (isset($ta_dataType[$i]) && $ta_dataType[$i] == 'N') {
                                $ta_colArray[$i] = 0;
                            }
                        } else {
                            if ($ts_char != self::csDbChar && (!isset($ta_default[$i]) || (isset($ta_default[$i]) && $ta_default[$i] != 'N')))
                                $ta_colArray[$i] = mb_convert_encoding($ta_colArray[$i], self::csDbChar, $ts_char);
                        }

                        if (!self::isEmpty($ts_conSep)) {
                            $ts_reg = '/^' . $ts_conSep . '.+' . $ts_conSep . '$/';
                            if (preg_match($ts_reg, $ta_colArray[$i]))   //�p�G�����e���j�Ÿ��h�������j�Ÿ�
                                $ta_colArray[$i] = substr($ta_colArray[$i],1,-1);
                        }
                         
                        if (!self::isEmpty($ts_escSign))    
                            $ta_colArray[$i] = str_replace($ts_escSign, "", $ta_colArray[$i]);  //�h������Ÿ�
                        
                        if (!self::isEmpty($ta_escape)) {
                            foreach ($ta_escape as $ta_row) {               //�ഫ������r���~���䥦���ഫ���r��
                                if(count($ta_row) == 2)
                                    $ta_colArray[$i] = str_replace($ta_row[0], $ta_row[1], $ta_colArray[$i]);
                            }
                        }
                        
                        if (isset($ta_dataType[$i]) && $ta_dataType[$i] == 'S2') {  //�ഫS2������޸������޸�
                            $ta_colArray[$i] = str_replace("'","#a#", $ta_colArray[$i] );
                            $ta_colArray[$i] = str_replace("\"","#b#", $ta_colArray[$i]);                                        
                        }
                        
                        $ta_colArray[$i] = self::addQuote($ta_dataType[$i], $ta_colArray[$i]);
                    }
                    $tn_valid++;                //��ƳB�z�����Y�����ĸ��
                    $ta_insArgs = '';
                    $ta_chkArgs = '';           //���i��chkSql, insSql�����Ѽƪ�args���e���P�A�]�����O�إ�
                    switch ($ts_impType) {      //switch�ѯS���޿�B�z�ΡA�̫��n�e��format�B�z���Ѽ�$ta_chkArgs, $ta_insArgs�եX�ӧY�i
                        /*
                        case "TypeName":
                        
                            data preparation...
                            
                            braek;
                        */
                        default:               
                            $ta_insArgs = $ta_colArray;
                            $ta_chkArgs = $ta_colArray;   //�w�]chkSql, insSql���Htext�ɪ���ư���args
                    }
                    
                    $tb_dup = false;
                    if (!self::isEmpty($ts_chkSql)) {
                        if (isset($ta_chkArgs) && count($ta_chkArgs) > 0) {
                            array_unshift($ta_chkArgs, $ts_chkSql); //�N�n�Q�����Ѽƪ�sql��b�}�C��0�Ӥ���
                            $ts_sql = call_user_func_array("self::format", $ta_chkArgs);
                        } else {
                            $ts_sql = $ts_chkSql;
                        }
                        $t_res = $t_db->readRows($ts_sql);
                        if ($t_res === NULL) {  //sql query���~
                            $ta_data['msg'] = "MySQL query error!! - " . mysql_error();
                            break; 
                        }
                        
                        if(count($t_res) > 0) {
                            $tn_duplicate++;
                            $tb_dup = true;     //���и��
                            if ($tb_logDup) {
                                $ts_error = "Line ".$tn_total." - Duplicate entry!!";
                                self::importLog($ts_fileName, $ts_error);                                     
                            }
                        }
                    }
                        
                    if (!$tb_dup && !self::isEmpty($ts_insSql)) {
                        if (isset($ta_insArgs) && count($ta_insArgs) > 0) {
                            array_unshift($ta_insArgs, $ts_insSql); //�N�n�Q�����Ѽƪ�sql��b�}�C��0�Ӥ���
                            $ts_sql = call_user_func_array("self::format", $ta_insArgs);
                        } else {
                            $ts_sql = $ts_insSql;
                        }                        
                        $tb_ok = $t_db->insertIdent($ts_sql);
                        if (self::isEmpty($tb_ok)) {
                            $ts_error = "Line ".$tn_total." - " . mysql_error();
                            self::importLog($ts_fileName, $ts_error, true);                                
                            $tn_fail++;     //�g�J��Ʈw����
                        } else {
                            $tn_success++;  //�g�J���\
                        }
                    }
                }
                fclose($t_file);  
                
                if (!isset($ta_data['msg']) || (isset($ta_data['msg']) && self::isEmpty($ta_data['msg']))) {
                    $ts_report = "Total Rows: " . $tn_total . self::csTextCarrier
                           . "Valid Rows: " . $tn_valid . self::csTextCarrier
                           . "Invalid Rows: " . $tn_invalid . self::csTextCarrier
                           . "Duplicate Rows: " . $tn_duplicate . self::csTextCarrier
                           . "Insert Successful: " . $tn_success . self::csTextCarrier
                           . "Insert Failed: " . $tn_fail;
                           
                    self::importLog($ts_fileName, $ts_report);
                    
                    $ta_data['report'] = array(
                        'total' => $tn_total,
                        'valid' => $tn_valid,
                        'invalid' => $tn_invalid,
                        'duplicate' => $tn_duplicate,
                        'success' => $tn_success,
                        'fail' => $tn_fail
                    );
                    $ta_data['logFile'] = $ts_fileName . ".log";  //�Nlog file path�Ǧ^��Flex���ѤU��
                } 
                break;
            default:
                $ta_data['msg'] = "Request error!!";
        } 
        if (isset($ta_data) && !self::isEmpty($ta_data))
            return $ta_data;  
        else 
            return null;     
    }
    
    public static function importLog($ps_fileName, $ps_msg, $pb_error=false){
        $ts_file = self::$sDirTemp . $ps_fileName . ".log";
        $ts_error = ($pb_error) ? "ERROR: ":"";

        if (!file_exists($ts_file))
            $t_file = fopen($ts_file, "w");
        else
            $t_file = fopen($ts_file, "a");

        fwrite($t_file,  $ts_error . $ps_msg . self::csTextCarrier);
        fclose($t_file);
    }    
    //add by Louis 20120420 - end
    
    
    //return right string
    public static function checkRight($p_data, $ps_app){

        //check local session
        //if (p_hc.Session[self::csUserId] === null)
        /* temp remark
        if (!Fun2::hasSession())
        {
            echo self::jsonError("S01");
            return "";
        }
        */

        //�]�w�����ܼ�
        //self::setGlobal();
        $sApp = $ps_app;

        /*
        //chrome�W���ɮ׮�, session Id �|�]��, ��������, http://www.cnblogs.com/rupeng/archive/2012/01/30/2332427.html
        if ($p_data["session"] != null){
            self::logFile("se2=" . $p_data["session"]);
            session_id($p_data["session"]);
        }else{
            self::logFile("no session var");            
        }
        */
        
        //session_start();
        
        $ts_error = "";
        $ts_right = self::getRight($ps_app);
        if ($ts_right == "")
        {
            /* temp change !!
            $ts_error = "No access right for this app. (" . $ps_app . ")";
            Fun::utf8();
            echo self::jsonError($ts_error);
            return "";
            */
            //�Ȯɤ��ˬd�v�� !!
            self::updateSession();
            return "5555555";            
        }


        //log action if need.
        $ts_fun = isset($p_data["fun"]) ? $p_data["fun"] : "";
        if (strpos("Json,Var,SQL,SQL2,", $ts_fun . ",") === false)    //�]�t ts_fun == ""
        {
            if ($ts_fun != "")
                $ts_fun = "(" . $ts_fun . ")";

            self::logAct($ps_app . $ts_fun, $ts_error);
        }


        //renew session !!
        self::updateSession();

        return $ts_right;
    }

    
    //�Ҧ��{������������ decode, 
    //pb_encrypt: �O�_�[�K
    //public static function decode($ps_text, $pb_encrypt){
    public static function decode($pb_encrypt=true){
        //setGlobal(p_hc);    //set first for getKey() !!
        //JsonObject t_row;
        /*
        if (pn_encryptType == 0 || Fun2::cnEncryptType == 0)
        {
            t_row = self::strToJson(ps_text);
            self::sLang = !self::isEmpty((String)t_row["_lang"]) ? t_row["_lang"] : Fun2::csDefaultLang;
            return t_row;
        }
        */
        
        //initialize !!
        self::init();

        //temp replace
        /*
        $ts_data = str_replace("\\n", "<br>", $_REQUEST["data"]);
        $ts_data = str_replace("\\", "", $ts_data);
        $ts_data = str_replace("\\", "", $_REQUEST["data"]);
        */
        
        //back
        $ts_data = str_replace('\"', '"', $_REQUEST["data"]);   //important!! 2011-12-15
        //$ts_data = str_replace("\\", "", $_REQUEST["data"]);
        try
        {
            if ($pb_encrypt)
                $ts_data = Fun2::decrypt($ts_data);
                
            //�ǰejson�r����ݮ�, �p�G�̭������]�t object/array, �h����쥲���ϥΪ��󪺫���, ���i�A�]���r��, �_�h�L�k���T�ѽX !!            
            //�̭����_��Ÿ���, �L�k���T�ѽX, Flex�ݥ������N���󤺮e�ഫ���r��, �ҥH�o�̥����٭즨����
            $ts_data = str_replace(array('"[',']"','"{','}"'), array('[',']','{','}'), $ts_data);
            $t_row = json_decode($ts_data, true);

            //set self::sLang global variables
            self::$sLang = !self::isEmpty($t_row["_lang"]) ? $t_row["_lang"] : Fun2::csDefaultLang;
            return $t_row;
        }
        catch(Exception $t_e)
        {
            //send root
            logError("self::decode() failed.");
            
            return null;
        }
    }

        
    public static function jsonData($ps_fid, $ps_data)
    {
        $t_data = array(
            $ps_fid=>$ps_data
        );
        return json_encode($t_data);
    }
        

    //return array    
    public static function readRows($ps_sql)
    {
        return self::readRowsByDB("", $ps_sql);
    }

    
    //return array    
    public static function readRowsByDB($ps_db, $ps_sql)
    {
        //connect db
        $t_db = new DB($ps_db);
        if (!$t_db){
            return null;
        }else{
            $ta_row = self::readRowsByConn($t_db, $ps_sql);
            $t_db->close();
            return $ta_row;
        }
    }

    
    //return array    
    public static function readRowsByConn($p_db, $ps_sql)
    {
        return $p_db->readRows($ps_sql);
    }

        
    //public static function sendRoot($ps_type, $ps_msg)
    public static function sendRoot($ps_msg, $pb_status)
    {
        //temp add
        //self::logFile($ps_msg, true);
        

        if (self::isEmpty(Config::rootMail))
        {
            self::logFile("rootMail does not Exist in web.config !", true);
            return;
        }


        $ps_msg = '(' . Config::sysName . ')' . $ps_msg;
        $ts_typeName;
        if ($pb_status){
            $ts_typeName = "Message";
        }else{
            $ts_typeName = "Error";
            $ps_msg = $ps_msg . " (App: " . self::$sApp . ")";
        }

        //send
        self::sendMail($ts_typeName,
            $ps_msg . self::csHtmlCarrier . "By " . self::getUserName(),
            Config::rootMail);

        //return "";
    }

    
    public static function isEmpty($ps_str){
        return ($ps_str === null || $ps_str == "");
    }

        
    public static function logFile($ps_msg, $pb_error=false){
        //$ts_file = self::$sDirLog . date("Y-m-d") . ($pb_error ? "-Error" : "") . ".text";
        $ts_file = self::$sDirLog . self::today() . ($pb_error ? "-Error" : "") . ".text";
        $t_file;
        if (!file_exists($ts_file))
            $t_file = fopen($ts_file, "w");
        else
            $t_file = fopen($ts_file, "a");

        fwrite($t_file, $ps_msg . self::csTextCarrier);
        fclose($t_file);
    }

    
    public static function logError($ps_log)
    {
        //�O���ԲӪ���T
        $ps_log = self::getUserId() . "; " .
            self::getIP() . "; " .
            date("G:i:s") . "; " .
            $ps_log;        
        self::logFile($ps_log, false);

        //self::logFile($ps_log, true);

        //send root
        self::sendRoot($ps_log, false);
    }

    
    public static function jsonError($ps_error){
        return json_encode(array(
            "error"=>$ps_error
        ));
    }
    

    /*    
    public static function setGlobal($pb_dir="")
    {
        //�����ܼ�
        //self::hc = p_hc;

        //����
        if ($pb_dir != ""){
            mkdir(self::$sDirTemp, 0700);
            mkdir(self::$sDirLog, 0700);
            mkdir(self::$sDirUpload, 0700);
        }
    }
    */
    
    
    public static function getRight($ps_app){
        //if (Fun2::cbOpenAllMenu){
        if (Fun2::csAutoLoginId != ""){
            return "1555115";     //CRUDPEV
        }else if($ps_app == self::csMain){    //���\�D�e���n�J��I�s��ݵ{��
            return "0000000";     //CRUDPEV
        }
/*            
//temp add
self::logFile("app=" . $ps_app);
self::logFile("var=" . self::csAppList);
//self::logFile("sess=" . $_SESSION);

//$ts_str = session_id("111");
$ts_str = "";

foreach ($_SESSION as $key => &$value) {
    //echo $value;
    $ts_str .= $key . ":" . $value . ",";
}

self::logFile("sessid=" . session_id());
self::logFile("sess=" . $ts_str);
*/

        //check app Id
        //find app in ap list 
        $ts_right = "0000000";     //initial value !!
        $ts_list = $_SESSION[self::csAppList];
        $tn_pos = strpos($ts_list, "," . $ps_app . ":");
        if ($tn_pos === false)
        {
            return "";
        }
        else
        {
            $tn_pos2 = $tn_pos + strlen($ps_app) + 2;
            if (strlen($ts_list) > $tn_pos && substr($ts_list, $tn_pos2, 1) != ",")
            {
                $ts_right = substr($ts_list, $tn_pos2, 7);   //CRUDPEV
            }
        }

        return $ts_right;
    }

    
    public static function logUserAct($ps_userId, $ps_app, $ps_note){
        self::logFile($ps_userId . "; " .
            self::getIP() . "; " .
            date("G:i:s") . "; " .
            $ps_app . 
            (($ps_note != "") ? "; " . $ps_note : ""), false);
    }


    public static function logAct($ps_app, $ps_note)
    {
        self::logUserAct(self::getUserId(), $ps_app, $ps_note);
    }

    
    public static function getIP(){
        return $_SERVER['REMOTE_ADDR'];;
    }
    
    
    public static function getUserId(){
        return isset($_SESSION[self::csUserId]) ? $_SESSION[self::csUserId] : "";
    }

    
    public static function getUserName()
    {
        try
        {
            return isset($_SESSION[self::csUserName]) ? $_SESSION[self::csUserName] : "";
        }
        catch(Exception $e)
        {
            return "Root";
        }
    }

    
    public static function getLoginId()
    {
        return $_SESSION[self::csLoginId];
    }

    
    public static function getIdno()
    {
        return $_SESSION[self::csIdno];
    }

    
    public static function updateSession()
    {
        $_SESSION[self::csLogin] = "Y";      //refresh session 
    }


    //return string
    public static function queryList($pb_query, $ps_inf, $p_data, $pn_funRange)
    {
        //open inf file and get sql statement
        //Boolean tb_sysError = false;
        $ts_error = "";
        $ts_sql;
        $ts_db = "";
        $ts_data = isset($p_data["data"]) ? $p_data["data"] : "";
        //if (!isset($p_data["useInf"]) || $p_data["useInf"] == "1")
        //{
            $ts_list = isset($p_data["list"]) ? $p_data["list"] : "list";
            //$ts_list = "list";
            $t_inf;
            $ts_error = self::infToJsonSql($ps_inf, $pb_query, $ts_list, $pn_funRange, $ts_data, $t_inf);
            if ($ts_error != "")
                return self::jsonSysError($ts_error);


            //check row count limitation
            $tn_maxRow;
            if ($pb_query)
                $tn_maxRow = isset($t_inf["maxRow"]) ? intval($t_inf["maxRow"]) : Config::maxRow;
            else
            {
                if (isset($t_inf["rptMaxRow"]))
                    $tn_maxRow = intval($t_inf["rptMaxRow"]);
                elseif (isset($t_inf["maxRow"]))
                    $tn_maxRow = intval($t_inf["maxRow"]);
                else
                    $tn_maxRow = Config::rptMaxRow;

                //tn_maxRow = (t_data["rptMaxRow"] != null) ? Convert.ToInt32(t_data["rptMaxRow"]) : self::nRptMaxRow;
            }

            $ts_db = isset($t_inf["db"]) ? $t_inf["db"] : "";

            //count if need
            if ($tn_maxRow > 0)
            {
                //$ts_sql = self::GetCountSql(t_data["from"], "rows");
                $ts_sql = "select count(*) as rows " . $t_inf["from"];

                $t_row = self::readRowByDb($ts_db, $ts_sql);
                if ($t_row === null)
                    return "";
                //return self::jsonError(false, "������U�L������ !");

                $tn_rows = intval($t_row["rows"]);
                if ($tn_rows > $tn_maxRow)
                    return self::jsonError("�̦h�u��d�� " . strval($tn_maxRow) . " ����� !", false);
                else if ($tn_rows == 0)
                    return "";
                //return self::jsonError(false, "������U�L������ !");

            }


            //$ts_conn = (t_list["db"] != null) ? t_list["db"] : "";
            $ts_sql = $t_inf["select"] . " " . $t_inf["from"];
            if (isset($t_inf["order"]))
                $ts_sql = $ts_sql . " " . $t_inf["order"];

        /*
        }
        else  //getSql(): ���ˬd��Ƶ��ƩM�v���d�� !!
        {
            $ts_db2 = "";
            $ts_sql = Fun2::_getSql($p_data, $ts_db2);
            if ($ts_sql == "")
                return self::jsonSysError("Fun2::_getSql() Return Empty for [data] = " . $p_data["data"] . (isset($p_data["type"]) ? " and [type] = " . $p_data["type"] : "") . " !");

        }
        */
        
        if (!$pb_query)
        {
            $ts_table = isset($p_data["table"]) ? $p_data["table"] : $ps_inf;
            $ts_template = isset($p_data["template"]) ? $p_data["template"] : $ps_inf;
            $ts_row1 = isset($p_data["row1"]) ? $p_data["row1"] : "";
            //$ts_settings = ($p_data["settings"] != null) ? $p_data["settings"] : "";

            //return self::genExcel($ts_db, $ts_sql, $ts_template, $ts_table, $ts_row1);
            return self::genExcel2($ts_db, $ts_sql, $ts_template);
        }


        //case of query        
        $ta_row;
        if (!isset($p_data["isJson"]) || $p_data["isJson"] == "1"){
            $ta_row = self::readRowsByDB($ts_db, $ts_sql);
        }else{
            $ta_row = self::readArrayByDb($ts_db, $ts_sql);
        }

        return ($ta_row === null) ? "" : self::json($ta_row);


        /*
        lab_exit:
            if (ts_error == "")
                return "";
            else if (tb_sysError)
                return self::jsonSysError(ts_error);
            else
                return self::jsonError(false, ts_error);
        */

    }

    
    public static function jsonSysError($ps_error){
        //mailto root
        self::logError($ps_error);

        //return json error string
        return self::jsonError($ps_error);
    }
    
    
    public static function infToJsonSql($ps_inf, $pb_query, $ps_fList, $pn_funRange, $pa_qItem, &$p_inf){

        //open inf file and get sql statement
        //Boolean tb_mail = false;
        //JsonObject t_rtn = new JsonObject();    //return object
        $t_inf = self::infToJson($ps_inf);
        $ts_error = "";
        if ($t_inf === null)
        {
            $ts_error = "Can not Open inf File.";
            goto lab_error;
            return;
        }

        //adjust 
        if ($ps_fList == "")
            $ps_fList = "list";


        //=== get sql statement begin ===        
        //set ts_where(where condition) by user input(ps_qItem).
        $t_replace = array();    //for replace string, �ϥΪ̿�J���ȷ|�s�즹�ܼ�, �A�]�w�� inf �ɮפ�
        $t_list = $t_inf[$ps_fList];
        $ts_defTable = isset($t_list[self::csDefTable]) ? $t_list[self::csDefTable] . "." : "";     //default table
        $ts_whereInput = "";
        $ts_and = "";
        $i;
        //if ($pa_qItem != null && $pa_qItem != "" && $pa_qItem != "[]")
        if ($pa_qItem != null && count($pa_qItem) > 0)
        {

            //save inf.items to ta_item[] and get mapping(t_map)
            //$ta_item = json_decode($t_list["items"], true);
            $ta_item = $t_list["items"];
            $t_map;
            for ($i = 0; $i < count($ta_item); $i++){
                $t_map[$ta_item[$i]["fid"]] = $i;
            }


            //element format:[fid,datatype,op,(value,value2)(if need)]
            //$ta_qItem = json_decode($pa_qItem, true);
            $ta_qItem = $pa_qItem;
            $ts_and = "";
            $ts_fid; $ts_value; $ts_ftype;
            $ts_col; $ts_table; $ts_op; $ts_str;
            $tas_value = array();
            $tn_item;
            $t_item;
            $tn_len = ($ta_qItem === null) ? 0 : count($ta_qItem);
            for ($i = 0; $i < $tn_len; $i++)
            {
                //$ta_qItem2 = json_decode($ta_qItem[$i], true);   //�@���d�߱�����
                $ta_qItem2 = $ta_qItem[$i];   //�@���d�߱�����
                $ts_fid = $ta_qItem2[0];
                $tn_item = self::idToNo($t_map, $ts_fid);
                if ($tn_item == -1)
                {
                    $ts_error = "Can not Find Query Item of " . $ts_fid . " in inf File.";
                    goto lab_error;
                    return;
                }

                $t_item = $ta_item[$tn_item];
                $ts_table = (!isset($t_item[self::csTable])) ? $ts_defTable : (($t_item[self::csTable] == "") ? "" : $t_item[self::csTable] . ".");
                $ts_col = $ts_table . (isset($t_item[self::csColName]) ? $t_item[self::csColName] : $ts_fid);
                /*
                if (ts_col.IndexOf(".") < 0)
                {
                    ts_col = ts_table + ts_col;
                }
                */

                $ts_op = isset($t_item["op"]) ? strtolower($t_item["op"]) : "=";
                $ts_ftype = $ta_qItem2[1];
                $ts_value = $ta_qItem2[2];

                //add where 
                $tb_cond;
                if (!isset($t_item["isCond"]) || $t_item["isCond"] == "1")
                {
                    $tb_cond = true;
                    $ts_str = self::qItemToWhere($ts_col, $ts_ftype, $ts_op, $ts_value);
                    if ($ts_str != "")
                    {
                        $ts_whereInput .= $ts_and . $ts_str;
                        $ts_and = " and ";
                    }
                }else{
                    $tb_cond = false;
                }

                //add into t_replace
                if (strpos($ts_op, "between") !== false)
                {
                    $tas_value = explode(',', $ts_value);
                    if (count($tas_value) > 1)
                    {
                        if ($tb_cond){
                            $t_replace[$ts_fid] = self::addQuote($ts_ftype, $tas_value[0]);
                            $t_replace[$ts_fid . "2"] = self::addQuote($ts_ftype, $tas_value[1]);
                        }else{
                            $t_replace[$ts_fid] = $tas_value[0];
                            $t_replace[$ts_fid . "2"] = $tas_value[1];
                        }
                    }
                    else
                    {
                        $t_replace[$ts_fid] = ($tb_cond) ? self::addQuote($ts_ftype, $ts_value) : $ts_value;
                    }
                }
                else
                {
                    $t_replace[$ts_fid] = ($tb_cond) ? self::addQuote($ts_ftype, $ts_value) : $ts_value;
                }
            }
        }


        //consider access right and add to ts_where
        $ts_whereRange = "";
        if (isset($t_inf[self::csProgRange]) && $pn_funRange < 5)
        {
            $tn_progRange = intval($t_inf[self::csProgRange]);   //program range.
            if ($tn_progRange > 0)
            {
                if ($tn_progRange == 1)
                    $pn_funRange = 1;

                //int i;
                $ts_and = "";
                switch ($pn_funRange)
                {
                    case 3:     //cross departments
                    case 2:     //one department
                        if (!isset($t_inf[self::csFDepts]))
                        {
                            $ts_error = "inf File " . self::csFDepts . " can not be Empty!";
                            goto lab_error;
                            return;
                        }

                        $ts_deptId;
                        $ts_op;
                        if ($pn_funRange == 2)
                        {
                            $ts_op = " = ";
                            //ts_deptId = self::hc.Session[Fun2::csDeptId].ToString();
                            $ts_deptId = self::getDeptId();
                            if ($ts_deptId != "")
                            {
                                //if (t_inf[self::csFDeptType] === null || t_inf[self::csFDeptType].ToString() == "S")
                                if (Fun2::cbDeptStr)
                                    $ts_deptId = "'" . $ts_deptId . "'";

                            }
                         }
                        else
                        {
                            $ts_op = " in ";
                            //ts_deptId = self::hc.Session[Fun2::csDeptIds].ToString();
                            $ts_deptId = self::getDeptIds();
                            if ($ts_deptId != "")
                                $ts_deptId = "(" . $ts_deptId . ")";

                        }

                        if ($ts_deptId != "")
                        {
                            $tas_fDept = explode(',', $t_inf[self::csFDepts]);
                            for ($i = 0; $i < count($tas_fDept); $i++)
                            {
                                $ts_whereRange .= $ts_and . $tas_fDept[$i] . $ts_op . $ts_deptId;
                                $ts_and = " or ";
                            }
                        }
                        break;
                        
                    case 1:     //current user
                        if (!isset($t_inf[self::csFOwners]) || !isset($t_inf[self::csFOwnerTypes]))
                        {
                            $ts_error = "inf File " . self::csFOwners . " or " . self::csFOwnerTypes . " can not be Empty!";
                            goto lab_error;
                            //return;
                        }

                        /*
                        $ts_userId = self::getUserId();
                        if (!isset($t_inf[self::csFOwnerType]) || $t_inf[self::csFOwnerType] == "S")
                            $ts_userId = "'" . $ts_userId . "'";
                        */
                        
                        $tas_fOwner = explode(',', $t_inf[self::csFOwners]);
                        $tas_fOwnerType = explode(',', $t_inf[self::csFOwnerTypes]);
                        if (count($tas_fOwner) != count($tas_fOwnerType))
                        {
                            $ts_error = "inf File " . self::csFOwners . " and " . self::csFOwnerTypes . " length not equal!";
                            goto lab_error;                            
                        }
                        
                        //$ts_id;
                        for ($i = 0; $i < count($tas_fOwner); $i++)
                        {
                            if ($tas_fOwnerType[$i] == "U"){    //userId
                                $ts_id = self::getUserId();
                                $tb_str = Fun2::cbUserIdStr;
                            }else{  //loginId
                                $ts_id = self::getLoginId();
                                $tb_str = Fun2::cbLoginIdStr;
                            }
                            if ($tb_str){
                                $ts_id = "'" . $ts_id . "'";
                            }
                            $ts_whereRange .= $ts_and . $tas_fOwner[$i] . "=" . $ts_id;
                            $ts_and = " or ";
                        }
                        break;
                }
            }//if
        }//if tn_progRange


        //add where string by Fun2::getListWhere()
        $ts_whereList = Fun2::_getListWhere($ps_inf);
        

        //=== combine where string to ts_where begin ===
        $ts_where = "";
        $ts_and = "";
        if ($ts_whereInput != "")
        {
            $ts_where .= $ts_and . "(" . $ts_whereInput . ")";
            $ts_and = " and ";
        }
        if ($ts_whereList != "")
        {
            $ts_where .= $ts_and . "(" . $ts_whereList . ")";
            $ts_and = " and ";
        }

        //consider orRangeCond only ts_whereRange != ""
        if ($ts_whereRange != "" && isset($t_inf[self::csOrRangeCond]))
            $ts_whereRange .= " or " . $t_inf[self::csOrRangeCond];

        if ($ts_whereRange != "")
            $ts_where .= $ts_and . "(" . $ts_whereRange . ")";

        //=== end ===


        //replace select [_where] by where string
        $tc_select = "select"; 
        $tc_from = "from"; 
        $tc_order = "order";
        if (!$pb_query)
        {
            if (isset($t_list["rptSelect"]))
                $tc_select = "rptSelect";

            if (isset($t_list["rptFrom"]))
                $tc_from = "rptFrom";

            if (isset($t_list["rptOrder"]))
                $tc_order = "rptOrder";
        }


        //string ts_tag = (pb_query || t_list["rptSelect"] === null) ? "select" : "rptSelect" ;
        //string ts_from = self::sqlAddDB(t_list[tc_from].ToString(), t_replace);
        //string ts_select = self::sqlAddDB(t_list[tc_select].ToString(), t_replace);
        $ts_from = self::replaceStrArray($t_list[$tc_from], $t_replace, false);
        $ts_select = self::replaceStrArray($t_list[$tc_select], $t_replace, false);
        if ($ts_where != ""){
            $ts_where = (strpos(strtolower($ts_from), " where ") === false ? " where " : " and ") . $ts_where;
        }
        if (strpos($ts_from, "[_where]") !== false){
            $ts_from = str_replace("[_where]", $ts_where, $ts_from);
        }else{
            $ts_from .= $ts_where;
        }

        /* cancel !!
        if (t_list["select2"] != null)
        {
            ts_sql += " " + t_list["select2"].ToString();
        }
        */

        //p_inf["sql"] = ts_sql;
        $p_inf["select"] = self::replaceStrArray($ts_select, $t_replace, false);  //inf �i�H�����ܼ� !!
        $p_inf["from"] = self::replaceStrArray($ts_from, $t_replace, false);      //inf �i�H�����ܼ� !!

        if (isset($t_list[$tc_order]))
            $p_inf["order"] = $t_list[$tc_order];

        if (isset($t_list["db"]))
            $p_inf["db"] = $t_list["db"];

        if (isset($t_list["maxRow"]))
            $p_inf["maxRow"] = intval($t_list["maxRow"]);

        if (isset($t_list["rptMaxRow"]))
            $p_inf["rptMaxRow"] = intval($t_list["rptMaxRow"]);

        return "";
    //}

    lab_error:
        $p_inf = null;
        $ts_error .= " (Inf: " . $ps_inf . ")";
        self::logError($ts_error);        //send root
        return $ts_error;
    }


    //return row
    public static function readRowByConn($p_db, $ps_sql){
        $ta_data = self::readRowsByConn($p_db, $ps_sql);
        return ($ta_data === null) ? null : $ta_data[0];
    }

    /// <summary>
    /// �Q�άY�� sql ?�z��Ū���@�����, �åB�Ǧ^ JsonObject.
    /// </summary>
    /// <param name="p_conn">connection �ܼ�, �p�G null, �h�۰ʳs����Ʈw</param>
    /// <param name="ps_sql">sql ?�z</param>
    /// <returns>JsonObject ���</returns>
    public static function readRowByDb($ps_db, $ps_sql)
    {
        $ta_data = self::readRowsByDB($ps_db, $ps_sql);
        return ($ta_data === null) ? null : $ta_data[0];
    }


    /// <summary>
    /// �Q�άY�� sql ?�z��Ū���@�����, �åB�Ǧ^ JsonObject.
    /// </summary>
    /// <param name="ps_db">web.config �̪��ܼƦW��, �p�G�ť�, �h�ϥιw�]��</param>
    /// <param name="ps_sql">sql ?�z</param>
    /// <returns>JsonObject ���</returns>
    public static function readRow($ps_sql)
    {
        $ta_data = self::readRows($ps_sql);
        return ($ta_data == null) ? null : $ta_data[0];
    }

    
    /*
     * �Ninfo�ɮ��ഫ�� json �ܼ�
     * @param ps_inf �w�]�� info �ɮ�(���t���ɦW, �ϥιw�]���|), �p�G�t���ɮ�, �h�����]�t���㪺���|
     * @return json �ܼ�
     */
    public static function infToJson($ps_inf)
    {

        if (strpos($ps_inf, '.') === false)
            $ps_inf = self::$sDirInf . $ps_inf . ".info";
        
        //if file not found, return error msg
        //$ts_file = self::$sDirInf . $ps_inf . ".info";      //query record
        if (!file_exists($ps_inf))
            return null;


        //read file into json object variables
        //�� utf-8 �~���|���ýX!!
        //Encoding t_encode = Encoding.GetEncoding("utf-8");
        //StreamReader t_file = new StreamReader(ts_file, t_encode);
        $t_file = fopen($ps_inf, "r");
        //
        //StringReader t_file = new StringReader(ps_from);  //will contain carrier char and cause error
        //string ts_str = t_file.ReadToEnd();
        $ts_str = "";
        //$ts_line;
        while (!feof($t_file))
        {
            $ts_line = trim(fgets($t_file));
            if (strlen($ts_line) >= 2 && substr($ts_line, 0, 1) == self::csRemark)
            {
                continue;
            }
            else
            {
                $ts_str .= $ts_line . ' ';
            }
        }
        fclose($t_file);


        //add "{" and "}" if need
        if (substr($ts_str, 0, 1) != "{")
            $ts_str = "{" . $ts_str . "}";


        //get inf
        try
        {
            return json_decode($ts_str, true);
            //return json_decode(self::normalJson($ts_str), true);
        }
        catch(Exception $e)
        {
            return null;
        }

    }


    public static function idToNo($p_map, $ps_fid)
    {
        return (!isset($p_map[$ps_fid])) ? -1 : intval($p_map[$ps_fid]);
    }


    public static function qItemToWhere($ps_column, $ps_type, $ps_op, $ps_value)
    {
        if (self::isEmpty($ps_value))
            return "";


        //set operator and value
        $tas_value = array();
        $ts_op = strtolower($ps_op);
        //$tb_str = ($ps_type == "S" || $ps_type == "D") ? true : false;
        $tb_str = ($ps_type != "N");
        if (strtolower($ps_value == "null"))
            $ts_op = ($ts_op == "=") ? "is" : "is not";


        switch ($ts_op)
        {
            case "like":
            case "not like":
                $ps_value = "'" . $ps_value . "%'";
                break;

            case "like2":
                $ts_op = "like";    //�վ�
                $ps_value = "'%" . $ps_value . "%'";
                break;

            case "between":
            case "not between":
                $tas_value = explode(',', $ps_value);
                if (count($tas_value) == 1)
                {
                    $ts_op = "=";
                    $ps_value = self::addQuote($ps_type, $ps_value);
                    /*
                    if ($tb_str)
                        $ps_value = "'" . $tas_value[0] . "'";
                    else
                        $ps_value = $tas_value[0];
                    */
                }
                else
                {
                    $ps_value = self::addQuote($ps_type, $tas_value[0]) . " and " . self::addQuote($ps_type, $tas_value[1]);
                    /*
                    if ($tb_str)
                        $ps_value = "'" . $tas_value[0] . "' and '" . $tas_value[1] . "'";
                    else
                        $ps_value = $tas_value[0] . " and " . $tas_value[1];
                    */ 
                }
                break;

            case "in":
            case "not in":
                if ($tb_str)
                {
                    $tas_value = explode(',', $ps_value);
                    $ts_value = "";
                    $ts_comm = "";
                    for ($j = 0; $j < count($tas_value); $j++)
                    {
                        //�Ҽ{�r��M�Ʀr ??
                        $ts_value = $ts_value . $ts_comm . "'" . $tas_value[$j] . "'";
                        $ts_comm = ",";
                    }
                    $ps_value = $ts_value;
                }

                $ps_value = "(" . $ps_value . ")";
                break;


            //=== �S����, ���� return, begin ===
            case "fill":
                return "(" . self::format($ps_column, $ps_value) . ")";

            //=== �S���� end ===


            default:
                if ($tb_str)
                    $ps_value = "'" . $ps_value . "'";

                break;
        }

        return "(" . $ps_column . " " . $ts_op . " " . $ps_value . ")";
    }

    
    //��Y�Ӧr����ݭn�[�W��޸�, �p�G ps_type = 'T', �����N����r���ഫ��unix time
    public static function addQuote($ps_type, $ps_value)
    {
        if ($ps_type == "N" || strtolower($ps_value) == "null"){
            return $ps_value;
        }else if ($ps_type == "T"){
            return strval(strtotime($ps_value));            
        }else{
            return  "'" . $ps_value . "'";
        }
    }

    
    //�r��[�W���޸�
    public static function addQuote2($ps_str)
    {
        return '"' . $ps_str . '"';
    }

    public static function listAddQuote($ps_list)
    {
        return "'" . str_replace(",", "','", $ps_list) . "'";
    }

    
    public static function getDeptId()
    {
        return isset($_SESSION[self::csDeptId]) ? $_SESSION[self::csDeptId] : "";
    }


    public static function getDeptIds()
    {
        return isset($_SESSION[self::csDeptIds]) ? $_SESSION[self::csDeptIds] : "";
    }

    
    //http://zh-tw.w3support.net/index.php?db=so&id=1241177
    public static function format() {
        $args = func_get_args();     
        if (count($args) == 0)
            return;     
                 
        if (count($args) == 1)
            return $args[0];
            
        $str = array_shift($args);     
        $str = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', create_function('$match', '$args = '.var_export($args, true).'; return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];'), $str);     
        return $str; 
    }     


    //�Ҽ{�h��y
    public static function localeFile($ps_file)
    {

        //string ts_lang;
        $ts_file = self::$sDirLocale . self::$sLang . "/" . $ps_file;
        if (file_exists($ts_file))
            return $ts_file;
        else if (self::$sLang != Fun2::csDefaultLang)
        {
            $ts_file = self::$sDirLocale . Fun2::csDefaultLang . "/" . $ps_file;
            if (file_exists($ts_file))
                return $ts_file;

        }

        //case of file not exist !!
        //mail root
        self::logError($ts_file . " did not Exist!");
        return "";
    }

    
    public static function jsonByCodeFile($ps_file)
    {
        $ts_file = self::localeFile($ps_file);
        if ($ts_file == "")
            return null;

        $ts_line;
        $t_row = array();
        $t_file = fopen($ts_file, "r");
        while (!feof($t_file))
        {
            $ts_line = trim(fgets($t_file));
            if ($ts_line == "" || substr($ts_line, 0, 1) == self::csRemark){
                continue;
            }

            //temp add    
            //echo $ts_line;
            //return;
            //$ts_line = "create=�s�W";
                
            $tn_pos = strpos($ts_line, "=");
            if ($tn_pos === false){
                continue;
            }

            $t_row[substr($ts_line, 0, $tn_pos)] = substr($ts_line, $tn_pos + 1);
            
            //break;
        }
        fclose($t_file);
        return $t_row;    
    }

    
    //public static function zz_sendMail($ps_subject, $ps_body, $ps_to, $ps_cc, $p_smtp)
    public static function sendMail($ps_subject, $ps_body, $ps_toBox, $ps_charSet="utf-8"){

        //include    
        //include_once VIP_ROOT.'/plus/PHPMailer/class.phpmailer.php';
        //require_once(dirname(__FILE__) . '/PHPMailer/class.phpmailer.php'); 
        require_once(dirname(__FILE__) . '/../Bin/PHPMailer/class.phpmailer.php'); 

        //�H��B�z
        $t_mail= new PHPMailer(); //�إ߷s����
        $t_mail->IsSMTP(); //�]�w�ϥ�SMTP�覡�H�H
        if (Config::smtpSSL == 1){
            $t_mail->SMTPAuth = true;
            $t_mail->SMTPSecure = "ssl"; // Gmail��SMTP�D���ݭn�ϥ�SSL�s�u        
        }else{
            $t_mail->SMTPAuth = false;
        }
        
        $t_mail->Host = Config::smtpHost;
        //$t_mail->Port = Config::smtpPort;
        $t_mail->Username = Config::smtpUserId;
        $t_mail->Password = Config::smtpPwd;
        $t_mail->CharSet = $ps_charSet; //�]�w�l��s�X
        
        $t_mail->From = Config::smtpFromBox;
        $t_mail->FromName = Config::smtpFromName;
        $t_mail->Subject = $ps_subject; //�]�w�l����D
        $t_mail->Body = $ps_body; //�]�w�l�󤺮e
        $t_mail->IsHTML(true); //�]�w�l�󤺮e��HTML
        $t_mail->AddAddress($ps_toBox, "user"); //�]�w����̶l��ΦW��

        if($t_mail->Send()) {
            return 1;
        } else {
            return 0;
        }
    }


    /*
    public static function sendMailByFile($ps_file, $p_dw)
    {
        if (strpos($ps_file, ".") < 0){
            $ps_file = Fun::$sDirLocale . $ps_file . "mail";
        }
        if (!self::localeFile($ps_file)){
            return "";
        }
        
        $ts_str = self::replaceStrArray(self::fileToStr($ps_file), $p_dw);
        $t_data = self::strToJson($ts_str);
        self::sendMail($t_data["subject"])
        JsonObject $t_mail = Fun.mailFileToJson($ps_template);
        if ($t_mail == null)
            return "Fun.mailFileToJson() failed, (" + $ps_template + ")";
        else
            return sendMailByJson($t_mail, $pa_dw, null);

    }
    */
    

/*    
    public static function sendMailByJson(JsonObject $p_mail, JsonObject $p_dw, SmtpClient $p_smtp)
    {
        JsonArray $ta_dw = new JsonArray();
        $ta_dw.Add($p_dw);
        return sendMailByJson($p_mail, $ta_dw, $p_smtp);
    }


    public static function sendMailByJson(JsonObject $p_mail, JsonArray $pa_dw, SmtpClient $p_smtp)
    {

        //send Mail
        //string ts_to = (p_mail[Fun.csToUsers] != null) ? Fun.jsonToMailBoxes((JsonObject)p_mail[Fun.csToUsers], pa_dw) : "";
        //string ts_cc = (p_mail[Fun.csCCUsers] != null) ? Fun.jsonToMailBoxes((JsonObject)p_mail[Fun.csCCUsers], pa_dw) : "";
        string $ts_to = ($p_mail[Fun.csToUsers] != null) ? Fun.jsonToMailBoxes($p_mail[Fun.csToUsers].ToString(), $pa_dw) : "";
        string $ts_cc = ($p_mail[Fun.csCCUsers] != null) ? Fun.jsonToMailBoxes($p_mail[Fun.csCCUsers].ToString(), $pa_dw) : "";

        return Fun.sendMail($p_mail["subject"].ToString(),
            Fun.replaceStr($p_mail["body"].ToString(), $pa_dw, true),
            $ts_to, $ts_cc, $p_smtp);
    }
*/

    
/**************************************************************
 *
 *    �ϥίS�wfunction???���Ҧ�������?�z
 *    @param    string    &$array        �n?�z���r�Ŧ�
 *    @param    string    $function    �n?�檺��?
 *    @return boolean    $apply_to_keys_also        �O�_�]?�Ψ�key�W
 *    @access public
 *
 *************************************************************/
//function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
private static function arrayRecursive(&$array, $function)
{
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            //arrayRecursive($array[$key], $function, $apply_to_keys_also);
            self::arrayRecursive($array[$key], $function);
        } else {
            $array[$key] = $function($value);
        }
 
        /*
        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
        */
    }
    $recursive_counter--;
}

 
    /**************************************************************
     *
     *    ??????JSON�r�Ŧ�]�ݮe����^
     *    @param    array    $array        �n??��??
     *    @return string        ??�o�쪺json�r�Ŧ�
     *    @access public
     *
     *************************************************************/
    public static function json($pa_1) {
        self::arrayRecursive($pa_1, 'urlencode');
        $ts_json = json_encode($pa_1);
        return urldecode($ts_json);
        //return $json;
    }

    
    //set session after user login
    //return error msg if any.    
    public static function setSession($p_data, $pn_loginTime, $pb_hasPwd){
    //pn_loginMode 1(login with pwd), 2(login without pwd), 3(relogin)
    //public static function setSession($p_data, $pn_loginMode){

        //string ts_loginTable = Fun2::csSysTablePre + "UserLogin";

        //clear all session first !!
        //session_unset(); 
        
        //start 
        //session_start();
        
        //set session of menu and  appList
        $ts_loginId = $p_data[self::csLoginId];
        foreach ($p_data as $key => $value)
        {
            $_SESSION[$key] = $value;
        }


        //get menu and app list
        $ts_menu = "";
        $ts_list = "";
        //if (!Fun2::cbOpenAllMenu){
        if (Fun2::csAutoLoginId == ""){

            $ta_menu = Fun2::_getAppList($pb_hasPwd);
            if ($ta_menu === null)
                return "No Access Right for this Account !!";


            //get ts_list for write session[appList]
            //get ts_menu
            $ts_list = ",";
            $t_menu;
            for ($i = 0; $i < count($ta_menu); $i++)
            {
                //get app list, format: ,AppId:CRUDP,
                $t_menu = $ta_menu[$i];
                $ts_list .= $t_menu["data"] . ":" . $t_menu["fun"] . ",";
            }

            $ts_menu = self::json($ta_menu);
            
            //�_�h flex �L�k decode !!
            //$ts_menu = str_replace(array('"[',']"'), array('[',']'), $ts_menu);    
        }


        //=== insert into Fun2::ReLoginTable table begin ===
        if ($pn_loginTime == 0){
        //if ($pn_loginMode != 3){    //not relogin            
            $pn_loginTime = time();
            //string ts_info = p_data[self::csLoginId].ToString() + "," + p_data[self::csUserName].ToString();
            
            $t_db = new DB();            
            if (!$t_db)
                return "DB connect failed(1) !";            
            if (!$t_db->bConn)
                return "DB connect failed(2) !";            
        
            
            //�R���ª��n�J�O��
            //$ts_ip = self::getIP();
            //$ts_sql = "delete from _UserLogin " .
            //    "where userId = '" . $ts_loginId . "' " .
            //    "and ip = '" . $ts_ip . "'";
            $ts_sql = "delete from _UserLogin2 where userId = '" . $ts_loginId . "'";
            //t_cmd.CommandText = ts_sql;

            try{
                $t_db->exeUpdate($ts_sql);
            }catch(Exception $e){
                $t_db->close();
                return "_UserLogin2 table did not exist(1) !";
            }


            //insert
            //$ts_hasPwd = ($pb_hasPwd) ? "1" : "0";
            //$ts_sql = "insert into _UserLogin(userId, ip, loginDate, hasPwd, note) values(" .
            //    "'" . $ts_loginId . "','" . $ts_ip . "','" . $ps_loginTime . "'," . $ts_hasPwd . ",'')";
            //$tn_loginTime = time();
            $ts_sql = "insert into _UserLogin2(userId, loginTime) values('{0}', {1})";
            $ts_sql = self::format($ts_sql, $ts_loginId, $pn_loginTime);
            //t_cmd.CommandText = ts_sql;

            //int tn_status = t_cmd.ExecuteNonQuery();
            $tn_status = $t_db->exeUpdate($ts_sql);
            //self::closeDB(t_conn, t_cmd, null);
            $t_db->close();

            if ($tn_status != 1)
            {
                //return ts_loginTable + " ���s�b, ���p���޲z�� !!";
                return "_UserLogin2 table did not exist(2) !";
            }
            
            //�n�J�ɥ����O��
            //$_SESSION[self::csLoginTime] = $tn_loginTime;  //for �ǰe��e��(���s�n�J�ɷ|�Ψ�)
        }
        //=== end ===


        //write session
        $_SESSION[self::csLogin] = "Y";                //�ΨӧP�_�n�J���A�γ]�w session �Ĵ�
        $_SESSION[self::csLoginTime] = $pn_loginTime;  //for �ǰe��e��(���s�n�J�ɷ|�Ψ�)
        $_SESSION[self::csMenu] = $ts_menu;            //�ΨӶǰe��e�� in _Main.ashx
        $_SESSION[self::csAppList] = $ts_list;         //�Ψ��ˬd�ϥΪ̪������v��

        //initial and set dept session
        $t_dept = Fun2::_setDeptSession();
        $_SESSION[self::csDeptId] = isset($t_dept[self::csDeptId]) ? $t_dept[self::csDeptId] : "";          //�Τ���ݳ���
        $_SESSION[self::csDeptIds] = isset($t_dept[self::csDeptIds]) ? $t_dept[self::csDeptIds] : ""; ;     //�Τ���ҳ����M��, �r�����j


        //set key
        //$tn_len = Fun2::cnKeySize / 8 - Fun2::csBaseKey.Length;  //���i�p�� 0.
        //Random t_random = new Random();
        //string ts_key = t_random.Next(10000000, 99999999).ToString();
        //int tn_len2 = ts_key.Length - tn_len;
        //ts_key = (tn_len2 >= 0) ? ts_key.Substring(0, tn_len) : ts_key + preZero(tn_len2 * (-1), "");
        //Fun.hc.Session[Fun.csKey] = ts_key;
        
        return "";
    }
    

    public static function now()
    {
        //return date("Y-m-d H:i:s");   //�p�ɫe����0 (if need).
        return date("Y-m-d G:i:s");    //�p�ɫe������0
    }


    public static function today()
    {
        return date("Y-m-d");
    }

    
    //split to array and trim
    //�� bug, �|�o�Ϳ��~ !!
    public static function splitTrim($ps_sep, $ps_1)
    {
        
        $tas_2 = explode($ps_sep, $ps_1);
        foreach ($tas_2 as &$t_item)
            $t_item = trim($t_item);        

        return $tas_2;                
    }


    //return string
    public static function replaceStr($ps_source)
    {
        return self::replaceStrArrays($ps_source, null, false);
    }


    /// <summary>
    /// �N�Y�Ӧr��H p_dw �����e���N, �O�d���W�ٰѼ� replaceStr()
    /// </summary>
    /// <param name="ps_source">��l�r��</param>
    /// <param name="p_dw">�n���N�� JsonObject ���</param>
    /// <param name="pb_html">�O�_�� html ���, �p�G�O, �h�t�η|�۰ʸm������Ÿ�</param>
    /// <returns>�s�r��</returns>
    public static function replaceStrArray($ps_source, $p_dw, $pb_html)
    {
        $ta_dw = array();
        $ta_dw[0] = $p_dw;
        return self::replaceStrArrays($ps_source, $ta_dw, $pb_html);
    }


    /// <summary>
    /// �N�Y�Ӧr��H pa_dw �����e���N, �Φb�m�� mail body �M inf �ɮפ��e, 
    /// �j�M���B�J���M�� ps_source �̪� [xxx], �A�M pa_dw ���, �p�G�䤣��, �h���|���m���o�����. (�H�K�}�a sql �����e)
    /// �O�d���W��: _now, _today, _userId, _userName, _loginId, _deptId, _deptName
    /// �i�ϥΪ��ܼ�: cf, db, ss, f2
    /// </summary>
    /// <param name="ps_source">��l�r��</param>
    /// <param name="pa_dw">�n���N�� JsonArray ���</param>
    /// <param name="pb_html">�O�_�� html ���, �p�G�O, �h�t�η|�۰ʸm������Ÿ�</param>
    /// <returns>�s�r��</returns>
    public static function replaceStrArrays($ps_source, $pa_dw, $pb_html)
    {

        //get tas_find[] tas_replace[]
        $ts_dw = "";
        $tn_start = 0;
        $tn_dw; $tn_left; $tn_right;
        $ts_find; $ts_fid; $ts_replace = "";
        $t_dw;
        $ta_find = array();    //find-replace
        while (true)
        {
            //initial
            $ts_replace = "_";  //flag for not found
            
            //find "["
            $tn_left = strpos($ps_source, "[", $tn_start);
            if ($tn_left === false){
                break;
            }

            //find "]"
            $tn_right = strpos($ps_source, "]", $tn_left + 1);
            if ($tn_right === false){
                break;
            }

            $ts_find = substr($ps_source, $tn_left, $tn_right - $tn_left + 1); //find string
            $ts_fid = substr($ts_find, 1, strlen($ts_find) - 2);
            //tn_start = tn_right + 1;
            $tn_start = $tn_left + 1;     //�Ҽ{ "[[" ������


            //**************** case of �O�d�r, �����X�R !! ****************
            if (substr($ts_fid, 0, 1) == "_")  
            {
                //ts_fid = ts_fid.Substring(1);
                switch ($ts_fid)
                {
                    case "_now":        //�{�b�ɶ� (yyyy/MM/dd HH:mm:ss)
                        $ts_replace = self::now();
                        break;
                    case "_today":      //���Ѥ�� (yyyy/MM/dd)
                        $ts_replace = self::today();
                        break;
                    /*    
                    case "_twYear":     //����~��
                        $ts_replace = Convert.ToString(DateTime.Now.Year - 1911);
                        break;
                    case "_westYear":   //�褸�~��
                        ts_replace = DateTime.Now.Year.ToString();
                        break;
                    */    
                    case "_userId":     //�Τ�N�X
                        $ts_replace = self::getUserId();
                        break;
                    case "_userName":   //�Τ�W��
                        $ts_replace = self::getUserName();
                        break;
                    case "_loginId":    //�n�J�b��
                        $ts_replace = self::getLoginId();
                        break;
                    case "_idno":       //��������
                        $ts_replace = self::getIdno();
                        break;
                    default:
                        continue;  //do nothing
                }
            }
            //*************************************************************

            else
            {
                $tn_pos = strpos($ts_fid, ":");
                if ($tn_pos > 1)
                {
                    $ts_fid2 = substr($ts_fid, $tn_pos + 1);
                    if (substr($ts_fid, 0, 2) == "cf"){         //web.config �ܼ�
                        $ts_replace = constant("Config::" . $ts_fid2);        
                    }elseif (substr($ts_fid, 0, 2) == "db"){    //�ϥ� web.config �ܼ�(�t�b�K)�s���~����Ʈw
                        $ts_replace = "OpenDataSource('SQLOLEDB','" . constant("Config::" . $ts_fid2) . "').";   //�̥k��]�t "."
                    }elseif (substr($ts_fid, 0, 2) == "ss"){    //session �ܼ�, �ܼƥ����s�b !!
                        $ts_replace = $_SESSION[$ts_fid2];
                    }elseif (substr($ts_fid, 0, 2) == "f2"){    //get Fun2._getSql()                    
                        //�i�H�����Ѽ�, ex: [f2:type1;list:a1,a2]
                        if ($pa_dw != null)
                            $t_dw = $pa_dw[0];
                        else
                            $t_dw = array();

                        $tn_pos2 = strpos($ts_fid2, ";");
                        if ($tn_pos2 > 1)
                        {
                            $t_dw["data"] = trim(substr($ts_fid2, 0, $tn_pos2));
                            $ts_fid2 = trim(substr($ts_fid2, $tn_pos2 + 1));
                            $tn_pos2 = strpos($ts_fid2, ":");
                            if ($tn_pos2 > 1)
                            {
                                $t_dw[substr($ts_fid2, 0, $tn_pos2)] = trim(substr($ts_fid2, $tn_pos2 + 1));
                            }
                        }
                        else
                        {
                            $t_dw["data"] = $ts_fid2;
                        }

                        $ts_db = "";
                        $ts_replace = Fun2::_getSql($t_dw, $ts_db);
                    }
                    else{
                        continue;
                    }

                }
                else
                {
                    if ($pa_dw === null)
                        continue;
                    elseif ($tn_pos == 1)
                    {
                        $ts_dw = $ts_fid[0];    //char 0
                        /* temp remark
                        if (!Char.IsNumber($ts_dw))
                            continue;
                        */
                        
                        $tn_dw = intval($ts_dw);
                        $ts_fid = substr($ts_fid, 2);
                    }
                    else
                    {
                        $tn_dw = 0;
                    }

                    $t_dw = $pa_dw[$tn_dw];
                    if (!isset($t_dw[$ts_fid]))
                        continue;

                    $ts_replace = (string)$t_dw[$ts_fid];
                }
            }


            //add to replace[] list
            //if (!Fun::isEmpty($ts_replace)){
            if ($ts_replace != '_'){
                $tb_find = false;
                $tn_len = count($ta_find);
                for ($i = 0; $i < $tn_len; $i = $i + 2){
                    if ($ts_find == $ta_find[$i]){
                        $tb_find = true;
                        break;
                    }
                }
                if (!$tb_find){
                    //$tn_len = count($ta_find);
                    $ta_find[$tn_len] = $ts_find;
                    $ta_find[$tn_len+1] = $ts_replace;
                }
            }
        }


        //replace string[]
        for ($i = 0; $i < count($ta_find); $i = $i + 2){
            $ps_source = str_replace($ta_find[$i], $ta_find[$i + 1], $ps_source);
        }


    //lab_exit:
        //replace carrier if need.
        if ($pb_html){
            $ps_source = str_replace("\r\n", self::csHtmlCarrier, $ps_source);
        }

        return $ps_source;
    }


    //returns JsonObject �r��(��Ʃο��~�T��)
    //public static function queryEdit($ps_inf, $ps_input)
    //public static function queryEdit($ps_inf, $pa_input)
    public static function queryEdit($ps_inf, $pa_input, $p_send)
    {

        //open inf file and get sql statement
        //Boolean tb_sysError = false;
        //ps_inf += "E";    //�ѵ{���ۦ���w, ���۰ʦb�᭱�[ 'E' (2010-10-1) !!
        $t_conn = null;
        $ts_error = "";
        $t_inf = self::infToJson($ps_inf);
        if ($t_inf === null)
        {
            $ts_error = "Can not Open inf: " . $ps_inf . " in Fun.queryEdit() !";
            //return self::when_error2($t_conn, $ts_error);            
            goto lab_error;
        }


        //=== get sql statement begin ===
        //$ta_input = json_decode($ps_input, true);   //decode input value
        $ta_input = $pa_input;  
        $ta_dw = $t_inf["dws"];

        if (count($ta_dw) != count($ta_input))
        {
            $ts_error = "ps_input does not Match DWs.length in Fun.queryEdit() !";
            //return self::when_error2($t_conn, $ts_error);
            goto lab_error;
        }


        //create database connection
        $t_conn = new DB();
        //t_conn.Open();


        //get field condition info
        $ts_fid = "";
        $ts_defTable; //, ts_table;
        $t_data = array();
        $ts_dw;
        for ($tn_dw = 0; $tn_dw < count($ta_dw); $tn_dw++)
        {
            $ts_dw = "dw" . $tn_dw;
            $t_dw = $ta_dw[$tn_dw];
            $t_input = $ta_input[$tn_dw];
            if (count($t_input) == 0)
                continue;

            /*
            if (ta_key2.Length != tn_key){
                t_conn.Close();
                return self::jsonError("queryEdit(): ta_key[] did not match ta_key2[] !");
            }
            */

            //default table
            $ts_defTable = isset($t_dw[self::csDefTable]) ? $t_dw[self::csDefTable] . "." : "";


            //get mapping
            $ta_item = $t_dw["items"];
            $t_map = array();
            $j;
            for ($j = 0; $j < count($ta_item); $j++)
                $t_map[$ta_item[$j]["fid"]] = $j;


            //get column list string (has alias if need)
            $ta_fid = $t_input["fids"];
            $ts_colList = "";
            $ts_sep = "";
            $ts_col; $ts_table;
            $tn_item;
            $t_item;
            for ($j = 0; $j < count($ta_fid); $j++)
            {
                $ts_fid = $ta_fid[$j];
                $tn_item = self::idToNo($t_map, $ts_fid);
                if ($tn_item == -1)
                {
                    $ts_error = "Can not Find fid=" . $ts_fid . " in " . $ps_inf . " File in Fun.queryEdit() !";
                    //return self::when_error2($t_conn, $ts_error);
                    goto lab_error;
                }

                //get column name
                $t_item = $ta_item[$tn_item];
                $ts_table = !isset($t_item[self::csTable]) ? $ts_defTable : (($t_item[self::csTable] == "") ? "" : $t_item[self::csTable] . ".");
                if (isset($t_item[self::csColName]))
                    $ts_col = $ts_table . $t_item[self::csColName] . " as " . $ts_fid;
                else
                    $ts_col = $ts_table . $ts_fid;

                $ts_colList .= $ts_sep . $ts_col;
                $ts_sep = ",";
            }


            //set t_list for write into ta_list
            $ta_key = $t_input["keys"];  //more than one key
            $ta_key2;      //key info array
            $tn_key = count($ta_key);
            $tas_col = array($tn_key);
            $tas_type = array($tn_key);
            $tas_value = array($tn_key);
            $ts_where = "";
            $ts_str;
            $ts_and = "";
            $tb_row = true;
            for ($j = 0; $j < $tn_key; $j++)
            {
                //tas_value[j] = ta_input2[j].ToString();
                $ta_key2 = $ta_key[$j];
                $ts_fid = $ta_key2[0];
                $tn_item = self::idToNo($t_map, $ts_fid);
                if ($tn_item == -1)
                {
                    $ts_error = "Can not Find key fid=" . $ts_fid . " in " . $ps_inf . " File in Fun.queryEdit() !";
                    //return self::when_error2($t_conn, $ts_error);
                    goto lab_error;
                }

                //get key column name
                $t_item = $ta_item[$tn_item];
                $ts_table = (!isset($t_item[self::csTable])) ? $ts_defTable : (($t_item[self::csTable] == "") ? "" : $t_item[self::csTable] . ".");
                if (isset($t_item[self::csColName]))
                    $ts_col = $ts_table . $t_item[self::csColName];
                else
                    $ts_col = $ts_table . $ts_fid;

                $tas_col[$j] = $ts_col;
                //tas_col[j] = (t_item[self::csColName] != null) ? t_item[self::csColName].ToString() : ts_fid;
                $tas_type[$j] = $ta_key2[1];

                if ($tn_dw == 0)
                {
                    if ($ta_key2[2] === null)
                    {
                        $ts_error = "PK can not be null in Fun.queryEdit(), Please Check " . $ps_inf . " File or Flex List Window !";
                        //return self::when_error2($t_conn, $ts_error);
                        goto lab_error;
                    }

                    $tas_value[$j] = $ta_key2[2];
                }
                else
                {
                    //get column value from parent dw
                    $tn_upDW = (isset($t_dw["upDW"])) ? intval($t_dw["upDW"]) : 0;
                    $ts_upFid = $ta_key2[3];
                    $ta_t1 = $t_data["dw" . $tn_upDW];

                    if ($ta_t1 === null || count($ta_t1) == 0)
                    {
                        $tb_row = false;
                        break;
                    }

                    $t_t1 = $ta_t1[0];
                    $tas_value[$j] = $t_t1[$ts_upFid];
                    /*
                    t_list = (JsonObject)ta_list[tn_upDW];
                    //ta_key2 = (JsonArray)ta_key[j];
                    tas_value[j] = t_list[ts_fid].ToString();
                    */
                }

                //get where string
                $ts_str = self::qItemToWhere($tas_col[$j], $tas_type[$j], "=", $tas_value[$j]);
                if ($ts_str != "")
                {
                    $ts_where .= $ts_and . $ts_str;
                    $ts_and = " and ";
                }
            }

            //get sql statement
            //$ts_sql = self::sqlAddDB(t_dw["selectTable"].ToString());
            //$ts_sql = self::replaceStr($t_dw["selectTable"]);
            //$ts_sql = self::replaceStrArray($t_dw["selectTable"], $p_send, false);
            $ts_sql = $t_dw["selectTable"];
            if ($ts_where != ""){
                $ts_where = (strpos(strtolower($ts_sql), " where ") > 0 ? " and " : " where ") . $ts_where;
            }
            if (strpos($ts_sql, "[_where]") !== false){
                $ts_sql = str_replace("[_where]", $ts_where, $ts_sql);
            }else{
                $ts_sql .= $ts_where;
            }
            
            //ts_sql = "select " + ts_colList + " from " + self::sqlAddDB(ts_sql);
            //$ts_sql = self::replaceStr("select " . $ts_colList . " from " . $ts_sql);            
            $ts_sql = self::replaceStrArray("select " . $ts_colList . " from " . $ts_sql, $p_send, false);
            /*            
            //$ts_sql = self::replaceStrArray($t_dw["selectTable"], $p_send, false);
            ts_sql = "select " + ts_colList + " " +
                "from " + self::sqlAddDB(t_dw["selectTable"].ToString()) + " " +
                ts_where + " " +
                ((t_dw["select2"] === null) ? "" : " " + t_dw["select2"].ToString());
            */

            if ($tb_row)
            {
                //retrieve database and write into json object
                //t_writer.WriteMember("dw" + i.ToString());
                $t_data[$ts_dw] = array();
                $tas_value = array(1);    //?? for next loop
                //int tn_rows = self::sqlToJson(t_conn, ts_sql, t_writer, t_list);
                $ta_data = self::readRowsByConn($t_conn, $ts_sql);
                if ($ta_data === null)
                {
                    if ($tn_dw == 0)
                    {
                        //tb_sysError = true;
                        $ts_error = "Can not Find dw0 Record in Fun.queryEdit() with sql: " . $ts_sql;
                        //return self::when_error2($t_conn, $ts_error);
                        goto lab_error;
                    }
                }
                else
                {
                    //save record into ta_list(ArrayList) for related dw using
                    //coz we can not get value from JsonWriter object !!
                    //ta_list.Add(t_list);
                    $tn_len = count($t_data[$ts_dw]);
                    for ($j = 0; $j < count($ta_data); $j++)
                    {
                        //$ta_data2 = $t_data[$ts_dw];
                        //$ta_data2[] = $ta_data[$j];
                        $t_data[$ts_dw][$tn_len+$j] = $ta_data[$j];
                    }
                }
            }

            /*
            }
            else if(i == 0)
            {   //output report
                $ts_dirTemp = (self::config("dirTemp") != null) ? self::config("dirTemp") : "temp";
                $ts_dirTemplate = (self::config("dirTemplate") != null) ? self::config("dirTemplate") : "template";
                $ts_template = self::sDirWeb + ts_dirTemplate + "/" + ps_template;
                $ts_target = self::sDirWeb + ts_dirTemp + "/" + ps_template;
                //temp remark
                //$ts_error = self::genWord("", ts_sql, 1000, ts_template, ts_target);
                $ts_error = "";
                if (ts_error != "")
                {
                    ts_error = self::jsonError(ts_error);
                }
                else
                {
                    ts_error = self::jsonData("url", ts_dirTemp + "/" + ps_template);
                }
                return ts_error;
                //break;
                
            }
            */

        }//for dws            

        return self::json($t_data);
    //}

    lab_error:
        if ($t_conn != null)
            $t_conn->close();

        //if (tb_sysError)
        //self::sendRoot("E", ts_error);
        
        return self::jsonSysError($ts_error);

    }


    //return ���ʫ᪺ identity JsonArray �r��� JsonError �r��
    public static function updateDB($p_data, $pf_update)
    {
        return Fun2::updateDB($p_data, $pf_update);
    }

    
    public static function insertIdent($ps_sql)
    {
        //only update default DB !!
        $t_db = new DB();
        $ts_id = $t_db->insertIdent($ps_sql);
        $t_db->close();
        return $ts_id;
    }

    
    //�p�G�O insert into �ӥB�� auto increase ���, �h�Ǧ^ auto increase ����!!
    //�p�G���O, �h�Ǧ^���ʪ�����
    public static function exeUpdate($ps_sql)
    {
        //only update default DB !!
        $t_db = new DB();
        $tn_row = $t_db->exeUpdate($ps_sql);
        $t_db->close();
        return $tn_row;
    }

    
    public static function exeUpdateByConn($p_db, $ps_sql)
    {
        try
        {
            return $p_db->exeUpdate($ps_sql);
        }
        catch (Exception $t_e)
        {
            //send root
            self::logError($t_e.getMessage());

            throw $t_e;
        }
    }

/*    
    //public static function genExcel($ps_db, $ps_sql, $ps_template, $ps_table, $ps_row1)
    public static function genExcel($ps_db, $ps_sql, $ps_template, $ps_table, $ps_row1)
    {

        //declare db connection variable
        //$t_db = null;

        if ($ps_table == "")
            $ps_table = $ps_template;

        //check template file
        $ts_error = "";
        //$ts_toFile = "";      //initial
        $ts_from = self::localeFile($ps_template . ".xls2");
        if ($ts_from == "")
        {
            $ts_error = "Excel template file did not exist. (" . $ps_template . ")";
            goto lab_exit3;
            //return;
        }

        $t_file = fopen($ts_from, "r");
        $ts_line = trim(fgets($t_file));
        fclose($t_file);
        //
        $tas_field = explode(",", $ts_line);
        $tn_field = 0;
        $tas_fid = array();
        $tas_fname = array();
        for ($i = 0; $i < count($tas_field); $i=$i+2){
            $tas_fid[$tn_field] = $tas_field[$i];
            $tas_fname[$tn_field] = $tas_field[$i+1];
            $tn_field++;
        }
        

        //get db field name array (tas_fname[])
        //writer header
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=" . $ps_template . ".xls");
        $ts_row = "";
        for ($i = 0; $i < $tn_field; $i++)
            $ts_row .= $tas_fname[$i] . "/t";
           
        echo $ts_row;
            
        
        //$t_db = new DB();
        $ta_row = self::readRows($ps_sql);
        for ($tn_row = 0; $tn_row < count($ta_row); $tn_row++){
            $ts_row = "";
            for ($i = 0; $i < $tn_field; $i++)
                $ts_row .= $ta_row[$i][$tas_fid[$i]] . "/t";
               
            echo $ts_row;            
        }
    //}

    //excel_error:
    //    // Close the connection. 


    lab_exit3:
        if ($ts_error != "")
        {
            $ts_error .= " (Excel Template: " . $ps_template . ")";
            return self::jsonSysError($ts_error);
        }
        else
        {
            return self::jsonData("file", $ps_template);
        }

        //return ts_error;
    }
*/

    
    //public static function genExcel($ps_db, $ps_sql, $ps_template, $ps_table, $ps_row1)
    public static function genExcel2($ps_db, $ps_sql, $ps_template)
    {

        //declare db connection variable
        //$t_db = null;

        //if ($ps_table == "")
        //    $ps_table = $ps_template;

mysql_query("set names utf8");
        
        //check template file
        $ts_error = "";
        //$ts_toFile = "";      //initial
        $ts_from = self::localeFile($ps_template . ".xls2");    //Ū����� header
        if ($ts_from == "")
        {
            $ts_error = "Excel template file did not exist. (" . $ps_template . ")";
            //self::lab_exit3($ts_error, $ps_template);
            //return;
            goto lab_exit3;
        }

        $t_file = fopen($ts_from, "r");
        $ts_line = trim(fgets($t_file));
        fclose($t_file);
        //
        $tas_field = explode(",", $ts_line);
        $ts_lang = "";
        $tn_start = 0;
        if (count($tas_field) % 2 == 1){
            $ts_lang = $tas_field[0];
            $tn_start = 1;
        }
        $tn_field = 0;
        $tas_fid = array();
        $tas_fname = array();
        for ($i = $tn_start; $i < count($tas_field); $i=$i+2){
            $tas_fid[$tn_field] = $tas_field[$i];
            $tas_fname[$tn_field] = $tas_field[$i+1];
            $tn_field++;
        }
        
//meta http-equiv="Content-Type" content="text/html; charset=utf-8"

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment;filename=" . $ps_template . ".xls");

//echo "\xEF\xBB\xBF";

        //get db field name array (tas_fname[])
        //writer header
        //header("Content-type:application/vnd.ms-excel");
        //header("Content-Disposition:filename=" . $ps_template . ".xls");
        $ts_row = "";
        for ($i = 0; $i < $tn_field; $i++)
            $ts_row .= $tas_fname[$i] . "\t";
           
        echo $ts_row . "\r\n";
            
        
        //$t_db = new DB();
        $ta_row = self::readRows($ps_sql);
        for ($r = 0; $r < count($ta_row); $r++){
            $ts_row = "";
            for ($i = 0; $i < $tn_field; $i++)
                $ts_row .= $ta_row[$r][$tas_fid[$i]] . "\t";
               
            if ($ts_lang != ""){
                $ts_row = mb_convert_encoding($ts_row, $ts_lang, "utf-8");
            }

            echo $ts_row . "\r\n";            
        }
        return "";
    //}

    //excel_error:
    //    // Close the connection. 


    //public static function lab_exit3($ts_error, $ps_template){
    lab_exit3:
        if ($ts_error != "")
        {
            $ts_error .= " (Excel Template: " . $ps_template . ")";
            return self::jsonSysError($ts_error);
        }
        else
        {
            return self::jsonData("file", $ps_template);
        }

        //return ts_error;
    }
    
    
/* here!!    
    //generate excel file by .xls2
    //$p_data �p�Gnull, ���xls2, �_�h��xls3
    public static function genExcelByFile($ps_db, $ps_file, $p_data)
    {

        //declare db connection variable
        //$t_db = null;

        //if ($ps_table == "")
        //    $ps_table = $ps_template;

mysql_query("set names utf8");
        
        //check template file
        $ts_error = "";
        //$ts_toFile = "";      //initial
        $ps_file = $ps_file . ".xls" . $ps_fileType;
        $ts_file = self::localeFile($ps_file);    //Ū����� header
        if ($ts_file == "")
        {
            $ts_error = "Excel template file did not exist. (" . $ps_file . ")";
            goto lab_exit3;
        }

        $t_file = fopen($ts_file, "r");
        $ts_line = trim(fgets($t_file));
        fclose($t_file);
    }

    
    public static function genExcel($ps_db, $ps_file, $ps_fileType)
    {
        //
        $tas_field = explode(",", $ts_line);
        $ts_lang = "";
        $tn_start = 0;
        if (count($tas_field) % 2 == 1){
            $ts_lang = $tas_field[0];
            $tn_start = 1;
        }
        $tn_field = 0;
        $tas_fid = array();
        $tas_fname = array();
        for ($i = $tn_start; $i < count($tas_field); $i=$i+2){
            $tas_fid[$tn_field] = $tas_field[$i];
            $tas_fname[$tn_field] = $tas_field[$i+1];
            $tn_field++;
        }
        
//meta http-equiv="Content-Type" content="text/html; charset=utf-8"

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");;
header("Content-Disposition: attachment;filename=" . $ps_template . ".xls");

//echo "\xEF\xBB\xBF";

        //get db field name array (tas_fname[])
        //writer header
        //header("Content-type:application/vnd.ms-excel");
        //header("Content-Disposition:filename=" . $ps_template . ".xls");
        $ts_row = "";
        for ($i = 0; $i < $tn_field; $i++)
            $ts_row .= $tas_fname[$i] . "\t";
           
        echo $ts_row . "\r\n";
            
        
        //$t_db = new DB();
        $ta_row = self::readRows($ps_sql);
        for ($r = 0; $r < count($ta_row); $r++){
            $ts_row = "";
            for ($i = 0; $i < $tn_field; $i++)
                $ts_row .= $ta_row[$r][$tas_fid[$i]] . "\t";
               
            if ($ts_lang != "")   
                $ts_row = mb_convert_encoding($ts_row, $ts_lang, "utf-8");

            echo $ts_row . "\r\n";            
        }
        return "";
    //}

    //excel_error:
    //    // Close the connection. 


    lab_exit3:
        if ($ts_error != "")
        {
            $ts_error .= " (Excel Template: " . $ps_template . ")";
            return self::jsonSysError($ts_error);
        }
        else
        {
            return self::jsonData("file", $ps_template);
        }

        //return ts_error;
    }
*/
    
        
    //return sql string 
    public static function removeDbo($ps_sql)
    {
        if (Fun2::cnDBType == self::cnMySql)
            return str_replace("dbo.", "", $ps_sql);
        else
            return $ps_sql;
    }
    
    
    //�ন�з� json �r��
    public static function normalJson($ps_json){
        $ps_json = str_replace(
            array('"',  "'"),
            array('\"', '"'),
            $ps_json
        );
        
        return preg_replace('/(\w+):/i', '"\1":', $ps_json);
        //$ps_json = preg_replace('/(\w+):/i', '"\1":', $ps_json);
        //return json_decode(sprintf('{%s}', $ps_json));
    }    

    
    public static function utf8(){
        header('Content-Type:text/plain; charset=utf-8');
    }



    /// <summary>
    /// Ū���@�Ӥ�r�ɪ����e��r��.
    /// </summary>
    /// <param name="ps_file">�ɮק�����|</param>
    /// <returns>�ɮפ��e�r��, �p�G�ɮפ��s�b, �h�Ǧ^�ť�</returns>
    public static function fileToStr($ps_file)
    {
        if (!file_exists($ps_file)){
            self::logError("no file! (" + $ps_file + ")");
            return "";
        }

        return file_get_contents($ps_file);
    }

    
    public static function pureStrToJson($ps_str)
    {

        $ts_fid = "";     //now field id
        $ts_value = "";
        $ts_oldFid = "";
        $ts_oldValue = "";
        $tas_line = explode("\n", $ps_str);
        $t_data;
        foreach ($tas_line as $ts_line)
        {
            if ($ts_line == "" || (strlen($ts_line) >= 2 && substr($ts_line, 0, 1) == self::csRemark)){         //����
                continue;
                
            }else if (strlen($ts_line) >= 2 && substr($ts_line, 0, 1) == self::csVar){
                //add old fid first if need
                if ($ts_oldFid != "")
                {
                    $t_data[$ts_oldFid] = $ts_oldValue;
                    $ts_oldFid = "";
                    $ts_oldValue = "";
                }


                $ts_fid = substr($ts_line, 1);
                $tn_pos = strpos($ts_fid, "=");
                if ($tn_pos > 0)
                {
                    $ts_value = rtrim(substr($ts_fid, $tn_pos + 1));
                    $ts_fid = rtrim(substr($ts_fid,0, $tn_pos));
                    $t_data[$ts_fid] = $ts_value;
                }
                else
                {
                    $tn_pos = strpos($ts_fid, ":");
                    if ($tn_pos > 0)
                    {
                        $ts_oldValue = rtrim(substr($ts_fid, $tn_pos + 1));
                        $ts_oldFid = rtrim(substr($ts_fid, 0, $tn_pos));
                    }
                }
            }
            else
            {
                $ts_oldValue .= self::csTextCarrier . $ts_line;
            }
        }


        if ($ts_oldFid != "")
        {
            $t_data[$ts_oldFid] = $ts_oldValue;
        }
        return $t_data;
    }

    
    //loginTime�i����client��key value
    public static function getLoginTime(){
        return isset($_SESSION[self::csLoginTime]) ? $_SESSION[self::csLoginTime] : 0;        
    }
    
}//class

?>
