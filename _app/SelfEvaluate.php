<?php

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $selfEvaluateCheckSn = $t_data["selfEvaluateCheckSn"];
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

    /*
    $pdf->Image('../images/1.png', 13, 2, 40, '', '', 'http://www.smarten.com.tw', '', false, 300, '', false, $mask);
    $pdf->SetFontSize(6);
    $pdf->SetXY(135.0, 5.0);
    $pdf->write(10, "TEL:(02)2655-1002 FAX:(02)2655-1711");
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(135.0, 8.0);
    $pdf->write(10, "台北市南港區三重路19-13號E棟5樓515室(南港軟體園區)");
    $pdf->Ln();
    */
    $pdf->SetXY(13.0, 4.0);
    $pdf->SetFontSize(20);
    $pdf->write(10, "自評表");
    
    $pdf->SetXY(140.0, 2.0);
    $pdf->write1DBarcode('Smarten 2012', 'C39', '', '', '', 10, 0.2, $style, 'N');
    
    $pdf->SetXY(14.0, 18.0);
    $pdf->SetFontSize(6);
    
    header('Content-Type:text/plain; charset=utf-8');

    $ts_sql1 = "SELECT 
                a.selfEvaluateItem,
                a.itemName, b.contents,
                d.score,
                d.remark
                FROM SelfEvaluate a 
                INNER JOIN SelfEvaluateDetail b ON a.sn = b.selfEvaluateSn AND a.deleFlag = 0 
                LEFT OUTER JOIN SelfEvaluateCheck c 
                INNER JOIN SelfEvaluateCheckDetail d ON c.sn = d.selfEvaluateCheckSn ON d.selfEvaluateDetailSn = b.sn 
                AND c.sn = ".$selfEvaluateCheckSn.
                " ORDER BY a.selfEvaluateItem, b.sn";
    $ta_row1 = Fun::readRows($ts_sql1);
    if ($ta_row1 != null){        
        $cNo = 1;
        $seiFlag = "";
        $inValue = "";
        
        for ($r = 0; $r < count($ta_row1); $r++){
            $cNo = $r + 1;
            $t_row1 = $ta_row1[$r];

            if ($cNo!=1){
                if ($seiFlag==$t_row1["itemName"]){                    
                    $inValue = "";
                }else{                    
                    $inValue = $t_row1["itemName"];
                }
                $seiFlag = $t_row1["itemName"];
            }else{
                $seiFlag = $t_row1["itemName"];
                $inValue = $t_row1["itemName"];                
            }
            
            $textConMulti = $textConMulti."<tr align='left'>
                <td align=\"right\"><b>".$cNo."</b></td>
                <td align=\"left\">".$inValue."</td>
                <td align=\"left\">".$t_row1["contents"]."</td>
                <td align=\"right\"><b>".$t_row1["score"]."</b></td>            
                <td align=\"left\">".$t_row1["remark"]."</td></tr>";
        }
     
$html = <<<EOD
      <div align="center">            
        <center>        
        <table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse" bordercolor="#B4B4B4" width="100%" id="AutoNumber2" bgcolor="#F5F5F5">
          <tr>
            <td align="center" bgcolor="#FFFFCA" width="5%"><font size="10">No.</font></td>
            <td align="center" bgcolor="#FFFFCA" width="15%"><font size="10">評鑑項目</font></td>
            <td align="center" bgcolor="#FFFFCA" width="64%"><font size="10">評鑑內容</font></td>
            <td align="center" bgcolor="#FFFFCA" width="6%"><font size="10">評分</font></td>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10">備註</font></td>            
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