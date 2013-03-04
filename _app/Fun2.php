<?PHP

require_once(dirname(__FILE__) . "/Fun.php"); 
require_once(dirname(__FILE__) . "/UpdateDB.php"); 
require_once(dirname(__FILE__) . "/Louis.php"); 
//require_once(dirname(__FILE__) . "/AES.class.php"); 


class Fun2{
    //property
    //public static cnEncryptType = 2;      //?�設?��??��?密方�? 0(??, 1(?��?key), 2(?��?key)
    //const csBaseKey = "tqmtmkshjrrmgwghvifvfyiu";  //"差勤管�?系統"
    //public $csBaseKey = "tqmtmkshjrrmgwghvifvfyiu";  //"差勤管�?系統"
    //public const int cnKeySize = 256;

    //constant
    //const cbOpenAllMenu = false;
    //const csAutoLoginId = "";
    
    //const csAutoLoginId = "";      //如�?不為空白, ?��??��?????�能?�目
    const csAutoLoginId = "";
    //const csAutoLoginId = "tch1";
    //const cbOpenAllMenu = true;     //only when csAutoLoginId is set !!
    
    
    //const csAutoLoginId = "U000000001";
    
    const cnDBType = Fun::cnMySql;
    //const cnDBType = Fun::cnMSSql;
    //
    const cbDeptStr = false;       //?��?�???�否?��?�?
    const csDefaultLang = "tw";
    
    const csAccountSn = "accountSn";
    const csUsersSn = "usersSn";
    //const csStudentSn = "studentSn";
    const csUsersCName = "userCName";
    const csUsersEName = "userEName";
    const csUserType = "userType";
    //const csRoleSn = "roleSn";
    const csUserTypeName = "userTypeName";
    const csAcademicYear = "academicYear";
    const csSemester = "semester";
    const csSchoolDSn = "schoolDSn";    
    const csSchoolName = "schoolName";
    const csFileName = "fileName";
    const csWebSiteUrl = "webSiteUrl";
    
    //
    const cbUserIdStr = true;     //?�戶�???�否?��?�?
    const cbLoginIdStr = true;     //?�入�???�否?��?�??��?使用)
    
    //?��?變數
    public static $bAdmin = false;      //is admin

        
    /*
    //constructor
    public function __construct(){        
    }
    */
    
    //=== method begin ===
    //called by Fun
    public static function init()
    {
        
    }
    
    //JsonArray.
    public static function _getAppList($pb_hasPwd)
    {
         //如�??�管?��?給全?��???
         if ($_SESSION[Fun::csLoginId]=="AdminSmarten"){             
            $ts_sqlb = "SELECT pCode AS data, sysName AS label, groupId, 
                    CONCAT('image/child/', imageSource) AS imageSource, '1111111' AS fun
                    FROM Program
                    WHERE deleFlag = 0
                    ORDER BY sysCode";            
            $ta_rowb = Fun::readRows($ts_sqlb);
            return $ta_rowb;
         }else{
            //?��?使用?��???roleSn
            $ts_sqla = "SELECT sn, userType, userSn 
                        FROM Account 
                        WHERE loginId='" . $_SESSION[Fun::csLoginId] . "' 
                        AND deleFlag = 0";
            $t_rowa = Fun::readRow($ts_sqla);
            if ($t_rowa){
                //CONCAT(a.insertFunc, a.searchFunc, a.updateFunc, a.deleteFunc, a.reportFunc, a.exportFunc, a.viewFunc) AS fun 權�??��??��?
                $ts_sqlb = "SELECT b.pCode AS data, b.sysName AS label, b.groupId, 
                        CONCAT('image/child/', b.imageSource) AS imageSource,
                        CONCAT(
                        CAST(CASE b.insertFunc WHEN 1 THEN MAX(a.insertFunc) ELSE 0 END AS CHAR(1)),
                        CAST(CASE b.searchFunc WHEN 1 THEN MAX(a.searchFunc) ELSE 0 END AS CHAR(1)),
                        CAST(CASE b.updateFunc WHEN 1 THEN MAX(a.updateFunc) ELSE 0 END AS CHAR(1)),
                        CAST(CASE b.deleteFunc WHEN 1 THEN MAX(a.deleteFunc) ELSE 0 END AS CHAR(1)),
                        CAST(CASE b.reportFunc WHEN 1 THEN MAX(a.reportFunc) ELSE 0 END AS CHAR(1)),
                        CAST(CASE b.exportFunc WHEN 1 THEN MAX(a.exportFunc) ELSE 0 END AS CHAR(1)),
                        CAST(CASE b.viewFunc WHEN 1 THEN MAX(a.viewFunc) ELSE 0 END AS CHAR(1))
                        ) AS fun, '' AS labelShort
                        FROM RoleAca a, Program b, RoleUser c
                        WHERE a.programSn = b.sn AND a.roleSn = c.roleSn
                        AND b.deleFlag = 0 AND c.userType = ".$t_rowa["userType"]." AND c.userSn = ".$t_rowa["userSn"]."
                        GROUP BY b.pCode, b.sysName, b.groupId
                        ORDER BY b.sysCode";
                $ta_rowb = Fun::readRows($ts_sqlb);
                return $ta_rowb;            
            }
         }
    }
    
    
    public static function hasSession()
    {
        //temp change
        //return true;
        return (isset($_SESSION) && isset($_SESSION[Fun::csUserId]));
    }


    public static function _getListWhere($ps_app)
    {
        return "";
    }
    
    
    //?��?: AES 256
    //?��?字串
    //public static function decode($ps_text, $pb_encrypt){
    public static function decrypt($ps_text){
        //temp ?��?�??
        return $ps_text;
        
    //$csBaseKey = "tqmtmkshjrrmgwghvifvfyiu";  //"差勤管�?系統"
    
        /*
        //return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, csBaseKey, base64_decode(str_replace("\\", "", $ps_text)), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));     
        //$t1 = str_replace("\\", "", $ps_text);
        $t1 = base64_decode($ps_text);
        $t1 = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $csBaseKey, $t1, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));     
        return trim($t1);
        */
    
    /*
    $Cipher = new AES(AES::AES256); 
    //$key_256bit = '95f2a4078895d24af1fba8baa93ac5bdbfb098f4bbd9822757806bde7523776c'; 
    $key_256bit = $Cipher->stringToHex($csBaseKey);

    //$cryptext = "74fbfdb1d49598f882b6097c835ca3c4"; 

    // Decryption 
    $content = $Cipher->decrypt($ps_text, $key_256bit); 
    $content = $Cipher->hexToString($content);

    return $content; 
    */
        
    }
    
    
    /// <summary>
    /// [���n!!] �p�G�Ǧ^�Ŧr��, �h�| sendRoot()
    /// 1.�Ǧ^�d�ߤֶq��ƪ� sql ?�z, �� _CRUD.ashx �I�s, �Ǧ^�r���|�A���� sqlAddDB()
    /// 2.�Ǧ^ inf �̭��n���N���r�� (�ϥ� [Var:xxx], xxx ���o�̪� data �ܼ�)
    /// </summary>
    /// <param name="p_data">JsonObject ���</param>
    /// <returns>1.sql ?�z 2.inf �����r��</returns>    

    public static function _getSql(array $p_data, &$ps_db){
        if (!isset($p_data["data"]))
            return "";


        $ts_sql = ""; 
        $ts_where = "";
        $ts_data = $p_data["data"];
        $ts_type = (isset($p_data["type"])) ? $p_data["type"] : "";
        
        //for list data
        //�??
        $sqlDate7 = "BETWEEN 
                        CASE WEEKDAY(CURDATE())+1 
                            WHEN 1 THEN CURDATE()
                            WHEN 2 THEN DATE_SUB(CURDATE( ), INTERVAL 1 DAY)
                            WHEN 3 THEN DATE_SUB(CURDATE( ), INTERVAL 2 DAY)
                            WHEN 4 THEN DATE_SUB(CURDATE( ), INTERVAL 3 DAY)
                            WHEN 5 THEN DATE_SUB(CURDATE( ), INTERVAL 4 DAY) 
                            WHEN 6 THEN DATE_SUB(CURDATE( ), INTERVAL 5 DAY)
                            WHEN 7 THEN DATE_SUB(CURDATE( ), INTERVAL 6 DAY)
                        END
                     AND 
                        CASE WEEKDAY(CURDATE())+1 
                            WHEN 1 THEN DATE_ADD(CURDATE( ), INTERVAL 6 DAY)
                            WHEN 2 THEN DATE_ADD(CURDATE( ), INTERVAL 5 DAY)
                            WHEN 3 THEN DATE_ADD(CURDATE( ), INTERVAL 4 DAY)
                            WHEN 4 THEN DATE_ADD(CURDATE( ), INTERVAL 3 DAY)
                            WHEN 5 THEN DATE_ADD(CURDATE( ), INTERVAL 2 DAY)
                            WHEN 6 THEN DATE_ADD(CURDATE( ), INTERVAL 1 DAY)
                            WHEN 7 THEN CURDATE()
                     END";
                     
        switch ($ts_data)
        {

            //====== 變數必�?存在 begin !! ======
            case "LoginUser":   //如�??�登?�畫?? ?��??�實�?! ?��? Fun::csPwd, csUserId, csUserName
                $_SESSION["isAdmin"] = null;
                
                //?�斷?�無此�???
                $ts_sql = "SELECT * 
                            FROM SchoolD
                            WHERE schoolCode = '".$p_data["schoolCode"]."' AND deleFlag = 0";
                $t_row = Fun::readRowByDB("dbSch", $ts_sql);
                if ($t_row === null){
                    return "err100";    //error code
                }else{                    
                    $_SESSION[self::csSchoolDSn] = $t_row["sn"];
                    $_SESSION[self::csSchoolName] = $t_row["schoolName"];
                    $_SESSION[self::csFileName] = $t_row["fileName"];
                    $_SESSION[self::csWebSiteUrl] = $t_row["webSiteUrl"];                    
                }
                
                $ts_loginId = $p_data[Fun::csLoginId];
                
                if ($ts_loginId!="AdminSmarten"){
                    $ts_sqla = "SELECT a.sn, a.userType, a.userSn 
                                FROM Account a, RoleUser b
                                WHERE a.userType = b.userType AND a.userSn = b.userSn
                                AND a.loginId='" . $ts_loginId . "' 
                                AND a.deleFlag = 0";
                    $t_rowa = Fun::readRow($ts_sqla);
                    if ($t_rowa === null){
                        return "err101";    //error code
                        }
                }
                
                //Administrator
                $ts_sql = "SELECT loginId as {0}, userSn as {1}, loginId as {2}, loginPw as {3}, {5} as {6}
                            FROM Account
                            WHERE deleFlag=0 AND loginId='{4}'";
                    
                return Fun::format($ts_sql, Fun::csUserId, Fun::csLoginId, Fun::csUserName, Fun::csPwd, $ts_loginId, $t_row["sn"], self::csSchoolDSn);

            case "ServerDT":
                if (self::cnDBType == Fun::cnMSSql)
                    return "select getdate() serverDT";
                else if (self::cnDBType == Fun::cnMySql)
                    return "select now() serverDT";
                else
                    return "";

            /*
            //case "SystemTable": //??
            //    return "select top 1 * from AttendSetting";

            //email address, return one field only, input "list" para
            //called by Fun::jsonToMailboxes()

            //?��? userId, userName only !!
            //?��?簽核流�??�細, 欄�??�稱必�??��??�端: levelNo, 程�??�稱(procName), 
            簽核?�代??signerId), 簽核?��???signerName), //簽核?��??? //課�?�??, //課�??�稱 ;(如�??��??��? sendRoot())
            //輸入?�數 p_data ?�含變數: //_flowName, _procCname, _userName, _levelNo
            case "FlowJobs":
                string ts_signerValue = (p_data["signerValue"] != null) ? p_data["signerValue"].ToString() : "";

                switch (p_data["signerType"].ToString())
                {
                    case "AM":  //?��?主管
                        return "select u.userId, u.userName " +
                            "from _Dept d " +
                            "inner join _User u on u.userId = d.bossId " +
                            "where d.deptSeq = " + Fun::getDeptId();
                    case "SM":  //?��??��?主管, 使用 deptName !!
                        return "select u.userId, u.userName " +
                            "from _Dept d " +
                            "inner join _User u on u.userId = d.bossId " +
                            "where d.deptName = '" + ts_signerValue + "'";
                    case "SF":  //?��?欄�??�用??
                        return "select userId, userName " +
                            "from _User " +
                            "where userId = '" + p_data[ts_signerValue] + "'";
                    case "SU":  //?��?人員, 必�?實�??�部份�?式碼 !!
                        return "select userId, userName from _User where userId='" + ts_signerValue + "'";

                    case "SR":  //?��?角色, 必�?實�??�部份�?式碼 !!
                        break;

                    default:
                        break;
                }

                break;


            //欄�?: data, type, list
            //?��?�??欄�?, 欄�??�稱??"email", ?�容?�信�?
            case "MailBox":        
                switch (ts_type)
                {
                    case "user":    //user, 必�?實�??��??�目?��?式碼, see Fun::jsonToMailBoxes() !!
                        //temp change !!
                        //return "select 'malcom@shanger.com.tw' as email " + 
                        return "select case when email is null or email = '' then userId+'@shanger.com.tw' else email end as email " + 
                            "from _User " +
                            "where userId in (" + Fun::listAddQuote(p_data["list"].ToString()) + ") ";

                        //return Fun::sqlAddDB(ts_sql);
                        //return Fun::replaceStr(ts_sql);

                }

                return "";
            //====== 變數必�?存在 end !! ======

            case "SystemTable":
                return "select top 1 * from Setting";

                
            */

            case "UpdateDB":
                switch ($ts_type){
                    case "type1":
                        $ts_sql = "UPDATE {0} SET {1} = '{2}' WHERE {3} = '{4}'";
                        return Fun::format($ts_sql, $p_data["tableName"], $p_data["columnName"], $p_data["theValue"], $p_data["columnKeyName"], $p_data["keyValue"]);
                    case "type2":
                        $ts_sql = "UPDATE {0} SET {1} = '{2}' WHERE {3} = '{4}' AND {5} = '{6}'";
                        return Fun::format($ts_sql, $p_data["tableName"], $p_data["columnName"], $p_data["theValue"], $p_data["columnKeyName1"], $p_data["keyValue1"], $p_data["columnKeyName2"], $p_data["keyValue2"]);
                    case "type3":
                        $ts_sql = "UPDATE {0} SET {1} = '{2}' WHERE {3} IN ({4})";
                        return Fun::format($ts_sql, $p_data["tableName"], $p_data["columnName"], $p_data["theValue"], $p_data["columnKeyName"], $p_data["keyValue"]);
                }
                break;                
            case "Json":        //?�編輯畫?�輸?�seq?��??�檢?��??��???
                switch ($ts_type){
                    case "getSession":
                        $ta_data = $p_data["sessionName"];
                
                        foreach($ta_data as $value) {
                            if ($value != null && trim($value) != "") {
                                $ta_session[$value] = ((isset($_SESSION[$value]))? $_SESSION[$value]:null);
                            }
                        }
                        return Fun::json($ta_session);
                }
                break;                
            case "Var":     //server variables
                switch (p_type){
                    case "userType":
                        return $_SESSION["csUserType"];
                    case "userSn":
                        return $_SESSION["csUsersSn"];
                    case "academicYear":
                        return $_SESSION["csAcademicYear"];
                    case "semester":
                        return $_SESSION["csSemester"];
                }
                break;
            

            case "CodeTable":   //???語系
                return "select codeId as data, codeDesc as label from " .
                    "_Code_" . Fun::$sLang . " " .
                    "where typeId='" . $ts_type . "' " .
                    "and rowStatus='A' " .
                    "order by orderNo, codeId";
                                            
            case "ComboBox":
                switch ($ts_type)
                {                        
				    //add by Louis - start
                    case "classInfo":    //?�別
                        if ($p_data["getAll"]) {
                             return "select a.sn as data, a.theClassName as label 
                                    from classinfo a join (startclass b, classleaves c) 
                                    on (b.classleavesSn = c.sn and c.classinfoSn = a.sn) 
                                    group by a.sn 
                                    order by a.sn";
                        } else {
                             return "select a.sn as data, a.theClassName as label 
                                    from classinfo a join (startclass b, classleaves c, classteacher d) 
                                    on (b.classleavesSn = c.sn and c.classinfoSn = a.sn and d.startclassSn = b.sn) 
                                    where d.staffSn = '".$_SESSION[self::csUsersSn]."' 
                                    group by a.sn order by a.sn";
                        }
                        break;
                    case "classLeaves":  //?��?
                        if ($p_data["getAll"]) {
                            return "select c.sn as data, c.classLeavesName as label 
                                    from classleaves c join (classinfo a, startclass b) 
                                    on (b.classleavesSn = c.sn and c.classinfoSn = a.sn) 
                                    where a.sn = '".$p_data["classSn"]."' 
                                    order by c.sn";
                        } else {
                            return "select c.sn as data, c.classLeavesName as label 
                                    from classleaves c join (classinfo a, startclass b, classteacher d) 
                                    on (b.classleavesSn = c.sn and c.classinfoSn = a.sn and d.startclassSn = b.sn) 
                                    where d.staffSn = '".$_SESSION[self::csUsersSn]."' and a.sn = '".$p_data["classSn"]."'
                                    order by c.sn";
                        }
                        break;
                    case "staffInfo":
                        return "SELECT sn AS data, deptName AS label FROM Dept ORDER BY deptCode";
                //add by Louis - end
                
                    //主系統�?�?
                    case "ProgramM":
                        return "SELECT sn AS data, CONCAT( RTRIM( sysCode ) , ' ', RTRIM( sysName ) ) AS label
                                FROM ProgramM                                
                                ORDER BY sysCode";
                    //系統?�稱
                    case "Program":
                        return "SELECT sn AS data, CONCAT( RTRIM( sysCode ) , ' ', RTRIM( sysName ) ) AS label
                                FROM Program
                                WHERE deleFlag = 0
                                ORDER BY sysCode";
                    //?��?            
                    case "Dept":
                        return "SELECT sn as data, deptName AS label
                                FROM Dept
                                ORDER BY deptCode";
                    //?�稱            
                    case "Titles":
                        return "SELECT sn as data, titlesName AS label
                                FROM Titles
                                ORDER BY titlesCode";
                    //角色
                    case "Role":
                        return "SELECT sn as data, roleName AS label
                                FROM Role"; 
                    //?�員清單
                    case "staffList":
                        return "SELECT sn as data, CONCAT(cName, ' ', eName) AS label
                                FROM Staff
                                ORDER BY deptSn, empNo";
                    //家長清單
                    /*
                    case "parentList":
                        return "SELECT a.sn as data, CONCAT(b.codeDesc, ' ' ,a.idNo, ' ', a.cName, ' ', a.conTel, ' ', a.address) AS label
                                FROM Parent a, _code_".Fun::$sLang." b
                                WHERE b.typeId = 'gender'
                                AND a.gender=b.codeId
                                AND a.deleFlag = 0
                                ORDER BY a.cName, a.idNo";
                    */
                    //家長姓�?
                    case "parentName":
                        return "SELECT a.sn as data, CONCAT(b.codeDesc, ' ', a.cName) AS label
                                FROM Parent a, _code_".Fun::$sLang." b
                                WHERE b.typeId = 'gender'
                                AND a.gender=b.codeId
                                ORDER BY a.cName, a.idNo";
                    //學�?清單
                    /*
                    case "studentList":
                        return "SELECT a.sn as data, CONCAT(b.codeDesc, ' ' ,a.stuNo, ' ', a.cName, ' ', a.eName, ' ', a.address) AS label
                                FROM Student a, _code_".Fun::$sLang." b
                                WHERE b.typeId = 'gender'
                                AND a.gender=b.codeId                                
                                ORDER BY a.stuNo, a.cName";
                    */
                    //學�?姓�?
                    case "studentName":
                        return "SELECT a.sn as data, CONCAT(b.codeDesc, ' ', a.cName, ' ', a.eName) AS label
                                FROM Student a, _code_".Fun::$sLang." b
                                WHERE b.typeId = 'gender'
                                AND a.gender=b.codeId                                
                                ORDER BY a.stuNo, a.cName";
                    //學�?(保險)?��??��?, 身�?證�?
                    case "studentBI":
                        return "SELECT sn as data, CONCAT(birthDate, ' ', idNo) AS label
                                FROM Student                               
                                ORDER BY stuNo, cName";
                    //學�?清單(已�???
                    case "studentStart":
                        return "SELECT a.sn as data, CONCAT(d.codeDesc, ' ', a.cName, ' ', a.eName) AS label
                                FROM Student a INNER JOIN
                                StudentStatus b ON a.sn = b.studentSn INNER JOIN
                                StartClass c ON b.startClassSn = c.sn INNER JOIN
                                _code_".Fun::$sLang." d ON a.gender = d.codeId
                                WHERE (c.academicYear =".$_SESSION[self::csAcademicYear].") AND (c.semester =".$_SESSION[self::csSemester].")
                                AND (d.typeId = 'gender') AND a.deleFlag = 0";
                    //家長之學?��???
                    case "pStudent":
                        return "SELECT b.sn as data, CONCAT(c.codeDesc, ' ' ,b.stuNo, ' ', b.cName, ' ', b.eName) AS label
                                FROM PSRelation a INNER JOIN
                                Student b ON a.studentSn = b.sn INNER JOIN
                                _code_".Fun::$sLang." c ON b.gender = c.codeId
                                WHERE (c.typeId = 'gender') AND b.deleFlag = 0 AND a.parentSn = '".$_SESSION[self::csUsersSn]."'";
                    //學�?之家?��???
                    case "sParent":
                       //modi by ken 2013/01/02
                       // return "SELECT b.sn as data, CONCAT(a.relationship, ' ', b.cName, ' ', c.codeDesc, ' ', b.idNo) AS label
                        return "SELECT b.sn as data, CONCAT(a.relationship, ' ', b.cName, ' ', c.codeDesc, ' ', b.conTel) AS label
                                FROM PSRelation a INNER JOIN
                                Parent b ON a.parentSn = b.sn INNER JOIN
                                _code_".Fun::$sLang." c ON b.gender = c.codeId
                                WHERE (c.typeId = 'gender') AND b.deleFlag = 0 AND a.studentSn = '".$p_data["studentSn"]."'";                                
                    //?��?清單
                    case "classInfoList":
                        return "SELECT sn as data, CONCAT(theClassCode, ' ', theClassName) AS label
                                FROM ClassInfo
                                WHERE classType = ".$p_data["classType"]."
                                ORDER BY theClassCode";
                    //?�別清單
                    case "classLeavesList":
                        return "SELECT sn as data, CONCAT(classLeavesCode, ' ', classLeavesName) AS label
                                FROM ClassLeaves
                                WHERE classInfoSn='".$p_data["classInfoSn"]."'                                
                                ORDER BY classLeavesCode";
                    //??�??��??�別??
                    case "classInfoName":
                        return "SELECT b.sn AS data, CONCAT(a.theClassName, ' ', b.classLeavesName) AS label
                                FROM ClassInfo a INNER JOIN ClassLeaves b ON a.sn = b.classInfoSn
                                ORDER BY a.theClassCode, b.classLeavesCode";
                    //?��?清單(已�???
                    case "classInfoStart":
                        //?��??��?師�?資�?, ?��??�部?��?
                        $ts_sql = "SELECT c.sn AS data, CONCAT(c.theClassCode, ' ', c.theClassName) AS label
                                FROM StartClass a
                                INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn
                                INNER JOIN ClassInfo c ON b.classInfoSn = c.sn
                                INNER JOIN ClassTeacher d ON a.sn = d.startClassSn
                                WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                                AND (a.classType IN (0, 1))
                                AND (d.staffSn = '".$_SESSION[self::csUsersSn]."')
                                GROUP BY c.sn
                                ORDER BY c.theClassCode";                                
                        $t_row = Fun::readRow($ts_sql);
                        if ($t_row){
                            return $ts_sql;
                        }else{
                            return "SELECT c.sn AS data, CONCAT(c.theClassCode, ' ', c.theClassName) AS label
                                    FROM StartClass a
                                    INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn
                                    INNER JOIN ClassInfo c ON b.classInfoSn = c.sn
                                    WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                                    AND (a.classType IN (0, 1))
                                    GROUP BY c.sn
                                    ORDER BY c.theClassCode";
                        }
                                
                    //?�別清單(已�???
                    case "classLeavesStart":                    
                        $ts_sql = "SELECT b.sn as data, CONCAT(b.classLeavesCode, ' ', b.classLeavesName) AS label 
                                FROM StartClass a
                                INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn
                                INNER JOIN ClassTeacher c ON a.sn = c.startClassSn
                                WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                                AND (a.classType IN (0, 1)) AND c.staffSn = ".$_SESSION[self::csUsersSn]."
                                AND b.classInfoSn='".$p_data["classInfoSn"]."'
                                GROUP BY a.classLeavesSn
                                ORDER BY b.classLeavesCode";
                        $t_row = Fun::readRow($ts_sql);
                        if ($t_row){
                            return $ts_sql;
                        }else{
                            return "SELECT b.sn as data, CONCAT(b.classLeavesCode, ' ', b.classLeavesName) AS label 
                                    FROM StartClass a
                                    INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn
                                    WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                                    AND (a.classType IN (0, 1))
                                    AND b.classInfoSn='".$p_data["classInfoSn"]."'
                                    ORDER BY b.classLeavesCode";
                        }       
                    //?��??��??��?�?已�???
                    case "classInfoStartConb":
                        //?��??��?師�?資�?, ?��??�部?��?
                        $ts_sql = "SELECT b.sn AS data, CONCAT(c.theClassName, ' ', b.classLeavesName) AS label
                                FROM StartClass a
                                INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn
                                INNER JOIN ClassInfo c ON b.classInfoSn = c.sn
                                INNER JOIN ClassTeacher d ON a.sn = d.startClassSn
                                WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                                AND (a.classType IN (0, 1))
                                AND (d.staffSn = '".$_SESSION[self::csUsersSn]."')
                                GROUP BY b.sn
                                ORDER BY c.theClassCode, b.classLeavesCode";                                
                        $t_row = Fun::readRow($ts_sql);
                        if ($t_row){
                            return $ts_sql;
                        }else{
                            return "SELECT b.sn AS data, CONCAT(c.theClassName, ' ', b.classLeavesName) AS label
                                    FROM StartClass a
                                    INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn
                                    INNER JOIN ClassInfo c ON b.classInfoSn = c.sn
                                    WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                                    AND (a.classType IN (0, 1))
                                    GROUP BY b.sn
                                    ORDER BY c.theClassCode, b.classLeavesCode";
                        }
                    //普通班 開班清單
                    case "classInfoStartConbA":
                        //?��??��?師�?資�?, ?��??�部?��?
                        $ts_sql = "SELECT b.sn AS data, CONCAT(c.theClassName, ' ', b.classLeavesName) AS label
                                FROM StartClass a
                                INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn
                                INNER JOIN ClassInfo c ON b.classInfoSn = c.sn
                                INNER JOIN ClassTeacher d ON a.sn = d.startClassSn
                                WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                                AND (a.classType = 0)
                                AND (d.staffSn = '".$_SESSION[self::csUsersSn]."')
                                GROUP BY b.sn
                                ORDER BY c.theClassCode, b.classLeavesCode";                                
                        $t_row = Fun::readRow($ts_sql);
                        if ($t_row){
                            return $ts_sql;
                        }else{
                            return "SELECT b.sn AS data, CONCAT(c.theClassName, ' ', b.classLeavesName) AS label
                                    FROM StartClass a
                                    INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn
                                    INNER JOIN ClassInfo c ON b.classInfoSn = c.sn
                                    WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                                    AND (a.classType = 0)
                                    GROUP BY b.sn
                                    ORDER BY c.theClassCode, b.classLeavesCode";
                        }
                                
                    //給學?��?年�??�別(已�???
                    case "studentGetClassName":
                        return "SELECT a.studentSn as data, CONCAT(d.theClassName, ' ', c.classLeavesName) AS label
                                FROM StudentStatus a INNER JOIN
                                StartClass b ON a.startClassSn = b.sn INNER JOIN
                                ClassLeaves c ON b.classLeavesSn = c.sn INNER JOIN
                                ClassInfo d ON c.classInfoSn = d.sn
                                WHERE (b.academicYear =".$_SESSION[self::csAcademicYear].") AND (b.semester =".$_SESSION[self::csSemester].")
                                AND (b.classType IN (0, 1))";
                    //交�?�?           
                    case "BabyCarInfo":
                        return "SELECT a.sn AS data, CONCAT(a.carCode, ' ', b.cName, ' ', a.vehTel) AS label
                                FROM BabyCarInfo a INNER JOIN Staff b
                                ON a.venStaffSn = b.sn
                                WHERE b.deleFlag = 0
                                ORDER BY a.carCode";
                    //學年?��???           
                    case "AcaSetupList":
                        return "SELECT sn as data, CONCAT(academicYear, ' - ', semester) AS label
                                FROM AcaSetup
                                WHERE deleFlag = 0
                                ORDER BY currentStatus DESC , academicYear, semester";
                    //學年清單            
                    case "academicYearList":
                        return "SELECT academicYear as data, academicYear AS label
                                FROM AcaSetup
                                WHERE deleFlag = 0
                                GROUP BY academicYear
                                ORDER BY currentStatus DESC , academicYear";
                    //學�?清單            
                    case "semesterList":
                        return "SELECT semester as data, semester AS label
                                FROM AcaSetup
                                WHERE deleFlag = 0
                                GROUP BY semester
                                ORDER BY currentStatus DESC, semester";
                    //???�?��?��?學�?年�???
                    case "oriAcaSetupList":
                        $ts_sql = "SELECT MAX(academicYear+semester) AS acaYearS
                                    FROM AcaSetup
                                    WHERE deleFlag = 0
                                    AND academicYear+semester <> ".$_SESSION[self::csAcademicYear]." + ".$_SESSION[self::csSemester];
                        $t_row = Fun::readRow($ts_sql);
                        if ($t_row){
                            return "SELECT sn as data, CONCAT(academicYear, ' - ', semester) AS label
                                    FROM AcaSetup
                                    WHERE deleFlag = 0
                                    AND (academicYear + semester = ".$t_row["acaYearS"].")
                                    ORDER BY currentStatus DESC , academicYear, semester";
                        }
                    //??學年(?? ?��?(??
                    case "oriStartClassList":
                        $ts_sql = "SELECT MAX(academicYear+semester) AS acaYearS
                                    FROM AcaSetup
                                    WHERE deleFlag = 0
                                    AND academicYear+semester <> ".$_SESSION[self::csAcademicYear]." + ".$_SESSION[self::csSemester];
                        $t_row = Fun::readRow($ts_sql);
                        if ($t_row){
                            return "SELECT a.sn as data, CONCAT(academicYear, ' - ', semester, '  ', c.theClassName, ' - ', b.classLeavesName) AS label
                                    FROM StartClass a, ClassLeaves b, ClassInfo c
                                    WHERE a.classLeavesSn = b.sn AND b.classInfoSn = c.sn
                                    AND (a.academicYear + a.semester = ".$t_row["acaYearS"].")
                                    ORDER BY c.theClassCode, b.classLeavesCode";
                        } 
                    //??學年(?? ?��?(??
                    case "nowStartClassList":
                        return "SELECT a.sn as data, CONCAT(academicYear, ' - ', semester, '  ', c.theClassName, ' - ', b.classLeavesName) AS label
                                FROM StartClass a, ClassLeaves b, ClassInfo c
                                WHERE a.classLeavesSn = b.sn AND b.classInfoSn = c.sn
                                AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                                ORDER BY c.theClassCode, b.classLeavesCode";
                                
                    //綜�? 1 保健, 2 保險, 3 身�??�常, 4 專�?, 5 ?��?, 6 行為�??
                    case "itemName":
                        return "SELECT sn AS data, itemName AS label
                                FROM FuncItem
                                WHERE sysType=".$p_data["sysType"]."
                                AND deleFlag = 0
                                ORDER BY sn";
                    //?��?檢核
                    case "controlItem":
                        return "SELECT sn AS data, controlItem AS label
                                FROM DevControlDetail";
                    //?��?檢核(年齡?��?)
                    case "devControlName":
                        return "SELECT sn AS data, devAgeName AS label
                                FROM DevControl";
                    //?�報?�目
                    case "leagalInfectItemName":
                        return "SELECT sn AS data, itemName AS label
                                FROM LeagalInfectItem
                                ORDER BY sn";
                    //?�末評�?
                    case "finalEvaluateItem":
                        return "SELECT b.sn AS data, CONCAT(a.targetName, ' - ', b.remark, ' - ', b.contents) AS label
                                FROM FinalEvaluate a, FinalEvaluateDetail b
                                WHERE a.sn = b.finalEvaluateSn
                                ORDER BY a.sn, b.sn";
                    //給 SN 秀當學年的 學生班別級
                    case "classInfoNameSpec":
                        /*
                        return "SELECT d.studentSn AS data, CONCAT(a.theClassName, ' ', b.classLeavesName) AS label
                                FROM ClassInfo a INNER JOIN
                                ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                                StartClass c ON b.sn = c.classLeavesSn INNER JOIN
                                StudentStatus d ON c.sn = d.startClassSn
                                WHERE (c.academicYear = '".$_SESSION[self::csAcademicYear]."' AND c.semester = '".$_SESSION[self::csSemester]."') 
                                AND (c.classType = 0)";
                         */
                        return "SELECT d.studentSn AS data, CONCAT(c.academicYear, ' - ',c.semester, '   ',a.theClassName, ' ',b.classLeavesName) AS label
                                FROM ClassInfo a INNER JOIN
                                ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                                StartClass c ON b.sn = c.classLeavesSn AND (c.classType = 0) INNER JOIN
                                StudentStatus d ON c.sn = d.startClassSn
                                ORDER BY c.academicYear DESC, c.semester DESC";
                    /*
                    case "accIocName":
                        return "SELECT c.sn AS data, CONCAT(a.accMainCode, b.accTitleCode, c.accIocCode) AS label
                                FROM AccMain a INNER JOIN
                                AccTitle b ON a.sn = b.accMainSn INNER JOIN
                                AccIoc c ON b.sn = c.accTitleSn
                                WHERE (a.accMainType = ".$p_data["accType"].")
                                AND c.sn = '".$p_data["sn"]."'";
                     */
                    case "accName":
                        return "SELECT c.sn AS data, CONCAT(a.accMainName, ' - ', b.accTitleName, ' - ', c.accIocName) AS label
                                FROM AccMain a INNER JOIN
                                AccTitle b ON a.sn = b.accMainSn INNER JOIN
                                AccIoc c ON b.sn = c.accTitleSn
                                WHERE (a.accMainType = ".$p_data["accType"].")";
                    case "sumOfMoney":                        
                        return "SELECT a.sn AS data, SUM(b.amounts) AS label
                                FROM AccMIncomeStu a, AccMIncome b
                                WHERE a.sn = b.accMIncomeStuSn
                                GROUP BY a.sn";
                    case "sumOfMoneyPaid":                        
                        return "SELECT a.sn AS data, SUM(b.amounts) AS label
                                FROM AccMonthPaidDT a, AccMonthPaid b
                                WHERE a.sn = b.accMonthPaidDTSn
                                GROUP BY a.sn";
                    case "sumOfMoneyRequestPaid":                        
                        return "SELECT a.sn AS data, SUM(b.amounts) AS label
                                FROM AccRequestPaidDT a, AccRequestPaid b
                                WHERE a.sn = b.accRequestPaidDTSn
                                GROUP BY a.sn";
                        
                }
                break;                
            //學�?資�?
            case "Student":
                $ts_sql = "select * from Student where stuId ='{0}' AND deleFlag = 0";                    
                return Fun::format($ts_sql, (isset($p_data["stuId"]) ? $p_data["stuId"] : Fun::getUserId()));
            //user info
            case "userInfo":
                $ts_sql = "select sn, userType, userSn from Account where loginId='" . $_SESSION[Fun::csLoginId] . "'";                
                $t_row = Fun::readRow($ts_sql);
                switch ($t_row["userType"]){
                    case "2":
                        return "select sn, cName, 'Parent' AS userType from Parent where deleFlag=0 AND sn=" . $t_row["userSn"] ;
                    case "3":
                        return "select sn, cName, 'Staff' AS userType from Staff where deleFlag=0 AND sn=" . $t_row["userSn"] ;
                    case "4":
                        return "select 0 as sn, 'Admin' as cName, 'Admin' AS userType" ;
                }
            case "AcaSetup":
                return "select academicYear, semester from AcaSetup where currentStatus=1 AND deleFlag = 0";
            case "LogoSetup":
                return "SELECT ".$_SESSION[self::csSchoolDSn]." AS sn, 
                                '".$_SESSION[self::csSchoolName]."' AS schoolName, 
                                '".$_SESSION[self::csFileName]."' AS fileName, 
                                '".$_SESSION[self::csWebSiteUrl]."' AS webSiteUrl";
            //?�班級查詢學?��???
            case "BabyCheck":            
                    return "SELECT CASE WHEN d.sn IS NULL THEN 0 ELSE d.sn END AS sn, 
                            CASE WHEN d.attendFlag IS NULL THEN 'I' ELSE 'E' END AS ieFlag,
                            CASE d.attendFlag WHEN 1 THEN d.attendFlag ELSE 0 END AS attendFlag, 
                            c.sn AS studentSn, CONCAT(c.cName, ' ', c.eName) AS cname,
                            CASE WHEN c.fileName<>'' THEN c.fileName 
                            ELSE 'images.jpg' END AS imageSource,                           
                            CASE WHEN e.exRemark<>'' THEN CONCAT(f.codeDesc, ': ', e.exRemark) ELSE '' END AS exRemark
                            FROM StartClass a INNER JOIN
                            StudentStatus b ON a.sn = b.startClassSn INNER JOIN
                            Student c ON b.studentSn = c.sn AND c.deleFlag = 0 AND c.enrollFlag = 1 LEFT OUTER JOIN
                            BabyCheck d ON c.sn = d.studentSn AND d.cDate = '".$p_data["cDate"]."' 
                            AND a.classLeavesSn = d.classLeavesSn 
                            AND a.academicYear = d.academicYear AND a.semester = d.semester
                            LEFT OUTER JOIN BabyExcuse e ON c.sn = e.studentSn AND e.exDate = '".$p_data["cDate"]."' 
                            AND a.classLeavesSn = e.classLeavesSn
                            AND a.academicYear = e.academicYear AND a.semester = e.semester
                            LEFT OUTER JOIN _code_".Fun::$sLang." f ON e.excuse = f.codeId AND f.typeId = 'excuse'
                            WHERE (a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester].") 
                            AND (a.classLeavesSn = '".$p_data["classInfoSn"]."')
                            ORDER BY c.stuNo";
            //?�班級查詢課程表
            case "ClassScheduleView":
                    return "SELECT a.remark, b.*
                            FROM ClassSchedule a INNER JOIN ClassScheduleDetail b
                            ON a.sn = b.classScheduleSn
                            WHERE (a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester].") 
                            AND (a.classLeavesSn = '".$p_data["classInfoSn"]."')
                            ORDER BY b.sn";
            //?��?資�?
            case "BabyCarList":
                    return "SELECT c.carCode, a.classLeavesSn, CONCAT(b.cName, ' ', b.eName) AS studentName, b.gender, 
                            sLoaction, CONCAT(a.sTimeH, ':', a.sTimeM) AS sTimeHM, d.cName AS parentSName, d.conTel AS parentSTel, 
                            eLoaction, CONCAT(a.eTimeH, ':', a.eTimeM) AS eTimeHM, e.cName AS parentEName, e.conTel AS parentETel, '' AS remark
                            FROM BabyCar a INNER JOIN
                            Student b ON a.studentSn = b.sn INNER JOIN
                            BabyCarInfo c ON a.babyCarInfoSn = c.sn LEFT OUTER JOIN
                            Parent d ON a.parentSnS = d.sn LEFT OUTER JOIN
                            Parent e ON a.parentSnE = e.sn
                            WHERE a.babyCarInfoSn ='".$p_data["babyCarInfoSn"]."'";
            //幼�?身�??�常統�?
            case "SickSum":
                    if ($p_data["academicYear"]!="" && $p_data["semester"]!=""){
                        $tSqlCon = "(b.academicYear = '".$p_data["academicYear"]."' AND b.semester = '".$p_data["semester"]."')";
                    }else{
                        $tSqlCon = "(b.academicYear = '".$p_data["academicYear"]."')";
                    }
                    return "SELECT funcItemSn, SUM(M1) AS M1, SUM(M2) AS M2, SUM(M3) AS M3, SUM(M4) AS M4, SUM(M5) AS M5, SUM(M6) AS M6, 
                            SUM(M7) AS M7, SUM(M8) AS M8, SUM(M9) AS M9, SUM(M10) AS M10, SUM(M11) AS M11, SUM(M12) AS M12, SUM(mSumAll) AS mSumAll
                            FROM(
                            SELECT a.funcItemSn,
                            CASE month(b.cDate) WHEN 1 THEN COUNT(*) ELSE 0 END AS M1,
                            CASE month(b.cDate) WHEN 2 THEN COUNT(*) ELSE 0 END AS M2,
                            CASE month(b.cDate) WHEN 3 THEN COUNT(*) ELSE 0 END AS M3,
                            CASE month(b.cDate) WHEN 4 THEN COUNT(*) ELSE 0 END AS M4,
                            CASE month(b.cDate) WHEN 5 THEN COUNT(*) ELSE 0 END AS M5,
                            CASE month(b.cDate) WHEN 6 THEN COUNT(*) ELSE 0 END AS M6,
                            CASE month(b.cDate) WHEN 7 THEN COUNT(*) ELSE 0 END AS M7,
                            CASE month(b.cDate) WHEN 8 THEN COUNT(*) ELSE 0 END AS M8,
                            CASE month(b.cDate) WHEN 9 THEN COUNT(*) ELSE 0 END AS M9,
                            CASE month(b.cDate) WHEN 10 THEN COUNT(*) ELSE 0 END AS M10,
                            CASE month(b.cDate) WHEN 11 THEN COUNT(*) ELSE 0 END AS M11,
                            CASE month(b.cDate) WHEN 12 THEN COUNT(*) ELSE 0 END AS M12,
                            COUNT(*) AS mSumAll
                            FROM SickLog a, TeachBook b, FuncItem c
                            WHERE a.teachBookSn = b.sn AND a.funcItemSn = c.sn
                            AND c.deleFlag = 0
                            AND ".$tSqlCon."
                            GROUP BY a.funcItemSn, month(b.cDate)
                            ) AS c
                            GROUP BY funcItemSn";
            //?�師人數統�?
            case "rptTeacher":
                return "SELECT a.deptSn, COUNT(*) AS mSumAll
                        FROM Staff a INNER JOIN
                        Dept b ON a.deptSn = b.sn
                        WHERE (a.dutyFlag = 1) AND (a.deleFlag = 0)
                        GROUP BY a.deptSn
                        ORDER BY b.deptCode";
            //學�?人數統�?
            case "rptStudent":
                return "SELECT a.classLeavesSn, COUNT(*) AS mSumAll
                        FROM StartClass a INNER JOIN
                        StudentStatus b ON a.sn = b.startClassSn INNER JOIN
                        Student c ON b.studentSn = c.sn
                        WHERE (a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester].") 
                        AND (a.classType IN (0, 1)) AND (c.deleFlag = 0) AND c.enrollFlag = 1
                        GROUP BY a.classLeavesSn
                        ORDER BY a.sn";
            //?�斷?�否已�?設�?學年??
            case "CurrStatus":
                return "SELECT COUNT(*) AS cs FROM AcaSetup WHERE currentStatus=1 AND deleFlag = 0";
            //?�斷?�否已�??��?學年??
            case "acaYearS":
                switch ($p_data["cuEvent"]){
                    case "C":
                        $tSqlCon = "";
                        break;
                    case "U":
                        $tSqlCon = " AND (sn <> '".$p_data["sn"]."')";
                        break;
                }
                return "SELECT COUNT(*) AS cs 
                        FROM AcaSetup 
                        WHERE academicYear = ".$p_data["academicYear"]."
                        AND semester = ".$p_data["semester"]."
                        AND deleFlag = 0". $tSqlCon;
            case "StartClassS":
                switch ($p_data["cuEvent"]){
                    case "C":
                        $tSqlCon = "";
                        break;
                    case "U":
                        $tSqlCon = " AND (sn <> '".$p_data["sn"]."')";
                        break;
                }
                return "SELECT COUNT(*) AS cs 
                        FROM StartClass 
                        WHERE academicYear = ".$_SESSION[self::csAcademicYear]."
                        AND semester = ".$_SESSION[self::csSemester]."
                        AND classLeavesSn = ".$p_data["classLeavesSn"]. $tSqlCon;
            //?�斷?�否已�??��??�入帳�?
            case "AccountFlag":
                switch ($p_data["cuEvent"]){
                    case "C":
                        $tSqlCon = "";
                        break;
                    case "U":
                        $tSqlCon = " AND (sn <> '".$p_data["sn"]."')";
                        break;
                }
                return "SELECT COUNT(*) AS cs 
                        FROM Account 
                        WHERE loginId = '".$p_data["loginId"]."'". $tSqlCon;
            //?�斷?�否已�??��??�班學年??
            case "startClassS":
                return "SELECT COUNT(*) AS cs 
                        FROM StartClass 
                        WHERE academicYear = ".$p_data["academicYear"]."
                        AND semester = ".$p_data["semester"];
            //?�斷?�否已�??��??��?編�?, ISBN
            case "bookStatus":            
                switch ($p_data["cuEvent"]){
                    case "C":
                        $tSqlCon = "";
                        break;
                    case "U":
                        $tSqlCon = " AND (sn <> '".$p_data["sn"]."')";
                        break;
                }
                
                return "SELECT COUNT(*) AS cs 
                        FROM BookData 
                        WHERE (bookNo = '".$p_data["bookNo"]."' OR isbn = '".$p_data["isbn"]."')". $tSqlCon;
            //?�別清單
            case "getClassInfoSn":
                return "SELECT classInfoSn 
                        FROM ClassLeaves
                        WHERE sn=".$p_data["classLeavesSn"] ;
            //?�課序�?(已�???
            case "getStartClassSn":
                return "SELECT sn, classType
                        FROM StartClass
                        WHERE (academicYear =".$_SESSION[self::csAcademicYear].") AND (semester =".$_SESSION[self::csSemester].")
                        AND classLeavesSn=".$p_data["classLeavesSn"] ;
            //?�別清單(已�???
            case "getClassInfoSnStart":
                return "SELECT b.classInfoSn, b.sn as classLeavesSn
                        FROM StartClass a
                        INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn                        
                        WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                        AND (a.classType IN (0, 1))
                        AND a.sn=".$p_data["startClassSn"] ;
            //?�斷此學?�在該年度是?��?資�?
            case "getStudentStatus":
                return "SELECT b.sn
                        FROM StartClass a
                        INNER JOIN StudentStatus b ON a.sn=b.startClassSn                        
                        WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                        AND (a.classType IN (0, 1))
                        AND b.studentSn=".$p_data["studentSn"] ;                
            case "FindPStuList":
                $ts_sql = "SELECT e.*, c.classLeavesSn, CONCAT(a.theClassName, ' ', b.classLeavesName) AS classInfoName
                    FROM ClassInfo a INNER JOIN
                    ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                    StartClass c ON b.sn = c.classLeavesSn INNER JOIN
                    StudentStatus d ON c.sn = d.startClassSn INNER JOIN
                    Student e ON d.studentSn = e.sn AND e.deleFlag = 0 INNER JOIN
                    PSRelation f ON e.sn = f.studentSn
                    WHERE c.academicYear = {0}
                    AND c.semester = {1}
                    AND f.parentSn = {2}";                    
                return Fun::format($ts_sql, $_SESSION[self::csAcademicYear], $_SESSION[self::csSemester], $_SESSION[self::csUsersSn]);
            case "FindPStuListAll":
                $ts_sql = "SELECT c.academicYear, c.semester, CONCAT(c.academicYear, '(', c.semester, ')') AS acaName, e.*, CONCAT('".$p_data["defaultImgPath"]."', e.fileName) AS imageSource, c.classLeavesSn, CONCAT(a.theClassName, '(', b.classLeavesName, ')') AS classInfoName
                    FROM ClassInfo a INNER JOIN
                    ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                    StartClass c ON b.sn = c.classLeavesSn INNER JOIN
                    StudentStatus d ON c.sn = d.startClassSn INNER JOIN
                    Student e ON d.studentSn = e.sn AND e.deleFlag = 0 INNER JOIN
                    PSRelation f ON e.sn = f.studentSn
                    WHERE f.parentSn = {0}
                    ORDER BY c.academicYear DESC, c.semester DESC";                    
                return Fun::format($ts_sql, $_SESSION[self::csUsersSn]);
            case "FindWeekList":
                $ts_sql = "SELECT *, CONCAT(sDate, ' ~ ', eDate) AS yearEduName
                    FROM YearEdu
                    WHERE academicYear = {0} AND semester = {1}
                    ORDER BY sDate";
                return Fun::format($ts_sql, $_SESSION[self::csAcademicYear], $_SESSION[self::csSemester]);
            case "DispImagesLB":
                return "SELECT fileType, fileName AS imageSource, tagCon AS label
                        FROM (
                            SELECT a.fileType, a.fileName, b.tagCon, a.createDate, a.reviseDate
                            FROM LearnProPT a, LearnProTG b
                            WHERE a.sn = b.learnProPTSn
                            AND a.learnProSn = ".$p_data["learnProSn"]."
                            AND b.academicYear = ".$p_data["academicYear"]." AND b.semester = ".$p_data["semester"]."
                            AND b.classLeavesSn = ".$p_data["classLeavesSn"]." AND b.studentSn = ".$p_data["studentSn"]."
                            UNION ALL
                            SELECT b.fileType, b.fileName, b.fileCon AS tagCon, b.createDate, b.reviseDate
                            FROM LearnPro a, LearnProPT b
                            WHERE a.sn = b.learnProSn
                            AND a.sn = ".$p_data["learnProSn"]."
                            AND b.sn NOT IN (
                                SELECT c.learnProPTSn
                                FROM LearnPro a, LearnProPT b, LearnProTG c
                                WHERE a.sn = b.learnProSn AND b.sn = c.learnProPTSn
                                AND a.sn = ".$p_data["learnProSn"]."
                            )) AS LearnProParent
                            ORDER BY createDate, reviseDate";
                /*
                return "SELECT CONCAT('".$p_data["defaultImgPath"]."', a.fileName) AS imageSource, b.tagCon AS label
                    FROM LearnProPT a, LearnProTG b
                    WHERE a.sn = b.learnProPTSn
                    AND a.learnProSn = ".$p_data["learnProSn"]."
                    AND b.academicYear = ".$p_data["academicYear"]." AND b.semester = ".$p_data["semester"]."
                    AND b.classLeavesSn = ".$p_data["classLeavesSn"]." AND b.studentSn = ".$p_data["studentSn"]."
                    ORDER BY a.createDate, a.reviseDate";
                 */
            case "FindClassTeacherList":
                $ts_sql = "SELECT c.sn, b.cTitles, c.cName, c.eName, c.gender
                    FROM StartClass a INNER JOIN
                    ClassTeacher b ON a.sn = b.startClassSn INNER JOIN
                    Staff c ON b.staffSn = c.sn
                    WHERE (a.academicYear = {0}) AND (a.semester = {1}) 
                    AND (a.classLeavesSn = {2})
                    AND (c.dutyFlag = 1) AND (c.deleFlag = 0)                     
                    ORDER BY  c.empNo";
                    
                return Fun::format($ts_sql, $_SESSION[self::csAcademicYear], $_SESSION[self::csSemester], $p_data["classLeavesSn"]);
            case "FindClassInfoLeavesList":
                $ts_sql = "SELECT a.sn, a.academicYear, a.semester, a.classLeavesSn, c.theClassName, b.classLeavesName
                    FROM StartClass a, ClassLeaves b, ClassInfo c
                    WHERE a.classLeavesSn = b.sn
                    AND b.classInfoSn = c.sn
                    AND a.academicYear = {0} AND a.semester = {1}
                    AND a.classType IN (0, 1)
                    ORDER BY c.theClassCode, b.classLeavesCode";
                    
                return Fun::format($ts_sql, $_SESSION[self::csAcademicYear], $_SESSION[self::csSemester]);
            //?�斷?�否已�??��?學年??
            case "StudentStatusS":
                return "SELECT COUNT(*) AS cs 
                        FROM StudentStatus 
                        WHERE startClassSn = ".$p_data["nowStartClassSn"];
            //�???�室清�??��???
            case "CleanListDetail":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT sn AS funcItemSn, '' AS checkSign, '' AS checkFlag
                                FROM FuncItem
                                WHERE sysType = 7 AND deleFlag = 0
                                ORDER BY sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN b.sn IS NULL THEN 0 ELSE b.sn END AS sn,
                                CASE WHEN b.cleanListSn IS NULL THEN 0 ELSE b.cleanListSn END AS cleanListSn,
                                a.sn AS funcItemSn, 
                                CASE WHEN b.checkSign IS NULL THEN '' ELSE b.checkSign END AS checkSign, 
                                CASE WHEN b.checkFlag IS NULL THEN '' ELSE b.checkFlag END AS checkFlag                                
                                FROM FuncItem a LEFT OUTER JOIN
                                CleanListDetail b ON a.sn = b.funcItemSn AND b.cleanListSn = ".$p_data["sn"]."
                                WHERE a.sysType = 7 AND a.deleFlag = 0
                                ORDER BY a.sn";                    
                }
            //�???�室清�??��???準�??��?)
            case "CleanListGoods":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT sn AS funcItemSn, '' AS checkSign, '' AS checkFlag
                                FROM FuncItem
                                WHERE sysType = 8 AND deleFlag = 0
                                ORDER BY sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN b.sn IS NULL THEN 0 ELSE b.sn END AS sn,
                                CASE WHEN b.cleanListSn IS NULL THEN 0 ELSE b.cleanListSn END AS cleanListSn,
                                a.sn AS funcItemSn, 
                                CASE WHEN b.checkSign IS NULL THEN '' ELSE b.checkSign END AS checkSign                                
                                FROM FuncItem a LEFT OUTER JOIN
                                CleanListGoods b ON a.sn = b.funcItemSn AND b.cleanListSn = ".$p_data["sn"]."
                                WHERE a.sysType = 8 AND a.deleFlag = 0
                                ORDER BY a.sn";                    
                } 
            //�???��?檢核�?
            case "DevControl":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT 
                                a.sn AS devControlSn, a.devAgeName, a.sAge, a.sMonth, a.sDay, a.eAge, a.eMonth, a.eDay, a.fileName, a.remark,                                
                                b.answer,
                                b.sn AS devControlDetailSn,
                                '' AS checkSign 
                                FROM DevControl a, DevControlDetail b
                                WHERE a.sn = b.devControlSn
                                AND ".$p_data["ageDays"]." BETWEEN sAge*365 + sMonth*30 + sDay AND eAge*365 + eMonth*30 + eDay
                                ORDER BY b.sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                a.sn AS devControlSn, a.devAgeName, a.sAge, a.sMonth, a.sDay, a.eAge, a.eMonth, a.eDay, a.fileName, a.remark,
                                CASE WHEN d.sn IS NULL THEN 0 ELSE d.sn END AS sn,
                                CASE WHEN d.devControlStuSn IS NULL THEN 0 ELSE d.devControlStuSn END AS devControlStuSn,
                                b.answer,
                                b.sn AS devControlDetailSn,
                                CASE WHEN d.checkSign IS NULL THEN '' ELSE d.checkSign END AS checkSign                                
                                FROM DevControl a INNER JOIN
                                DevControlDetail b ON a.sn = b.devControlSn LEFT OUTER JOIN
                                DevControlStu c INNER JOIN
                                DevControlStuDetail d ON c.sn = d.devControlStuSn ON c.devControlSn = a.sn AND 
                                d.devControlDetailSn = b.sn AND c.sn = ".$p_data["sn"]."
                                WHERE (a.sn = ".$p_data["devControlSn"].")
                                ORDER BY b.sn";             
                }
            //?��?法�??��??��??��?�?
            case "LeagalInfectItem":
                return "SELECT remark
                        FROM LeagalInfectItem 
                        WHERE sn = ".$p_data["sn"];
            //法�??��???主�??��?)
            case "LeagalInfectDetail":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT sn AS funcItemSn, '' AS checkSign, '' AS checkFlag
                                FROM FuncItem
                                WHERE sysType = 9 AND deleFlag = 0
                                ORDER BY sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN b.sn IS NULL THEN 0 ELSE b.sn END AS sn,
                                CASE WHEN b.leagalInfectSn IS NULL THEN 0 ELSE b.leagalInfectSn END AS leagalInfectSn,
                                a.sn AS funcItemSn, 
                                CASE WHEN b.checkSign IS NULL THEN '' ELSE b.checkSign END AS checkSign                                
                                FROM FuncItem a LEFT OUTER JOIN
                                LeagalInfectDetail b ON a.sn = b.funcItemSn AND b.leagalInfectSn = ".$p_data["sn"]."
                                WHERE a.sysType = 9 AND a.deleFlag = 0
                                ORDER BY a.sn";                    
                }                        
            //�???�末評�?
            case "FinalEvaluateDetail":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT b.sn AS finalEvaluateDetailSn, 
                                a.targetName, b.contents, b.remark,
                                '' AS checkSign
                                FROM FinalEvaluate a, FinalEvaluateDetail b
                                WHERE a.sn = b.finalEvaluateSn
                                ORDER BY a.sn, b.sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN d.sn IS NULL THEN 0 ELSE d.sn END AS sn,
                                CASE WHEN d.finalEvaluateStuSn IS NULL THEN 0 ELSE d.finalEvaluateStuSn END AS finalEvaluateStuSn,
                                b.sn AS finalEvaluateDetailSn,
                                a.targetName, b.contents, b.remark,
                                CASE WHEN d.checkSign IS NULL THEN '' ELSE d.checkSign END AS checkSign
                                FROM FinalEvaluate a INNER JOIN
                                FinalEvaluateDetail b ON a.sn = b.finalEvaluateSn LEFT OUTER JOIN
                                FinalEvaluateStu c INNER JOIN
                                FinalEvaluateStuDetail d ON c.sn = d.finalEvaluateStuSn ON d.finalEvaluateDetailSn = b.sn 
                                AND c.sn = ".$p_data["sn"]."                                
                                ORDER BY b.sn";
                }
            //�??安全檢核
            case "SafeControlCheck":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT b.sn AS multiFuncItemDetailSn, 
                                a.itemName, b.contents,
                                '' AS checkSign
                                FROM MultiFuncItem a, MultiFuncItemDetail b
                                WHERE a.sn = b.multiFuncItemSn
                                AND a.multiSysType = 1 AND a.deleFlag = 0
                                ORDER BY a.sn, b.sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN d.sn IS NULL THEN 0 ELSE d.sn END AS sn,
                                CASE WHEN d.safeControlCheckSn IS NULL THEN 0 ELSE d.safeControlCheckSn END AS safeControlCheckSn,
                                b.sn AS multiFuncItemDetailSn,
                                a.itemName, b.contents,
                                CASE WHEN d.checkSign IS NULL THEN '' ELSE d.checkSign END AS checkSign
                                FROM MultiFuncItem a INNER JOIN
                                MultiFuncItemDetail b ON a.sn = b.multiFuncItemSn AND a.multiSysType = 1 AND a.deleFlag = 0 LEFT OUTER JOIN
                                SafeControlCheck c INNER JOIN
                                SafeControlCheckDetail d ON c.sn = d.safeControlCheckSn ON d.multiFuncItemDetailSn = b.sn 
                                AND c.sn = ".$p_data["sn"]."                                
                                ORDER BY b.sn";
                }
            //�??行�?檢查?�目資�?
            case "CarCheckItem":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT sn AS funcItemSn, '' AS checkSign
                                FROM FuncItem
                                WHERE sysType = 11 AND deleFlag = 0
                                ORDER BY sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN b.sn IS NULL THEN 0 ELSE b.sn END AS sn,
                                CASE WHEN b.carCheckSn IS NULL THEN 0 ELSE b.carCheckSn END AS carCheckSn,
                                a.sn AS funcItemSn, 
                                CASE WHEN b.checkSign IS NULL THEN '' ELSE b.checkSign END AS checkSign                                
                                FROM FuncItem a LEFT OUTER JOIN
                                CarCheckItem b ON a.sn = b.funcItemSn AND b.carCheckSn = ".$p_data["sn"]."
                                WHERE a.sysType = 11 AND a.deleFlag = 0
                                ORDER BY a.sn";                    
                } 
            //�???��??��???
            case "SpStudentDI":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT codeId, codeDesc, '' AS checkSign
                                FROM _code_". Fun::$sLang ."
                                WHERE typeId = 'SpStudentDI'
                                ORDER BY orderNo";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN b.sn IS NULL THEN 0 ELSE b.sn END AS sn,
                                CASE WHEN b.spStudentSn IS NULL THEN 0 ELSE b.spStudentSn END AS spStudentSn,
                                a.codeId, a.codeDesc,
                                CASE WHEN b.checkSign IS NULL THEN '' ELSE b.checkSign END AS checkSign                                
                                FROM _code_". Fun::$sLang ." a LEFT OUTER JOIN
                                SpStudentDI b ON a.codeId = b.codeId AND b.spStudentSn = ".$p_data["sn"]."
                                WHERE a.typeId = 'SpStudentDI'
                                ORDER BY a.orderNo";                    
                }
            //�???��????資�?
            case "SpStudentStatus":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT codeId, codeDesc, '' AS checkSign
                                FROM _code_". Fun::$sLang ."
                                WHERE typeId = '".$p_data["statusItem"]."'
                                ORDER BY orderNo";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN b.sn IS NULL THEN 0 ELSE b.sn END AS sn,
                                CASE WHEN b.spStudentSn IS NULL THEN 0 ELSE b.spStudentSn END AS spStudentSn,
                                a.codeId, a.codeDesc,
                                CASE WHEN b.checkSign IS NULL THEN '' ELSE b.checkSign END AS checkSign,
                                remark                                
                                FROM _code_". Fun::$sLang ." a LEFT OUTER JOIN
                                ".$p_data["statusItem"]." b ON a.codeId = b.codeId AND b.spStudentSn = ".$p_data["sn"]."
                                WHERE a.typeId = '".$p_data["statusItem"]."'
                                ORDER BY a.orderNo";                    
                }
            //�??消防安全
            case "FireControlCheck":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT b.sn AS multiFuncItemDetailSn, 
                                a.itemName, b.contents,
                                '' AS checkSign
                                FROM MultiFuncItem a, MultiFuncItemDetail b
                                WHERE a.sn = b.multiFuncItemSn
                                AND a.multiSysType = 2 AND a.deleFlag = 0
                                ORDER BY a.sn, b.sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN d.sn IS NULL THEN 0 ELSE d.sn END AS sn,
                                CASE WHEN d.fireControlCheckSn IS NULL THEN 0 ELSE d.fireControlCheckSn END AS fireControlCheckSn,
                                b.sn AS multiFuncItemDetailSn,
                                a.itemName, b.contents,
                                CASE WHEN d.checkSign IS NULL THEN '' ELSE d.checkSign END AS checkSign
                                FROM MultiFuncItem a INNER JOIN
                                MultiFuncItemDetail b ON a.sn = b.multiFuncItemSn AND a.multiSysType = 2 AND a.deleFlag = 0 LEFT OUTER JOIN
                                FireControlCheck c INNER JOIN
                                FireControlCheckDetail d ON c.sn = d.fireControlCheckSn ON d.multiFuncItemDetailSn = b.sn 
                                AND c.sn = ".$p_data["sn"]."                                
                                ORDER BY b.sn";
                }
            //�??衛�??�主管�?
            case "HygieneControl":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT b.sn AS multiFuncItemDetailSn, 
                                a.itemName, b.contents,
                                '' AS checkSign
                                FROM MultiFuncItem a, MultiFuncItemDetail b
                                WHERE a.sn = b.multiFuncItemSn
                                AND a.multiSysType = 3 AND a.deleFlag = 0
                                ORDER BY a.sn, b.sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN d.sn IS NULL THEN 0 ELSE d.sn END AS sn,
                                CASE WHEN d.hygieneControlSn IS NULL THEN 0 ELSE d.hygieneControlSn END AS hygieneControlSn,
                                b.sn AS multiFuncItemDetailSn,
                                a.itemName, b.contents,
                                CASE WHEN d.checkSign IS NULL THEN '' ELSE d.checkSign END AS checkSign
                                FROM MultiFuncItem a INNER JOIN
                                MultiFuncItemDetail b ON a.sn = b.multiFuncItemSn AND a.multiSysType = 3 AND a.deleFlag = 0 LEFT OUTER JOIN
                                HygieneControl c INNER JOIN
                                HygieneControlDetail d ON c.sn = d.hygieneControlSn ON d.multiFuncItemDetailSn = b.sn 
                                AND c.sn = ".$p_data["sn"]."                                
                                ORDER BY b.sn";
                }
            //法�??��???主�??��?)
            case "SitotoxismAnnDetail":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT sn AS funcItemSn, '' AS checkSign, '' AS checkFlag
                                FROM FuncItem
                                WHERE sysType = 14 AND deleFlag = 0
                                ORDER BY sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN b.sn IS NULL THEN 0 ELSE b.sn END AS sn,
                                CASE WHEN b.sitotoxismAnnSn IS NULL THEN 0 ELSE b.sitotoxismAnnSn END AS sitotoxismAnnSn,
                                a.sn AS funcItemSn, 
                                CASE WHEN b.checkSign IS NULL THEN '' ELSE b.checkSign END AS checkSign                                
                                FROM FuncItem a LEFT OUTER JOIN
                                SitotoxismAnnDetail b ON a.sn = b.funcItemSn AND b.sitotoxismAnnSn = ".$p_data["sn"]."
                                WHERE a.sysType = 14 AND a.deleFlag = 0
                                ORDER BY a.sn";                    
                } 
            //?�藥給藥�??
            case "MedicineRecord":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT codeId AS medTime, '' AS checkSign, '' AS medTimeCon
                                FROM _code_tw
                                WHERE typeId = 'medTime'
                                ORDER BY orderNo";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN b.sn IS NULL THEN 0 ELSE b.sn END AS sn,
                                CASE WHEN b.medicineTicketSn IS NULL THEN 0 ELSE b.medicineTicketSn END AS medicineTicketSn,
                                a.codeId AS medTime, 
                                CASE WHEN b.checkSign IS NULL THEN '' ELSE b.checkSign END AS checkSign,
                                b.medTimeCon
                                FROM _code_tw a LEFT OUTER JOIN
                                MedicineRecord b ON a.codeId = b.medTime AND b.medicineTicketSn = ".$p_data["sn"]."
                                WHERE a.typeId = 'medTime'
                                ORDER BY a.orderNo";
                } 
            //�??評�??��??�目
            case "SelfEvaluateCheck":
                switch ($p_data["cuEvent"]){
                    case "C":
                        return "SELECT a.sn AS selfEvaluateSn, b.sn AS selfEvaluateDetailSn, 
                                a.itemName, b.contents,
                                '' AS score, '' AS remark
                                FROM SelfEvaluate a, SelfEvaluateDetail b
                                WHERE a.sn = b.selfEvaluateSn AND a.deleFlag = 0
                                AND a.selfEvaluateItem = ".$p_data["sysType"]."
                                ORDER BY a.sn, b.sn";
                    case "U":
                    case "V":
                        return "SELECT 
                                CASE WHEN d.sn IS NULL THEN 0 ELSE d.sn END AS sn,
                                CASE WHEN d.selfEvaluateCheckSn IS NULL THEN 0 ELSE d.selfEvaluateCheckSn END AS selfEvaluateCheckSn,
                                a.sn AS selfEvaluateSn,
                                b.sn AS selfEvaluateDetailSn,
                                a.itemName, b.contents,
                                d.score,
                                d.remark
                                FROM SelfEvaluate a INNER JOIN
                                SelfEvaluateDetail b ON a.sn = b.selfEvaluateSn AND a.deleFlag = 0 
                                AND a.selfEvaluateItem = ".$p_data["sysType"]." LEFT OUTER JOIN
                                SelfEvaluateCheck c INNER JOIN
                                SelfEvaluateCheckDetail d ON c.sn = d.selfEvaluateCheckSn ON d.selfEvaluateDetailSn = b.sn 
                                AND c.sn = ".$p_data["sn"]."                                
                                ORDER BY b.sn";
                }
            //�???��?資�?
            case "SchoolInfo":
                return "SELECT *
                    FROM ManaInfo.SchoolD
                    WHERE sn = " . $_SESSION[self::csSchoolDSn] . " AND deleFlag = 0";
            //�???�人資�?
            case "StaffSelf":
            /*
                return "SELECT * 
                        FROM Staff
                        WHERE sn = " . $_SESSION[self::csUsersSn] . " AND deleFlag = 0";
            */
                return "SELECT b.* 
                        FROM Account a, Staff b
                        WHERE a.userSn = b.sn
                        AND a.userType = '".$_SESSION[self::csUserType]."' AND a.userSn = '".$_SESSION[self::csUsersSn]."' AND a.loginId = '".$_SESSION[Fun::csLoginId]."'
                        AND a.deleFlag = 0 AND b.deleFlag = 0";
            //�???�人資�?
            case "ParentSelf":
                return "SELECT a.sn, a.userSn, a.loginId, a.loginPw, b.cName, c.codeDesc AS gender, b.birthDate, b.idNo, b.conTel, b.address, b.eMail, b.eduExtent, b.company
                        FROM Account a, Parent b, _code_".Fun::$sLang." c
                        WHERE a.userSn = b.sn
                        AND b.gender = c.codeId AND c.typeId = 'gender'
                        AND a.userType = '".$_SESSION[self::csUserType]."' AND a.userSn = '".$_SESSION[self::csUsersSn]."' AND a.loginId = '".$_SESSION[Fun::csLoginId]."'
                        AND a.deleFlag = 0 AND b.deleFlag = 0";
            //列家長之學生
            case "ListParentSelf":
                return "SELECT a.relationship, b.sn, b.stuNo, b.cName, b.eName, c.codeDesc AS gender, b.birthDate
                        FROM PSRelation a, Student b, _code_".Fun::$sLang." c
                        WHERE a.studentSn = b.sn
                        AND b.gender = c.codeId AND c.typeId = 'gender'
                        AND a.parentSn = '".$_SESSION[self::csUsersSn]."'
                        GROUP BY b.sn
                        ORDER BY b.stuNo";
            //�??待執行�?�?
            case "SysProcess":
                return "SELECT b.pCode, CONCAT(c.sysName, ' / ', b.sysName) AS sysName,                                 
                                b.imageSource, CONCAT('系統路�?�?, c.sysName, ' / ', b.sysName, '，�??��??�建立�?') AS proCon
                        FROM (
                            SELECT *
                            FROM (
                                SELECT 1 AS oSn, COUNT(*) AS ynFlag, 'Dept' AS pCode
                                FROM Dept
                                UNION ALL
                                SELECT 2 AS oSn,  COUNT(*) AS ynFlag, 'Titles' AS pCode
                                FROM Titles
                                UNION ALL
                                SELECT 3 AS oSn,  COUNT(*) AS ynFlag, 'ClassInfo' AS pCode
                                FROM ClassLeaves
                                UNION ALL
                                SELECT 4 AS oSn,  COUNT(*) AS ynFlag, 'AcaSetup' AS pCode
                                FROM AcaSetup
                                WHERE currentStatus = 1 AND deleFlag = 0
                                UNION ALL
                                SELECT 5 AS oSn,  COUNT(*) AS ynFlag, 'StartClass' AS pCode
                                FROM StartClass
                                WHERE academicYear = '".$_SESSION[self::csAcademicYear]."' AND semester = '".$_SESSION[self::csSemester]."'
                                AND classType = 0
                                UNION ALL
                                SELECT 6 AS oSn,  COUNT(*) AS ynFlag, 'Staff' AS pCode
                                FROM Staff
                                WHERE dutyFlag = 1 AND deleFlag = 0
                                UNION ALL
                                SELECT 7 AS oSn,  COUNT(*) AS ynFlag, 'Student' AS pCode
                                FROM Student
                                WHERE enrollFlag = 1 AND deleFlag = 0
                            ) AS sysGroup
                        ) a, Program b, ProgramM c, RoleAca d, RoleUser e
                        WHERE a.pCode = b.pCode
                        AND b.programMSn = c.sn
                        AND b.sn = d.programSn AND d.roleSn = e.roleSn
                        AND a.ynFlag = 0 AND b.deleFlag = 0
                        AND b.pCode IN ('Dept', 'Titles', 'ClassInfo', 'AcaSetup', 'StartClass', 'Staff', 'Student')
                        AND d.insertFunc<>0
                        AND e.userType = ".$_SESSION[self::csUserType]." AND e.userSn = ".$_SESSION[self::csUsersSn]."
                        GROUP BY b.pCode
                        ORDER BY a.oSn";

            //系統?��?
            case "SysSearch":
                return "SELECT b.pCode, CONCAT(d.sysName, ' / ', b.sysName) AS sysName, b.imageSource
                        FROM RoleAca a, Program b, RoleUser c, ProgramM d
                        WHERE a.programSn = b.sn AND a.roleSn = c.roleSn AND b.programMSn = d.sn
                        AND b.deleFlag = 0 AND c.userType = ".$_SESSION[self::csUserType]." AND c.userSn = ".$_SESSION[self::csUsersSn]."
                        AND b.sysName LIKE '%".$p_data["sysName"]."%'
                        GROUP BY b.pCode
                        ORDER BY d.sysCode, b.sysCode";
            //讀取個人公佈資料
            case "BoardView":
                return "SELECT a.sn, a.subject, a.contents, CONCAT(a.sDate, ' ~ ', a.eDate) AS seDate, a.fileName,
                        CASE WHEN a.fileName<>'' AND a.fileName IS NOT NULL THEN 1 ELSE 0 END AS fileFlag,
                        CASE WHEN a.fileName<>'' AND a.fileName IS NOT NULL THEN '請點選開啟此檔案' ELSE '無檔案可開啟' END AS fileFlagTip
                        FROM Board a INNER JOIN
                        BoardTG b ON a.sn = b.boardSn INNER JOIN
                        RoleUser c ON b.roleSn = c.roleSn                         
                        AND (c.userType = ".$_SESSION[self::csUserType].") AND (c.userSn = ".$_SESSION[self::csUsersSn].") 
                        WHERE CURDATE() BETWEEN a.sDate AND a.eDate
                        AND a.sn NOT IN (
                        SELECT d.boardSn
                        FROM BoardUE d
                        WHERE (d.userType = ".$_SESSION[self::csUserType].") AND (d.userSn = ".$_SESSION[self::csUsersSn].")
                        )
                        GROUP BY a.sn
                        ORDER BY a.sDate";
            case "ToDoListDoc":
                $ts_sql = "SELECT docNoa, subject, contents
                            FROM TodoList
                            WHERE sn = ".$p_data["sn"]."";
                $t_row = Fun::readRow($ts_sql);
                if ($t_row){
                    return "SELECT '".$t_row["docNoa"]."' AS docNoa, max(docNob) + 1 AS docNob, 
                            '".$t_row["subject"]."' AS subject, '".$t_row["contents"]."' AS contents
                            FROM TodoList
                            WHERE docNoa = '".$t_row["docNoa"]."'";
                }
            case "ToDoListDocNoa":
                return "SELECT docNoa
                        FROM TodoList
                        WHERE correlSn = ".$p_data["sn"];
            //待辦事項
            case "ToDoListHistory":
                return "SELECT *
                        FROM TodoList
                        WHERE docNoa = '".$p_data["docNoa"]."'
                        ORDER BY docNob";     
            //讀取為普遍亦或才藝
            case "classType":
                return "SELECT classType
                        FROM StartClass 
                        WHERE sn = ".$p_data["sn"];
            //批次匯入週次
            case "InputWeek":
                return "SELECT sDay, WEEKDAY(sDay)+1 AS sDayW, DATE_ADD(sDay, INTERVAL 4 DAY) AS eDay, WEEKDAY(DATE_ADD(sDay, INTERVAL 4 DAY))+1 AS eDayW
                        FROM (
                            SELECT DATE_ADD(DATE_SUB('".$p_data["sDate"]."', INTERVAL 1 DAY), INTERVAL sn DAY) AS sDay
                            FROM YearDays) acaDays
                        WHERE sDay BETWEEN STR_TO_DATE('".$p_data["sDate"]."', '%Y/%m/%d') AND STR_TO_DATE('".$p_data["eDate"]."', '%Y/%m/%d')
                        AND WEEKDAY(sDay)+1 = WEEKDAY('".$p_data["sDate"]."')+1;";
                //return "CALL sp_weekList('".$p_data["sDate"]."', '".$p_data["eDate"]."')";
                //return "CALL sp_weekList('2010/2/22', '2010/8/13')";                
                /*
                return "DELIMITER $$
                        CREATE PROCEDURE sp_weekList(sDateTemp varchar(10), eDateTemp varchar(10))
                        BEGIN
                            set @sDate = STR_TO_DATE(sDateTemp, '%Y/%m/%d');
                            set @eDate = STR_TO_DATE(eDateTemp, '%Y/%m/%d');
                        
                            set @theDay = DATE_SUB(@sDate, INTERVAL 1 DAY);
                            set @theWeekDay = WEEKDAY(@sDate)+1;
                            set @sNo = 0;
                            set @mycnt = 0;

                            SELECT @sNo := @sNo + 1 AS iNo, sDay, DATE_ADD(sDay, INTERVAL 4 DAY) AS eDay
                            FROM (
                                SELECT DATE_ADD(@theDay, INTERVAL @mycnt := @mycnt + 1 DAY) as sDay
                                FROM YearDays) acaDays
                            WHERE sDay BETWEEN @sDate AND @eDate
                            AND WEEKDAY(sDay)+1 = @theWeekDay;
                        END$$";                
               */
            case "AccIoc":
                return "SELECT c.sn AS accIocSn, c.accIocName
                        FROM AccMain a INNER JOIN
                        AccTitle b ON a.sn = b.accMainSn INNER JOIN
                        AccIoc c ON b.sn = c.accTitleSn
                        WHERE (a.accMainType = ".$p_data["accType"].")
                        AND c.accIocCode = '".$p_data["accCode"]."'";
            case "AccSerialNo":
                return "SELECT CONCAT('".$p_data["accSysC"]."', CASE WHEN MAX(RIGHT(serialNo, 13)) IS NULL THEN CONCAT(CURDATE() + 0, '00001') ELSE MAX(RIGHT(serialNo, 13)) +1 END) AS serialNo
                        FROM ".$p_data["accSysN"]."
                        WHERE MID(serialNo, 2, 8) = CURDATE() + 0 AND MID(serialNo, 1, 1) = '".$p_data["accSysC"]."'";
                /*
                return "SELECT CASE WHEN MAX(serialNo) IS NULL THEN CONCAT(CURDATE() + 0, '0001') ELSE MAX(serialNo) +1 END AS serialNo
                        FROM ".$p_data["accSysN"]."
                        WHERE LEFT(serialNo, 8) = CURDATE() + 0";                 
                 */
            case "AccMIncome":
                return "SELECT b.accMIncomeStuSn, a.serialNo, a.classLeavesSn, CONCAT(c.stuNo, '/', c.cName, ' ', c.eName) AS studentName, CONCAT(a.cYear, ' / ', a.cMonth) AS cYearMonth, SUM(b.amounts) AS amounts
                        FROM AccMIncomeStu a, AccMIncome b, Student c
                        WHERE a.sn = b.accMIncomeStuSn
                        AND a.studentSn = c.sn
                        AND b.accIocSn = 1
                        AND a.serialNo = '".$p_data["serialNo"]."'
                        GROUP BY a.serialNo";
                
            //進入系統預設列出資料
            
            //教職員工
            //待辦事項
            case "listToDoList":
                return "SELECT * 
                        FROM TodoList
                        WHERE (staffSnA = ".$_SESSION[self::csUsersSn]." OR staffSnB = ".$_SESSION[self::csUsersSn]." 
                        OR staffSnC = ".$_SESSION[self::csUsersSn].")
                        AND (proFlag<>1 OR proFlagReply<>1)                        
                        ORDER BY docNoa, docNob, cDate";
            //?�修??
            case "listRepairForm":
                return "SELECT a.*, b.fortuneNo, b.fortuneName, CONCAT(c.cName, ' ', c.eName) AS rName
                        FROM DamageRepair a INNER JOIN
                        Fortune b ON a.fortuneSn = b.sn AND a.staffSnA = ".$_SESSION[self::csUsersSn]." AND a.finishDate IS NULL 
                        LEFT OUTER JOIN Staff c ON a.staffSnR = c.sn
                        ORDER BY a.createDate";
                
            //?��?經�?
            //?��?幼�?請�?
            case "listBabyExcuse":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM BabyExcuse a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.exDate>=CURDATE() 
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.exDate";
            //?�藥給藥�??
            case "listMedicineTicket":                
                return "SELECT a.*, 
                        CASE WHEN a.userType = 2 THEN '★' ELSE '' END AS selfFlag,
                        CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM MedicineTicket a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate>=CURDATE() 
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //傷害事故
            case "listHurtInfo":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName,                     
                        CONCAT( CAST( a.occurTimeH AS CHAR ) , '：', CAST( a.occurTimeM AS CHAR ) ) AS occurTime
                        FROM HurtInfo a INNER JOIN
                        Student b ON a.studentSnA = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.occurDate ".$sqlDate7." 
                        INNER JOIN ClassLeaves c ON a.classLeavesSnA = c.sn
                        WHERE a.classLeavesSnA IN (".$p_data["csDataTemp"].")
                        ORDER BY a.occurDate";
            //幼兒行為觀察表
            case "listBabyWatch":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT( CAST( a.timeH AS CHAR ) , '：', CAST( a.timeM AS CHAR ) ) AS watchTime
                        FROM BabyWatch a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7." 
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //伙�??��??�形
            case "listFoodSick":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM FoodSick a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7." 
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //?�學?��?
            case "listTeachBook":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM TeachBook a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7." 
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //?��?維護
            case "listTeachProject":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM TeachProject a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7." 
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //?��??�絡�??�師)
            case "listEContactBook":                
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM EContactBook a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate>=DATE_SUB(CURDATE( ), INTERVAL 1 DAY)
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //家長?�聯�??
            case "listParentTalk":                
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM ParentTalk a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //幼�?保健
            case "listBabyHealth":                
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM BabyHealth a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //家長?��??��?
            case "listParentMsgReply": 
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM ParentMsg a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.createDate ".$sqlDate7."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.createDate";
            //?�室清�???
            case "listCleanList":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM CleanList a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate >= DATE_SUB(CURDATE( ), INTERVAL 1 DAY) 
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";                        
            //?��?檢核�?
            case "listDevControlStu":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM DevControlStu a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.classLeavesSn, b.stuNo";
            //法�??��???
            case "listLeagalInfect":            
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName,                     
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT( CAST( a.cTimeH AS CHAR ) , '：', CAST( a.cTimeM AS CHAR ) ) AS cTime
                        FROM LeagalInfect a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate DESC 
                        LIMIT 0, 10";
            //?�末評�?
            case "listFinalEvaluateStu":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM FinalEvaluateStu a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //學�?�??
            case "listLearnPro":
                return "SELECT a.*, CONCAT(b.academicYear, ' - ', b.semester) AS acaName,
                        CONCAT(b.sDate, ' ~ ', b.eDate) AS yearEduName
                        FROM LearnPro a, YearEdu b, ClassLeaves c
                        WHERE a.yearEduSn = b.sn
                        AND a.classLeavesSn = c.sn
                        AND b.academicYear = ".$_SESSION[self::csAcademicYear]." AND b.semester = ".$_SESSION[self::csSemester]."                        
                        ORDER BY b.sDate";
                //AND a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        
            //?��?管�?
            
            //?�議�??
            case "listUploadMeeting":
                return "SELECT a.*, CONCAT(b.cName, ' ', b.eName) AS chairmanStaffName, CONCAT(c.cName, ' ', c.eName) AS recorderStaffName
                        FROM UploadMeeting a LEFT OUTER JOIN
                        Staff b ON a.chairmanStaffSn = b.sn LEFT OUTER JOIN
                        Staff c ON a.recorderStaffSn = c.sn
                        WHERE a.cDate ".$sqlDate7."
                        ORDER BY a.cDate";
                        
            //?��?行政管�?
            
            //?�班�?
            case "listTimeTable":
                return "SELECT a.*, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName,                    
                        CONCAT(LPAD(a.sTimeH, 2, 0), ':', LPAD(a.sTimeM, 2, 0), '~', 
                        LPAD(a.eTimeH, 2, 0), ':', LPAD(a.eTimeM, 2, 0)) AS seTimeHM
                        FROM TimeTable a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE a.sn IN (
                            SELECT timeTableSn
                            FROM TimeTableCD
                            WHERE EXTRACT(YEAR_MONTH FROM cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                            GROUP BY timeTableSn
                        )
                        ORDER BY a.sDate, a.eDate";
            //?��?�?
            case "listBoard":
                return "SELECT *, CONCAT(sDate, ' ??', eDate) AS seDate
                        FROM Board
                        WHERE CURDATE() BETWEEN sDate AND eDate
                        ORDER BY sDate, eDate DESC";
            //專�?企�?
            case "listProject":
                return "SELECT *, 
                        CONCAT(sTimeH, '�?, sTimeM, ' ??', eTimeH, '�?, eTimeM) AS seTimeHM
                        FROM Project
                        WHERE EXTRACT(YEAR_MONTH FROM eDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY eDate";
            //每�?伙�?�? ?�出?��??��??��???
            case "listFoodTable":
                return "SELECT *
                        FROM FoodTable
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY cDate";
            //?�班資�?(?��???
            case "listSkillClass":
                return "SELECT c.*, 
                    CONCAT(a.theClassCode, ' ', a.theClassName) AS theClassName, 
                    CONCAT(b.classLeavesCode, ' ', b.classLeavesName) AS classLeavesName
                    FROM ClassInfo a INNER JOIN
                    ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                    StartClass c ON b.sn = c.classLeavesSn
                    WHERE (c.academicYear = ".$_SESSION[self::csAcademicYear]." AND c.semester = ".$_SESSION[self::csSemester].") 
                    AND (c.classType = 1)
                    ORDER BY a.theClassCode, b.classLeavesCode";                        
            //?��?檢核�?
            case "listDevControl":
                return "SELECT *, CONCAT(sAge, '�?, sMonth, '?��?', sDay, '�?~ ', eAge, '�?, eMonth, '?��?', eDay, '�?) AS ageScope
                        FROM DevControl
                        ORDER BY sAge, sMonth, sDay";
            //?��??�報
            case "listViolenceAnn":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT( CAST( a.timeH AS CHAR ) , '：', CAST( a.timeM AS CHAR ) ) AS timeHM
                        FROM ViolenceAnn a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //衛�??�主管�?
            case "listHygieneControl":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM HygieneControl a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY a.cDate";
            //食�?中�??�報
            case "listSitotoxismAnn":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName,                     
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT( CAST( a.cTimeH AS CHAR ) , '：', CAST( a.cTimeM AS CHAR ) ) AS cTime
                        FROM SitotoxismAnn a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate DESC
                        LIMIT 0, 10";
            //評�?表單
            case "listSelfEvaluate":
                return "SELECT *
                        FROM SelfEvaluate 
                        WHERE deleFlag = 0
                        ORDER BY selfEvaluateItem, sn";
            //?��?�?
            case "listSelfEvaluateCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM SelfEvaluateCheck a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        ORDER BY a.cDate DESC
                        LIMIT 0, 10";
            //檔�?管�?
            case "listFilesMana":
                return "SELECT *
                        FROM FilesMana
                        WHERE createDate ".$sqlDate7."
                        ORDER BY filesAlias";
            //?�具管�?
            case "listTeachTool":
                return "SELECT a.*, b.cardNo, b.cardName, CONCAT(c.cName, ' ', c.eName) AS staffName
                        FROM TeachTool a INNER JOIN
                        TeachCard b ON a.teachCardSn = b.sn INNER JOIN
                        Staff c ON a.staffSn = c.sn
                        WHERE cDate ".$sqlDate7."
                        ORDER BY a.cDate";
            //?�書資�?
            case "listBookData":
                return "SELECT *
                        FROM BookData
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY bookNo";
            //?�書管�?
            case "listBookMana":
                return "SELECT a.*, b.bookNo, b.bookName, CONCAT(c.cName, ' ', c.eName) AS staffName
                        FROM BookMana a INNER JOIN
                        BookData b ON a.bookDataSn = b.sn INNER JOIN
                        Staff c ON a.staffSn = c.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY a.cDate";
            //財產管�?
            case "listFortune":
                return "SELECT a.*, CONCAT(b.cName, ' ', b.eName) AS uName, CONCAT(c.cName, ' ', c.eName) AS kName
                        FROM Fortune a LEFT OUTER JOIN
                        Staff b ON a.staffSnU = b.sn LEFT OUTER JOIN
                        Staff c ON a.staffSnK = c.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())                        
                        ORDER BY a.fortuneNo";
            //?�修??
            case "listDamageRepair":
                return "SELECT a.*, b.fortuneNo, b.fortuneName, CONCAT(c.cName, ' ', c.eName) AS aName, CONCAT(d.cName, ' ', d.eName) AS rName
                        FROM DamageRepair a INNER JOIN
                        Fortune b ON a.fortuneSn = b.sn AND a.finishDate IS NULL LEFT OUTER JOIN
                        Staff c ON a.staffSnA = c.sn LEFT OUTER JOIN
                        Staff d ON a.staffSnR = d.sn
                        ORDER BY a.createDate";
            //?�室清�???
            case "listCleanListCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM CleanList a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        AND a.staffSnB = 0
                        ORDER BY a.cDate";
            //安全檢核檢查
            case "listSafeControlCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM SafeControlCheck a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY a.cDate";
            //專用車�?車檢??
            case "listCarCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM CarCheck a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE a.cDate ".$sqlDate7."
                        ORDER BY a.cDate";
            //消防安全檢查
            case "listFireControlCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM FireControlCheck a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY a.cDate";
                        
            //?�本資�?
            
            //?��?資�?
            case "listDept":
                return "SELECT *
                        FROM Dept
                        ORDER BY deptCode";
            //?�稱資�?
            case "listTitles":
                return "SELECT *
                        FROM Titles
                        ORDER BY titlesCode";
            //?�工?�本資�?
            case "listStaff":
                return "SELECT * 
                        FROM Staff 
                        ORDER BY createDate DESC";
            //?��?資�?
            case "listClassInfo":
                return "SELECT * 
                        FROM ClassInfo 
                        ORDER BY theClassCode";
            //學年度設�?
            case "listAcaSetup":
                return "SELECT * 
                        FROM AcaSetup 
                        WHERE academicYear = ".$_SESSION[self::csAcademicYear]."
                        ORDER BY academicYear, semester";
            //?�班資�?
            case "listStartClass":
                return "SELECT c.*, 
                    CONCAT(a.theClassCode, ' ', a.theClassName) AS theClassName, 
                    CONCAT(b.classLeavesCode, ' ', b.classLeavesName) AS classLeavesName
                    FROM ClassInfo a INNER JOIN
                    ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                    StartClass c ON b.sn = c.classLeavesSn
                    WHERE (c.academicYear = ".$_SESSION[self::csAcademicYear]." AND c.semester = ".$_SESSION[self::csSemester].") 
                    AND (c.classType = 0)
                    ORDER BY a.theClassCode, b.classLeavesCode";
            //幼�??�本資�?
            case "listStudent":                
                return "SELECT e.*, c.classLeavesSn,                    
                    CONCAT(c.academicYear, ' - ', c.semester) AS acaName, 
                    CONCAT(e.cName, ' ', e.eName) AS studentName
                    FROM ClassInfo a INNER JOIN
                    ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                    StartClass c ON b.sn = c.classLeavesSn INNER JOIN
                    StudentStatus d ON c.sn = d.startClassSn INNER JOIN
                    Student e ON d.studentSn = e.sn
                    WHERE (c.academicYear = ".$_SESSION[self::csAcademicYear]." AND c.semester = ".$_SESSION[self::csSemester].") 
                    AND (c.classType = 0) AND c.classLeavesSn IN (".$p_data["csDataTemp"].")
                    AND e.sn IN (
                        SELECT studentSn
                        FROM StudentStatus
                        GROUP BY studentSn
                        HAVING COUNT(*)=1
                    )
                    ORDER BY a.theClassCode, b.classLeavesCode, e.stuNo, e.cName";                        
            //?��?學�?清單
            case "listStudentStart":
                return "SELECT b.stuNo, b.cName, b.sn, b.eName, b.gender, b.birthDate
                        FROM StudentStatus a, Student b
                        WHERE a.startClassSn = '" . $p_data["startClassSn"] . "'
                        AND a.studentSn = b.sn
                        AND b.deleFlag = 0 AND b.enrollFlag = 1
                        ORDER BY b.stuNo";
            //家長資�?
            case "listParent":
                return "SELECT a.*
                        FROM Parent a, PSRelation b
                        WHERE a.sn = b.parentSn
                        AND b.studentSn IN (
                            SELECT a.studentSn
                            FROM StudentStatus a, StartClass b
                            WHERE a.startClassSn = b.sn
                            AND b.academicYear = 900 AND b.semester = 1
                            AND b.classType = 0
                            GROUP BY a.studentSn
                            HAVING COUNT(*)=1
                        )
                        GROUP BY a.sn
                        ORDER BY a.cName, a.idNo";
            //交�?車�??��???
            case "listBabyCarRec":
                return "SELECT *
                        FROM BabyCarRec
                        WHERE cDate ".$sqlDate7."
                        ORDER BY babyCarInfoSn";
            //角色管�?
            case "listRole":
                return "SELECT *
                        FROM Role
                        ORDER BY roleName";
                        
            //家長?�能??
                                    
            //?��??�絡�?家長)
            case "listEContactBookParent":
                return "SELECT b.*, CONCAT(b.academicYear, ' - ', b.semester) AS acaName, 
                        CONCAT(d.cName, ' ', d.eName) AS studentName
                        FROM ClassLeaves a INNER JOIN
                        EContactBook b ON a.sn = b.classLeavesSn INNER JOIN
                        PSRelation c ON b.studentSn = c.studentSn INNER JOIN
                        Student d ON c.studentSn = d.sn
                        WHERE b.cDate>=DATE_SUB(CURDATE( ), INTERVAL 1 DAY)
                        AND (c.parentSn = '" . $_SESSION[self::csUsersSn] . "')
                        ORDER BY b.cDate";
            //幼�?請�?記�?(家長)
            case "listBabyRestParent":
                return "SELECT b.*, 
                        CASE WHEN b.userType = ".$_SESSION[self::csUserType]." AND b.userSn = ".$_SESSION[self::csUsersSn]." 
                        THEN '★' ELSE '' END AS selfFlag,
                        CONCAT(b.academicYear, ' - ', b.semester) AS acaName, 
                        CONCAT(d.cName, ' ', d.eName) AS studentName
                        FROM ClassLeaves a INNER JOIN
                        BabyExcuse b ON a.sn = b.classLeavesSn INNER JOIN
                        PSRelation c ON b.studentSn = c.studentSn INNER JOIN
                        Student d ON c.studentSn = d.sn
                        WHERE (b.exDate BETWEEN DATE_SUB(CURDATE( ), INTERVAL 15 DAY) AND DATE_ADD(CURDATE( ), INTERVAL 15 DAY))
                        AND (c.parentSn = '" . $_SESSION[self::csUsersSn] . "')
                        ORDER BY b.exDate";
            //家長?��?(家長)
            case "listParentMsg":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT(d.cName, ' ', d.eName) AS staffName
                        FROM ParentMsg a INNER JOIN
                        Student b ON a.studentSn = b.sn INNER JOIN
                        ClassLeaves c ON a.classLeavesSn = c.sn LEFT OUTER JOIN 
                        Staff d ON a.staffSn = d.sn
                        WHERE (a.createDate BETWEEN DATE_SUB(CURDATE( ), INTERVAL 15 DAY) AND DATE_ADD(CURDATE( ), INTERVAL 15 DAY))
                        AND (a.parentSn = '" . $_SESSION[self::csUsersSn] . "')
                        ORDER BY a.createDate";
            //?�藥給藥�??(家長)
            case "listMedicineTicketParent":
                return "SELECT b.*, 
                        CASE WHEN b.userType = 2 THEN '★' ELSE '' END AS selfFlag,
                        CONCAT(b.academicYear, ' - ', b.semester) AS acaName, 
                        CONCAT(d.cName, ' ', d.eName) AS studentName
                        FROM ClassLeaves a INNER JOIN
                        MedicineTicket b ON a.sn = b.classLeavesSn INNER JOIN
                        PSRelation c ON b.studentSn = c.studentSn INNER JOIN
                        Student d ON c.studentSn = d.sn
                        WHERE (b.cDate BETWEEN DATE_SUB(CURDATE( ), INTERVAL 15 DAY) AND DATE_ADD(CURDATE( ), INTERVAL 15 DAY))
                        AND (c.parentSn = '" . $_SESSION[self::csUsersSn] . "')
                        ORDER BY b.cDate";
            //期末評量(家長)
            case "listFinalEvaluateStuParent":
                return "SELECT b.*, CONCAT(b.academicYear, ' - ', b.semester) AS acaName, 
                        CONCAT(d.cName, ' ', d.eName) AS studentName
                        FROM ClassLeaves a INNER JOIN
                        FinalEvaluateStu b ON a.sn = b.classLeavesSn INNER JOIN
                        PSRelation c ON b.studentSn = c.studentSn INNER JOIN
                        Student d ON c.studentSn = d.sn
                        WHERE (c.parentSn = '" . $_SESSION[self::csUsersSn] . "')
                        ORDER BY b.cDate";
            //學�?�??(家長)
            case "LearnProParent":
                return "SELECT sn, CONCAT(sDate, ' ~ ', eDate) AS yearEduName, subject, CONCAT('".$p_data["defaultImgPath"]."', CASE fileType WHEN '1' THEN 'Video.jpg' ELSE fileName END) AS imageSource, COUNT(*) AS picNum
                        FROM (
                            SELECT b.sn, a.sDate, a.eDate, b.subject, c.fileType, c.fileName, c.createDate, c.reviseDate
                            FROM YearEdu a, LearnPro b, LearnProPT c, LearnProTG d
                            WHERE a.sn = b.yearEduSn AND b.sn = c.learnProSn AND c.sn = d.learnProPTSn
                            AND d.academicYear = ".$p_data["academicYear"]." AND d.semester = ".$p_data["semester"]."
                            AND d.classLeavesSn = ".$p_data["classLeavesSn"]." AND d.studentSn = ".$p_data["studentSn"]."
                            UNION ALL
                            SELECT b.sn, a.sDate, a.eDate, b.subject, c.fileType, c.fileName, c.createDate, c.reviseDate
                            FROM YearEdu a, LearnPro b, LearnProPT c
                            WHERE a.sn = b.yearEduSn AND b.sn = c.learnProSn
                            AND a.academicYear = ".$p_data["academicYear"]." AND a.semester = ".$p_data["semester"]."
                            AND b.classLeavesSn = ".$p_data["classLeavesSn"]."
                            AND c.sn NOT IN (
                                SELECT d.learnProPTSn
                                FROM YearEdu a, LearnPro b, LearnProPT c, LearnProTG d
                                WHERE a.sn = b.yearEduSn AND b.sn = c.learnProSn AND c.sn = d.learnProPTSn
                                AND a.academicYear = ".$p_data["academicYear"]." AND a.semester = ".$p_data["semester"]."
                                AND b.classLeavesSn = ".$p_data["classLeavesSn"]."
                            )) AS LearnProParent
                        GROUP BY sn
                        ORDER BY sDate, createDate, reviseDate";
                /*              
                return "SELECT b.sn, CONCAT(a.sDate, ' ~ ', a.eDate) AS yearEduName, b.subject, CONCAT('".$p_data["defaultImgPath"]."', c.fileName) AS imageSource, COUNT(*) AS picNum
                        FROM YearEdu a, LearnPro b, LearnProPT c, LearnProTG d
                        WHERE a.sn = b.yearEduSn AND b.sn = c.learnProSn AND c.sn = d.learnProPTSn
                        AND d.academicYear = ".$p_data["academicYear"]." AND d.semester = ".$p_data["semester"]."
                        AND d.classLeavesSn = ".$p_data["classLeavesSn"]." AND d.studentSn = ".$p_data["studentSn"]."
                        GROUP BY b.sn
                        ORDER BY a.sDate, c.createDate, c.reviseDate";
                 */
                
            //年度行�???
            case "listYearCal":
                return "SELECT *, CONCAT(academicYear, ' - ', semester) AS acaName
                        FROM YearEdu
                        WHERE sDate >= CURDATE()
                        ORDER BY sDate";
            //伙�??��??�形
            case "listFoodSickHistory":
                return "SELECT *
                        FROM FoodSick
                        WHERE (studentSn = '" . $p_data["studentSn"] . "')                        
                        ORDER BY cDate";
            //伙�??��??�形
            case "listFoodTableSick":
                switch ($_SESSION[self::csUserType]){
                    case "2":
                        $tSqlCon = " AND e.sn IN (
                                        SELECT studentSn
                                        FROM PSRelation
                                        WHERE parentSn = '".$_SESSION[self::csUsersSn]."'
                                    )";
                        break;
                    case "3":
                    case "4":
                        $tSqlCon = "";                        
                        break;
                }
                
                return "SELECT a.sysType, b.itemName, d.classLeavesSn, d.senCon, e.sn AS studentSn, CONCAT(e.cName, ' ', e.eName) AS ceName
                        FROM FoodTableDetail a,  FuncItem b, FoodSickFB c, FoodSick d, Student e
                        WHERE a.funcItemSn = b.sn AND b.sn = c.funcItemSn
                        AND c.foodSickSn = d.sn AND d.studentSn = e.sn
                        AND a.foodTableSn = ".$p_data["foodTableSn"]."
                        AND d.academicYear = ".$_SESSION[self::csAcademicYear]." AND d.semester = ".$_SESSION[self::csSemester]."
                        AND e.enrollFlag = 1 AND e.deleFlag = 0
                        ".$tSqlCon."
                        ORDER BY a.sysType, d.classLeavesSn, e.stuNo";
            //學�??��?歷史
            case "listStudentStatusHistory":
                return "SELECT b.sn AS startClassSn, 
                        CASE WHEN b.academicYear = ".$_SESSION[self::csAcademicYear]." AND b.semester = ".$_SESSION[self::csSemester]." 
                        THEN '★' ELSE '' END AS acaFlag,
                        b.academicYear, b.semester, b.classLeavesSn
                        FROM StudentStatus a INNER JOIN StartClass b ON a.startClassSn = b.sn AND b.classType IN (0, 1)
                        WHERE a.studentSn = '" . $p_data["studentSn"] . "'
                        ORDER BY b.academicYear, b.semester";
            //?��?清�???
            /*
            case "listCleanList":
                if ($p_data["selfFlag"]=="Y"){
                    $tSqlCon = " AND (a.creator = '" . $_SESSION[Fun::csLoginId] . "')";
                }else{
                    $tSqlCon = "";
                }
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM CleanList a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn
                        WHERE (a.cDate BETWEEN DATE_SUB(CURDATE( ), INTERVAL 15 DAY) AND DATE_ADD(CURDATE( ), INTERVAL 15 DAY))
                        ".$tSqlCon."
                        ORDER BY a.cDate";
            */
            //calendar                         
            case "gCalendar":
                switch ($_SESSION[self::csUserType]){
                    case "2":
                        $tSqlCon = "";
                        break;
                    case "3":
                    case "4":
                        $tSqlCon = " UNION ALL
                                    SELECT 1 AS isWork, b.cDate AS calDate, 
                                    CONCAT('∮ ', c.cName, ' ', c.eName , ' ', d.codeDesc,  
                                    LPAD(a.sTimeH, 2, 0), ':', LPAD(a.sTimeM, 2, 0), '~', LPAD(a.eTimeH, 2, 0), ':', LPAD(a.eTimeM, 2, 0)) AS comment, 
                                    5 AS infoFlag
                                    FROM TimeTable a, TimeTableCD b, Staff c, _code_".Fun::$sLang." d
                                    WHERE a.sn = b.timeTableSn
                                    AND a.staffSn = c.sn
                                    AND a.jobs = d.codeId AND d.typeId = 'jobs'
                                    AND c.dutyFlag = 1 AND c.deleFlag = 0
                                    AND EXTRACT(YEAR_MONTH FROM b.cDate) LIKE '" . $p_data["ym"] . "%'";                        
                        break;
                }
                
                return "SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('※ ', contents) AS comment, 1 AS infoFlag
                        FROM YearCal
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%'
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('☆ ', LPAD(timeH, 2, 0), ':', LPAD(timeM, 2, 0), ' ', contents) AS comment, 2 AS infoFlag
                        FROM PersonCal
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' 
                        AND staffSn = '" . $_SESSION[self::csUsersSn] . "')
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('＃ ', contents) AS comment, 3 AS infoFlag
                        FROM TodoList
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' 
                        AND (staffSnA = '" . $_SESSION[self::csUsersSn] . "' OR staffSnB = '" . $_SESSION[self::csUsersSn] . "' 
                        OR staffSnC = '" . $_SESSION[self::csUsersSn] . "'))
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('Ψ ', remark) AS comment, 4 AS infoFlag
                        FROM FoodTable
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%'
                        ".$tSqlCon."
                        ";
            case "gCalendarStaffView":
                return "SELECT 1 AS isWork, cDate AS calDate, CONCAT('※ ', contents) AS comment, 1 AS infoFlag
                        FROM YearCal
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%'
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('☆ ', LPAD(timeH, 2, 0), ':', LPAD(timeM, 2, 0), ' ', contents) AS comment, 2 AS infoFlag
                        FROM PersonCal
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' 
                        AND staffSn = '".$p_data["StaffSn"]."')
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, CONCAT('＃ ', contents) AS comment, 3 AS infoFlag
                        FROM TodoList
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' 
                        AND (staffSnA = '".$p_data["StaffSn"]."' OR staffSnB = '".$p_data["StaffSn"]."'))
                        UNION ALL
                        SELECT 1 AS isWork, b.cDate AS calDate, CONCAT('∮ ', c.cName, ' ', c.eName , ' ', d.codeDesc,  
                        LPAD(a.sTimeH, 2, 0), ':', LPAD(a.sTimeM, 2, 0), '~', LPAD(a.eTimeH, 2, 0), ':', LPAD(a.eTimeM, 2, 0)) AS comment, 5 AS infoFlag
                        FROM TimeTable a, TimeTableCD b, Staff c, _code_".Fun::$sLang." d
                        WHERE a.sn = b.timeTableSn
                        AND a.staffSn = c.sn
                        AND a.jobs = d.codeId AND d.typeId = 'jobs'
                        AND c.dutyFlag = 1 AND c.deleFlag = 0
                        AND EXTRACT(YEAR_MONTH FROM b.cDate) LIKE '" . $p_data["ym"] . "%'
                        AND a.staffSn = '".$p_data["StaffSn"]."'
                        ";

            //?�人行�????�人?�日)
            case "gPersonCalList":
                $ts_sql = "SELECT *
                    FROM PersonCal
                    WHERE cDate = '{0}' AND staffSn = '{1}'";                    
                return Fun::format($ts_sql, $p_data["ymd"], $_SESSION[self::csUsersSn]);
            //年度行�???
            case "gYearCalList":
                $ts_sql = "SELECT *
                    FROM YearCal
                    WHERE cDate = '{0}'";                    
                return Fun::format($ts_sql, $p_data["ymd"]);
            //待辦事�?(?�人?�日)
            case "gTodoList":
                $ts_sql = "SELECT *
                    FROM TodoList
                    WHERE cDate = '{0}' AND (staffSnA = '{1}' OR staffSnB = '{1}' OR staffSnC = '{1}')";
                return Fun::format($ts_sql, $p_data["ymd"], $_SESSION[self::csUsersSn]);
            //伙�?�??�人?�日)
            case "gFoodTable":
                $ts_sql = "SELECT b.sysType, c.itemName
                        FROM FoodTable a, FoodTableDetail b, FuncItem c
                        WHERE a.sn = b.foodTableSn
                        AND b.funcItemSn = c.sn
                        AND a.cDate = '{0}'
                        ORDER BY b.sysType";                
                return Fun::format($ts_sql, $p_data["ymd"]);
            //?�班�?級
            case "gTimeTable":
                $ts_sql = "SELECT a.staffSn, 
                    CONCAT(LPAD(a.sTimeH, 2, 0), ':', LPAD(a.sTimeM, 2, 0), '~', LPAD(a.eTimeH, 2, 0), ':', LPAD(a.eTimeM, 2, 0)) AS seTimeHM, a.jobs
                    FROM TimeTable a, TimeTableCD b
                    WHERE a.sn = b.timeTableSn
                    AND b.cDate = '{0}'";                    
                return Fun::format($ts_sql, $p_data["ymd"]);
            //衛�?事�?流�?
            case "listHygieneProcess":
                return "SELECT *, 
                        CASE WHEN academicYear = '".$_SESSION[self::csAcademicYear]."' 
                        AND semester = '".$_SESSION[self::csSemester]."' THEN '★' ELSE '' END AS acaFlag
                        FROM HygieneProcess
                        ORDER BY cDate";
            //?��??��???
            case "listFuncItem":
            /*
                return "SELECT *
                        FROM FuncItem
                        WHERE sysType = '".$p_data["sysType"]."'
                        AND deleFlag = 0
                        ORDER BY sn";
            */
                return "SELECT *
                        FROM FuncItem
                        WHERE sysType in (".$p_data["sysType"].")
                        AND deleFlag = 0
                        ORDER BY sn";
            //?�藥給藥�??
            case "listMedicineRecord":
                return "SELECT a.medTime, a.medTimeCon,
                        CONCAT(a.medTimeH, ':', a.medTimeM) AS medTimeHM, 
                        a.inMedType,
                        CONCAT(b.cName, ' ', b.eName) AS ceName
                        FROM MedicineRecord a LEFT OUTER JOIN Staff b ON a.staffSn = b.sn
                        WHERE (a.medicineTicketSn = '" . $p_data["medicineTicketSn"] . "')                        
                        ORDER BY a.medTime";
            //學�?統�?
            case "listRptStudent":
                return "SELECT CONCAT(a.theClassName, ' ', b.classLeavesName) AS label, COUNT(*) AS data
                        FROM ClassInfo a INNER JOIN
                        ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                        StartClass c ON b.sn = c.classLeavesSn INNER JOIN
                        StudentStatus d ON c.sn = d.startClassSn INNER JOIN
                        Student e ON d.studentSn = e.sn
                        WHERE (c.academicYear = '".$_SESSION[self::csAcademicYear]."' AND c.semester = '".$_SESSION[self::csSemester]."') 
                        AND (c.classType IN (0, 1)) AND (e.enrollFlag = 1) AND (e.deleFlag = 0)
                        GROUP BY c.classLeavesSn
                        ORDER BY a.theClassCode, b.classLeavesCode";
            //?�職?�統�?
            case "listRptTeacher":
                return "SELECT b.deptName AS label, COUNT(*) AS data
                        FROM Staff a INNER JOIN
                        Dept b ON a.deptSn = b.sn
                        WHERE (a.dutyFlag = 1) AND (a.deleFlag = 0)
                        GROUP BY a.deptSn
                        ORDER BY b.deptCode";
            //幼�??�康?�常統�?
            case "listSickSum":
                if ($p_data["semester"]!=""){
                    $tSqlCon = " AND (b.academicYear = ".$p_data["academicYear"]." AND b.semester = ".$p_data["semester"].")";
                }else{
                    $tSqlCon = " AND (b.academicYear = ".$p_data["academicYear"].")";
                }
                return "SELECT itemName, funcItemSn, SUM(M1) AS M1, SUM(M2) AS M2, SUM(M3) AS M3, SUM(M4) AS M4, SUM(M5) AS M5, SUM(M6) AS M6, 
                        SUM(M7) AS M7, SUM(M8) AS M8, SUM(M9) AS M9, SUM(M10) AS M10, SUM(M11) AS M11, SUM(M12) AS M12
                        FROM(
                            SELECT c.itemName, a.funcItemSn,
                            CASE month(b.cDate) WHEN 1 THEN COUNT(*) ELSE 0 END AS M1,
                            CASE month(b.cDate) WHEN 2 THEN COUNT(*) ELSE 0 END AS M2,
                            CASE month(b.cDate) WHEN 3 THEN COUNT(*) ELSE 0 END AS M3,
                            CASE month(b.cDate) WHEN 4 THEN COUNT(*) ELSE 0 END AS M4,
                            CASE month(b.cDate) WHEN 5 THEN COUNT(*) ELSE 0 END AS M5,
                            CASE month(b.cDate) WHEN 6 THEN COUNT(*) ELSE 0 END AS M6,
                            CASE month(b.cDate) WHEN 7 THEN COUNT(*) ELSE 0 END AS M7,
                            CASE month(b.cDate) WHEN 8 THEN COUNT(*) ELSE 0 END AS M8,
                            CASE month(b.cDate) WHEN 9 THEN COUNT(*) ELSE 0 END AS M9,
                            CASE month(b.cDate) WHEN 10 THEN COUNT(*) ELSE 0 END AS M10,
                            CASE month(b.cDate) WHEN 11 THEN COUNT(*) ELSE 0 END AS M11,
                            CASE month(b.cDate) WHEN 12 THEN COUNT(*) ELSE 0 END AS M12
                            FROM SickLog a, TeachBook b, FuncItem c
                            WHERE a.teachBookSn = b.sn AND a.funcItemSn = c.sn
                            AND c.sysType=3 AND c.deleFlag = 0
                            ".$tSqlCon."
                            GROUP BY a.funcItemSn, month(b.cDate)
                        ) AS c
                        GROUP BY funcItemSn";
            //今日?�席統�?
            case "listAbsentStutas":
                if ($p_data["classLeavesSn"]!=""){
                    $tSqlCon = " AND (c.classLeavesSn = ".$p_data["classLeavesSn"].")";
                }else{
                    $tSqlCon = "";
                }
                return "SELECT CONCAT(a.theClassName, ' ', b.classLeavesName) AS itemName, COUNT(e.sn) AS M1, 
                        COUNT(f.sn) AS M2, 
                        COUNT(e.sn) - COUNT(f.sn) AS M3, 
                        CASE g.excuse WHEN '1' THEN COUNT(g.sn) ELSE 0 END AS M4,
                        CASE g.excuse WHEN '2' THEN COUNT(g.sn) ELSE 0 END AS M5,
                        CASE g.excuse WHEN '9' THEN COUNT(g.sn) ELSE 0 END AS M6
                        FROM ClassInfo a INNER JOIN
                        ClassLeaves b ON a.sn = b.classInfoSn INNER JOIN
                        StartClass c ON b.sn = c.classLeavesSn INNER JOIN
                        StudentStatus d ON c.sn = d.startClassSn INNER JOIN
                        Student e ON d.studentSn = e.sn LEFT OUTER JOIN
                        BabyCheck f ON e.sn = f.studentSn AND f.attendFlag = 1 AND f.cDate = '".$p_data["aDate"]."'
                        LEFT OUTER JOIN BabyExcuse g ON e.sn = g.studentSn AND g.exDate = '".$p_data["aDate"]."'
                        WHERE (c.academicYear = '".$_SESSION[self::csAcademicYear]."' AND c.semester = '".$_SESSION[self::csSemester]."') 
                        AND (c.classType IN (0, 1)) AND (e.enrollFlag = 1) AND (e.deleFlag = 0)
                        ".$tSqlCon."
                        GROUP BY c.classLeavesSn
                        ORDER BY a.theClassCode, b.classLeavesCode";
        }
        return "";
    }
    
    
    //return  JsonObject
    public static function _setDeptSession()
    {
        return null;
        //$ts_sql = "select deptSeq as " . Fun::csDeptId . " from _User where userId = '" . Fun::getUserId() . "'";
        //return Fun::readRow(ts_sql);
    }


    //p_data ?�含?�種變數: data,fun,send...
    public static function updateDB($p_data, $pf_update)
    {

        $t_upd = new UpdateDB();

        //string ts_msg = t_upd->init(ps_fun, ps_inf, ps_input, pf_update);
        $ts_msg = $t_upd->init($p_data, $pf_update);
        if ($ts_msg != "")
        {
            $ts_msg = Fun::jsonError($ts_msg);
            goto lab_exit;
            //return;
        }

        $ts_msg = $t_upd->save();
        if ($ts_msg != "")
            $ts_msg = Fun::jsonError($ts_msg);
        else
            $ts_msg = Fun::jsonData("auto", $t_upd->getIdentStr());
            //$ts_msg = $t_upd->getIdentStr();


        //return self::lab_exit($t_upd, $ts_msg);                
        goto lab_exit;
        //case of ok below !

        //t_upd = null;
    //t_upd->release();
    //return ts_result;
    //}

    lab_exit:
        //release first !
        $t_upd = null;

        return $ts_msg;

        //t_upd->release();

        //?��?詳細?�錯誤�??�給管�???
        //Fun.sendRoot("E", ts_error);

        //return Fun.jsonError(ts_error);
    }

    
    //called by _Login.ashx, ?�入?��??�登?��??�叫此函??此�?已�?寫入 session).
    public static function afterLogin(){
         //?��?使用?��???roleSn
         $ts_sql = "SELECT sn, userType, userSn 
                    FROM Account 
                    WHERE loginId='" . $_SESSION[Fun::csLoginId] . "' 
                    AND deleFlag = 0";
         $t_row = Fun::readRow($ts_sql);
         if ($t_row){
            //?�斷使用?��???
            $ts_sqla = "";
            switch ($t_row["userType"]){
            case "2":
                $ts_sqla = "select sn, cName, '' AS eName, 'Parent' AS userTypeName from Parent where deleFlag=0 AND sn = " . $t_row["userSn"] ;
                break;
            case "3":
                $ts_sqla = "select sn, cName, eName, 'Staff' AS userTypeName from Staff where deleFlag=0 AND sn = " . $t_row["userSn"] ;
                break;
            case "4":
                $ts_sqla = "select 0 as sn, 'Admin' as cName, 'Administrator' AS eName, 'Admin' AS userTypeName" ;
                break;
            }
            //?��??��?資�?
            if ($ts_sqla != ""){
                $t_rowa = Fun::readRow($ts_sqla);
                if ($t_rowa){
                    $t_user[self::csAccountSn] = $t_row["sn"]; //使用?�帳??
                    $t_user[self::csUsersSn] = $t_rowa["sn"]; //使用?�唯�????      
                    $t_user[self::csUsersCName] = $t_rowa["cName"]; //使用?�中?��?
                    $t_user[self::csUsersEName] = $t_rowa["eName"]; //使用?�英?��?
                    $t_user[self::csUserTypeName] = $t_rowa["userTypeName"];    //使用?��??��?�?
                    $t_user[self::csUserType] = $t_row["userType"]; //使用?��??�代??
                    //$t_user[self::csRoleSn] = $t_row["roleSn"]; //使用?��??��???                   
                }
            }
         }         
         //?��??��?設�??�學年�?
         $ts_sql = "SELECT academicYear, semester FROM AcaSetup WHERE currentStatus=1 AND deleFlag = 0";
         $t_row = Fun::readRow($ts_sql);
         if ($t_row){
            $t_user[self::csAcademicYear] = $t_row["academicYear"];
            $t_user[self::csSemester] = $t_row["semester"];
         }
         
         foreach ($t_user as $key => $value){
            $_SESSION[$key] = $value;
         }
    }
    
    //called by _Login.ashx, ?�入?��??�登?��??�叫此函??此�?已�?寫入 session).
    public static function getDbStr($ps_db = ""){
        if ($ps_db == "")
            $ps_db = "db";
            
        $ts_schoolSn = isset($_SESSION[self::csSchoolDSn]) ? $_SESSION[self::csSchoolDSn] : '';     
        return Fun::format(constant("Config::" . $ps_db), $ts_schoolSn);      
    }

    
    //exam rowtype to table pre
    //S, M, T, TM, R, W
    public static function rowTypeToPre($ps_rowType){
        return ($ps_rowType == "SE" || $ps_rowType == "SME") ? "Sys" : "" ;
    }        

}//class

?>
