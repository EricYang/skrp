<?php

    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $sn = $t_data["sn"];
    //$sn = 2;
    $fileNameTmp = $t_data["fileNameTmp"];
    $defaultPdfPath = $t_data["defaultPdfPath"];

    require_once(dirname(__FILE__)."/../tcpdf/config/lang/eng.php");
    require_once(dirname(__FILE__)."/../tcpdf/tcpdf.php");

    //$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
    $pdf->write(10, "幼兒收費表套印");

    header('Content-Type:text/plain; charset=utf-8');
    
    $THESCHOOLNAME = $_SESSION["schoolName"];
    $cDate = date('Y-m-d');
    $textConMulti = '';

    $ts_sql = "SELECT a.sn AS accMIncomeStuSn, a.serialNo, CONCAT(a.cYear, ' / ', a.cMonth) AS cYearMonth, CONCAT(d.theClassName, ' / ', c.classLeavesName) AS classInfoName, CONCAT(b.cName, ' ', b.eName) AS studentName
                FROM AccMIncomeStu a INNER JOIN
                Student b ON a.studentSn = b.sn INNER JOIN
                ClassLeaves c ON a.classLeavesSn = c.sn INNER JOIN
                ClassInfo d ON c.classInfoSn = d.sn
                WHERE a.sn = ".$sn;
    $ta_row = Fun::readRow($ts_sql);
    if ($ta_row != null){
        $serialNo = $ta_row["serialNo"];
        $cYearMonth = $ta_row["cYearMonth"];
        $classInfoName = $ta_row["classInfoName"];
        $studentName = $ta_row["studentName"];

        $pdf->SetXY(140.0, 2.0);
        $pdf->write1DBarcode($serialNo, 'C39', '', '', '', 10, 0.2, $style, 'N');
        
        $pdf->SetXY(14.0, 18.0);
        $pdf->SetFontSize(8);
    
        $ts_sql0 = "SELECT b.accIocName, a.amounts
                    FROM AccMIncome a INNER JOIN
                    AccIoc b ON a.accIocSn = b.sn INNER JOIN
                    AccTitle c ON b.accTitleSn = c.sn
                    WHERE a.amounts > 0 AND a.accMIncomeStuSn = ".$ta_row["accMIncomeStuSn"];
        $ta_row0 = Fun::readRows($ts_sql0);
        if ($ta_row0 != null){
            
            $pMoney = 0;
            
            $hCs = "<tr>";
            //$hCm = "</tr><tr>";
            $hCm = "</tr><tr bgcolor=\"#FFFFCA\">
                <td><font size=\"10\"><b>項目</b></font></td>
                <td><font size=\"10\"><b>金額</b></font></td>
                <td><font size=\"10\"><b>項目</b></font></td>
                <td><font size=\"10\"><b>金額</b></font></td>
                <td><font size=\"10\"><b>項目</b></font></td>
                <td><font size=\"10\"><b>金額</b></font></td></tr><tr>";
            $hCe = "</tr>";
            $totalVal = count($ta_row0);
            
            for ($p = 0; $p < count($ta_row0); $p++){
                $t_row0 = $ta_row0[$p];            
                
                $pMoney = $pMoney + $t_row0["amounts"];
                
                if ($p>0 && $p%3==0){
                    $textConMulti = $textConMulti.$hCm;
                }
                
                $textConMulti = $textConMulti."<td>".$t_row0["accIocName"]."</td>
                    <td align=\"right\">".number_format($t_row0["amounts"], 2)."</td>";
            }
            
            if ($totalVal%3==1){
                $textConMulti = $textConMulti ."<td></td><td></td><td></td><td></td>";
            }
            if ($totalVal%3==2){
                $textConMulti = $textConMulti ."<td></td><td></td>";
            }
                
            $textConMulti = $hCs . $textConMulti . $hCe;
            
            $pMoney = number_format($pMoney, 2);
     
$html = <<<EOD
<div align="center">
  <center>
  <table border="0" width="100%" cellspacing="0" cellpadding="3">
    <tr>
      <td width="100%" align="center"><font size="16">$THESCHOOLNAME  【繳費通知】</font></td> 
    </tr> 
  </center> 
  <tr> 
    <td width="100%" align="right"><font size="12"><b>No</b>：$serialNo</font></td>
  </tr>
  <center>
  <tr>
    <td width="100%" align="center">
      <div align="center">
        <center>
        <table border="0" width="100%" cellspacing="0" cellpadding="3">
          <tr>
            <td width="10%" align="right"><font size="12"><b>月份</b>：</font></td>
            <td width="10%" align="left"><font size="12">$cYearMonth</font></td>
            <td width="10%" align="right"><font size="12"><b>班別</b>：</font></td>
            <td width="30%" align="left"><font size="12">$classInfoName</font></td>
            <td width="10%" align="right"><font size="12"><b>姓名</b>：</font></td>
            <td width="30%" align="left"><font size="12">$studentName</font></td>
          </tr>
        </table>
        </center>
      </div>
    </td>
  </tr>
  <tr>
    <td width="100%" align="center">
      <div align="center">
        <center>
        <table border="1" width="100%" cellspacing="0" cellpadding="3" bordercolor="#000000">
        <tr bgcolor="#FFFFCA">
            <td width="16%" align="center"><font size="10"><b>項目</b></font></td>
            <td width="16%" align="center"><font size="10"><b>金額</b></font></td>
            <td width="17%" align="center"><font size="10"><b>項目</b></font></td>
            <td width="17%" align="center"><font size="10"><b>金額</b></font></td>
            <td width="17%" align="center"><font size="10"><b>項目</b></font></td>
            <td width="17%" align="center"><font size="10"><b>金額</b></font></td>
        </tr>
        $textConMulti
      </table>
    </div>
  </td>
  </tr>
  <tr>
	<td width="100%" align="right"><font size="12"><b>合計</b>：$pMoney</font></td>
  </tr>
  <center>
  <tr>
    <td width="100%" align="center">
      <div align="center">
        <center>
        <table border="0" width="100%" cellspacing="0" cellpadding="3">
          <tr>
            <td width="16%" align="right"><font size="12"><b>列印日期</b>：</font></td>
            <td width="16%" align="left"><font size="12">$cDate</font></td>
            <td width="17%"><font size="12"><b>會計部</b>：</font></td>
            <td width="17%"></td>
            <td width="17%"><font size="12"><b>出納組</b>：</font></td>
            <td width="17%"></td>
          </tr>
        </table>
        </center>
      </div>
    </td>
  </tr>
  </center>
  <tr>
    <td width="100%" align="center"><font size="10">第一聯：學生收執   第二聯：出納   第三聯：會計</font></td> 
  </tr> 
  </table> 
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