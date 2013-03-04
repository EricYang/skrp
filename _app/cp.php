<?php
    require_once(dirname(__FILE__) . "/Fun.php"); 

    Fun::init();
    
    $uploaddir = Fun::$sDirRoot.$_GET['pathName'];
              
    if ( !(is_dir($uploaddir) && is_writeable($uploaddir) )) {
        mkdir($uploaddir, 0755);
    }

    $uploadfile = $uploaddir . basename($_GET['fileName']);

    $passFlag = 0; //直接上傳的旗標
    
    //影片 & 圖片 分開
    if (intval($_GET['ftFlag']) > 10){
        $passFlag = 1;
    }else{
        switch ($_GET['ftFlag']){
            case "1":
                $src = imagecreatefromjpeg($_FILES['Filedata']['tmp_name']);
                break;
            case "2":
                $src = imagecreatefromgif($_FILES['Filedata']['tmp_name']);
                break;
            case "3":
                $src = imagecreatefromjpeg($_FILES['Filedata']['tmp_name']);
                break;
            case "4":
                $src = imagecreatefrompng($_FILES['Filedata']['tmp_name']);
                break;
        }
    
        // get the source image's widht and hight
        $src_w = imagesx($src);
        $src_h = imagesy($src);
 
        // assign thumbnail's widht and hight
        $limitPicSize = 600;
        if($src_h > $limitPicSize){
        
            if($src_w > $src_h){
                $thumb_w = $limitPicSize;
                $thumb_h = intval($src_h / $src_w * $limitPicSize);
            }else{
                $thumb_h = $limitPicSize;
                $thumb_w = intval($src_w / $src_h * $limitPicSize);
            }
        
            // if you are using GD 1.6.x, please use imagecreate()
            $thumb = imagecreatetruecolor($thumb_w, $thumb_h);
 
            // start resize
            imagecopyresized($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h); 
 
            // save thumbnail
            switch ($_GET['ftFlag']){
                case "1":
                    imagejpeg($thumb, $uploadfile);
                    break;
                case "2":            
                    imagegif($thumb, $uploadfile);
                    break;
                case "3":
                    imagejpeg($thumb, $uploadfile);
                    break;
                case "4":
                    imagepng($thumb, $uploadfile);
                    break;
            }

        }else{
            $passFlag = 1;
        }
    }
    
    //直接上傳
    if ($passFlag == 1){
        $temploadfile = $_FILES['Filedata']['tmp_name'];
        move_uploaded_file($temploadfile , $uploadfile);
    }
?>