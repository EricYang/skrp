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
    $pdf->write(10, "每日收入明細表");

    header('Content-Type:text/plain; charset=utf-8');
    
    $textConMulti = '';

    $ts_sql = "SELECT sn, serialNo, cDate
                FROM AccMonthPaidDT
                WHERE (cDate = '".$cDate."')";
    $ta_row = Fun::readRow($ts_sql);
    if ($ta_row != null){
        $accMonthPaidDTsn = $ta_row["sn"];
        $serialNo = $ta_row["serialNo"];
        $cDate = $ta_row["cDate"];

        $pdf->SetXY(240.0, 2.0);
        $pdf->write1DBarcode($serialNo, 'C39', '', '', '', 10, 0.2, $style, 'N');
        
        $pdf->SetXY(14.0, 18.0);
        $pdf->SetFontSize(8);
    
        $ts_sql0 = "SELECT a.accMIncomeStuSn, b.serialNo, CONCAT(e.theClassName, ' / ', d.classLeavesName) AS classInfoName, c.cName AS studentName, CONCAT(b.cYear, ' / ', b.cMonth) AS cYearMonth, a.amounts
                    FROM AccMonthPaid a INNER JOIN
                    AccMIncomeStu b ON a.accMIncomeStuSn = b.sn INNER JOIN
                    Student c ON b.studentSn = c.sn INNER JOIN
                    ClassLeaves d ON b.classLeavesSn = d.sn INNER JOIN
                    ClassInfo e ON d.classInfoSn = e.sn
                    WHERE (a.accMonthPaidDTSn = ".$accMonthPaidDTsn.")
                    ORDER BY  b.serialNo";    
        $ta_row0 = Fun::readRows($ts_sql0);
        if ($ta_row0 != null){
            
            $pMoney = 0;
            
            for ($p = 0; $p < count($ta_row0); $p++){
                $t_row0 = $ta_row0[$p];
            
                $accDetailTmp = "";
                
                $pMoney = $pMoney + $t_row0["amounts"];
            
                $ts_sql1 = "SELECT CONCAT(b.accIocName, '：' ,a.amounts) AS accDetail
                            FROM AccMIncome a INNER JOIN
                            AccIoc b ON a.accIocSn = b.sn
                            WHERE a.amounts > 0 AND (a.accMIncomeStuSn = ".$t_row0["accMIncomeStuSn"].")";
                $ta_row1 = Fun::readRows($ts_sql1);
                if ($ta_row1 != null){
                    for ($r = 0; $r < count($ta_row1); $r++){
                        $t_row1 = $ta_row1[$r];
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
     
$html = <<<EOD
    <table width="100%"  border="0">
        <tr>
            <td><font size="12"><b>日期：$cDate</b></font></td>
        </tr>
    </table>
      <div align="center">            
        <center>        
        <table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse" bordercolor="#B4B4B4" width="100%" bgcolor="#F5F5F5">
          <tr>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10"><b>收據號碼</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10"><b>班級</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="20%"><font size="10"><b>姓名</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="5%"><font size="10"><b>月份</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10"><b>金額</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="45%"><font size="10"><b>明細</b></font></td>
          </tr>          
          $textConMulti
          <tr>
            <td align="right" bgcolor="#FFFFCA" colspan="4"><font size="10"><b>合計：</b></font></td>
            <td align="right" bgcolor="#FFFFCA"><font size="10"><b>$pMoney</b></font></td>
            <td align="right" bgcolor="#FFFFCA"></td>
          </tr>
        </table>
        </center>
      </div>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

            //Close and output PDF document    
            $pdf->Output($defaultPdfPath.$fileNameTmp, 'F');
        }
        echo "1" ;
    }else{    
        echo "0" ;
    }
?>