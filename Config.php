<?php

class Config{  
    //database connection
    //const db = "localhost:3306,StuExam,root,home"; //server name(:port), db name, login id, login password (中間不可空白)
    //const db = "localhost:3306,smart20b,root,home"; //server name(:port), db name, login id, login password (中間不可空白)
    //const db = "10.20.30.242:3306,G7b,smartenG7b,smartenG7b"; //server name(:port), db name, login id, login password (中間不可空白)
    //const db = "10.20.30.242:3306,smart20b,smartenG7b,smartenG7b"; //server name(:port), db name, login id, login password (中間不可空白)
    //const db = "10.20.30.242:3306,smart_data,smartenG7b,smartenG7b"; //server name(:port), db name, login id, login password (中間不可空白)
    //const db = "10.20.30.242:3306,smart_culture,smartenG7b,smartenG7b"; //server name(:port), db name, login id, login password (中間不可空白)
    //const db = "140.137.101.51:3306,smart20b,smartenG7b,smartenG7b"; //server name(:port), db name, login id, login password (中間不可空白)

    //const dbSch = "10.20.30.242:3306,ManaInfo,smartenERP,smartenERP"; //server name(:port), db name, login id, login password (中間不可空白)
    //const db = "10.20.30.242:3306,School_{0},smartenERP,smartenERP"; //server name(:port), db name, login id, login password (中間不可空白)    
    
    const dbSch = "localhost:3306,ManaInfo,root,"; //server name(:port), db name, login id, login password (中間不可空白)
    const db = "localhost:3306,School_{0},root,"; //server name(:port), db name, login id, login password (中間不可空白)    
    
    const sysName = "KRP";
    
    //
    const rootMail = "william@smarten.com.tw";
    const maxRow = 1600;
    const rptMaxRow = 2000;
    //
    /* 
    //gmail smtp, smtpFromBox 無作用 !!
    const smtpSSL = 1;
    const smtpHost = "smtp.gmail.com:465";
    //const smtpPort = "465";
    const smtpUserId = "app@smarten.com.tw";
    const smtpPwd = "abcd1234+";
    const smtpFromBox = "AutoSender@smarten.com.tw";
    const smtpFromName = "AutoSender";
    */
    
    
    //hinet smtp
    const smtpSSL = 0;
    const smtpHost = "msa.hinet.net:25";
    //const smtpPort = "25";
    const smtpUserId = "smarten.g7@msa.hinet.net";
    const smtpPwd = "nk19-13";
    const smtpFromBox = "AutoSender@msa.hinet.net";
    const smtpFromName = "AutoSender";
    
}

?>
