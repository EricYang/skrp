<?php
require_once(dirname(__FILE__) . "/Fun.php"); 
require_once(dirname(__FILE__) . "/Fun2.php"); 
//require_once("d:\smarten\skrp\_app\Fun.php"); 
//require_once("d:\smarten\skrp\_app\Fun2.php"); 


//輸出 Excel 時, 必須先輸出header, 所以先執行!!
header('Content-Type:text/plain; charset=utf-8');        
$ts_data = str_replace('\"', '"', $_REQUEST["data"]);

//傳送json字串到後端時, 如果裡面的欄位包含 object/array, 則此欄位必須使用物件的型式, 不可再包成字串, 否則無法正確解碼 !!            
//裡面有斷行符號時, 無法正確解碼, Flex端必須先將物件內容轉換成字串, 所以這裡必須還原成物件
$ts_data = str_replace(array('"[',']"','"{','}"'), array('[',']','{','}'), $ts_data);
$t_row = json_decode($ts_data, true);
$ts_code = DBQuery::QueryData($t_row);

//如果傳回空白, 則 Flex 會觸發 Fail event !!
if ($ts_code !== null && $ts_code !== "")
{
    echo $ts_code;
}
else
{
  echo "{\"QueryStatus\":0}";   //必須先執行 Fun::utf8()!!, or got Error: 系統錯誤: -1072896658 in ie    
}
exit;
//***********************************************************************************************************************
class DBQuery
{
	 public static function QueryData($param)
	 {
      session_start();
      $ts_conn=Fun2::getDbStr($ps_db);
      $tas_1 = explode(',', $ts_conn);
      
      //"127.0.0.1";   database location
      $URL = $tas_1[0];                 
      //"school_1";    database name
      $DATABASE = $tas_1[1];          
      //"root";        database username
      $USERNAME = $tas_1[2];            
      //"db88u88";     database password
      $PASSWORD = $tas_1[3];            
      
      
      $pi_SQLCommand=$param["SQLCommand"];
      //$pi_bookNo='SOL02';
      //$DATABASE="school_1";
      mysql_connect($URL, $USERNAME, $PASSWORD);
      mysql_select_db($DATABASE) or die('Cannot connect to database.');
      
      $returnArray = array();      
      switch ($pi_SQLCommand)
      {
          case "BookManaE_Q1":         //傳回多國語資料
               $pi_bookNo=$param["bookNo"];
               $query = "select case when d.lended_quantity <> 0 then a.quantity-d.lended_quantity  
                         else a.quantity  end as balanced_quantity 
                         from bookdata a
                         LEFT OUTER join (select count(*) as lended_quantity,b.bookdatasn
                         from bookmana b 
                         inner join  bookdata c on c.sn=b.bookdatasn and b.rdate is NULL group by b.bookDataSn) d 
                         on d.bookdatasn=a.sn 
                         where a.bookno='".$pi_bookNo."'";
               break;
          default:
                return "";                
                         
      }
      $result = mysql_query($query);
      
      while($row = mysql_fetch_assoc($result))
      {
          array_push($returnArray, $row);
      }
      
      mysql_close();
      return json_encode($returnArray);
  }
}  
?>
