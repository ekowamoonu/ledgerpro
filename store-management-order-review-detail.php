<?php ob_start();

if(!isset($_COOKIE['storelog'])){header("Location: index");}


include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();

$log_error="<h4 style='background-color:#00B16A;'>Customer Order Details</h4>";

/*select all product categories*/
$categories=$query_guy->find_all("PRODUCT_CATEGORY");
$category_list="";

//pick first name of this user from database
$emp_id=$_COOKIE['storelog'];
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

		            /*actual order details*/
		            $customer_name=$orders['CUSTOMER_NAME'];
		            $customer_contact=$orders['CUSTOMER_CONTACT'];
		            $order_category=$orders['CA_ID'];
		            $ordered_quantity=$orders['ORDERED_QUANTITY'];
		            $order_date=date("D d M, Y",strtotime($orders['ORDER_DATE']));
                    $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID'];
		      
		            
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
else{ header("Location: store-management-order-review");}


/****************************************************/
//if user chooses to update stock
if(isset($_POST['submit'])){

	if($form_man->emptyField($_POST['actual_produced_quantity'])){
      $log_error="<h4 style='background-color:red'>Oops! No Quantity Specified!</h4>";
	}//end nested if
	else{
		   //old order quantity
		  $old_quantity= $ordered_quantity;

		  //get actual quantity produced and calculate new amount to be paid
		  $actual_produced_quantity=$form_man->cleanString($_POST['actual_produced_quantity']);
		  $new_amount_to_be_paid=$actual_produced_quantity*$product_price;

          //update amount cleared
		  $update_query="UPDATE ORDERS SET ORDERED_QUANTITY=".$actual_produced_quantity.", AMOUNT_TO_BE_PAID=".$new_amount_to_be_paid;
		  $update_query.=" WHERE ORDER_ID=".$order_id;
		  $run_update=mysqli_query(DB_Connection::$connection,$update_query);


		  if($run_update){

				  	/**after update, log the process into  logs table for record keeping**/

					//get time that changes were made
					$logging_date=strftime("%Y-%m-%d %H:%M:%S", time());
					$logging_year=date("Y",strtotime($logging_date));
					$logging_month=date("F",strtotime($logging_date));//full representation of month
					$logging_day_figure=date("j",strtotime($logging_date));
					$logging_week_day=date("l",strtotime($logging_date));//full representation of day of the week

					//get name of employee making the changes
					$person_logging=$employee_name;
					$logging_info=$form_man->cleanString($person_logging." made changes to the ordered quantity of ".$customer_name." on ".$product_name."(".$product_code.") which was ordered. Ordered quantity was changed from ".$old_quantity." to ".$actual_produced_quantity);



				  	$log_query="INSERT INTO LOGS(ORD_ID,PERSON_LOGGING,LOGGING_INFO,LOGGING_DATE,LOGGING_YEAR,LOGGING_MONTH,LOGGING_DAY_FIGURE,LOGGING_WEEK_DAY) VALUES(";
				  	$log_query.="'{$order_id}',";
				  	$log_query.="'{$person_logging}',";
				  	$log_query.="'{$logging_info}',";
				  	$log_query.="'{$logging_date}',";
				  	$log_query.="'{$logging_year}',";
				  	$log_query.="'{$logging_month}',";
				  	$log_query.="'{$logging_day_figure}',";
				  	$log_query.="'{$logging_week_day}')";

				    $run_log_query=mysqli_query(DB_Connection::$connection,$log_query);

				    echo mysqli_error(DB_Connection::$connection);

				    //if(!$run_log_query){echo  mysqli_error(DB_Connection::$connection); }

		  	        $log_error="<h4 style='background-color:#00B16A;'>".ucfirst($customer_name)." Order Changes Processed! <i class='fa fa-check'></i></h4>";

		            //immediately update display
				  	$orders=$query_guy->find_by_id("ORDERS","ORDER_ID",$order_id);
					$ordered_quantity=$orders['ORDERED_QUANTITY'];
					$amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID'];

		  }

	}
}//end if user processes new change


?>
<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/store-management-order-review-detail.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Store Order Details</title>

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
        	<li><a href="store-management-order-review"> <i class="fa fa-arrow-left"></i> Back To Orders</a></li>
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
			<li><a href='#'><i class='fa fa-exclamation-circle'></i> Just in case the production sector produces less than the
				order specified by the sales person, this is where you will enter that produced amount. The new amount to be paid by the
			  customer for this order will be automatically recalculated</a></li>
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
				<p><a href="store-management-order-review" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back To List</a></p>
			</div>
		</div>

		<div class="col-md-7 order-form">
			<form action="store-management-order-review-detail?order=<?php echo $order_id; ?>" role="form" method="post">
				  <div class="form-group">
					  <label class="col-lg-3 control-label">Actually Quantity Produced</label>
					  <div class="col-lg-9">
						<input type="number" name="actual_produced_quantity" class="form-control"/>
					  </div>
				  </div>

				  <div class="form-group">
					  <div class="col-lg-12">
						<input type="submit" class="btn btn-info pull-right" name="submit" value="Process New Change">
					  </div>
				  </div>

				</form>
			
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