<?php
	$uploaddir = ‘image/’;
	$uploadfile = $uploaddir . basename($_FILES['Filedata']['name']);
	$temploadfile = $_FILES['Filedata']['tmp_name'];
	move_uploaded_file($temploadfile , $uploadfile);
?>