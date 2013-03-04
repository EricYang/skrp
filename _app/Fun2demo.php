<?php

require_once(dirname(__FILE__)."/Fun.php"); 
require_once(dirname(__FILE__)."/UpdateDB.php"); 
//require_once(dirname(__FILE__)."/AES.class.php"); 


class Fun2{
    //property
    //public static cnEncryptType = 2;      //�w�]����ƥ[�K�覡, 0(�L), 1(�R�Akey), 2(�ʺAkey)
    //const csBaseKey = "tqmtmkshjrrmgwghvifvfyiu";  //"�t�Ժ޲z�t��"
    //public $csBaseKey = "tqmtmkshjrrmgwghvifvfyiu";  //"�t�Ժ޲z�t��"
    //public const int cnKeySize = 256;

    //constant
    const cbOpenAllMenu = false;
    const csAutoLoginId = "";
    //const cbOpenAllMenu = true;
    //const csAutoLoginId = "Joyce";
    //const csAutoLoginId = "U000000001";
    
    const cnDBType = Fun::cnMySql;
    //const cnDBType = Fun::cnMSSql;
    //
    const cbDeptStr = false;
    const csDefaultLang = "tw";
    
    //add by Louis - start
    //login user data expansion    
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
    
    //add by Louis - end
    
    //�ۭq�ܼ�
    public static $bAdmin = false;      //is admin
    
    //constructor    
    public function __construct(){
        
    }
    
    //=== method begin ===
//called by Fun
    public static function init()
    {
        
    }
    //JsonArray.
    public static function _getAppList($pb_hasPwd)    
    {
         //�p�G�O�޲z�̵������v��
         if ($_SESSION[Fun::csLoginId]=="AdminSmarten"){             
            $ts_sqlb = "SELECT pCode AS data, sysName AS label, groupId, 
                    CONCAT('image/child/', imageSource) AS imageSource, '1111111' AS fun
                    FROM Program
                    WHERE deleFlag = 0
                    ORDER BY sysCode";            
            $ta_rowb = Fun::readRows($ts_sqlb);
            return $ta_rowb;
         }else{
            //����ϥΪ̸�� roleSn
            $ts_sqla = "SELECT sn, userType, userSn 
                        FROM Account 
                        WHERE loginId='" . $_SESSION[Fun::csLoginId] . "' 
                        AND deleFlag = 0";
            $t_rowa = Fun::readRow($ts_sqla);
            if ($t_rowa){
                //CONCAT(a.insertFunc, a.searchFunc, a.updateFunc, a.deleteFunc, a.reportFunc, a.exportFunc, a.viewFunc) AS fun �v���ƦC����
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
                /*
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
                        ) AS fun                        
                        FROM RoleAca a INNER JOIN
                        Program b ON a.programSn = b.sn
                        WHERE (a.roleSn = ".$t_rowa["roleSn"].")
                        AND b.deleFlag = 0
                        GROUP BY b.pCode, b.sysName, b.groupId
                        ORDER BY b.sysCode";            
                */
                $ta_rowb = Fun::readRows($ts_sqlb);
                return $ta_rowb;            
                }             
         } 
                
        /*
        $ta_row;
        if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] === true){
            $ta_row = array(
                array("data"=>"Student", "fun"=>"1111010"),
                array("data"=>"Exam", "fun"=>"1111000"),
                array("data"=>"StuExam", "fun"=>"1111000")
            );
        }else{
            $ta_row = array(
                array("data"=>"SelfExam", "fun"=>"0001001"),
                array("data"=>"StuBook", "fun"=>"1111000")
            );            
        }
        */
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
    
    
    //�[�K: AES 256
    //�Ǧ^�r��
    //public static function decode($ps_text, $pb_encrypt){
    public static function decrypt($ps_text){
        //temp �Ȥ��ѱK
        return $ps_text;
        
    //$csBaseKey = "tqmtmkshjrrmgwghvifvfyiu";  //"�t�Ժ޲z�t��"
    
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
        //�@�g
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

            //====== �ܼƥ����s�b begin !! ======
            case "LoginUser":   //�p�G���n�J�e��, �h������@!! �Ǧ^ Fun::csPwd, csUserId, csUserName
                $_SESSION["isAdmin"] = null;
                
                //�P�_���L�����
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
                    //$_SESSION["csSchoolDSn"] = $t_row["sn"];
                }
                
                $ts_loginId = $p_data[Fun::csLoginId];
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

            //�Ǧ^ userId, userName only !!
            //�Ǧ^ñ�֬y�{����, ���W�٥����t�X�e��: levelNo, �{�ǦW��(procName), 
            ñ�֪̥N��(signerId), ñ�֪̩m�W(signerName), //ñ�֪�����, //�ҵ{�N��, //�ҵ{�W�� ;(�p�G�䤣��h sendRoot())
            //��J�Ѽ� p_data �]�t�ܼ�: //_flowName, _procCname, _userName, _levelNo
            case "FlowJobs":
                string ts_signerValue = (p_data["signerValue"] != null) ? p_data["signerValue"].ToString() : "";

                switch (p_data["signerType"].ToString())
                {
                    case "AM":  //�����D��
                        return "select u.userId, u.userName " +
                            "from _Dept d " +
                            "inner join _User u on u.userId = d.bossId " +
                            "where d.deptSeq = " + Fun::getDeptId();
                    case "SM":  //���w�����D��, �ϥ� deptName !!
                        return "select u.userId, u.userName " +
                            "from _Dept d " +
                            "inner join _User u on u.userId = d.bossId " +
                            "where d.deptName = '" + ts_signerValue + "'";
                    case "SF":  //���w��쪺�Τ�
                        return "select userId, userName " +
                            "from _User " +
                            "where userId = '" + p_data[ts_signerValue] + "'";
                    case "SU":  //���w�H��, ������@�o�����{���X !!
                        return "select userId, userName from _User where userId='" + ts_signerValue + "'";

                    case "SR":  //���w����, ������@�o�����{���X !!
                        break;

                    default:
                        break;
                }

                break;


            //���: data, type, list
            //�Ǧ^�@�����, ���W�٬� "email", ���e���H�c
            case "MailBox":        
                switch (ts_type)
                {
                    case "user":    //user, ������@�o�Ӷ��ت��{���X, see Fun::jsonToMailBoxes() !!
                        //temp change !!
                        //return "select 'malcom@shanger.com.tw' as email " + 
                        return "select case when email is null or email = '' then userId+'@shanger.com.tw' else email end as email " + 
                            "from _User " +
                            "where userId in (" + Fun::listAddQuote(p_data["list"].ToString()) + ") ";

                        //return Fun::sqlAddDB(ts_sql);
                        //return Fun::replaceStr(ts_sql);

                }

                return "";
            //====== �ܼƥ����s�b end !! ======

            case "SystemTable":
                return "select top 1 * from Setting";

                
            */

            case "UpdateDB":
                switch ($ts_type){
                    case "type1":
                        $ts_sql = "UPDATE {0} SET {1} = '{2}' WHERE sn = '{3}'";
                        return Fun::format($ts_sql, $p_data["tableName"], $p_data["columnName"], $p_data["theValue"], $p_data["keyValue"]);                        
                }
                break;
            case "Json":        //�b�s��e����Jseq�ɥ����ˬd�s���v��
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
            

            case "CodeTable":   //�Ϥ��y�t
                return "select codeId as data, codeDesc as label from " .
                    "_Code_" . Fun::$sLang . " " .
                    "where typeId='" . $ts_type . "' " .
                    "and rowStatus='A' " .
                    "order by orderNo, codeId";
                                            
            case "ComboBox":
                switch ($ts_type)
                {                        
				    //add by Louis - start
                    case "classInfo":    //�Z�O
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
                    case "classLeaves":  //�Z��
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
                
                    //�D�t�ΦW��
                    case "ProgramM":
                        return "SELECT sn AS data, CONCAT( RTRIM( sysCode ) , ' ', RTRIM( sysName ) ) AS label
                                FROM ProgramM                                
                                ORDER BY sysCode";
                    //�t�ΦW��
                    case "Program":
                        return "SELECT sn AS data, CONCAT( RTRIM( sysCode ) , ' ', RTRIM( sysName ) ) AS label
                                FROM Program
                                WHERE deleFlag = 0
                                ORDER BY sysCode";
                    //����            
                    case "Dept":
                        return "SELECT sn as data, deptName AS label
                                FROM Dept
                                ORDER BY deptCode";
                    //¾��            
                    case "Titles":
                        return "SELECT sn as data, titlesName AS label
                                FROM Titles
                                ORDER BY titlesCode";
                    //����
                    case "Role":
                        return "SELECT sn as data, roleName AS label
                                FROM Role"; 
                    //¾���M��
                    case "staffList":
                        return "SELECT sn as data, CONCAT(cName, ' ', eName) AS label
                                FROM Staff
                                ORDER BY deptSn, empNo";
                    //�a���M��
                    /*
                    case "parentList":
                        return "SELECT a.sn as data, CONCAT(b.codeDesc, ' ' ,a.idNo, ' ', a.cName, ' ', a.conTel, ' ', a.address) AS label
                                FROM Parent a, _code_".Fun::$sLang." b
                                WHERE b.typeId = 'gender'
                                AND a.gender=b.codeId
                                AND a.deleFlag = 0
                                ORDER BY a.cName, a.idNo";
                    */
                    //�a���m�W
                    case "parentName":
                        return "SELECT a.sn as data, CONCAT(b.codeDesc, ' ', a.cName) AS label
                                FROM Parent a, _code_".Fun::$sLang." b
                                WHERE b.typeId = 'gender'
                                AND a.gender=b.codeId
                                ORDER BY a.cName, a.idNo";
                    //�ǥͲM��
                    /*
                    case "studentList":
                        return "SELECT a.sn as data, CONCAT(b.codeDesc, ' ' ,a.stuNo, ' ', a.cName, ' ', a.eName, ' ', a.address) AS label
                                FROM Student a, _code_".Fun::$sLang." b
                                WHERE b.typeId = 'gender'
                                AND a.gender=b.codeId                                
                                ORDER BY a.stuNo, a.cName";
                    */
                    //�ǥͩm�W
                    case "studentName":
                        return "SELECT a.sn as data, CONCAT(b.codeDesc, ' ', a.cName, ' ', a.eName) AS label
                                FROM Student a, _code_".Fun::$sLang." b
                                WHERE b.typeId = 'gender'
                                AND a.gender=b.codeId                                
                                ORDER BY a.stuNo, a.cName";
                    //�ǥ�(�O�I)�X�ͤ��, �����Ҹ�
                    case "studentBI":
                        return "SELECT sn as data, CONCAT(birthDate, ' ', idNo) AS label
                                FROM Student                               
                                ORDER BY stuNo, cName";
                    //�ǥͲM��(�w�}��)
                    case "studentStart":
                        return "SELECT a.sn as data, CONCAT(d.codeDesc, ' ', a.cName, ' ', a.eName) AS label
                                FROM Student a INNER JOIN
                                StudentStatus b ON a.sn = b.studentSn INNER JOIN
                                StartClass c ON b.startClassSn = c.sn INNER JOIN
                                _code_".Fun::$sLang." d ON a.gender = d.codeId
                                WHERE (c.academicYear =".$_SESSION[self::csAcademicYear].") AND (c.semester =".$_SESSION[self::csSemester].")
                                AND (d.typeId = 'gender') AND a.deleFlag = 0";
                    //�a�����ǥͲM��
                    case "pStudent":
                        return "SELECT b.sn as data, CONCAT(c.codeDesc, ' ' ,b.stuNo, ' ', b.cName, ' ', b.eName) AS label
                                FROM PSRelation a INNER JOIN
                                Student b ON a.studentSn = b.sn INNER JOIN
                                _code_".Fun::$sLang." c ON b.gender = c.codeId
                                WHERE (c.typeId = 'gender') AND b.deleFlag = 0 AND a.parentSn = '".$_SESSION[self::csUsersSn]."'";
                    //�ǥͤ��a���M��
                    case "sParent":
                        return "SELECT b.sn as data, CONCAT(a.relationship, ' ', b.cName, ' ', c.codeDesc, ' ', b.idNo) AS label
                                FROM PSRelation a INNER JOIN
                                Parent b ON a.parentSn = b.sn INNER JOIN
                                _code_".Fun::$sLang." c ON b.gender = c.codeId
                                WHERE (c.typeId = 'gender') AND b.deleFlag = 0 AND a.studentSn = '".$p_data["studentSn"]."'";                                
                    //�Z�ŲM��
                    case "classInfoList":
                        return "SELECT sn as data, CONCAT(theClassCode, ' ', theClassName) AS label
                                FROM ClassInfo
                                WHERE classType = ".$p_data["classType"]."
                                ORDER BY theClassCode";
                    //�Z�O�M��
                    case "classLeavesList":
                        return "SELECT sn as data, CONCAT(classLeavesCode, ' ', classLeavesName) AS label
                                FROM ClassLeaves
                                WHERE classInfoSn='".$p_data["classInfoSn"]."'                                
                                ORDER BY classLeavesCode";
                    //�Z(��)�O�M��O�W
                    case "classInfoName":
                        return "SELECT b.sn AS data, CONCAT(a.theClassName, ' ', b.classLeavesName) AS label
                                FROM ClassInfo a INNER JOIN ClassLeaves b ON a.sn = b.classInfoSn
                                ORDER BY a.theClassCode, b.classLeavesCode";
                    //�Z�ŲM��(�w�}�Z)
                    case "classInfoStart":
                        //���h��Ѯv�����, �L�h�������q
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
                                
                    //�Z�O�M��(�w�}�Z)
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
                    //�Z�ŧO�M��X��(�w�}�Z)
                    case "classInfoStartConb":
                        //���h��Ѯv�����, �L�h�������q
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
                                
                    //���Ǹ����~�ůZ�O(�w�}�Z)
                    case "studentGetClassName":
                        return "SELECT a.studentSn as data, CONCAT(d.theClassName, ' ', c.classLeavesName) AS label
                                FROM StudentStatus a INNER JOIN
                                StartClass b ON a.startClassSn = b.sn INNER JOIN
                                ClassLeaves c ON b.classLeavesSn = c.sn INNER JOIN
                                ClassInfo d ON c.classInfoSn = d.sn
                                WHERE (b.academicYear =".$_SESSION[self::csAcademicYear].") AND (b.semester =".$_SESSION[self::csSemester].")
                                AND (b.classType IN (0, 1))";
                    //��q��            
                    case "BabyCarInfo":
                        return "SELECT a.sn AS data, CONCAT(a.carCode, ' ', b.cName, ' ', a.vehTel) AS label
                                FROM BabyCarInfo a INNER JOIN Staff b
                                ON a.venStaffSn = b.sn
                                WHERE b.deleFlag = 0
                                ORDER BY a.carCode";
                    //�Ǧ~���M��            
                    case "AcaSetupList":
                        return "SELECT sn as data, CONCAT(academicYear, ' - ', semester) AS label
                                FROM AcaSetup
                                WHERE deleFlag = 0
                                ORDER BY currentStatus DESC , academicYear, semester";
                    //�Ǧ~�M��            
                    case "academicYearList":
                        return "SELECT academicYear as data, academicYear AS label
                                FROM AcaSetup
                                WHERE deleFlag = 0
                                GROUP BY academicYear
                                ORDER BY currentStatus DESC , academicYear";
                    //�Ǵ��M��            
                    case "semesterList":
                        return "SELECT semester as data, semester AS label
                                FROM AcaSetup
                                WHERE deleFlag = 0
                                GROUP BY semester
                                ORDER BY currentStatus DESC, semester";
                    //�̪�@�Ǵ����Ǵ��~�M��
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
                    //�� �Ǧ~(��) �Z��(�O)
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
                    //�s �Ǧ~(��) �Z��(�O)
                    case "nowStartClassList":
                        return "SELECT a.sn as data, CONCAT(academicYear, ' - ', semester, '  ', c.theClassName, ' - ', b.classLeavesName) AS label
                                FROM StartClass a, ClassLeaves b, ClassInfo c
                                WHERE a.classLeavesSn = b.sn AND b.classInfoSn = c.sn
                                AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                                ORDER BY c.theClassCode, b.classLeavesCode";
                                
                    //��X 1 �O��, 2 �O�I, 3 ���鲧�`, 4 �M��, 5 �f�g, 6 �欰�[��
                    case "itemName":
                        return "SELECT sn AS data, itemName AS label
                                FROM FuncItem
                                WHERE sysType=".$p_data["sysType"]."
                                AND deleFlag = 0
                                ORDER BY sn";
                    //�o�i�ˮ�
                    case "controlItem":
                        return "SELECT sn AS data, controlItem AS label
                                FROM DevControlDetail";
                    //�o�i�ˮ�(�~�֧O�W)
                    case "devControlName":
                        return "SELECT sn AS data, devAgeName AS label
                                FROM DevControl";
                    //�q������
                    case "leagalInfectItemName":
                        return "SELECT sn AS data, itemName AS label
                                FROM LeagalInfectItem
                                ORDER BY sn";
                    //�������q
                    case "finalEvaluateItem":
                        return "SELECT b.sn AS data, CONCAT(a.targetName, ' - ', b.remark, ' - ', b.contents) AS label
                                FROM FinalEvaluate a, FinalEvaluateDetail b
                                WHERE a.sn = b.finalEvaluateSn
                                ORDER BY a.sn, b.sn";
                }
                break;
                
            //�ǥ͸��
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
            //�ίZ�Ŭd�߾ǥͲM��
            case "BabyCheck":            
                    return "SELECT CASE WHEN d.sn IS NULL THEN 0 ELSE d.sn END AS sn, 
                            CASE WHEN d.attendFlag IS NULL THEN 'I' ELSE 'E' END AS ieFlag,
                            CASE d.attendFlag WHEN 1 THEN d.attendFlag ELSE 0 END AS attendFlag, 
                            c.sn AS studentSn, CONCAT(c.cName, ' ', c.eName) AS cname,
                            CASE WHEN c.fileName<>'' THEN CONCAT('../dbUpLoadFiles/StudentsFolder/', c.fileName) 
                            ELSE '../dbUpLoadFiles/StudentsFolder/images.jpg' END AS imageSource,                            
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
            //�ίZ�Ŭd�߽ҵ{��
            case "ClassScheduleView":
                    return "SELECT a.remark, b.*
                            FROM ClassSchedule a INNER JOIN ClassScheduleDetail b
                            ON a.sn = b.classScheduleSn
                            WHERE (a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester].") 
                            AND (a.classLeavesSn = '".$p_data["classInfoSn"]."')
                            ORDER BY b.sn";
            //�H�����
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
            //���ਭ�鲧�`�έp
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
            //�Юv�H�Ʋέp
            case "rptTeacher":
                return "SELECT a.deptSn, COUNT(*) AS mSumAll
                        FROM Staff a INNER JOIN
                        Dept b ON a.deptSn = b.sn
                        WHERE (a.dutyFlag = 1) AND (a.deleFlag = 0)
                        GROUP BY a.deptSn
                        ORDER BY b.deptCode";
            //�ǥͤH�Ʋέp
            case "rptStudent":
                return "SELECT a.classLeavesSn, COUNT(*) AS mSumAll
                        FROM StartClass a INNER JOIN
                        StudentStatus b ON a.sn = b.startClassSn INNER JOIN
                        Student c ON b.studentSn = c.sn
                        WHERE (a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester].") 
                        AND (a.classType IN (0, 1)) AND (c.deleFlag = 0) AND c.enrollFlag = 1
                        GROUP BY a.classLeavesSn
                        ORDER BY a.sn";
            //�P�_�O�_�w���]�w�Ǧ~��
            case "CurrStatus":
                return "SELECT COUNT(*) AS cs FROM AcaSetup WHERE currentStatus=1 AND deleFlag = 0";
            //�P�_�O�_�w���ۦP�Ǧ~��
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
            //�P�_�O�_�w���ۦP�n�J�b��
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
            //�P�_�O�_�w���ۦP�}�Z�Ǧ~��
            case "startClassS":
                return "SELECT COUNT(*) AS cs 
                        FROM StartClass 
                        WHERE academicYear = ".$p_data["academicYear"]."
                        AND semester = ".$p_data["semester"];
            //�P�_�O�_�w���ۦP���y�s��, ISBN
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
            //�Z�O�M��
            case "getClassInfoSn":
                return "SELECT classInfoSn 
                        FROM ClassLeaves
                        WHERE sn=".$p_data["classLeavesSn"] ;
            //�}�ҧǸ�(�w�}�Z)
            case "getStartClassSn":
                return "SELECT sn, classType
                        FROM StartClass
                        WHERE (academicYear =".$_SESSION[self::csAcademicYear].") AND (semester =".$_SESSION[self::csSemester].")
                        AND classLeavesSn=".$p_data["classLeavesSn"] ;
            //�Z�O�M��(�w�}�Z)
            case "getClassInfoSnStart":
                return "SELECT b.classInfoSn, b.sn as classLeavesSn
                        FROM StartClass a
                        INNER JOIN ClassLeaves b ON a.classLeavesSn = b.sn                        
                        WHERE (a.academicYear =".$_SESSION[self::csAcademicYear].") AND (a.semester =".$_SESSION[self::csSemester].")
                        AND (a.classType IN (0, 1))
                        AND a.sn=".$p_data["startClassSn"] ;
            //�P�_���ǥͦb�Ӧ~�׬O�_�����
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
            //�P�_�O�_�w���ۦP�Ǧ~��
            case "StudentStatusS":
                return "SELECT COUNT(*) AS cs 
                        FROM StudentStatus 
                        WHERE startClassSn = ".$p_data["nowStartClassSn"];
            //Ū���ЫǲM�����
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
            //Ū���ЫǲM�����(�ǳƪ��~)
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
            //Ū���o�i�ˮ֪�
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
            //����k�w�ǬV�f���ةw�q
            case "LeagalInfectItem":
                return "SELECT remark
                        FROM LeagalInfectItem 
                        WHERE sn = ".$p_data["sn"];
            //�k�w�ǬV�f(�D�n�g��)
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
            //Ū���������q
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
            //Ū���w���ˮ�
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
            //Ū���樮�ˬd���ظ��
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
            //Ū���e�f�v���
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
            //Ū���ثe���p���
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
            //Ū�������w��
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
            //Ū���åͦۥD�޲z
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
            //�k�w�ǬV�f(�D�n�g��)
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
            //���ĵ��Ĭ���
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
            //Ū����Ų�۵�����
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
            //Ū����Ҹ��
            case "SchoolInfo":
                return "SELECT *
                    FROM ManaInfo.SchoolD
                    WHERE sn = " . $_SESSION[self::csSchoolDSn] . " AND deleFlag = 0";
            //Ū���ӤH���
            case "StaffSelf":
                return "SELECT * 
                        FROM Staff
                        WHERE sn = " . $_SESSION[self::csUsersSn] . " AND deleFlag = 0";
            //Ū���ӤH���
            case "ParentSelf":
                return "SELECT * 
                        FROM Parent
                        WHERE sn = " . $_SESSION[self::csUsersSn] . " AND deleFlag = 0";
            //Ū���ӤH���G���
            case "BoardView":
                return "SELECT a.sn, a.subject, a.contents, CONCAT(a.sDate, ' ~ ', a.eDate) AS seDate, a.fileName,
                        CASE WHEN a.fileName<>'' AND a.fileName IS NOT NULL THEN 1 ELSE 0 END AS fileFlag,
                        CASE WHEN a.fileName<>'' AND a.fileName IS NOT NULL THEN '���I��}�Ҧ��ɮ�' ELSE '�L�ɮץi�}��' END AS fileFlagTip
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
                return "SELECT max(docNob) + 1 AS docNob
                        FROM TodoList
                        WHERE docNoa = '".$p_data["docNoa"]."'";
            //�ݿ�ƶ�
            case "ToDoListHistory":
                return "SELECT *
                        FROM TodoList
                        WHERE docNoa = '".$p_data["docNoa"]."'
                        ORDER BY docNob";     
            //Ū�������M��Τ~��
            case "classType":                
                return "SELECT classType
                        FROM StartClass 
                        WHERE sn = ".$p_data["sn"];
                                    
            //�i�J�t�ιw�]�C�X���
            
            //��¾���u
            //�ݿ�ƶ�
            case "listToDoList":
                return "SELECT * 
                        FROM TodoList
                        WHERE (staffSnA = ".$_SESSION[self::csUsersSn]." OR staffSnB = ".$_SESSION[self::csUsersSn]." 
                        OR staffSnC = ".$_SESSION[self::csUsersSn].")
                        AND (proFlag<>1 OR proFlagReply<>1)                        
                        ORDER BY docNoa, docNob, cDate";
            //���׳�
            case "listRepairForm":
                return "SELECT a.*, b.fortuneNo, b.fortuneName, CONCAT(c.cName, ' ', c.eName) AS rName
                        FROM DamageRepair a INNER JOIN
                        Fortune b ON a.fortuneSn = b.sn AND a.staffSnA = ".$_SESSION[self::csUsersSn]." AND a.finishDate IS NULL 
                        LEFT OUTER JOIN Staff c ON a.staffSnR = c.sn
                        ORDER BY a.createDate";
                
            //�Z�Ÿg��
            //�Z�ť���а�
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
            //���ĵ��Ĭ���
            case "listMedicineTicket":                
                return "SELECT a.*, 
                        CASE WHEN a.userType = 2 THEN '��' ELSE '' END AS selfFlag,
                        CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM MedicineTicket a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate>=CURDATE() 
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //�ˮ`�ƬG
            case "listHurtInfo":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName,                     
                        CONCAT( CAST( a.occurTimeH AS CHAR ) , '�G', CAST( a.occurTimeM AS CHAR ) ) AS occurTime
                        FROM HurtInfo a INNER JOIN
                        Student b ON a.studentSnA = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.occurDate ".$sqlDate7." 
                        INNER JOIN ClassLeaves c ON a.classLeavesSnA = c.sn
                        WHERE a.classLeavesSnA IN (".$p_data["csDataTemp"].")
                        ORDER BY a.occurDate";
            //����欰�[���
            case "listBabyWatch":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT( CAST( a.timeH AS CHAR ) , '�G', CAST( a.timeM AS CHAR ) ) AS watchTime
                        FROM BabyWatch a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7." 
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //�뭹�L�ӱ���
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
            //�оǤ�x
            case "listTeachBook":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM TeachBook a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7." 
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //�Ю׺��@
            case "listTeachProject":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM TeachProject a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate ".$sqlDate7." 
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //�q�l�p��ï(�Юv)
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
            //�a���q�p����
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
            //����O��
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
            //�a���d���^��
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
            //�ЫǲM���
            case "listCleanList":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM CleanList a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND a.cDate >= DATE_SUB(CURDATE( ), INTERVAL 1 DAY) 
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";                        
            //�o�i�ˮ֪�
            case "listDevControlStu":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM DevControlStu a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.classLeavesSn, b.stuNo";
            //�k�w�ǬV�f
            case "listLeagalInfect":            
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName,                     
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT( CAST( a.cTimeH AS CHAR ) , '�G', CAST( a.cTimeM AS CHAR ) ) AS cTime
                        FROM LeagalInfect a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate DESC 
                        LIMIT 0, 10";
            //�������q
            case "listFinalEvaluateStu":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName
                        FROM FinalEvaluateStu a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
                        
            //�аȺ޲z
            
            //�|ĳ����
            case "listUploadMeeting":
                return "SELECT a.*, CONCAT(b.cName, ' ', b.eName) AS chairmanStaffName, CONCAT(c.cName, ' ', c.eName) AS recorderStaffName
                        FROM UploadMeeting a LEFT OUTER JOIN
                        Staff b ON a.chairmanStaffSn = b.sn LEFT OUTER JOIN
                        Staff c ON a.recorderStaffSn = c.sn
                        WHERE a.cDate ".$sqlDate7."
                        ORDER BY a.cDate";
                        
            //��Ȧ�F�޲z
            
            //�ƯZ��
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
            //���G��
            case "listBoard":
                return "SELECT *, CONCAT(sDate, ' �X ', eDate) AS seDate
                        FROM Board
                        WHERE CURDATE() BETWEEN sDate AND eDate
                        ORDER BY sDate, eDate DESC";
            //�M�ץ���
            case "listProject":
                return "SELECT *, 
                        CONCAT(sTimeH, '�G', sTimeM, ' �X ', eTimeH, '�G', eTimeM) AS seTimeHM
                        FROM Project
                        WHERE EXTRACT(YEAR_MONTH FROM eDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY eDate";
            //�C�g�뭹��, �C�X�u����몺���
            case "listFoodTable":
                return "SELECT *
                        FROM FoodTable
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY cDate";
            //�o�i�ˮ֪�
            case "listDevControl":
                return "SELECT *, CONCAT(sAge, '��', sMonth, '�Ӥ�', sDay, '�� ~ ', eAge, '��', eMonth, '�Ӥ�', eDay, '��') AS ageScope
                        FROM DevControl
                        ORDER BY sAge, sMonth, sDay";
            //�ɤO�q��
            case "listViolenceAnn":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT( CAST( a.timeH AS CHAR ) , '�G', CAST( a.timeM AS CHAR ) ) AS timeHM
                        FROM ViolenceAnn a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        AND EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate";
            //�åͦۥD�޲z
            case "listHygieneControl":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM HygieneControl a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY a.cDate";
            //���~���r�q��
            case "listSitotoxismAnn":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName,                     
                        CONCAT(b.cName, ' ', b.eName) AS studentName,
                        CONCAT( CAST( a.cTimeH AS CHAR ) , '�G', CAST( a.cTimeM AS CHAR ) ) AS cTime
                        FROM SitotoxismAnn a INNER JOIN
                        Student b ON a.studentSn = b.sn 
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        INNER JOIN ClassLeaves c ON a.classLeavesSn = c.sn
                        WHERE a.classLeavesSn IN (".$p_data["csDataTemp"].")
                        ORDER BY a.cDate DESC
                        LIMIT 0, 10";
            //��Ų���
            case "listSelfEvaluate":
                return "SELECT *
                        FROM SelfEvaluate 
                        WHERE deleFlag = 0
                        ORDER BY selfEvaluateItem, sn";
            //�۵���
            case "listSelfEvaluateCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM SelfEvaluateCheck a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        AND a.academicYear = ".$_SESSION[self::csAcademicYear]." AND a.semester = ".$_SESSION[self::csSemester]."
                        ORDER BY a.cDate DESC
                        LIMIT 0, 10";
            //�ɮ׺޲z
            case "listFilesMana":
                return "SELECT *
                        FROM FilesMana
                        WHERE createDate ".$sqlDate7."
                        ORDER BY filesAlias";
            //�Ш�޲z
            case "listTeachTool":
                return "SELECT a.*, b.cardNo, b.cardName, CONCAT(c.cName, ' ', c.eName) AS staffName
                        FROM TeachTool a INNER JOIN
                        TeachCard b ON a.teachCardSn = b.sn INNER JOIN
                        Staff c ON a.staffSn = c.sn
                        WHERE cDate ".$sqlDate7."
                        ORDER BY a.cDate";
            //�ϮѸ��
            case "listBookData":
                return "SELECT *
                        FROM BookData
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY bookNo";
            //�ϮѺ޲z
            case "listBookMana":
                return "SELECT a.*, b.bookNo, b.bookName, CONCAT(c.cName, ' ', c.eName) AS staffName
                        FROM BookMana a INNER JOIN
                        BookData b ON a.bookDataSn = b.sn INNER JOIN
                        Staff c ON a.staffSn = c.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY a.cDate";
            //�]���޲z
            case "listFortune":
                return "SELECT a.*, CONCAT(b.cName, ' ', b.eName) AS uName, CONCAT(c.cName, ' ', c.eName) AS kName
                        FROM Fortune a LEFT OUTER JOIN
                        Staff b ON a.staffSnU = b.sn LEFT OUTER JOIN
                        Staff c ON a.staffSnK = c.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())                        
                        ORDER BY a.fortuneNo";
            //���׳�
            case "listDamageRepair":
                return "SELECT a.*, b.fortuneNo, b.fortuneName, CONCAT(c.cName, ' ', c.eName) AS aName, CONCAT(d.cName, ' ', d.eName) AS rName
                        FROM DamageRepair a INNER JOIN
                        Fortune b ON a.fortuneSn = b.sn AND a.finishDate IS NULL LEFT OUTER JOIN
                        Staff c ON a.staffSnA = c.sn LEFT OUTER JOIN
                        Staff d ON a.staffSnR = d.sn
                        ORDER BY a.createDate";
            //�ЫǲM���
            case "listCleanListCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName
                        FROM CleanList a INNER JOIN
                        ClassLeaves b ON a.classLeavesSn = b.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        AND a.staffSnB = 0
                        ORDER BY a.cDate";
            //�w���ˮ��ˬd
            case "listSafeControlCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM SafeControlCheck a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY a.cDate";
            //�M�Ψ��樮�ˬd
            case "listCarCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM CarCheck a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE a.cDate ".$sqlDate7."
                        ORDER BY a.cDate";
            //�����w���ˬd
            case "listFireControlCheck":
                return "SELECT a.*, CONCAT(a.academicYear, ' - ', a.semester) AS acaName, 
                        CONCAT(b.cName, ' ', b.eName) AS staffName
                        FROM FireControlCheck a INNER JOIN
                        Staff b ON a.staffSn = b.sn
                        WHERE EXTRACT(YEAR_MONTH FROM a.cDate) = EXTRACT(YEAR_MONTH FROM CURDATE())
                        ORDER BY a.cDate";
                        
            //�򥻸��
            
            //�������
            case "listDept":
                return "SELECT *
                        FROM Dept
                        ORDER BY deptCode";
            //¾�ٸ��
            case "listTitles":
                return "SELECT *
                        FROM Titles
                        ORDER BY titlesCode";
            //���u�򥻸��
            case "listStaff":
                return "SELECT * 
                        FROM Staff 
                        ORDER BY createDate DESC 
                        LIMIT 10";
            //�Z�Ÿ��
            case "listClassInfo":
                return "SELECT * 
                        FROM ClassInfo 
                        ORDER BY theClassCode";
            //�Ǧ~�׳]�w
            case "listAcaSetup":
                return "SELECT * 
                        FROM AcaSetup 
                        WHERE academicYear = ".$_SESSION[self::csAcademicYear]."
                        ORDER BY academicYear, semester";
            //�}�Z���
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
            //����򥻸��
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
            //�Z�žǥͲM��
            case "listStudentStart":
                return "SELECT b.stuNo, b.cName, b.sn, b.eName, b.gender, b.birthDate
                        FROM StudentStatus a, Student b
                        WHERE a.startClassSn = '" . $p_data["startClassSn"] . "'
                        AND a.studentSn = b.sn
                        AND b.deleFlag = 0 AND b.enrollFlag = 1
                        ORDER BY b.stuNo";
            //�a�����
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
            //��q���ɶ�����
            case "listBabyCarRec":
                return "SELECT *
                        FROM BabyCarRec
                        WHERE cDate ".$sqlDate7."
                        ORDER BY babyCarInfoSn";
            //����޲z
            case "listRole":
                return "SELECT *
                        FROM Role
                        ORDER BY roleName";
                        
            //�a���\���
                                    
            //�q�l�p��ï(�a��)
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
            //����а��O��(�a��)
            case "listBabyRestParent":
                return "SELECT b.*, 
                        CASE WHEN b.userType = ".$_SESSION[self::csUserType]." AND b.userSn = ".$_SESSION[self::csUsersSn]." 
                        THEN '��' ELSE '' END AS selfFlag,
                        CONCAT(b.academicYear, ' - ', b.semester) AS acaName, 
                        CONCAT(d.cName, ' ', d.eName) AS studentName
                        FROM ClassLeaves a INNER JOIN
                        BabyExcuse b ON a.sn = b.classLeavesSn INNER JOIN
                        PSRelation c ON b.studentSn = c.studentSn INNER JOIN
                        Student d ON c.studentSn = d.sn
                        WHERE (b.exDate BETWEEN DATE_SUB(CURDATE( ), INTERVAL 15 DAY) AND DATE_ADD(CURDATE( ), INTERVAL 15 DAY))
                        AND (c.parentSn = '" . $_SESSION[self::csUsersSn] . "')
                        ORDER BY b.exDate";
            //�a���d��(�a��)
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
            //���ĵ��Ĭ���(�a��)
            case "listMedicineTicketParent":
                return "SELECT b.*, 
                        CASE WHEN b.userType = 2 THEN '��' ELSE '' END AS selfFlag,
                        CONCAT(b.academicYear, ' - ', b.semester) AS acaName, 
                        CONCAT(d.cName, ' ', d.eName) AS studentName
                        FROM ClassLeaves a INNER JOIN
                        MedicineTicket b ON a.sn = b.classLeavesSn INNER JOIN
                        PSRelation c ON b.studentSn = c.studentSn INNER JOIN
                        Student d ON c.studentSn = d.sn
                        WHERE (b.cDate BETWEEN DATE_SUB(CURDATE( ), INTERVAL 15 DAY) AND DATE_ADD(CURDATE( ), INTERVAL 15 DAY))
                        AND (c.parentSn = '" . $_SESSION[self::csUsersSn] . "')
                        ORDER BY b.cDate";
                        
            //�~�צ�ƾ�
            case "listYearCal":
                return "SELECT *
                        FROM YearCal
                        WHERE cDate >= CURDATE()
                        ORDER BY cDate";
            //�뭹�L�ӱ���
            case "listFoodSickHistory":
                return "SELECT *
                        FROM FoodSick
                        WHERE (studentSn = '" . $p_data["studentSn"] . "')                        
                        ORDER BY cDate";
            //�뭹�L�ӱ���
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
            //�ǥͯZ�ž��v
            case "listStudentStatusHistory":
                return "SELECT b.sn AS startClassSn, 
                        CASE WHEN b.academicYear = ".$_SESSION[self::csAcademicYear]." AND b.semester = ".$_SESSION[self::csSemester]." 
                        THEN '��' ELSE '' END AS acaFlag,
                        b.academicYear, b.semester, b.classLeavesSn
                        FROM StudentStatus a INNER JOIN StartClass b ON a.startClassSn = b.sn AND b.classType IN (0, 1)
                        WHERE a.studentSn = '" . $p_data["studentSn"] . "'
                        ORDER BY b.academicYear, b.semester";
            //�Z�ŲM���
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
                                    CONCAT('�� ', c.cName, ' ', c.eName , ' ', d.codeDesc,  
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
                        CONCAT('�� ', contents) AS comment, 1 AS infoFlag
                        FROM YearCal
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%'
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('�� ', LPAD(timeH, 2, 0), ':', LPAD(timeM, 2, 0), ' ', contents) AS comment, 2 AS infoFlag
                        FROM PersonCal
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' 
                        AND staffSn = '" . $_SESSION[self::csUsersSn] . "')
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('�� ', contents) AS comment, 3 AS infoFlag
                        FROM TodoList
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' 
                        AND (staffSnA = '" . $_SESSION[self::csUsersSn] . "' OR staffSnB = '" . $_SESSION[self::csUsersSn] . "' 
                        OR staffSnC = '" . $_SESSION[self::csUsersSn] . "'))
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('�� ', remark) AS comment, 4 AS infoFlag
                        FROM FoodTable
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%'
                        ".$tSqlCon."
                        ";
            case "gCalendarStaffView":
                return "SELECT 1 AS isWork, cDate AS calDate, CONCAT('�� ', contents) AS comment, 1 AS infoFlag
                        FROM YearCal
                        WHERE EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%'
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, 
                        CONCAT('�� ', LPAD(timeH, 2, 0), ':', LPAD(timeM, 2, 0), ' ', contents) AS comment, 2 AS infoFlag
                        FROM PersonCal
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' 
                        AND staffSn = '".$p_data["StaffSn"]."')
                        UNION ALL
                        SELECT 1 AS isWork, cDate AS calDate, CONCAT('�� ', contents) AS comment, 3 AS infoFlag
                        FROM TodoList
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' 
                        AND (staffSnA = '".$p_data["StaffSn"]."' OR staffSnB = '".$p_data["StaffSn"]."'))
                        UNION ALL
                        SELECT 1 AS isWork, b.cDate AS calDate, CONCAT('�� ', c.cName, ' ', c.eName , ' ', d.codeDesc,  
                        LPAD(a.sTimeH, 2, 0), ':', LPAD(a.sTimeM, 2, 0), '~', LPAD(a.eTimeH, 2, 0), ':', LPAD(a.eTimeM, 2, 0)) AS comment, 5 AS infoFlag
                        FROM TimeTable a, TimeTableCD b, Staff c, _code_".Fun::$sLang." d
                        WHERE a.sn = b.timeTableSn
                        AND a.staffSn = c.sn
                        AND a.jobs = d.codeId AND d.typeId = 'jobs'
                        AND c.dutyFlag = 1 AND c.deleFlag = 0
                        AND EXTRACT(YEAR_MONTH FROM b.cDate) LIKE '" . $p_data["ym"] . "%'
                        AND a.staffSn = '".$p_data["StaffSn"]."'
                        ";
              
            /*
            case "PersonCalV":
                return "SELECT 1 AS isWork, cDate AS calDate, CONCAT('�� ', timeH, ':', timeM, ' ', contents) AS comment
                        FROM PersonCal
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' AND staffSn = '" . $_SESSION[self::csUsersSn] . "')
                        ";
            case "TodoListV":
                return "SELECT 1 AS isWork, cDate AS calDate, CONCAT('�� ', contents) AS comment
                        FROM TodoList
                        WHERE (EXTRACT(YEAR_MONTH FROM cDate) LIKE '" . $p_data["ym"] . "%' AND staffSn = '" . $_SESSION[self::csUsersSn] . "')
                        ";
            */
            //�ӤH��ƾ�(�ӤH���)
            case "gPersonCalList":
                $ts_sql = "SELECT *
                    FROM PersonCal
                    WHERE cDate = '{0}' AND staffSn = '{1}'";                    
                return Fun::format($ts_sql, $p_data["ymd"], $_SESSION[self::csUsersSn]);
            //�~�צ�ƾ�
            case "gYearCalList":
                $ts_sql = "SELECT *
                    FROM YearCal
                    WHERE cDate = '{0}'";                    
                return Fun::format($ts_sql, $p_data["ymd"]);
            //�ݿ�ƶ�(�ӤH���)
            case "gTodoList":
                $ts_sql = "SELECT *
                    FROM TodoList
                    WHERE cDate = '{0}' AND (staffSnA = '{1}' OR staffSnB = '{1}' OR staffSnC = '{1}')";
                return Fun::format($ts_sql, $p_data["ymd"], $_SESSION[self::csUsersSn]);
            //�뭹��(�ӤH���)
            case "gFoodTable":
                $ts_sql = "SELECT b.sysType, c.itemName
                        FROM FoodTable a, FoodTableDetail b, FuncItem c
                        WHERE a.sn = b.foodTableSn
                        AND b.funcItemSn = c.sn
                        AND a.cDate = '{0}'
                        ORDER BY b.sysType";                
                return Fun::format($ts_sql, $p_data["ymd"]);
            //�ƯZ��
            case "gTimeTable":
                $ts_sql = "SELECT a.staffSn, 
                    CONCAT(LPAD(a.sTimeH, 2, 0), ':', LPAD(a.sTimeM, 2, 0), '~', LPAD(a.eTimeH, 2, 0), ':', LPAD(a.eTimeM, 2, 0)) AS seTimeHM, a.jobs
                    FROM TimeTable a, TimeTableCD b
                    WHERE a.sn = b.timeTableSn
                    AND b.cDate = '{0}'";                    
                return Fun::format($ts_sql, $p_data["ymd"]);
            //�åͨƬG�y�{
            case "listHygieneProcess":
                return "SELECT *, 
                        CASE WHEN academicYear = '".$_SESSION[self::csAcademicYear]."' 
                        AND semester = '".$_SESSION[self::csSemester]."' THEN '��' ELSE '' END AS acaFlag
                        FROM HygieneProcess
                        ORDER BY cDate";
            //��\��M��
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
            //���ĵ��Ĭ���
            case "listMedicineRecord":
                return "SELECT a.medTime, a.medTimeCon,
                        CONCAT(a.medTimeH, ':', a.medTimeM) AS medTimeHM, 
                        a.inMedType,
                        CONCAT(b.cName, ' ', b.eName) AS ceName
                        FROM MedicineRecord a LEFT OUTER JOIN Staff b ON a.staffSn = b.sn
                        WHERE (a.medicineTicketSn = '" . $p_data["medicineTicketSn"] . "')                        
                        ORDER BY a.medTime";
            //�ǥͲέp
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
            //��¾���έp
            case "listRptTeacher":
                return "SELECT b.deptName AS label, COUNT(*) AS data
                        FROM Staff a INNER JOIN
                        Dept b ON a.deptSn = b.sn
                        WHERE (a.dutyFlag = 1) AND (a.deleFlag = 0)
                        GROUP BY a.deptSn
                        ORDER BY b.deptCode";
            //���ష�d���`�έp
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
            //����X�u�έp
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
                //if (p_data["ym"] != null){
                //    return "select * from YearCal where convert(char,calDate,112) like '" . $p_data["ym"] . "%'";
                //}else{
                //    return "select * from gCalendar where convert(char,calDate,112) = '" + p_data["ymd"].ToString() + "'";            
                //}
        }


        //case of not found
        //Fun::sendRoot("E", "self::_getSql() �Ǧ^�Ŧr��, data=" + ts_data + ", type=" + ts_type);
        return "";
    }
    
    
    //return  JsonObject
    public static function _setDeptSession()
    {
        return null;
        //$ts_sql = "select deptSeq as " . Fun::csDeptId . " from _User where userId = '" . Fun::getUserId() . "'";
        //return Fun::readRow(ts_sql);
    }


    //set session if need
    public static function afterLogin()
    {
         /*
         $t_data["data"] = "Staff";
         $ts_db = "";
         $ts_sql = self::_getSql($t_data, $ts_db);
         $t_row = Fun::readRow($ts_sql);      
         */         
         //�����l�ȴ��ե�
         //¾��
         /*
         $t_user[self::csUsersSn] = 1;         
         $t_user[self::csUsersCName] = "�����X";
         $t_user[self::csUsersEName] = "Joyce";         
         $t_user[self::csUserType] = 3;
         */         
         //�a��
         /*
         $t_user[self::csUsersSn] = 773094108;         
         //$t_user[self::csStudentSn] = 85899347;
         $t_user[self::csUsersCName] = "�勵��";
         $t_user[self::csUsersEName] = "Winter";         
         $t_user[self::csUserType] = 2;         
         */
         
         //����ϥΪ̸�� roleSn
         $ts_sql = "SELECT sn, userType, userSn 
                    FROM Account 
                    WHERE loginId='" . $_SESSION[Fun::csLoginId] . "' 
                    AND deleFlag = 0";
         $t_row = Fun::readRow($ts_sql);
         if ($t_row){
            //�P�_�ϥΪ̨���
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
            //����������
            if ($ts_sqla != ""){
                $t_rowa = Fun::readRow($ts_sqla);
                if ($t_rowa){
                    $t_user[self::csAccountSn] = $t_row["sn"]; //�ϥΪ̱b��
                    $t_user[self::csUsersSn] = $t_rowa["sn"]; //�ϥΪ̰ߤ@�Ǹ�       
                    $t_user[self::csUsersCName] = $t_rowa["cName"]; //�ϥΪ̤���W
                    $t_user[self::csUsersEName] = $t_rowa["eName"]; //�ϥΪ̭^��W
                    $t_user[self::csUserTypeName] = $t_rowa["userTypeName"];    //�ϥΪ̨���W��
                    $t_user[self::csUserType] = $t_row["userType"]; //�ϥΪ̨���N��
                    //$t_user[self::csRoleSn] = $t_row["roleSn"]; //�ϥΪ̨���Ǹ�                    
                }
            }
         }         
         //����ثe�]�w���Ǧ~��
         $ts_sql = "SELECT academicYear, semester FROM AcaSetup WHERE currentStatus=1 AND deleFlag = 0";
         $t_row = Fun::readRow($ts_sql);
         if ($t_row){
            $t_user[self::csAcademicYear] = $t_row["academicYear"];
            $t_user[self::csSemester] = $t_row["semester"];
         }
         
         foreach ($t_user as $key => $value){
            $_SESSION[$key] = $value;
         }

         //$_SESSION["csUsersSn"] = $t_user[self::csUsersSn];
         /*
         //set session                
         $ts_error = Fun::setSession($t_user, "", "");         
         */
         /*
         $ts_sql = "";
         $t_row = Fun::readRow($ts_sql);
         $_SESSION["user"] = $t_row["??"]
         */
         
    }

    
    public static function updateDB($p_data, $pf_update)
    {

        $t_upd = new UpdateDB();

        //string ts_msg = t_upd->init(ps_fun, ps_inf, ps_input, pf_update);
        $ts_msg = $t_upd->init($p_data, $pf_update);
        if ($ts_msg != "")
        {
            $ts_msg = Fun::jsonError($ts_msg);
            self::lab_exit($t_upd, $ts_msg);
            return;
        }

        $ts_msg = $t_upd->save();
        if ($ts_msg != "")
            $ts_msg = Fun::jsonError($ts_msg);
        else
            $ts_msg = $t_upd->getIdentStr();


        return self::lab_exit($t_upd, $ts_msg);                
        //case of ok below !

        //t_upd = null;
    //t_upd->release();
    //return ts_result;
    }

    public static function lab_exit($t_upd, $ts_msg){
        //release first !
        $t_upd = null;

        return $ts_msg;

        //t_upd->release();

        //�ǰe�ԲӪ����~�T�����޲z��
        //Fun.sendRoot("E", ts_error);

        //return Fun.jsonError(ts_error);
    }
    
    
    public static function getDbStr($ps_db = ""){
        if ($ps_db == "")
            $ps_db = "db";
            
        $ts_schoolSn = isset($_SESSION[self::csSchoolDSn]) ? $_SESSION[self::csSchoolDSn] : '';     
        return Fun::format(constant("Config::" . $ps_db), $ts_schoolSn);        
    }    
}//class

?>
