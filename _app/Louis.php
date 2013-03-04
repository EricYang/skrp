<?PHP

require_once(dirname(__FILE__) . "/Fun.php"); 
require_once(dirname(__FILE__) . "/UpdateDB.php"); 


class Louis {
    
    public static function _getSql(array $p_data, &$ps_db){
        $ts_data = $p_data["data"];
        $ts_type = (isset($p_data["type"])) ? $p_data["type"] : "";
        
        $ts_rowType = isset($p_data["rowType"]) ? $p_data["rowType"]: "";
        //$ts_pre = Fun::isEmpty($ts_rowType) ? "":self::rowTypeToPre($ts_rowType);
        $ts_pre = "Sys";
        switch ($ts_data)
        {
            case "UpdateDB":
                switch ($ts_type){
                    case "mateDel":
                        $t_db = new DB();
                        $ts_sql = "UPDATE ".$ts_pre."Mate SET delFlag=1 WHERE sn=".$p_data["sn"];
                        Fun::exeUpdateByConn($t_db, $ts_sql);
                        $ts_sql = "UPDATE ".$ts_pre."MateSub SET delFlag=1 WHERE mateSn=".$p_data["sn"];
                        Fun::exeUpdateByConn($t_db, $ts_sql);
                        if ($ts_rowType == 'T')
                            $ts_sql = "DELETE FROM MateLesson WHERE mateSn=".$p_data["sn"];
                        else 
                            $ts_sql = "DELETE FROM ".$ts_pre."MateTypes WHERE mateSn=".$p_data["sn"];
                        Fun::exeUpdateByConn($t_db, $ts_sql);
                        break;
                    case "mateSubDel":
                        $ts_sql = "UPDATE ".$ts_pre."MateSub SET delFlag=1 WHERE sn=".$p_data["sn"];
                        Fun::exeUpdate($ts_sql);
                        break;
                }
                break;
            /*
            case "Json":        //在編輯畫面輸入seq時必須檢查存取權限
                break;            

            case "Var":     //server variables
                break;            
            */   
            case "ComboBox":
                switch ($ts_type) {
                    case "lesson":    //老師課程
                        return "SELECT sn AS data, cname AS label, startDate, endDate
                                FROM Lesson WHERE tchSeq=".Fun::getUserId();
                }            
                break;
          
            case "mateTypes":    //教材類別
                $ts_sql = "SELECT sn, upSn, cSubject, eSubject
                           FROM MatType WHERE upSn={0}";
                return Fun::format($ts_sql, $p_data["typeSn"]);
                
            case "mateInfo":     //子教材項目清單
                $ts_sql = "SELECT m.cSubject, m.eSubject, m.intro,s.*
                           FROM ".$ts_pre."Mate m INNER JOIN ".$ts_pre."MateSub s
                                ON m.sn = s.mateSn
                           WHERE m.delFlag = 0 AND s.delFlag = 0 AND s.checkFlag = 0 AND s.mateSn = {0} ORDER BY s.orderNo, s.sn";
                return Fun::format($ts_sql, $p_data["mateSn"]);
                
            case "mateSubCount": //子教材數量
                return "SELECT count(0) as c FROM ".$ts_pre."MateSub WHERE mateSn = ".$p_data['sn']." AND delFlag = 0 AND checkFlag = 0";
                
            case "mateLesson":   //老師教材所屬課程
                return "SELECT b.cname AS lessonName 
                        FROM MateLesson a LEFT JOIN Lesson b ON a.lessonSn = b.sn WHERE a.mateSn = ".$p_data['sn'];

            case "mateFiles": //單獨檔案類型子教材
                $ts_sql = "SELECT * FROM ".$ts_pre."MateFiles WHERE sn = {0}";
                return Fun::format($ts_sql, $p_data["fileSn"]); 
                                
            case "mateCont":     //中外雙語子教材
                $ts_sql = "SELECT * FROM ".$ts_pre."MateCont
                           WHERE sn = {0}";
                return Fun::format($ts_sql, $p_data["contSn"]); 
                
            case "mateContPg":   //中外雙語子教材文章段落
                $ts_sql = "SELECT p.*, s.*, s.sn AS pSubSn
                           FROM ".$ts_pre."MateContPg p LEFT JOIN ".$ts_pre."MateContSb s ON p.sn = s.pgSn
                           WHERE p.contSn = {0} 
                           ORDER BY p.sn, s.sn";
                return Fun::format($ts_sql, $p_data["contSn"]);    
                
            case "mateWord":
                 $ts_sql = "SELECT ws.*,w.* FROM ".$ts_pre."MateWords ws left join word w on ws.wordSn = w.sn
                            WHERE ws.upSn = {0} ORDER BY word";
                 return Fun::format($ts_sql, $p_data["wordSn"]);

            case "mateVCont":     //垂直文章子教材標題、簡介
                $ts_sql = "SELECT * FROM ".$ts_pre."MateVCont
                           WHERE sn = {0}";
                return Fun::format($ts_sql, $p_data["contSn"]); 
                
            case "mateVContPg":  //垂直文章子教材段落內容
                $ts_sql = "SELECT * FROM ".$ts_pre."MateVContPg
                           WHERE contSn = {0} ORDER BY sn";
                return Fun::format($ts_sql, $p_data["contSn"]);
                  
            case "setUser":     //個人資料維護
                if ($p_data["userSeq"] != Fun::getUserId()) {
                    return "";
                }
                $ts_pass = "";
                if (isset($p_data['oldPass'])) {
                    $ts_pass = " AND password = ".$p_data['oldPass'];
                }
                $ts_sql = "SELECT * FROM _user WHERE userSeq = {0}".$ts_pass;
                return Fun::format($ts_sql, $p_data["userSeq"]);                
        }
    }
    
    public static function rowTypeToPre($ps_rowType){
        return ($ps_rowType == "S") ? "Sys" : "" ;
    }    
    
}//class

?>
