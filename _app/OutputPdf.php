<?php
require_once(dirname(__FILE__)."/../tcpdf/config/lang/eng.php");
require_once(dirname(__FILE__)."/../tcpdf/tcpdf.php");

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
$pdf->SetFont('cid0jp','',20); 
$pdf->setPrintHeader(false); 
$pdf->setPrintFooter(false); 
$pdf->AddPage(); 
$pdf->write(10, "靜, 慧, 勇, 覺");
$pdf->Output('example.pdf', 'I');
?>