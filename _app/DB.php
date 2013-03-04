<?php
//for MySQL

require_once(dirname(__FILE__)."/../Config.php"); 
require_once(dirname(__FILE__)."/Fun.php"); 


class DB{
    
    const cbPConnect = true;    //使用 pconnect or not
    
    //=== public property ===
    public $bConn = true;
    
    
    //=== private instance variables ===
    private $i_conn;

    
    //=== public method begin === 
    public function __construct($ps_db = ""){
        
        //temp
        //Fun::logFile("db !");

        /*
        if ($ps_db == "")
            $ps_db = "db";
        
        $ts_conn = constant("Config::" . $ps_db);        
        */
        $ts_conn = Fun2::getDbStr($ps_db);        
        $ts_error = "Config." . $ps_db . "=" . $ts_conn . " is wrong.";        
        $tas_1 = explode(',', $ts_conn);
        //$tas_1 = splitTrim(',', $ts_conn);
        
        if (count($tas_1) != 4){
            goto lab_error;     
            //return;
        }   
        
        //temp
        //Fun::logFile("login db:" . $tas_1[0] . ":" . $tas_1[2] . ":" . $tas_1[3]);
        
        if (self::cbPConnect)
            $this->i_conn = mysql_pconnect($tas_1[0], $tas_1[2], $tas_1[3]);
        else
            $this->i_conn = mysql_connect($tas_1[0], $tas_1[2], $tas_1[3]);
        
        
        try{
            if (!$this->i_conn){
                goto lab_error; 
                //return;
            }                   
            
            $tb_db = mysql_select_db($tas_1[1], $this->i_conn);
            if (!$tb_db){
                $this->bConn = false;    
                $ts_error = "Database did not exist. (". $tas_1[1] . ")";
                goto lab_error;                    
            }
            Fun::logFile("login db:" . $tas_1[0] . ":" . $tas_1[2] . ":" . $tas_1[3], ":" . $tas_1[1]);
            
            mysql_query("SET NAMES utf8;", $this->i_conn);     
            $this->bConn = true;    
            //mysql_query("SET AUTOCOMMIT=0");             
            
        }catch(Exception $t_e){
            $ts_error = $t_e->getMessage();
            goto lab_error;    
            //return;        
        }
        
        return;
    //}
    
        
    lab_error:
        Fun::logError($ts_error);
        //throw new Exception( 'Failed!' );
        //
        //throw new NotFoundException(); 
        //unset($this);
        //return null;
        return;
            
    }     

    /*
    public function isNull(){
        return (!$this->i_conn || $this->i_conn == null);
    }
    */
    
    
    public function readRow($ps_sql){
        $ta_row = $this->readRows($ps_sql);
        if ($ta_row == null)
            return null;
        else
            return $ta_row[0];    
            
    }
    
    
    //return json array
    public function readRows($ps_sql)
    {
        $ta_row = array();
        $t_q = mysql_query($ps_sql); 
        if (!$t_q || mysql_num_rows($t_q) == 0)
            return null;
            
        //只傳回欄位名稱的資料, 不傳回索引資料   
        //$tb_find = false;
        while ($t_row = mysql_fetch_array($t_q, MYSQL_ASSOC)) { 
            $ta_row[] = $t_row; 
            //$tb_find = true;
        } 
        
        //if ($tb_find){
            return $ta_row;             
        //}else{
        //    return null;
        //}
    }

    
    //如果是 insert into 而且有 auto increase 欄位, 則傳回 auto increase 的值!!
    //如果不是, 則傳回異動的筆數 2012-1-17
    public function exeUpdate($ps_sql)
    {
        //openCommand();
        //i_cmd.CommandText = ps_sql;
        //$ps_sql = mysql_escape_string($ps_sql);
        //log act !!
        Fun::logAct("_UpdateDB", $ps_sql);
        
        mysql_query($ps_sql, $this->i_conn);
        $ts_error = mysql_error();
        if ($ts_error != ""){
            //Fun::logFile("error: " . $ts_error . Fun::csTextCarrier . "sql: " . $ps_sql, true);
            Fun::logError($ts_error . Fun::csTextCarrier . "   sql: " . $ps_sql);
            return 0;
        }else{
            $tn_id = mysql_insert_id();
            if ($tn_id != 0){
                return $tn_id;
            }else{
                return mysql_affected_rows();                                            
            }
        }
        

        /*
        try
        {
            $tb_ok = mysql_query($ps_sql, $this->i_conn);
            if (!$tb_ok){
                Fun::logFile("error: " . mysql_error() . Fun::csTextCarrier . "sql: " . $ps_sql, true);
                return 0;
            }else{
                return mysql_affected_rows();
            }
        }
        catch (Exception $t_e)
        {
            throw $t_e;
            //return 0;
        }
        */
    }
    
    
    //如果使用 pconnect, 則不需要 close
    public function close()
    {
        if (!self::cbPConnect)
            mysql_close($this->i_conn);
    }


    //return string
    public function insertIdent($ps_sql)
    {
        //temp
        $tb_ok = mysql_query($ps_sql, $this->i_conn);
        if (!$tb_ok){
            //Fun::logFile("error: " . mysql_error() . Fun::csTextCarrier . "sql: " . $ps_sql, true);
            Fun::logError(mysql_error() . Fun::csTextCarrier . "   sql: " . $ps_sql);
            return "";    //是否需要傳回 -1 ??
        }else{
            return strval(mysql_insert_id());
        }
        
//        //openCommand();
//        $ps_sql .= ";select @@identity;";

//        try
//        {
//            $ts_ident = "";
//            SqlDataReader t_reader = i_cmd.ExecuteReader();
//            if (t_reader.Read())
//            {
//                ts_ident = t_reader.GetValue(0).ToString();
//            }

//            t_reader.Close();
//            return $ts_ident;
//        }
//        catch (Exception $t_e)
//        {
//            throw $t_e;
//        }
    }
    
    
    public function beginTran()
    {
        mysql_query("BEGIN");
    }

    
    public function commit()
    {
        mysql_query("COMMIT");
    }

    public function rollback()
    {
        mysql_query("ROLLBACK");
    }
    //=== public method end === 
    
    //=== private function ===
    
}

?>
