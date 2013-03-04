<?php

    //require_once(dirname(__FILE__) . "/../_code/Fun.php"); 
    require_once(dirname(__FILE__) . "/Fun.php"); 


    Fun::utf8();        //輸出 Excel 時, 必須先輸出header, 所以先執行!!
    $ts_code = Fun::service(true);
    //session_write_close();

    //如果傳回空白, 則 Flex 會觸發 Fail event !!
    if ($ts_code !== null && $ts_code !== ""){
        echo $ts_code;
    }else{
        echo "_";   //必須先執行 Fun::utf8()!!, or got Error: 系統錯誤: -1072896658 in ie    
    }

    //exit;
?>
