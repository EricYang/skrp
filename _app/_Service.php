<?php

    //require_once(dirname(__FILE__) . "/../_code/Fun.php"); 
    require_once(dirname(__FILE__) . "/Fun.php"); 


    Fun::utf8();        //��X Excel ��, ��������Xheader, �ҥH������!!
    $ts_code = Fun::service(true);
    //session_write_close();

    //�p�G�Ǧ^�ť�, �h Flex �|Ĳ�o Fail event !!
    if ($ts_code !== null && $ts_code !== ""){
        echo $ts_code;
    }else{
        echo "_";   //���������� Fun::utf8()!!, or got Error: �t�ο��~: -1072896658 in ie    
    }

    //exit;
?>
