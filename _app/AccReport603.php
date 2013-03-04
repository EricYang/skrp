<?php

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $cYear = $t_data["cYear"];
    $cMonth = $t_data["cMonth"];
    $fileNameTmp = $t_data["fileNameTmp"];
    $defaultPdfPath = $t_data["defaultPdfPath"];

    require_once(dirname(__FILE__)."/../tcpdf/config/lang/eng.php");
    require_once(dirname(__FILE__)."/../tcpdf/tcpdf.php");

    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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
    $pdf->write(10, "現金收支月報表(註冊)");

    header('Content-Type:text/plain; charset=utf-8');
        
    $pdf->SetXY(14.0, 18.0);
    $pdf->SetFontSize(8);
    
    $dataFlag = 0;

    //收入
    $textConMulti = '';
    $ht1Text = '';
    $ts_sql0 = "SELECT a.cDate, SUM(d.amounts) AS amounts
                FROM AccMonthPaidDT a INNER JOIN
                AccMonthPaid b ON a.sn = b.accMonthPaidDTSn INNER JOIN
                AccMIncomeStu c ON b.accMIncomeStuSn = c.sn INNER JOIN
                AccMIncome d ON c.sn = d.accMIncomeStuSn INNER JOIN
                AccIoc e ON d.accIocSn = e.sn INNER JOIN
                AccTitle f ON e.accTitleSn = f.sn
                WHERE d.amounts > 0 AND (f.accTitleType = 1) AND (EXTRACT(YEAR_MONTH FROM a.cDate) = '".$cYear.$cMonth."')
                GROUP BY a.cDate
                ORDER BY a.cDate";
    $ta_row0 = Fun::readRows($ts_sql0);
    if ($ta_row0 != null){
        $pMoney = 0;        
            
        for ($p = 0; $p < count($ta_row0); $p++){
            $t_row0 = $ta_row0[$p];
            
            $accDetailTmp = '';
            $pMoney = $pMoney + $t_row0["amounts"];

            $ts_sql1 = "SELECT e.accIocName, SUM(d.amounts) AS amounts
                        FROM AccMonthPaidDT a INNER JOIN
                        AccMonthPaid b ON a.sn = b.accMonthPaidDTSn INNER JOIN
                        AccMIncomeStu c ON b.accMIncomeStuSn = c.sn INNER JOIN
                        AccMIncome d ON c.sn = d.accMIncomeStuSn INNER JOIN
                        AccIoc e ON d.accIocSn = e.sn INNER JOIN
                        AccTitle f ON e.accTitleSn = f.sn
                        WHERE d.amounts > 0 AND (f.accTitleType = 1) AND (a.cDate = '".$t_row0["cDate"]."')
                        GROUP BY e.accIocName
                        ORDER BY e.accIocName";
            $ta_row1 = Fun::readRows($ts_sql1);
            if ($ta_row1 != null){
                for ($y = 0; $y < count($ta_row1); $y++){
                    $t_row1 = $ta_row1[$y];
                    $accDetailTmp = $accDetailTmp . $t_row1["accIocName"].'：'.$t_row1["amounts"] . '。    ';                    
                }
            }
            
            $textConMulti = $textConMulti."<tr align='left'>
                    <td align=\"left\">".$t_row0["cDate"]."</td>
                    <td align=\"right\">".number_format($t_row0["amounts"], 2)."</td>
                    <td align=\"left\">".$accDetailTmp."</td></tr>";
        }
            
        $pMoney = number_format($pMoney, 2);
        $dataFlag = 1;
        
        $ht1Text = "<br>
    <table width=\"100%\"  border=\"0\">
        <tr>
            <td><font size=\"12\"><b>收入：</b></font></td>
        </tr>
    </table>
      <div align=\"center\">            
        <center>        
        <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#B4B4B4\" width=\"100%\" bgcolor=\"#F5F5F5\">
          <tr>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"10%\"><font size=\"10\"><b>日期</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"20%\"><font size=\"10\"><b>金額</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"70%\"><font size=\"10\"><b>明細</b></font></td>
          </tr>          
          $textConMulti
          <tr>
            <td align=\"right\" bgcolor=\"#FFFFCA\"><font size=\"10\"><b>合計：</b></font></td>
            <td align=\"right\" bgcolor=\"#FFFFCA\"><font size=\"10\"><b>$pMoney</b></font></td>
            <td align=\"right\" bgcolor=\"#FFFFCA\"></td>
          </tr>
        </table>
        </center>
      </div>";
            
    }
    
    //支出
    $textConMulti2 = '';
    $ht2Text = '';
    $ts_sql2 = "SELECT a.cDate, COUNT(*) AS theCo, SUM(b.amounts) AS amounts
                FROM AccRequestPaidDT a INNER JOIN
                AccRequestPaid b ON a.sn = b.accRequestPaidDTSn INNER JOIN
                AccRequestPayment c ON b.accRequestPaymentSn = c.sn INNER JOIN
                AccIoc d ON c.accIocSn = d.sn INNER JOIN
                AccTitle e ON d.accTitleSn = e.sn
                WHERE (e.accTitleType = 1) AND (EXTRACT(YEAR_MONTH FROM a.cDate) = '".$cYear.$cMonth."')
                GROUP BY a.cDate
                ORDER BY a.cDate";
    $ta_row2 = Fun::readRows($ts_sql2);
    if ($ta_row2 != null){
        $p2Money = 0;
                    
        for ($q = 0; $q < count($ta_row2); $q++){
            $t_row2 = $ta_row2[$q];
            
            $accDetailTmp2 = '';
            $p2Money = $p2Money + $t_row2["amounts"];

            $ts_sql3 = "SELECT d.accIocName AS accDetail
                        FROM AccRequestPaidDT a INNER JOIN
                        AccRequestPaid b ON a.sn = b.accRequestPaidDTSn INNER JOIN
                        AccRequestPayment c ON b.accRequestPaymentSn = c.sn INNER JOIN
                        AccIoc d ON c.accIocSn = d.sn INNER JOIN
                        AccTitle e ON d.accTitleSn = e.sn
                        WHERE (e.accTitleType = 1) AND (a.cDate = '".$t_row2["cDate"]."')
                        GROUP BY d.accIocName
                        ORDER BY d.accIocName";
            $ta_row3 = Fun::readRows($ts_sql3);
            if ($ta_row3 != null){
                for ($z = 0; $z < count($ta_row3); $z++){
                    $t_row3 = $ta_row3[$z];
                    $accDetailTmp2 = $accDetailTmp2 . $t_row3["accDetail"] . '。    ';                    
                }
            }
            
            $textConMulti2 = $textConMulti2."<tr align='left'>
                    <td align=\"left\">".$t_row2["cDate"]."</td>
                    <td align=\"left\">".$accDetailTmp2."</td>
                    <td align=\"right\">".$t_row2["theCo"]."</td>
                    <td align=\"right\">".number_format($t_row2["amounts"], 2)."</td></tr>";
        }
            
        $p2Money = number_format($p2Money, 2);
        
        $dataFlag = 1;
        
        $ht2Text = "<br>
    <table width=\"100%\"  border=\"0\">
        <tr>
            <td><font size=\"12\"><b>支出：</b></font></td>
        </tr>
    </table>
      <div align=\"center\">            
        <center>        
        <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#B4B4B4\" width=\"100%\" bgcolor=\"#F5F5F5\">
          <tr>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"15%\"><font size=\"10\"><b>日期</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"55%\"><font size=\"10\"><b>支出項目</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"15%\"><font size=\"10\"><b>筆數</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"15%\"><font size=\"10\"><b>支出金額</b></font></td>
          </tr>          
          $textConMulti2
          <tr>
            <td align=\"right\" bgcolor=\"#FFFFCA\" colspan=\"3\"><font size=\"10\"><b>合計：</b></font></td>            
            <td align=\"right\" bgcolor=\"#FFFFCA\"><font size=\"10\"><b>$p2Money</b></font></td>
          </tr>
        </table>
        </center>
      </div>";
    }
    
    if ($dataFlag == 1){
        
$html = <<<EOD
    <table width="100%"  border="0">
        <tr>
            <td><font size="12"><b>年 /月份：$cYear /$cMonth</b></font></td>
        </tr>
    </table>    
    $ht1Text
    $ht2Text
EOD;
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

        //Close and output PDF document
        $pdf->Output($defaultPdfPath.$fileNameTmp, 'F');
    }
    
    echo $dataFlag ;
?>