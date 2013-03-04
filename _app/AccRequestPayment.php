<?php
    require_once(dirname(__FILE__) . "/Fun.php");
    
    session_start();
    
    $t_data = Fun::decode();    
    $t_db = new DB();
    
    $sn = $t_data["sn"];
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
    //$pdf->SetFont('cid0jp', '', '18');

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
    $pdf->write(10, "請款單");
    
    header('Content-Type:text/plain; charset=utf-8');

    $ts_sql1 = "SELECT a.serialNo, CONCAT(b.cName, ' ', b.eName) AS staffName, a.cDate, a.cAmounts, a.eDate, a.funcItemSn,
                CONCAT(e.accMainCode, d.accTitleCode, c.accIocCode) AS accIocCode,
                CONCAT(e.accMainName, ' - ', d.accTitleName, ' - ', c.accIocName) AS accIocName,
                a.pExcerpt, a.cDesc, f.unitName, f.contactName, f.conTel
                FROM AccRequestPayment a INNER JOIN Staff b ON a.staffSn = b.sn AND a.accType = 1
                INNER JOIN AccIoc c ON a.accIocSn = c.sn
                INNER JOIN AccTitle d ON c.accTitleSn = d.sn
                INNER JOIN AccMain e ON d.accMainSn = e.sn 
                LEFT OUTER JOIN PublicDept f ON a.publicDeptSn = f.sn
                WHERE a.sn = ".$sn;
    $ta_row1 = Fun::readRow($ts_sql1);
    if ($ta_row1 != null){
        
        $serialNo = $ta_row1["serialNo"];
        $staffName = $ta_row1["staffName"];
        $cDate = $ta_row1["cDate"];
        
        $cAmounts = number_format($ta_row1["cAmounts"], 2);

        $eDate = $ta_row1["eDate"];
        
        $funcItemSn = $ta_row1["funcItemSn"];
        
        $accIocCode = $ta_row1["accIocCode"];
        $accIocName = $ta_row1["accIocName"];
        $pExcerpt = $ta_row1["pExcerpt"];
        $cDesc = $ta_row1["cDesc"];
        
        $unitName = $ta_row1["unitName"];
        $contactName = $ta_row1["contactName"];
        $conTel = $ta_row1["conTel"];
        
        $pdf->SetXY(140.0, 2.0);
        $pdf->write1DBarcode($serialNo, 'C39', '', '', '', 10, 0.2, $style, 'N');
        
        $pdf->SetXY(14.0, 18.0);
        $pdf->SetFontSize(6);
        
        $ts_sql2 = "SELECT itemName AS label
                    FROM FuncItem
                    WHERE sysType = 21 AND deleFlag = 0 AND sn = ".$funcItemSn;
        $ta_row2 = Fun::readRow($ts_sql2);
        if ($ta_row2 != null){
            $funcItemName = $ta_row2["label"];
        }else{
            $funcItemName = "";
        }
     
$htmlM = <<<EOD
    <div align="center">        
        <table width="100%"  border="0" cellspacing="3" cellpadding="3">
          <tr>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>流水編號：</b></font></div></td>
            <td colspan="5"><div align="left"><font size="12">$serialNo</font></div></td>
            </tr>
          <tr>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>請款人：</b></font></div></td>
            <td colspan="5"><div align="left"><font size="12">$staffName</font></div></td>
            </tr>
          <tr>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>請款日期：</b></font></div></td>
            <td colspan="2"><div align="left"><font size="12">$cDate</font></div></td>
            <td bgcolor="#F5F5F5" width="120"><div align="right"><font size="12"><b>預計付款日期：</b></font></div></td>
            <td colspan="2"><div align="left"><font size="12">$eDate</font></div></td>
            </tr>
          <tr>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>請款金額：</b></font></div></td>
            <td colspan="2"><div align="left"><font size="12">$cAmounts</font></div></td>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>付款方式：</b></font></div></td>
            <td colspan="2"><div align="left"><font size="12">$funcItemName</font></div></td>
            </tr>
          <tr>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>支出項目：</b></font></div></td>
            <td colspan="5"><div align="left"><font size="12">$accIocCode $accIocName</font></div></td>
            </tr>
          <tr>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>支出摘要：</b></font></div></td>
            <td colspan="5"><div align="left"><font size="12">$pExcerpt</font></div></td>
            </tr>
          <tr>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>請款說明：</b></font></div></td>
            <td colspan="5"><div align="left"><font size="12">$cDesc</font></div></td>
            </tr>
          <tr>
            <td bgcolor="#F5F5F5"><div align="right"><font size="12"><b>廠商名稱：</b></font></div></td>
            <td><div align="left"><font size="12">$unitName</font></div></td>
            <td bgcolor="#F5F5F5"><font size="12"><b>連絡人：</b></font></td>
            <td><div align="left"><font size="12">$contactName</font></div></td>
            <td bgcolor="#F5F5F5"><font size="12"><b>電話：</b></font></td>
            <td><div align="left"><font size="12">$conTel</font></div></td>
          </tr>
        </table>
      </div>
EOD;

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $htmlM, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

$htmlD = <<<EOD
<div align="center">
  <table width="100%"  border="0">
    <tr>
      <td><div align="center">
        <table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse" bordercolor="#B4B4B4" width="100%" bgcolor="#F5F5F5">
          <tr>
            <td><div align="center"><font size="12"><b>董事長</b></font></div></td>
            <td><div align="center"><font size="12"><b>區長</b></font></div></td>
            <td><div align="center"><font size="12"><b>總會計</b></font></div></td>
            <td><div align="center"><font size="12"><b>園長</b></font></div></td>
            <td><div align="center"><font size="12"><b>會計</b></font></div></td>
            <td><div align="center"><font size="12"><b>請款人</b></font></div></td>
          </tr>
          <tr>
            <td><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td>
            <td><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td>
            <td><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td>
            <td><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td>
            <td><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td>
            <td><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td>
          </tr>
        </table>
      </div></td>
    </tr>
  </table>
EOD;
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell($w=0, $h=0, $x=10, $y=245, $htmlD, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);        
        //Close and output PDF document    
        $pdf->Output($defaultPdfPath.$fileNameTmp, 'F');     
        
        echo "1" ;
    }else{
        echo "0" ;
    }
?>