<?php

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    header('Content-Type:text/plain; charset=utf-8');
    
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

    //define ('PDF_PAGE_ORIENTATION', 'L');//橫
    
    //$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    //$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
    $pdf->write(10, "日期別請款明細表");
    
    //$pdf->SetXY(140.0, 2.0);
    //$pdf->write1DBarcode('Smarten 2012', 'C39', '', '', '', 10, 0.2, $style, 'N');
    
    $pdf->SetXY(14.0, 18.0);
    $pdf->SetFontSize(6);
    
    $textConMulti = '';
    
    //header('Content-Type:text/plain; charset=utf-8');
    
    $ts_sql0 = "SELECT a.sn AS accRequestPaymentSn, a.serialNo, CONCAT(b.cName, ' ', b.eName) AS staffName, a.cDate, a.cAmounts, a.amounts, a.eDate, a.funcItemSn,
                CONCAT(e.accMainCode, d.accTitleCode, c.accIocCode) AS accIocCode,
                CONCAT(e.accMainName, ' - ', d.accTitleName, ' - ', c.accIocName) AS accIocName,
                a.pExcerpt, a.cDesc, f.unitName, f.contactName, f.conTel
                FROM AccRequestPayment a INNER JOIN Staff b ON a.staffSn = b.sn AND a.accType = 1
                INNER JOIN AccIoc c ON a.accIocSn = c.sn
                INNER JOIN AccTitle d ON c.accTitleSn = d.sn
                INNER JOIN AccMain e ON d.accMainSn = e.sn 
                LEFT OUTER JOIN PublicDept f ON a.publicDeptSn = f.sn
                WHERE a.cDate BETWEEN '".$cDate."' AND '".$cDate2."'
                ORDER BY a.cDate";
    $ta_row0 = Fun::readRows($ts_sql0);
    if ($ta_row0 != null){
        $pMoney = 0;
        $eMoney = 0;
        
        for ($p = 0; $p < count($ta_row0); $p++){
            $t_row0 = $ta_row0[$p];
            
            $pMoney = $pMoney + $t_row0["cAmounts"];
            /*
            $amountsTmp = 0;
            $ts_sql1 = "SELECT SUM(amounts) AS amounts
                        FROM AccRequestPaid
                        WHERE (accRequestPaymentSn = ".$t_row0["accRequestPaymentSn"].")";
            $ta_row1 = Fun::readRow($ts_sql1);
            if ($ta_row1 != null){
                $amountsTmp = $ta_row1["amounts"];
            }
             */

            $textConMulti = $textConMulti."<tr align='left'>
                <td align=\"left\">".$t_row0["cDate"]."</td>
                <td align=\"left\">".$t_row0["serialNo"]."</td>
                <td align=\"left\">".$t_row0["staffName"]."</td>
                <td align=\"left\">".$t_row0["accIocCode"].' '.$t_row0["accIocName"]."</td>
                <td align=\"left\">".$t_row0["unitName"]."</td>
                <td align=\"right\">".number_format($t_row0["cAmounts"], 2)."</td>
                <td align=\"right\">".number_format($t_row0["amounts"], 2)."</td></tr>";
        }
        
        $seDate = $cDate . ' ~ ' . $cDate2;
        $pMoney = number_format($pMoney, 2);
     
$html = <<<EOD
    <table width="100%"  border="0">
        <tr>
            <td><font size="12"><b>起迄日期：$seDate</b></font></td>
        </tr>
    </table>
      <div align="center">            
        <center>        
        <table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse" bordercolor="#B4B4B4" width="100%" bgcolor="#F5F5F5">
          <tr>
            <td align="center" bgcolor="#FFFFCA"><font size="10"><b>請款日期</b></font></td>
            <td align="center" bgcolor="#FFFFCA"><font size="10"><b>請款單號</b></font></td>
            <td align="center" bgcolor="#FFFFCA"><font size="10"><b>請款人</b></font></td>
            <td align="center" bgcolor="#FFFFCA"><font size="10"><b>支出項目</b></font></td>
            <td align="center" bgcolor="#FFFFCA"><font size="10"><b>廠商名稱</b></font></td>
            <td align="center" bgcolor="#FFFFCA"><font size="10"><b>請款金額</b></font></td>
            <td align="center" bgcolor="#FFFFCA"><font size="10"><b>已付金額</b></font></td>
          </tr>          
          $textConMulti
          <tr>
            <td align="right" bgcolor="#FFFFCA" colspan="5"><font size="10"><b>合計：</b></font></td>
            <td align="right" bgcolor="#FFFFCA"><font size="10"><b>$pMoney</b></font></td>
            <td align="right" bgcolor="#FFFFCA"><font size="10"><b>$eMoney</b></font></td>
          </tr>
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
