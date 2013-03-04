<?php

require_once(dirname(__FILE__)."/../Config.php"); 
require_once(dirname(__FILE__)."/Fun.php"); 


class UpdateDB{
    //=== public property ===
    
    
    //=== private instance variables ===
    private $i_db;
    private $i_inf;
    private $ia_input;
    private $i_send;
    private $ia_dw;
    private $ia_items;
    private $ia_itemMap;
    private $is_now;
    private $ias_ident;       //string type for JsonObject.WriteStringArray() !!
    private $is_userId;
    private $ib_ident = false;    
    private $is_logFuns;
    private $is_fun;
    private $is_inf;

    
    //=== public method begin === 
    
    public function __construct($ps_db = ""){
        $this->is_now = Fun::now();
        
    }
    
    
    public function init($p_data, &$pf_update)
    {

        //read inf file to JsonObject variables
        $this->is_inf = $p_data["inf"];

        //$ts_inf = is_inf + "E";  //edit
        $this->i_inf = Fun::infToJson($this->is_inf);
        $ts_error = "";
        if ($this->i_inf == null)
        {
            $ts_error = "Can not Open inf of " . $this->is_inf . " in UpdateDB Constructor";
            goto lab_error;
        }


        //set other instance
        $this->is_fun = $p_data["fun"];
        $this->if_update = $pf_update;
        $this->is_userId = Fun::getUserId();
        $this->is_logFuns = isset($this->i_inf["log"]) ? $this->i_inf["log"] : "";


        //check access right
        //skip ??, coz client need send userId and deptId field value !!


        //save inf items(ia_items) and field mapping info(ia_itemMap).
        $i;
        //$ia_input = json_decode(($p_data["data"]);
        $this->ia_input = $p_data["data"];
        //$i_send = isset($p_data["send"]) ? json_decode($p_data["send"]) : null;
        $this->i_send = isset($p_data["send"]) ? $p_data["send"] : null;
        $tn_input = count($this->ia_input);
        $this->ias_ident = array($tn_input);
        /*
        iabUpdate = new Boolean[tn_input];
        for (i = 0; i < tn_input; i++)
            iabUpdate[$i] = true;
        */

        $this->ia_dw = $this->i_inf["dws"];
        $this->ia_items = array();
        $this->ia_itemMap = array();
        $j;
        $tn_items = count($this->ia_items);
        $tn_map = count($this->ia_itemMap);
        for ($i = 0; $i < count($this->ia_dw); $i++)
        {
            $t_dw = $this->ia_dw[$i];
            $ta_item = $t_dw["items"];
            $this->ia_items[$tn_items+$i] = $ta_item;

            //save mapping info to ia_itemMap[]
            $t_map = array();
            for ($j = 0; $j < count($ta_item); $j++)
            {
                $t_map[$ta_item[$j]["fid"]] = $j;
            }
            $this->ia_itemMap[$tn_map+$i] = $t_map;
        }


        return "";
    //}

    lab_error:
        Fun::logError($ts_error);
        //this = null;
        return $ts_error;

    }


    //get dw no of input
    private function dwNo($p_input)
    {
        return isset($p_input["dw"]) ? intval($p_input["dw"]) : 0;
    }


    // 傳回 Db Connection
    public function getDB()
    {
        return $this->i_db;
    }



    // 傳回 inf 資料 JsonObject
    public function getInf()
    {
        return $this->i_inf;
    }


    // 傳回前端輸入資料 JsonArray
    public function getInputs()
    {
        return $this->ia_input;
    }


    /// 傳回前端輸入資料 Send JsonArray
    public function getSend()
    {
        return $this->i_send;
    }


    // 傳回 datawindow JsonArray
    public function getDWs()
    {
        return $this->ia_dw;
    }


    // 傳回 inf 裡的 items[] JsonArray
    public function getItems()
    {
        return $this->ia_items;
    }


    // 傳回 items[] 欄位代號和序號的對應.(fid:fno)
    public function getItemMaps()
    {
        return $this->ia_itemMap;
    }


    // 傳回開始執行的時間
    public function now()
    {
        return $this->is_now;
    }


    // 傳回新建資料的 identity 欄位值, 陣列序號與異動的資料序號相同
    public function getIdents()
    {
        return $this->ias_ident;
    }


    // 傳回目前這個 DW 的資料表名稱, 無 "dbo." 文字
    public function getTableByDW($p_dw)
    {
        $ts_table = isset($p_dw["updateTable"]) ? $p_dw["updateTable"] : $p_dw["selectTable"];
        if (strlen($ts_table) < 4)
            return $ts_table;
        else
            return (strtolower(substr($ts_table, 0, 4)) == "dbo.") ? substr($ts_table, 4) : $ts_table;
    }


    /// <summary>
    /// 傳回目前這筆資料的資料表名稱
    /// </summary>
    /// <param name="pn_row">資料序號JsonObject</param>
    /// <returns>資料表名稱</returns>
    public function getTableByNo($pn_row)
    {
        $t_input = $this->ia_input[$pn_row];
        //$tn_dw = (t_input["dw"] != null) ? Convert.ToInt32(t_input["dw"]) : 0;
        $tn_dw = $this->dwNo($t_input);
        return $this->getTableByDW($this->ia_dw[$tn_dw]);
    }


    /// <summary>
    /// 傳回某個欄位的值
    /// </summary>
    /// <param name="pa_field">欄位資料 JsonArray, 每筆資料前面3個欄位的結構為: fid,dataType,value</param>
    /// <param name="ps_fid">欄位名稱</param>
    /// <param name="pb_quote">是否加上單引號</param>
    /// <returns>傳回欄位值, 如果找不到則傳回 null</returns>
    public function getItem($pa_field, $ps_fid, $pb_quote)
    {
        for ($i = 0; $i < count(pa_field); $i++)
        {
            if ($ps_fid == $pa_field[$i][0])
                return $this->getItemByFno($pa_field, $i, $pb_quote);

        }

        //case of not find fid
        return null;
    }


    /// <summary>
    /// 傳回某個欄位的值
    /// </summary>
    /// <param name="pa_field">欄位資料 JsonArray, 每筆資料前面3個欄位的結構為: fid,dataType,value</param>
    /// <param name="pn_field">欄位序號</param>
    /// <param name="pb_quote">是否加上單引號</param>
    /// <returns>傳回欄位值, 如果找不到則傳回 null</returns>
    public function getItemByFno($pa_field, $pn_field, $pb_quote)
    {
        $ts_value = $pa_field[$pn_field][2];
        if ($pb_quote)
            $ts_value = "'" . $ts_value . "'";

        return $ts_value;
    }


    /// <summary>
    /// 傳回某個鍵值的欄位值
    /// </summary>
    /// <param name="pn_row">資料序號</param>
    /// <param name="ps_fid">欄位名稱</param>
    /// <returns>傳回欄位值, 不含單引號</returns>
    public function getKeyItem($pn_row, $ps_fid)
    {
        $t_input = $this->ia_input[$pn_row];
        $ta_key = $t_input["keys"];  //fid,dataType,value,inputType
        return $this->getItem($ta_key, $ps_fid, false);
    }


    /// <summary>
    /// 傳回所有新增資料的 identity 欄位值.
    /// </summary>
    /// <returns>字串</returns>
    public function getIdentStr()
    {
        if ($this->ib_ident)
        {
            //php陣列內容不為連續時, 陣列會自動轉成 json, 
            //所以必須強制轉換成陣列 !!
            /*
            $tn_ary = 0;
            $tas_1 = array();
            foreach ($this->ias_ident as $key => $value) {
                $tn_end = intval($key);
                for ($j=$tn_ary; $j<$tn_end; $j++){
                    $tas_1[$j] = "";
                }
                $tas_1[$tn_end] = intval($value);    
                $tn_ary = $tn_end + 1;        
            }            

            return json_encode(array($tas_1));
             */
            $ts_list = "";
            for ($i=0; $i<count($this->ias_ident); $i++){
                $ts_list .= $this->ias_ident[$i] . ",";
            }
            return substr($ts_list, 0, strlen($ts_list) - 1);
            
        }
        else
        {
            return "";
        }
    }

    
    /// <summary>
    /// 儲存異動資料到資料庫.(will commit/rollback)
    /// </summary>
    /// <returns>錯誤訊息 if any.</returns>
    public function save()
    {
        $this->i_db = new DB();
        $this->i_db->beginTran();


        //get updating sql string array
        //$j;
        $tn_input = count($this->ia_input);
        //Boolean tb_ident = false;
        $tb_mail = false;
        $ts_error = "";
        //string[] tas_sql = new string[tn_input];
        //string[] tas_ident = new string[tn_input];
        //string[] tas_field = null;
        //string[] tas_value = null;
        $ts_sql = ""; $ts_where = "";
        //$ts_error = "";
        for ($i = 0; $i < $tn_input; $i++)
        {

            //update begin
            //get sql string
            $t_input = $this->ia_input[$i];
            //$tn_dw = Convert.ToInt32(t_input["dw"]);
            $tn_dw = $this->dwNo($t_input);
            $t_dw = $this->ia_dw[$tn_dw];
            $ts_fun = $t_input["fun"];
            $ts_table = $this->getTableByDW($t_dw);
            $ts_sql = $this->getSql($i, $ts_where, $ts_error);
            if ($ts_error != "")
            {
                $ts_error = $ts_error . " (" . $this->is_inf . ")";
                //return lab_exit($this->i_db, $tb_mail, $ts_error);
                goto lab_exit;
            }
            else if ($ts_sql == "")
            {
                continue;
            }


            //run updating sql and save ident column value if need.
            //Boolean tb_ident2 = (ts_fun != "C") ? false : (Convert.ToInt32(t_input["ident"]) >= 0);
            //$ts_ident = (ts_fun != "C") ? "" : t_input["ident"].ToString();
            $this->ias_ident[$i] = "";      //initial
            try
            {
                //if (ts_fun == "C" && t_input["ident"].ToString() != "")
                if ($ts_fun == "C" && isset($t_input["ident"]) && $t_input["ident"] != "-1")
                {
                    //$ts_ident = 
                    $this->ib_ident = true;
                    $this->ias_ident[$i] = $this->i_db->insertIdent($ts_sql);
                }
                else
                {
                    $tn_row = $this->i_db->exeUpdate($ts_sql);
                    if ($tn_row > 1 || $tn_row < -1)
                    {
                        $ts_error = "Should only update 1 record ! (" . $tn_row . ")";
                        //return $this->lab_exit($this->i_db, $tb_mail, $ts_error);
                        goto lab_exit;
                    }
                }
            }
            catch (Exception $t_e)
            {
                $ts_error = $t_e->getMessage();
                goto lab_exit;
                //return lab_exit($this->i_db, $tb_mail, $ts_error);
            }


            //run delegate function !!
            $tb_mail = false;
            if ($this->if_update != null)
            {
                try
                {
                    $ts_error = $this->if_update($this, $i);
                    if ($ts_error != "")
                        goto lab_exit;
                        //return lab_exit($this->i_db, $tb_mail, $ts_error);

                }
                catch (Exception $t_e)
                {
                    //ts_error = "UpdateDB.cs if_update() failed !";
                    $tb_mail = true;
                    $ts_error = $t_e->getMessage();
                    //return lab_exit($this->i_db, $tb_mail, $ts_error);
                    goto lab_exit;
                }
            }

        }


        //commit db
        $this->i_db->commit();


        //傳送 mail if need.
        if (isset($this->i_inf["mail"]))
        {
            $t_mail = $this->i_inf["mail"];
            if (isset($t_mail["funs"]) && strpos($t_mail["funs"], $this->is_fun) !== false)
            {
                Fun::sendMailByJson($t_mail, $this->i_send, null);
            }
        }
    //}

    lab_exit:
        //disconnect database
        //Fun::closeDB(i_conn, i_cmd, null);
        if ($ts_error != "")
            $this->i_db->rollback();
        
        $this->i_db->close();

        if ($tb_mail && $ts_error != "")
            Fun::logError($ts_error);

        return $ts_error;
    }    

    /*
    function lab_error2(){
        $this->i_db->rollback();
        goto lab_exit;
    }
    */

    // 傳回某一筆資料更新資料的 sql 敍述
    public function getSql($pn_row, &$ps_where, &$ps_error)
    {

        //initial ps_error
        $ps_error = "";

        $tn_fLen = 0;
        $t_input = $this->ia_input[$pn_row];
        $ts_fun = $t_input["fun"];
        $ta_fields = array();
        if ($ts_fun != "D")
        {
            $ta_fields = $t_input["fields"];
            $tn_fLen = count($ta_fields);
            if ($tn_fLen == 0)
            {
                return "";
            }
        }

        $j;
        $tn_item;
        //$tn_dw = Convert.ToInt32(t_input["dw"]);
        $tn_dw = $this->dwNo($t_input);
        //JsonArray ta_dw = (JsonArray)p_inf["dws"];
        $t_dw = $this->ia_dw[$tn_dw];
        $ts_fid; $ts_col; $ts_sep;
        $ts_colList = ""; $ts_vlist = ""; $ts_sql = "";
        //$ts_table = (t_dw["updateTable"] != null) ? t_dw["updateTable"].ToString() : t_dw["selectTable"].ToString();
        $ts_table = $this->getTableByDW($t_dw);
        $tas_field = null;
        $tas_value = null;
        $ts_type; $ts_value;
        $t_map = $this->ia_itemMap[$tn_dw];
        $ta_item = $this->ia_items[$tn_dw];
        $ta_field;
        $t_item;
        
        switch ($ts_fun)
        {
            case "C":   //create new
                $tas_field = array($tn_fLen);
                $tas_value = array($tn_fLen);

                //consider filter Row
                //$tn_fRow = Convert.ToInt32(t_input["fRow"]);

                $ts_sep = "";
                //$tn_upRow = Convert.ToInt32(t_input["upRow"]);   //for field inputType="2"
                $tn_upRow = (isset($t_input["upRow"])) ? intval($t_input["upRow"]) : -1;   //for field inputType="2"
                $tn_upEditDW = 0;
                if (isset($t_dw["upEditDW"]))
                    $tn_upEditDW = intval($t_dw["upEditDW"]);
                elseif (isset($t_dw["upDW"]))
                    $tn_upEditDW = intval($t_dw["upDW"]);

                for ($j = 0; $j < $tn_fLen; $j++)
                {
                    //get column name
                    $ta_field = $ta_fields[$j];
                    $ts_fid = $ta_field[0];
                    $tn_item = Fun::idToNo($t_map, $ts_fid);
                    if ($tn_item == -1)
                    {
                        goto no_fid;
                        //return $this->no_fid($ps_error, $ts_fid);
                    }

                    //get column list and value list
                    $t_item = $ta_item[$tn_item];
                    $ts_col = (isset($t_item[Fun::csColName])) ? $t_item[Fun::csColName] : $ts_fid;
                    $ts_colList .= $ts_sep . $ts_col;
                    $tas_field[$j] = $ts_col;
                    //if (tn_fRow >= 0 && ta_field[3].ToString() == "C")
                    if (isset($ta_field[3]) && $ta_field[3] == "2")   //上一層是 identity 欄位!!
                    {
                        //如果這個值不為空白，才需要從上一層 DW 讀取 2010-12-31
                        if (isset($ta_field[2]) && $ta_field[2] != "")
                        {
                            $tas_value[$j] = strval($ta_field[2]);
                        }
                        //else if (tn_upRow == -1 || tn_upRow == 0)
                        elseif ($tn_upRow == -1)
                        {
                            $tas_value[$j] = $this->ias_ident[0]; //先取第一筆, 以後調整!!
                        }
                        else
                        {
                            //從上一層DW取得產生的 identity 欄位值 !! 2010-12-31
                            //用 DW, row 和 upRow 來尋找
                            $tb_find = false;
                            $t_input2;
                            for ($k = 0; $k<count($this->ia_input); $k++)
                            {
                                //if (pn_row != k)
                                //{
                                    $t_input2 = $this->ia_input[$k];
                                    $tn_row = (isset($t_input2["row"])) ? intval($t_input2["row"]) : 0 ;
                                    //if (tn_upEditDW == Convert.ToInt32(t_input2["dw"]) && tn_upRow == Convert.ToInt32(t_input2["row"]))
                                    if ($tn_upEditDW == $this->dwNo($t_input2) && $tn_upRow == $tn_row)
                                    {
                                        $tb_find = true;
                                        $tas_value[$j] = $this->ias_ident[$k]; //先取第一筆, 以後調整!!
                                        break;
                                    }
                                //}
                            }

                            if (!$tb_find)
                            {
                                $ps_error = "Can not find up identy value for inputType='2' field: " . $ts_fid . " (UpdateDB.php)";
                                return "";
                                //break;
                            }
                        }

                        //$tn_upDW = (t_dw["upDW"] != null) ? Convert.ToInt32(t_dw["upDW"]) : 0;
                        //JsonArray ta_ident = (JsonArray)gIdent["dw" + tn_dw];
                        //tas_value[j] = ta_ident[0].ToString();

                        //here !!
                        //tas_value[j] = ias_ident[0]; //先取第一筆, 以後調整!!

                    }
                    else
                    {
                        $tas_value[$j] = strval($ta_field[2]);
                    }

                    $ts_type = $ta_field[1];
                    $ts_value = Fun::addQuote($ts_type, $tas_value[$j]);
                    //$ts_value = $tas_value[$j];
                    //考慮多國語, 使用 unicode !!
                    if ($ts_type == "S" || $ts_type == "S2"){
                        $ts_value = "N" . $ts_value;
                        //$ts_value = "'" . mysql_escape_string($ts_value) . "'";
                    }

                    $ts_vlist .= $ts_sep . $ts_value;
                    $ts_sep = ",";
                }

                $ts_sql = "insert into " . $ts_table . "(" . $ts_colList . ") values (" . $ts_vlist . ")";
                break;

            case "U":   //update
                $tas_field = array($tn_fLen);
                $tas_value = array($tn_fLen);
                $ts_sep = "";
                for ($j = 0; $j < $tn_fLen; $j++)
                {
                    //get column name
                    $ta_field = $ta_fields[$j];
                    $ts_fid = $ta_field[0];
                    $tn_item = Fun::idToNo($t_map, $ts_fid);
                    if ($tn_item == -1)
                    {
                        goto no_fid;
                        //return no_fid($ps_error, $ts_fid);
                    }

                    //get column list and value list
                    $t_item = $ta_item[$tn_item];
                    $ts_col = (isset($t_item[Fun::csColName])) ? $t_item[Fun::csColName] : $ts_fid;
                    //tas_field[j] = ts_col;
                    $tas_value[$j] = (!isset($ta_field[2])) ? "null" : strval($ta_field[2]);

                    $ts_type = $ta_field[1];
                    $ts_value = Fun::addQuote($ts_type, $tas_value[$j]);
                    //$ts_value = $tas_value[$j];
                    //考慮多國語, 使用 unicode !!
                    if ($ts_type == "S" || $ts_type == "S2"){
                        $ts_value = "N" . $ts_value;
                        //$ts_value = "'" . mysql_escape_string($ts_value) . "'";
                    }

                    //ts_colList += ts_sep + ts_col + "=" + Fun::addQuote(ta_field[1].ToString(), tas_value[j]);
                    $ts_colList .= $ts_sep . $ts_col . "=" . $ts_value;
                    $ts_sep = ",";
                }

                $ts_sql = "update " . $ts_table . " set " . $ts_colList;
                break;

            case "D":   //delete
                $ts_sql = "delete from " . $ts_table;
                break;
            /*
            case "DA":  //delete all
                ts_sql = "delete from " + ts_table;
                break;
            */ 
            default:
                break;
        }


        //add where condition string
        $ps_where = "";
        //$ts_key = "";
        //$ts_value;
        //$ts_sep2 = "";
        if ($ts_fun == "U" || $ts_fun == "D" || $ts_fun == "DA")   //DA: delete all
        {
            //ps_where = this.getWhere(t_input, t_map, ta_item, ref ps_error);
            $ps_where = $this->getWhere($t_input);
            //if (ts_colList != "" && ts_fun != "C")
            if ($ps_where == "")
                return "";

            $ts_sql .= " where " . $ps_where;
        }

        return Fun::replaceStrArray($ts_sql, $this->i_send, false);
    //}

    no_fid:
        $ps_error = "UpdateDB.getSql() can not find fid: " . $ts_fid . ".";
        return "";
    }


    /// <summary>
    /// 傳回某一筆資料的 sql where 字串
    /// </summary>
    /// <param name="p_input">某一筆資料 JsonObject</param>
    /// <returns>sql where 字串</returns>
    public function getWhere($p_input)
    {
        //$tn_dw = Convert.ToInt32(p_input["dw"]);
        $tn_dw = $this->dwNo($p_input);
        //$t_dw = (JsonObject)ia_dw[tn_dw];
        $t_map = $this->ia_itemMap[$tn_dw];
        $ta_item = $this->ia_items[$tn_dw];

        $tn_item;
        $ts_fid; $ts_col;
        $ts_where = "";
        $ts_sep = "";
        $t_item;
        $ta_key;
        $ta_keys = $p_input["keys"];
        for ($j = 0; $j < count($ta_keys); $j++)
        {
            $ta_key = $ta_keys[$j];
            $ts_fid = $ta_key[0];
            $tn_item = Fun::idToNo($t_map, $ts_fid);
            if ($tn_item == -1)
            {
                //ps_error = "UpdateDB.getWhere() can not find fid: " + ts_fid + ".";
                return "";
            }
            else if (!isset($ta_key[2]))
            {
                //ps_error = "UpdateDB.getWhere() field value of "+ts_fid+" is null !";
                return "";
            }

            $t_item = $ta_item[$tn_item];
            $ts_col = (isset($t_item[Fun::csColName])) ? $t_item[Fun::csColName] : $ts_fid;
            $ts_where .= $ts_sep . $ts_col . "=" . Fun::addQuote($ta_key[1], strval($ta_key[2]));
            $ts_sep = " and ";
        }

        return $ts_where;
    }
    //=== public method end === 

    
    //=== private function ===
    
}//class end
  
?>
