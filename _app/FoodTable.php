<?php

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $cDate = trim($t_data["cDate"]);
    if ($cDate==""){
        $cDate = date('Y-m-d');
    }
    $cDate2 = trim($t_data["cDate2"]);
    if ($cDate2==""){
        $cDate2 = $cDate;
    }
    $fileNameTmp = $t_data["fileNameTmp"];
    $defaultPdfPath = $t_data["defaultPdfPath"];

    require_once(dirname(__FILE__)."/../tcpdf/config/lang/eng.php");
    require_once(dirname(__FILE__)."/../tcpdf/tcpdf.php");

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    //set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    //set some language-dependent strings
    $pdf->setLanguageArray($l);

    $pdf->SetFont('msungstdlight', '', '');

    $style = array(
        'position' => '',
        'align' => 'C',
        'stretch' => false,
        'fitwidth' => true,
        'cellfitalign' => '',
        'border' => true,
        'hpadding' => 'auto',
        'vpadding' => 'auto',
        'fgcolor' => array(0,0,0),
        'bgcolor' => false, //array(255,255,255),
        'text' => true,
        'font' => 'helvetica',
        'fontsize' => 4,
        'stretchtext' => 4
    );

    // add a page
    $pdf->AddPage();

    $pdf->SetXY(13.0, 4.0);
    $pdf->SetFontSize(20);
    $pdf->write(10, "伙食表");
    
    $pdf->SetXY(140.0, 2.0);
    $pdf->write1DBarcode('Smarten 2012', 'C39', '', '', '', 10, 0.2, $style, 'N');
    
    $pdf->SetXY(14.0, 18.0);
    $pdf->SetFontSize(6);

    header('Content-Type:text/plain; charset=utf-8');
    
    $textConMulti = '';
    
    $ts_sql0 = "SELECT sn, cDate, remark 
                FROM FoodTable 
                WHERE cDate BETWEEN '".$cDate."' AND '".$cDate2."'
                ORDER BY cDate";
    $ta_row0 = Fun::readRows($ts_sql0);
    if ($ta_row0 != null){
        for ($p = 0; $p < count($ta_row0); $p++){
            $t_row0 = $ta_row0[$p];
            
            $s1 = "";
            $s2 = "";
            $s3 = "";
            $s4 = "";
            $s5 = "";
            
            $ts_sql1 = "SELECT a.sysType, b.itemName
                        FROM FoodTableDetail a, FuncItem b
                        WHERE a.funcItemSn = b.sn
                        AND a.foodTableSn = ".$t_row0["sn"];
            $ta_row1 = Fun::readRows($ts_sql1);
            if ($ta_row1 != null){
                for ($r = 0; $r < count($ta_row1); $r++){
                    $t_row1 = $ta_row1[$r];
                        switch ($t_row1["sysType"]){
                        case "1":
                            $s1 = $s1 . $t_row1["itemName"] . ' ';
                            break;
                        case "2":
                            $s2 = $s2 . $t_row1["itemName"] . ' ';
                            break;
                        case "3":
                            $s3 = $s3 . $t_row1["itemName"] . ' ';
                            break;
                        case "4":
                            $s4 = $s4 . $t_row1["itemName"] . ' ';
                            break;
                        case "5":
                            $s5 = $s5 . $t_row1["itemName"] . ' ';
                            break;
                        }
                }
            }
            
            $textConMulti = $textConMulti."<tr align='left'>
                <td align=\"left\"><b>".$t_row0["cDate"]."</b></td>
                <td align=\"left\">".$s1."</td>
                <td align=\"left\">".$s2."</td>
                <td align=\"left\">".$s3."</td>
                <td align=\"left\">".$s4."</td>
                <td align=\"left\">".$s5."</td></tr>";            
        }
     
$html = <<<EOD
      <div align="center">            
        <center>        
        <table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse" bordercolor="#B4B4B4" width="100%" bgcolor="#F5F5F5">
          <tr>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10">日期</font></td>
            <td align="center" bgcolor="#FFFFCA" width="22%"><font size="10">早餐</font></td>
            <td align="center" bgcolor="#FFFFCA" width="22%"><font size="10">營養午餐</font></td>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10">水果</font></td>
            <td align="center" bgcolor="#FFFFCA" width="18%"><font size="10">下午點心</font></td>
            <td align="center" bgcolor="#FFFFCA" width="18%"><font size="10">晚上延托點心</font></td>
          </tr>          
          $textConMulti
        </table>
        </center>
      </div>
EOD;

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

        //Close and output PDF document    
        $pdf->Output($defaultPdfPath.$fileNameTmp, 'F');        
        
        echo "1" ;
    }else{
        echo "0" ;
    }
?>