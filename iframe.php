<?php    
    //require_once(dirname(__FILE__) . "/Fun.php"); 

    //Fun::init();
    
    //$uploaddir = Fun::$sDirRoot.$_GET['pathName'];
    //$uploaddir = Fun::$sDirRoot."dbUpLoadFiles/TeachersFolder/LearnPro/img/";

    //$uploaddir = "img/";
    $uploaddir = "dbUpLoadFiles/TeachersFolder/LearnPro/img/";    
    $uploadfile = $uploaddir . basename($_GET['fileName']);
?>
<html> 
<head></head> 

<body bgcolor="#000000"> 
    <div align="center">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
		<td align="center" valign="middle">
                    <object id="MediaPlayer" width="410" height="380" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" standby="Loading Windows Media Player components..." type="application/x-oleobject">
                        <param name="FileName" value="<?php echo $uploadfile; ?>">
                        <param name="ShowControls" value="true">
                        <param name="ShowStatusBar" value="false">
                        <param name="ShowDisplay" value="false">
                        <param name="autostart" value="true">
                    </object>
		</td>
            </tr>
	</table>
    </div>
</body> 
</html> 