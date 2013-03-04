<?php
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();
    //$t_db = new DB();
       
    $vip_smtp_host = "smtp.gmail.com";
    $vip_smtp_port = 465;
    $vip_smtp_username = "william@smarten.com.tw";
    $vip_smtp_password = "lee08012011";
    
    $sender = "william@smarten.com.tw";
    $sendername = "William";
    $subject = $t_data["subject"];
    $body = $t_data["body"];
    $receiver = "icf999999999@gmail.com";
    $receivername = "Future";
    $charset = "utf-8";

    require_once(dirname(__FILE__)."/../PHPMailer/class.phpmailer.php");
    $mail= new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host = $vip_smtp_host;
    $mail->Port = $vip_smtp_port;
    $mail->Username = $vip_smtp_username;
    $mail->Password = $vip_smtp_password;
    $mail->CharSet = $charset;
    
    $mail->From = $sender;
    $mail->FromName = $sendername;
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->IsHTML(true);
    $mail->AddAddress($receiver, $receivername);    
    
    if($mail->Send()) {
        return 1;
    } else {
        return 0;
    }   

    //$t_db->close();
?>