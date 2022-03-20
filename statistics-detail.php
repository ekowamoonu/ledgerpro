<?php ob_start();

if(!isset($_COOKIE['bosslog'])){header("Location: index");}


include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');
include('classes'.DS.'logging_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();
$logger=new Log();
$expense_guy=new Expenses();

$log_error="<h4 style='background-color:#00B16A;'>Customer Order Details</h4>";

/*select all product categories*/
$categories=$query_guy->find_all("PRODUCT_CATEGORY");
$category_list="";




//get al order details
if(isset($_GET['order'])){

	$id=$form_man->cleanString($_GET['order']);
	$orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$id);
 
            
		            $order_id=$orders['ORDER_ID'];
		            $seller_id=$orders['SELLER_ID'];
		            $product_id=$orders['PRODUCT_ID'];
		            $driver_id=$orders['DR_ID'];
		            $customer_id=$orders['CUST_ID'];

		            /*actual order details*/
		            $customer_name=$orders['CUSTOMER_NAME'];
		            $customer_contact=$orders['CUSTOMER_CONTACT'];
		            $order_category=$orders['CA_ID'];
		            $ordered_quantity=$orders['ORDERED_QUANTITY'];
		            $order_date=date("D d M, Y",strtotime($orders['ORDER_DATE']));
                    $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID'];
                    $amount_paid=$orders['AMOUNT_PAID'];
                    $invoice_number=$orders['INVOICE_NUMBER'];
		      
		            
		            /*get the name of seller who processed this order*/
		            $seller_details=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$seller_id);
		            $seller_fname=ucfirst($seller_details['FIRSTNAME']);
		            $seller_lname=ucfirst($seller_details['LASTNAME']);

		            //get name of customer's driver
	 				$driver_finder=$query_guy->find_by_id("DRIVERS","DRIVER_ID",$driver_id);
	 				$driver_name=$driver_finder['DRIVER_NAME'];

	 				if($driver_name==""){$driver_name="No driver";}

		            /*find name of product*/
		            $product_details=$query_guy->find_by_id("PRODUCTS","PRODUCT_ID",$product_id);
                    $product_name=$product_details['PRODUCT_NAME'];
                    $product_type=$product_details['PRODUCT_TYPE'];
                    $product_code=$product_details['PRODUCT_CODE'];
                    $product_price=$product_details['PRODUCT_PRICE'];
     
}//end main if
else{ header("Location: accounts-order-review");}









?>
<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/accounts-order-review-detail.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Accounts -Order Details</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="index" class="navbar-brand">Ordered Item Details</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<li><a href="statistics"> <i class="fa fa-arrow-left"></i> Back To Orders</a></li>
        	<li><a href="logs"> <i class="fa fa-pencil"></i> Logs</a></li>
        	<li><a href="print?customer=<?php echo $customer_id; ?>"> <i class="fa fa-print"></i> Printable Invoice</a></li>
        	<li><a href="boss-expenses"> <i class="fa fa-file"></i> Expenses</a></li>
            <li><a href="#" title="Notifications" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Boss<span class="caret"></span></a>
               <ul class="dropdown-menu">
		            <li><a href="logout">Logout</a><li>
		          <!--   <li><a href="#">Wife</a><li>
		            <li><a href="#">Family</a><li>
		            <li><a href="#">Kids</a><li> -->
               </ul>
            </li>
        </ul>

     </div>

</nav><!--nav ends-->

<!--body-->
<div class="container-fluid">
<div class="row">
	<div class="col-md-3 list">
	<h4><i class='fa fa-exclamation-circle'></i> Why Am I Here?</h4>	
	  <div class="sidebox">
		<ul class="nav">
			<li><a href='#'><i class='fa fa-exclamation-circle'></i> To view the details of the order you selected.</a></li>
		</ul>
	  </div>
	</div>

	<!--products display-->
	<div class="col-md-9 details">
		<?php echo $log_error; ?>
        <div class="col-md-5">
			<div class="product-box">
				<img src="images/customer_order.png" class="img img-responsive"/>
				<p>Customer Name: <span><?php echo $customer_name; ?></span></p>
				<p>Customer Contact: <span><?php echo $customer_contact; ?></span></p>
				<p>Seller: <span><?php echo ucfirst($seller_fname)." ".ucfirst($seller_lname); ?></span></p>
				<p>Driver Name: <span><?php echo $driver_name; ?></span></p>
				<p>Product Code: <span><?php echo $product_code; ?></span></p>
				<p>Product Name: <span><?php echo $product_name; ?></span></p>
				<p>Product Type: <span><?php echo $product_type; ?></span></p>
				<p>Ordered Quantity: <span><?php echo $ordered_quantity; ?></span></p>
				<p>Order Date: <span><?php echo $order_date; ?></span></p>
				<p>Amount To Be Paid:GHS <span> <?php echo $amount_to_be_paid; ?></span></p>
				<p>Amount Paid:GHS <span> <?php echo $amount_paid; ?></span></p>
				<p>Invoice Number: <span> <?php echo $invoice_number; ?></span></p>
				
			</div>
		</div>

     
	

	</div>
</div>
</div>


 	


<?php include("inc/footer.php"); ?>
<script>
        new WOW().init();
</script>
</body>
</html>