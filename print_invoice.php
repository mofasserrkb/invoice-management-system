<?php
session_start();
include 'Invoice.php';
$invoice = new Invoice();
$invoice->checkLoggedIn();
if(!empty($_GET['invoice_id']) && $_GET['invoice_id']) {
	echo $_GET['invoice_id'];
	$invoiceValues = $invoice->getInvoice($_GET['invoice_id']);		
	$invoiceItems = $invoice->getInvoiceItems($_GET['invoice_id']);		
}
$invoiceDate = date("d/M/Y, H:i:s", strtotime($invoiceValues['order_date']));
$output = '';
$output .= '<table width="100%"  cellpadding="5" cellspacing="0">
    <tr>
    <td colspan="2" align="left"> <img src="images/logof.png" width="250" height="50" alt="">          </td>
	</tr>
	<tr>
	<td colspan="2" align="right" style="font-size:18px"><b>Source Code Ltd</b> <br> Apt# 5A,70/B,Lake Circus,Kalabagan,Dhaka<br>+8801321127761<br>+8801321127767<br>www.sourcecodeltd.com.bd</td>
	</tr>
	<tr>
	<td colspan="2">
	<table width="100%" cellpadding="5">
	<tr>
	<td width="65%">
	To,<br />
	<b>RECEIVER (BILL TO)</b><br />
	Name : '.$invoiceValues['order_receiver_name'].'<br /> 
	Billing Address : '.$invoiceValues['order_receiver_address'].'<br />
	</td>
	<td width="35%">         
	Invoice No. : '.$invoiceValues['order_id'].'<br />
	Invoice Date : '.$invoiceDate.'<br />
	</td>
	</tr>
	</table>
	<hr>
	
	<table width="100%" style="background-color:white;" cellpadding="5" cellspacing="0" >
	<tr>
	<th align="left" bgcolor="#32c6f4">Sr No.</th>
	<th align="left" bgcolor="#32c6f4">Item Code</th>
	<th align="left" bgcolor="#32c6f4">Item Name</th>
	<th align="left" bgcolor="#32c6f4">Quantity</th>
	<th align="left" bgcolor="#32c6f4">Price</th>
	<th align="left" bgcolor="#32c6f4">Actual Amt.</th> 
	</tr>';
$count = 0;   
foreach($invoiceItems as $invoiceItem){
	$count++;
	$output .= '
	<tr>
	<td align="left">'.$count.'</td>
	<td align="left">'.$invoiceItem["item_code"].'</td>
	<td align="left">'.$invoiceItem["item_name"].'</td>
	<td align="left">'.$invoiceItem["order_item_quantity"].'</td>
	<td align="left">'.$invoiceItem["order_item_price"].'</td>
	<td align="left">'.$invoiceItem["order_item_final_amount"].'</td>   
	</tr>';
}
$output .= '
   
	<tr>
	<td align="right" colspan="5" ><b>Sub Total</b></td>
	<td align="left" bgcolor="#769998"><b>'.$invoiceValues['order_total_before_tax'].'</b></td>
	</tr>
	<tr>
	<td align="right"  colspan="5"><b>Tax Rate :</b></td>
	<td align="left"  bgcolor="#54bfbc">'.$invoiceValues['order_tax_per'].'</td>
	</tr>
	<tr>
	<td align="right" colspan="5">Tax Amount: </td>
	<td align="left" bgcolor="#769998">'.$invoiceValues['order_total_tax'].'</td>
	</tr>
	<tr>
	<td align="right" colspan="5">Total: </td>
	<td align="left"bgcolor="#54bfbc">'.$invoiceValues['order_total_after_tax'].'</td>
	</tr>
	<tr>
	<td align="right" colspan="5">Amount Paid:</td>
	<td align="left"bgcolor="#769998">'.$invoiceValues['order_amount_paid'].'</td>
	</tr>
	<tr>
	<td align="right" colspan="5"><b>Amount Due:</b></td>
	<td align="left"bgcolor="#54bfbc">'.$invoiceValues['order_total_amount_due'].'</td>
	</tr>';
$output .= '
	</table>	
	</td>
	</tr>
	</table>
 <div style="padding:10px;">	<b>Thank you!</b> </div>
	<div>Source Code Ltd</div>';
// create pdf of invoice	
$invoiceFileName = 'Invoice-'.$invoiceValues['order_id'].'.pdf';
require_once 'dompdf/src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
$dompdf = new Dompdf();
$dompdf->loadHtml(html_entity_decode($output));
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($invoiceFileName, array("Attachment" => false));
?>   
   