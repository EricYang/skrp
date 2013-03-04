<?php

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $classLeavesSn = $t_data["classLeavesSn"];
    $cYear = $t_data["cYear"];
    $cMonth = $t_data["cMonth"];
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
    $pdf->write(10, "班級每月收入明細表");

    header('Content-Type:text/plain; charset=utf-8');
    
    $textConMulti = '';
    $psMoney = 0;
    $ts_sql = "SELECT b.studentSn, c.stuNo, CONCAT(c.cName, ' ', c.eName) AS studentName
                FROM StartClass a INNER JOIN
                StudentStatus b ON a.sn = b.startClassSn INNER JOIN
                Student c ON b.studentSn = c.sn
                WHERE (a.academicYear = ".$_SESSION["academicYear"].") AND (a.semester = ".$_SESSION["semester"].") 
                AND (a.classLeavesSn = ".$classLeavesSn.")
                AND c.enrollFlag = 1 AND c.deleFlag = 0
                ORDER BY c.stuNo";
    $ta_row = Fun::readRows($ts_sql);
    if ($ta_row != null){
        
        for ($x = 0; $x < count($ta_row); $x++){
            $t_row = $ta_row[$x];            
            $pMoney = 0;
            $accDetailTmp = '';
            
            $ts_sql0 = "SELECT c.sn AS accMIncomeStuSn, b.amounts
                        FROM AccMonthPaidDT a INNER JOIN
                        AccMonthPaid b ON a.sn = b.accMonthPaidDTSn INNER JOIN
                        AccMIncomeStu c ON b.accMIncomeStuSn = c.sn
                        WHERE (c.classLeavesSn = ".$classLeavesSn.") AND (c.studentSn = ".$t_row["studentSn"].")
                        AND EXTRACT(YEAR_MONTH FROM a.cDate) = '".$cYear.$cMonth."'";
            /*
            $ts_sql0 = "SELECT b.accMIncomeStuSn, b.amounts
                        FROM AccMIncomeStu a INNER JOIN
                        AccMonthPaid b ON a.sn = b.accMIncomeStuSn
                        WHERE (a.classLeavesSn = ".$classLeavesSn.") AND (a.studentSn = ".$t_row["studentSn"].")
                        AND (a.cYear = ".$cYear.") AND (a.cMonth = ".$cMonth.")";
             */
            $ta_row0 = Fun::readRows($ts_sql0);
            if ($ta_row0 != null){
                for ($y = 0; $y < count($ta_row0); $y++){
                    $t_row0 = $ta_row0[$y];
                    $pMoney = $pMoney + $t_row0["amounts"];
                    
                    $ts_sql1 = "SELECT CONCAT(b.accIocName, '：' ,a.amounts) AS accDetail
                                FROM AccMIncome a INNER JOIN
                                AccIoc b ON a.accIocSn = b.sn
                                WHERE a.amounts > 0 AND (a.accMIncomeStuSn = ".$t_row0["accMIncomeStuSn"].")";
                    $ta_row1 = Fun::readRows($ts_sql1);
                    if ($ta_row1 != null){
                        for ($z = 0; $z < count($ta_row1); $z++){
                            $t_row1 = $ta_row1[$z];
                            $accDetailTmp = $accDetailTmp . $t_row1["accDetail"] . '。    ';
                        }
                    }                    
                }
            }
            
            if ($pMoney==0){
                $pMoneyTmp = '';
            }else{
                $pMoneyTmp = number_format($pMoney, 2);
            }
            
            $textConMulti = $textConMulti."<tr align='left'>
                    <td align=\"left\">".$t_row["stuNo"]."</td>
                    <td align=\"left\">".$t_row["studentName"]."</td>
                    <td align=\"right\">".$pMoneyTmp."</td>
                    <td align=\"left\">".$accDetailTmp."</td></tr>";
         
            $psMoney = $psMoney + $pMoney;
        }        
        
        $psMoney = number_format($psMoney, 2);
        
        $ts_sq2 = "SELECT CONCAT(b.theClassName, ' / ', a.classLeavesName) AS classInfoName
                    FROM ClassLeaves a, ClassInfo b
                    WHERE a.classInfoSn = b.sn
                    AND a.sn = ".$classLeavesSn;
        $ta_row2 = Fun::readRow($ts_sq2);
        if ($ta_row2 != null){
            $classInfoName = $ta_row2["classInfoName"];
        }
            
        $pdf->SetXY(14.0, 18.0);
        $pdf->SetFontSize(8);
        
$html = <<<EOD
    <table width="100%"  border="0">
        <tr>
            <td><font size="12"><b>班別(級)：$classInfoName</b></font></td>
        </tr>
        <tr>
            <td><font size="12"><b>年 /月份：$cYear /$cMonth</b></font></td>
        </tr>
    </table>
      <div align="center">            
        <center>        
        <table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse" bordercolor="#B4B4B4" width="100%" bgcolor="#F5F5F5">
          <tr>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10"><b>學號</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="20%"><font size="10"><b>姓名</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="10%"><font size="10"><b>金額</b></font></td>
            <td align="center" bgcolor="#FFFFCA" width="60%"><font size="10"><b>明細</b></font></td>
          </tr>          
          $textConMulti
          <tr>
            <td align="right" bgcolor="#FFFFCA" colspan="2"><font size="10"><b>合計：</b></font></td>
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