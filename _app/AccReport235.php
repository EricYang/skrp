<?php

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $fileNameTmp = $t_data["fileNameTmp"];
    $defaultPdfPath = $t_data["defaultPdfPath"];

    require_once(dirname(__FILE__)."/../tcpdf/config/lang/eng.php");
    require_once(dirname(__FILE__)."/../tcpdf/tcpdf.php");

    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
    $pdf->write(10, "家長未繳費明細表");

    header('Content-Type:text/plain; charset=utf-8');
    
    $textConMulti = '';
    $psMoney = 0;
    
    $ts_sql = "SELECT a.sn AS accMIncomeStuSn, a.classLeavesSn, a.studentSn, CONCAT(c.theClassName, ' / ', b.classLeavesName) AS classInfoName, d.stuNo, CONCAT(d.cName, ' ', d.eName) AS studentName, CONCAT(a.cYear, ' / ', a.cMonth) AS cYearMonth
                FROM AccMIncomeStu a INNER JOIN
                ClassLeaves b ON a.classLeavesSn = b.sn INNER JOIN
                ClassInfo c ON b.classInfoSn = c.sn INNER JOIN
                Student d ON a.studentSn = d.sn
                WHERE d.enrollFlag = 1 AND d.deleFlag = 0
                AND a.paidFlag = 0
                ORDER BY  c.theClassCode, b.classLeavesCode, d.stuNo";
    $ta_row = Fun::readRows($ts_sql);
    if ($ta_row != null){
        
        for ($x = 0; $x < count($ta_row); $x++){
            $t_row = $ta_row[$x];            
            $pMoney = 0;
            $accDetailTmp = '';
            
            $ts_sql0 = "SELECT CONCAT(b.accIocName, '：' ,a.amounts) AS accDetail, a.amounts
                        FROM AccMIncome a INNER JOIN
                        AccIoc b ON a.accIocSn = b.sn
                        WHERE a.amounts > 0 AND (a.accMIncomeStuSn = ".$t_row["accMIncomeStuSn"].")";
            $ta_row0 = Fun::readRows($ts_sql0);
            if ($ta_row0 != null){
                for ($y = 0; $y < count($ta_row0); $y++){
                    $t_row0 = $ta_row0[$y];
                    $pMoney = $pMoney + $t_row0["amounts"];                    
                    $accDetailTmp = $accDetailTmp . $t_row0["accDetail"] . '。    ';                    
                }
            }
            
            $parentTmp = '';
            $ts_sql1 = "SELECT CONCAT(b.cName, '：' ,b.conTel) AS parentDetail
                        FROM PSRelation a, Parent b
                        WHERE a.parentSn = b.sn AND b.deleFlag = 0
                        AND a.studentSn = ".$t_row["studentSn"]."
                        ORDER BY b.gender";
            $ta_row1 = Fun::readRows($ts_sql1);
            if ($ta_row1 != null){
                for ($z = 0; $z < count($ta_row1); $z++){
                    $t_row1 = $ta_row1[$z];                   
                    $parentTmp = $parentTmp . $t_row1["parentDetail"] . '。    ';                    
                }
            }
            
            if ($pMoney==0){
                $pMoneyTmp = '';
            }else{
                $pMoneyTmp = number_format($pMoney, 2);
            }
            
            $textConMulti = $textConMulti."<tr align='left'>
                    <td align=\"left\">".$parentTmp."</td>
                    <td align=\"left\">".$t_row["classInfoName"]."</td>
                    <td align=\"left\">".$t_row["stuNo"]."</td>
                    <td align=\"left\">".$t_row["studentName"]."</td>
                    <td align=\"left\">".$t_row["cYearMonth"]."</td>
                    <td align=\"right\">".$pMoneyTmp."</td>
                    <td align=\"left\">".$accDetailTmp."</td></tr>";
           
           $psMoney = $psMoney + $pMoney;
        }
        
        $psMoney = number_format($psMoney, 2);
            
        $pdf->SetXY(14.0, 18.0);
        $pdf->SetFontSize(8);
        
$html = <<<EOD
      <div align="center">            
        <center>        
        <table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse" bordercolor="#B4B4B4" width="100%" bgcolor="#F5F5F5">
          <tr>
            <td align="center" bgcolor="#FFFFCA" width="15%"><font size="10"><b>家長/電話</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10"><b>班級</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="5%"><font size="10"><b>學號</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="15%"><font size="10"><b>姓名</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="5%"><font size="10"><b>月份</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10"><b>金額</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="40%"><font size="10"><b>明細</b></font></td>
          </tr>          
          $textConMulti
          <tr>
            <td align="right" bgcolor="#FFFFCA" colspan="5"><font size="10"><b>合計：</b></font></td>
            <td align="right" bgcolor="#FFFFCA"><font size="10"><b>$psMoney</b></font></td>
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
        
        echo "1" ;
    }else{    
        echo "0" ;        
    }
?>