<?php

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $cDate = $t_data["cDate"];
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
    $pdf->write(10, "每日現金收支明細表(月費)");

    header('Content-Type:text/plain; charset=utf-8');
        
    $pdf->SetXY(14.0, 18.0);
    $pdf->SetFontSize(8);
    
    $dataFlag = 0;

    //收入
    $textConMulti = '';
    $ht1Text = '';
    $ts_sql0 = "SELECT c.sn AS accMIncomeStuSn, c.serialNo, CONCAT(i.theClassName, ' / ', h.classLeavesName) AS classInfoName, g.cName AS studentName, CONCAT(c.cYear, ' / ', c.cMonth) AS cYearMonth, b.amounts
                FROM AccMonthPaidDT a INNER JOIN
                AccMonthPaid b ON a.sn = b.accMonthPaidDTSn INNER JOIN
                AccMIncomeStu c ON b.accMIncomeStuSn = c.sn INNER JOIN
                AccMIncome d ON c.sn = d.accMIncomeStuSn INNER JOIN
                AccIoc e ON d.accIocSn = e.sn INNER JOIN
                AccTitle f ON e.accTitleSn = f.sn INNER JOIN
                Student g ON c.studentSn = g.sn INNER JOIN
                ClassLeaves h ON c.classLeavesSn = h.sn INNER JOIN
                ClassInfo i ON h.classInfoSn = i.sn
                WHERE d.amounts > 0 AND (f.accTitleType = 2) AND (a.cDate = '".$cDate."')
                GROUP BY c.sn
                ORDER BY c.serialNo, a.serialNo";
    $ta_row0 = Fun::readRows($ts_sql0);
    if ($ta_row0 != null){
        $pMoney = 0;        
            
        for ($p = 0; $p < count($ta_row0); $p++){
            $t_row0 = $ta_row0[$p];
            
            $accDetailTmp = '';
            $pMoney = $pMoney + $t_row0["amounts"];

            $ts_sql1 = "SELECT CONCAT(b.accIocName, '：' ,a.amounts) AS accDetail
                        FROM AccMIncome a INNER JOIN
                        AccIoc b ON a.accIocSn = b.sn INNER JOIN
                        AccTitle c ON b.accTitleSn = c.sn
                        WHERE a.amounts > 0 AND (c.accTitleType = 2)
                        AND (a.accMIncomeStuSn = ".$t_row0["accMIncomeStuSn"].")";
            $ta_row1 = Fun::readRows($ts_sql1);
            if ($ta_row1 != null){
                for ($y = 0; $y < count($ta_row1); $y++){
                    $t_row1 = $ta_row1[$y];
                    $accDetailTmp = $accDetailTmp . $t_row1["accDetail"] . '。    ';                    
                }
            }
            
            $textConMulti = $textConMulti."<tr align='left'>
                    <td align=\"left\">".$t_row0["serialNo"]."</td>
                    <td align=\"left\">".$t_row0["classInfoName"]."</td>
                    <td align=\"left\">".$t_row0["studentName"]."</td>
                    <td align=\"left\">".$t_row0["cYearMonth"]."</td>
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
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"10%\"><font size=\"10\"><b>收據號碼</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"10%\"><font size=\"10\"><b>班級</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"20%\"><font size=\"10\"><b>姓名</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"5%\"><font size=\"10\"><b>月份</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"10%\"><font size=\"10\"><b>金額</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"45%\"><font size=\"10\"><b>明細</b></font></td>
          </tr>          
          $textConMulti
          <tr>
            <td align=\"right\" bgcolor=\"#FFFFCA\" colspan=\"4\"><font size=\"10\"><b>合計：</b></font></td>
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
    $ts_sql2 = "SELECT a.serialNo AS serialNoP, f.cName, c.serialNo AS serialNoR, c.cDate, d.accIocName, c.cAmounts, b.amounts
                FROM AccRequestPaidDT a INNER JOIN
                AccRequestPaid b ON a.sn = b.accRequestPaidDTSn INNER JOIN
                AccRequestPayment c ON b.accRequestPaymentSn = c.sn INNER JOIN
                AccIoc d ON c.accIocSn = d.sn INNER JOIN
                AccTitle e ON d.accTitleSn = e.sn INNER JOIN
                Staff f ON c.staffSn = f.sn
                WHERE (e.accTitleType = 2) AND (a.cDate = '".$cDate."')
                ORDER BY c.serialNo, a.serialNo";
    $ta_row2 = Fun::readRows($ts_sql2);
    if ($ta_row2 != null){
        $r2Money = 0;
        $p2Money = 0;
                    
        for ($q = 0; $q < count($ta_row2); $q++){
            $t_row2 = $ta_row2[$q];            

            $r2Money = $r2Money + $t_row2["cAmounts"];
            $p2Money = $p2Money + $t_row2["amounts"];
            
            $textConMulti2 = $textConMulti2."<tr align='left'>
                    <td align=\"left\">".$t_row2["serialNoP"]."</td>
                    <td align=\"left\">".$t_row2["cName"]."</td>
                    <td align=\"left\">".$t_row2["serialNoR"]."</td>
                    <td align=\"left\">".$t_row2["cDate"]."</td>
                    <td align=\"left\">".$t_row2["accIocName"]."</td>
                    <td align=\"right\">".number_format($t_row2["cAmounts"], 2)."</td>
                    <td align=\"right\">".number_format($t_row2["amounts"], 2)."</td></tr>";
        }
            
        $r2Money = number_format($r2Money, 2);
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
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"15%\"><font size=\"10\"><b>支出單號</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"15%\"><font size=\"10\"><b>請款人</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"15%\"><font size=\"10\"><b>請款單號</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"10%\"><font size=\"10\"><b>請款日期</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"25%\"><font size=\"10\"><b>支出項目</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"10%\"><font size=\"10\"><b>請款金額</b></font></td>
            <td align=\"center\" bgcolor=\"#FFFFCA\" width=\"10%\"><font size=\"10\"><b>已付金額</b></font></td>
          </tr>          
          $textConMulti2
          <tr>
            <td align=\"right\" bgcolor=\"#FFFFCA\" colspan=\"5\"><font size=\"10\"><b>合計：</b></font></td>
            <td align=\"right\" bgcolor=\"#FFFFCA\"><font size=\"10\"><b>$r2Money</b></font></td>
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
            <td><font size="12"><b>日期：$cDate</b></font></td>
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