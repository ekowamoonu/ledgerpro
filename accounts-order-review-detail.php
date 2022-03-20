<?php ob_start();

if(!isset($_COOKIE['acclog'])){header("Location: index");}


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

//pick first name of this user from database
$emp_id=$_COOKIE['acclog'];
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];
$lastname=$query['LASTNAME'];
$employee_name=ucfirst($firstname)." ".ucfirst($lastname);


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



/**********applying a discount********/

if(isset($_POST['discount_submit'])){
	if($form_man->emptyField($_POST['discount'])){
      $log_error="<h4 style='background-color:red'>Oops! No Discount Specified!</h4>";
	}
	else{
		  $discount=$form_man->cleanString($_POST['discount']);

		  //update amount to be paid
		  $update_query="UPDATE ORDERS SET AMOUNT_TO_BE_PAID=AMOUNT_TO_BE_PAID-".$discount;
		  $update_query.=" WHERE ORDER_ID=".$order_id;
		  $run_update=mysqli_query(DB_Connection::$connection,$update_query);

		  if($run_update){

		  	 $person_logging=$employee_name;
		  	 $logging_info=$form_man->cleanString($person_logging." applied a discount of GHS".$discount." to ".$customer_name."'s order for ".$product_type."(".$product_type.")");

             //log this
             $logger->log_this($order_id,$person_logging,$logging_info);

             $log_error="<h4 style='background-color:#00B16A;'>".ucfirst($customer_name)." Order Discount Proccessed Successfully! <i class='fa fa-check'></i></h4>";
             
		      
		     //immediately update display
			$orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
			 $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID']; 
		  }


	}//end nested else
}


/*processing damaged goods*/
if(isset($_POST['damaged_submit'])){
	if($form_man->emptyField($_POST['damaged'])){
      $log_error="<h4 style='background-color:red'>Oops! No Damaged Item Quantity Specified!</h4>";
	}
	else{
		  $damaged=$form_man->cleanString($_POST['damaged']);

		  //update ordered quanity
		  $update_query="UPDATE ORDERS SET ORDERED_QUANTITY=ORDERED_QUANTITY-".$damaged;
		  $update_query.=" WHERE ORDER_ID=".$order_id;
		  $run_update=mysqli_query(DB_Connection::$connection,$update_query);

		  if($run_update){

		  	 //take new ordered quantity and multiply by product price
		  	 $orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
			 $ordered_quantity=$orders['ORDERED_QUANTITY']; 
			 $new_amount_to_be_paid=$ordered_quantity*$product_price;

			 //update amount to be paid
			 $update_query="UPDATE ORDERS SET AMOUNT_TO_BE_PAID=".$new_amount_to_be_paid;
			 $update_query.=" WHERE ORDER_ID=".$order_id;
			 $run_update=mysqli_query(DB_Connection::$connection,$update_query);


		  	 $person_logging=$employee_name;
		  	 $logging_info=$form_man->cleanString($person_logging." recorded ".$damaged." damaged items to ".$customer_name."'s order for ".$product_type."(".$product_type.")");

             //log this
             $logger->log_this($order_id,$person_logging,$logging_info);

             $log_error="<h4 style='background-color:#00B16A;'>".ucfirst($customer_name)." Damaged goods recorded Successfully! <i class='fa fa-check'></i></h4>";
             
		      
		     //immediately update display
			 $orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
			 $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID']; 
			 $ordered_quantity=$orders['ORDERED_QUANTITY']; 
		  }


	}//end nested else
}//end damaged if



/*processing returned goods*/
if(isset($_POST['return_submit'])){
	if($form_man->emptyField($_POST['returned'])){
      $log_error="<h4 style='background-color:red'>Oops! No Returned Item Quantity Specified!</h4>";
	}
	else{
		  $returned=$form_man->cleanString($_POST['returned']);

		  //update ordered quanity
		  $update_query="UPDATE ORDERS SET ORDERED_QUANTITY=ORDERED_QUANTITY-".$returned;
		  $update_query.=" WHERE ORDER_ID=".$order_id;
		  $run_update=mysqli_query(DB_Connection::$connection,$update_query);

		  if($run_update){

		  	 //take new ordered quantity and multiply by product price
		  	 $orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
			 $ordered_quantity=$orders['ORDERED_QUANTITY']; 
			 $new_amount_to_be_paid=$ordered_quantity*$product_price;

			 //update amount to be paid
			 $update_query="UPDATE ORDERS SET AMOUNT_TO_BE_PAID=".$new_amount_to_be_paid;
			 $update_query.=" WHERE ORDER_ID=".$order_id;
			 $run_update=mysqli_query(DB_Connection::$connection,$update_query);


		  	 $person_logging=$employee_name;
		  	 $logging_info=$form_man->cleanString($person_logging." recorded ".$returned." returned items by ".$customer_name."'s order for ".$product_type."(".$product_type.")");

             //log this
             $logger->log_this($order_id,$person_logging,$logging_info);

             $log_error="<h4 style='background-color:#00B16A;'>".ucfirst($customer_name)." returned goods recorded Successfully! <i class='fa fa-check'></i></h4>";
             
		      
		     //immediately update display
			 $orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
			 $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID']; 
			 $ordered_quantity=$orders['ORDERED_QUANTITY']; 
		  }


	}//end nested else
}//end returned if


/*processing customer payment*/
if(isset($_POST['amount_paid_submit'])){
	if($form_man->emptyField($_POST['amount_paid'])){
      $log_error="<h4 style='background-color:red'>Oops! No Payment Amount Specified!</h4>";
	}
	else{
		  $amount_paid=$form_man->cleanString($_POST['amount_paid']);

		  //update ordered quanity
		  $update_query="UPDATE ORDERS SET AMOUNT_PAID=AMOUNT_PAID+".$amount_paid;
		  $update_query.=" WHERE ORDER_ID=".$order_id;
		  $run_update=mysqli_query(DB_Connection::$connection,$update_query);

		  if($run_update){

			 //update amount to be paid
			 $update_query="UPDATE ORDERS SET AMOUNT_TO_BE_PAID=AMOUNT_TO_BE_PAID-".$amount_paid;
			 $update_query.=" WHERE ORDER_ID=".$order_id;
			 $run_update=mysqli_query(DB_Connection::$connection,$update_query);


		  	 $person_logging=$employee_name;
		  	 $logging_info=$form_man->cleanString($person_logging." recorded GHS ".$amount_paid." payed by ".$customer_name."'s order for ".$product_type."(".$product_type.")");

             //log this
             $logger->log_this($order_id,$person_logging,$logging_info);

             $log_error="<h4 style='background-color:#00B16A;'>".ucfirst($customer_name)." Payment Processed Successfully! <i class='fa fa-check'></i></h4>";
             
		      
		     //immediately update display
			 $orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
			 $amount_paid=$orders['AMOUNT_PAID']; 
			 $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID']; 
		  }


	}//end nested else
}//end returned if


/*adding and invoice*/
if(isset($_POST['invoice_submit'])){
	if($form_man->emptyField($_POST['invoice'])){
      $log_error="<h4 style='background-color:red'>Oops! No Invoice Specified!</h4>";
	}
	else{
		  $invoice=$form_man->cleanString($_POST['invoice']);

		  //update ordered quanity
		  $update_query="UPDATE ORDERS SET INVOICE_NUMBER='{$invoice}'";
		  $update_query.=" WHERE ORDER_ID=".$order_id;
		  $run_update=mysqli_query(DB_Connection::$connection,$update_query);

		  if($run_update){


		  	 $person_logging=$employee_name;
		  	 $logging_info=$form_man->cleanString($person_logging." recorded an invoice: ".$invoice." for ".$customer_name."'s order for ".$product_type."(".$product_type.")");

             //log this
             $logger->log_this($order_id,$person_logging,$logging_info);

             $log_error="<h4 style='background-color:#00B16A;'>".ucfirst($customer_name)." Invoice added! <i class='fa fa-check'></i></h4>";
             
		      
		     //immediately update display
			 $orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
			 $invoice_number=$orders['INVOICE_NUMBER']; 
		  }


	}//end nested else
}//end returned if


//if payment button is clicked

/*adding and invoice*/
if(isset($_GET['paid'])){

		  //update ordered quanity
		  $update_query="UPDATE ORDERS SET PAYMENT_STATUS=1";
		  $update_query.=" WHERE ORDER_ID=".$order_id;
		  $run_update=mysqli_query(DB_Connection::$connection,$update_query);

		  if($run_update){


		  	 $person_logging=$employee_name;
		  	 $logging_info=$form_man->cleanString($person_logging." recorded a full payment for: ".$customer_name."'s order for ".$product_type."(".$product_type.")");

             //log this
             $logger->log_this($order_id,$person_logging,$logging_info);

             $log_error="<h4 style='background-color:#00B16A;'>".ucfirst($customer_name)." Payment Status Changed! <i class='fa fa-check'></i></h4>";
             
		      
		     //immediately update display
			 $orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
			 $invoice_number=$orders['INVOICE_NUMBER']; 
		  }

}//end returned if

/*adding expense*/
if(isset($_POST['expense_submit'])){
	if($form_man->emptyField($_POST['expense_purpose'])||$form_man->emptyField($_POST['expense_amount'])){
      $log_error="<h4 style='background-color:red'>Oops! Some Expense Fields Left Blank!</h4>";
	}
	else{
		  $expense_purpose=$form_man->cleanString($_POST['expense_purpose']);
		  $expense_amount=$form_man->cleanString($_POST['expense_amount']);
          $person_logging=$employee_name;

		  //record into expense table
          $expense_guy->add_to_expense($order_id,$expense_purpose,$expense_amount,$person_logging);


		  $logging_info=$form_man->cleanString($person_logging." recorded an expense amount of: ".$expense_amount." for ".$expense_purpose." on ".$customer_name."'s order for ".$product_type."(".$product_type.")");
  
         //log this
          $logger->log_this($order_id,$person_logging,$logging_info);

          $log_error="<h4 style='background-color:#00B16A;'>".ucfirst($customer_name)." Expense Recorded! <i class='fa fa-check'></i></h4>";
             
		      
	


	}//end nested else
}//end returned if






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
        	<li><a href="accounts-order-review"> <i class="fa fa-arrow-left"></i> Back To Orders</a></li>
        	<li><a href="print?customer=<?php echo $customer_id; ?>"> <i class="fa fa-print"></i> Printable Invoice</a></li>
        	<li><a href="expenses"> <i class="fa fa-file"></i> Expenses</a></li>
            <li><a href="#" title="Notifications" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $firstname; ?><span class="caret"></span></a>
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
			<li><a href='#'><i class='fa fa-exclamation-circle'></i> To process some deductions on this order based on some constraints.
			 Deductions can be made based on damaged goods, returned goods or discounts. All activities carried out here are also marked as expenses.
			</a></li>
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
				<p><a href="accounts-order-review-detail?order=<?php echo $order_id; ?>&&paid=<?php echo $order_id; ?>" class="btn btn-info btn-block"><i class="fa fa-check"></i> Mark This Order As Payment Fully Made</a></p>
			</div>
		</div>

     
		<div class="col-md-7 order-form">
			<div class="container-fluid">
				 <div class="row">
					<form action="accounts-order-review-detail?order=<?php echo $order_id; ?>" role="form" method="post">
						  <div class="form-group">
							  <label class="col-lg-3 control-label">Enter Discount Value</label>
							  <div class="col-lg-9">
								<input type="text" name="discount" class="form-control"/>
							  </div>
						  </div>

						  <div class="form-group">
							  <div class="col-lg-12">
								<input type="submit" class="btn btn-info pull-right" name="discount_submit" value="Apply Discount">
							  </div>
						  </div>
		                   
						</form>
				 </div><!--end nested row-->

				  <div class="row">
					<form action="accounts-order-review-detail?order=<?php echo $order_id; ?>" role="form" method="post">
						  <div class="form-group">
							  <label class="col-lg-3 control-label">Enter Quantity Of Damaged Goods</label>
							  <div class="col-lg-9">
								<input type="number" name="damaged" class="form-control"/>
							  </div>
						  </div>

						  <div class="form-group">
							  <div class="col-lg-12">
								<input type="submit" class="btn btn-info pull-right" name="damaged_submit" value="Add Damaged Goods">
							  </div>
						  </div>
		                   
						</form>
				 </div><!--end nested row-->

				  <div class="row">
					<form action="accounts-order-review-detail?order=<?php echo $order_id; ?>" role="form" method="post">
						  <div class="form-group">
							  <label class="col-lg-3 control-label">Enter Quantity Of Returned Goods</label>
							  <div class="col-lg-9">
								<input type="number" name="returned" class="form-control"/>
							  </div>
						  </div>

						  <div class="form-group">
							  <div class="col-lg-12">
								<input type="submit" class="btn btn-info pull-right" name="return_submit" value="Process Returned Goods">
							  </div>
						  </div>
		                   
						</form>
				 </div><!--end nested row-->

				 <div class="row">
					<form action="accounts-order-review-detail?order=<?php echo $order_id; ?>" role="form" method="post">
						  <div class="form-group">
							  <label class="col-lg-3 control-label">Enter Amount Paid By Customer</label>
							  <div class="col-lg-9">
								<input type="text" name="amount_paid" class="form-control"/>
							  </div>
						  </div>

						  <div class="form-group">
							  <div class="col-lg-12">
								<input type="submit" class="btn btn-info pull-right" name="amount_paid_submit" value="Process Payment">
							  </div>
						  </div>
		                   
						</form>
				 </div><!--end nested row-->

				 <div class="row">
					<form action="accounts-order-review-detail?order=<?php echo $order_id; ?>" role="form" method="post">
						  <div class="form-group">
							  <label class="col-lg-3 control-label">Add Invoice Number</label>
							  <div class="col-lg-9">
								<input type="text" name="invoice" class="form-control"/>
							  </div>
						  </div>

						  <div class="form-group">
							  <div class="col-lg-12">
								<input type="submit" class="btn btn-info pull-right" name="invoice_submit" value="Add Invoice Number">
							  </div>
						  </div>
		                   
						</form>
				 </div><!--end nested row-->

				 <div class="row">
					<form action="accounts-order-review-detail?order=<?php echo $order_id; ?>" role="form" method="post">
						  <div class="form-group">
							  <label class="col-lg-3 control-label">Add Expense Purpose</label>
							  <div class="col-lg-9">
								<input type="text" name="expense_purpose" class="form-control"/>
							  </div>
						  </div>

						  <div class="form-group">
							  <label class="col-lg-3 control-label">Add Expense Amount</label>
							  <div class="col-lg-9">
								<input type="text" name="expense_amount" class="form-control"/>
							  </div>
						  </div>

						  <div class="form-group">
							  <div class="col-lg-12">
								<input type="submit" class="btn btn-info pull-right" name="expense_submit" value="Record Expense">
							  </div>
						  </div>
		                   
						</form>
				 </div><!--end nested row-->
			</div><!--end container fluid-->
			
	
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