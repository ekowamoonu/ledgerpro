<?php ob_start();


include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();


/**************************************get all details to be used for printing*************************************************/
$order_rows="";
if(isset($_GET['customer'])){
    

	$customer_id=$form_man->cleanString($_GET['customer']);

	//get name of customer
	$customers=$query_guy->find_by_id("CUSTOMERS","CUSTOMER_ID",$customer_id);
    $customer_name=$customers['CUSTOMER_NAME'];

    $order_query="SELECT * FROM ORDERS WHERE CUST_ID=".$customer_id." AND INVOICE_NUMBER!='no invoice' ORDER BY ORDER_DATE DESC";
    $order_query_process=mysqli_query(DB_Connection::$connection,$order_query);
    $total=0;

    if(mysqli_num_rows($order_query_process)==0){$order_rows="No invoices for this customer";$total=0;}
    else{
                $i=1;
    	  while($orders=mysqli_fetch_assoc($order_query_process)){
                  
		            $order_id=$orders['ORDER_ID'];
		            $order_date=date("D d M, Y",strtotime($orders['ORDER_DATE']));
		            $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID'];
		            $total+=$orders['AMOUNT_TO_BE_PAID'];
		            $invoice=$orders['INVOICE_NUMBER'];

		            $order_rows.="<tr class='clickable-row' data-href='accounts-order-review-detail?order=".$order_id."'>
										<td>".$i."</td>
										<td>".$order_date."</td>
										<td>".$amount_to_be_paid."</td>
										<td>". $invoice."</td>								
									</tr>";
							$i++;
	              }//end while


    }
    


}

	 

	 

?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/print.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Print Preview</title>

</head>


<body>

<!--body-->
<div class="container-fluid" style="margin-top:4%;">
<div class="row">
	<div class="col-md-6 col-md-offset-3 col-lg-offset-3 details">
		<h4 class="text-center">SAKIS BAKERY</h4>	
		<h5 class="text-center"><?php echo strtoupper($customer_name); ?> - BILLING FORM</h5>	

		<table class="table table-striped table-bordered">
			<thead>
			<tr style="color:#1E8BC3;">
				<th>Order #</th>
				<th>DATE</th>
				<th>AMOUNT TO BE PAID</th>
				<th>INVOICE</th>
			</tr>
			</thead>

			<tbody>
			
			<?php echo $order_rows; ?>
			<tr>
				<td><b>TOTAL (GHS):</b></td>
				<td></td>
				<td><b><?php echo number_format($total,2); ?></b></td>
			</tr>
			</tbody>
		</table>
	

	</div>
</div>
</div>



<?php include("inc/footer.php"); ?>
</body>

 <a href="javascript:window.print()" class="btn btn-danger" style="position:absolute; bottom:0; right:0;"><i class="fa fa-print"></i> Print this page</a>	
</html>