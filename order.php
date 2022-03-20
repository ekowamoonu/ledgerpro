<?php ob_start();

if(!isset($_COOKIE['saleslog'])){header("Location: index");}


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

//select and display all product categories from the database
while($category_results=mysqli_fetch_assoc($categories)){
     $cat_name=$category_results['CATEGORY_NAME'];
     $cat_id=$category_results['CATEGORY_ID'];

     $category_list.='<option value="'.$cat_id.'">'.ucfirst($cat_name).'</option>';

}


/******************************************************/


//pick first name of this user from database
$emp_id=$_COOKIE['saleslog'];
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];


/**************************************************************************************************************************/
//if new driver is added
if(isset($_POST['driver_submit'])){
	if($form_man->emptyField($_POST['driver_name'])||
		$form_man->emptyField($_POST['driver_contact'])
		){
      $log_error="<h4 style='background-color:red'>You had some errors in your submission, please re-fill the form!</h4>";
	}else{

		  $driver_name=$form_man->cleanString($_POST['driver_name']);
		  $driver_contact=$form_man->cleanString($_POST['driver_contact']);
		  $drivers="";

          //add new driver
		  $new_driver_query="INSERT INTO DRIVERS(DRIVER_NAME,DRIVER_CONTACT) VALUES(";
		  $new_driver_query.="'{$driver_name}',";
		  $new_driver_query.="'{$driver_contact}')";
		  $driver_query=mysqli_query(DB_Connection::$connection,$new_driver_query);

		  if($driver_query){

		  	     /*select all drivers*/
				$read_drivers=mysqli_query(DB_Connection::$connection,"SELECT * FROM DRIVERS ORDER BY DRIVER_NAME ASC");
				while($driver_records=mysqli_fetch_assoc($read_drivers)){

					    $driver_id=$driver_records['DRIVER_ID'];
					    $driver_name=$driver_records['DRIVER_NAME'];

					    $drivers.='<option value="'.$driver_id.'">'.ucfirst($driver_name).'</option>';

				  }//end while

				 $log_error="<h4 style='background-color:#00B16A;'>New Driver Added! <i class='fa fa-check'></i></h4>";

				}//end id driver_querry
				

		  }//end nested else
	}//end driver submit main if
	else{

         /*else if a post has not been made, go ahead to read normally from the database*/
		/*select all drivers*/
		$drivers="";
		$read_drivers=mysqli_query(DB_Connection::$connection,"SELECT * FROM DRIVERS ORDER BY DRIVER_NAME ASC");
		while($driver_records=mysqli_fetch_assoc($read_drivers)){
					$driver_id=$driver_records['DRIVER_ID'];
					$driver_name=$driver_records['DRIVER_NAME'];

					 $drivers.='<option value="'.$driver_id.'">'.ucfirst($driver_name).'</option>';

			 }//end while

	}//end else

/**************************************************************************************************************************/

/**************************************************************************************************************************/
//if new customer is added
if(isset($_POST['customer_submit'])){
	if($form_man->emptyField($_POST['customer_name'])||
		$form_man->emptyField($_POST['customer_contact'])
		){
      $log_error="<h4 style='background-color:red'>You had some errors in your customer submission, please re-fill the form!</h4>";
	}else{

		  $customer_name=$form_man->cleanString($_POST['customer_name']);
		  $customer_contact=$form_man->cleanString($_POST['customer_contact']);
		  $driver_id=$form_man->cleanString($_POST['customer_driver']);
		  $customers="";

          //add new driver
		  $new_customer_query="INSERT INTO CUSTOMERS(DRIVE_ID,CUSTOMER_NAME,CUSTOMER_CONTACT) VALUES(";
		  $new_customer_query.="'{$driver_id}',";
		  $new_customer_query.="'{$customer_name}',";
		  $new_customer_query.="'{$customer_contact}')";
		  $customer_query=mysqli_query(DB_Connection::$connection,$new_customer_query);

		  if($customer_query){

			  	     /*select all customers*/
					$read_customers=mysqli_query(DB_Connection::$connection,"SELECT * FROM CUSTOMERS ORDER BY CUSTOMER_NAME ASC");
					while($customer_records=mysqli_fetch_assoc($read_customers)){

						    $customer_id=$customer_records['CUSTOMER_ID'];
						    $customer_name=$customer_records['CUSTOMER_NAME'];

						    $customers.='<option value="'.$customer_id.'">'.ucfirst($customer_name).'</option>';

					  }//end while

					 $log_error="<h4 style='background-color:#00B16A;'>New Customer Added! <i class='fa fa-check'></i></h4>";

			}//end id driver_querry
				/*else{
					echo mysqli_error(DB_Connection::$connection);
				}*/
				

		  }//end nested else
	}//end driver submit main if
	else{

         /*else if a post has not been made, go ahead to read normally from the database*/
		 /*select all customers*/
		        $customers="";
				$read_customers=mysqli_query(DB_Connection::$connection,"SELECT * FROM CUSTOMERS ORDER BY CUSTOMER_NAME ASC");
				while($customer_records=mysqli_fetch_assoc($read_customers)){

					    $customer_id=$customer_records['CUSTOMER_ID'];
					    $customer_name=$customer_records['CUSTOMER_NAME'];

					    $customers.='<option value="'.$customer_id.'">'.ucfirst($customer_name).'</option>';

				  }//end while

	}//end else

/**************************************************************************************************************************/

/***************************************************************************************************************************/
//if sale person places order
if(isset($_POST['order_submit'])){

      $seller_id=$emp_id;
      $customer_id=$form_man->cleanString($_POST['customer_id']);
      $item_category=$_POST['item_category'];
      $item_name=$_POST['item_name'];
	  $ordered_quantity=$_POST['ordered_quantity'];
	  $order_date=strftime("%Y-%m-%d %H:%M:%S", time());
	  $order_year=date("Y",strtotime($order_date));//eg 2016
	  $order_month=date("F",strtotime($order_date));//full representation of month
	  $order_day_figure=date("j",strtotime($order_date));//date eg 16,21
	  $order_week_day=date("l",strtotime($order_date));//full rep of day of the week

	  //get details of customer
	  $driver_finder=$query_guy->find_by_id("CUSTOMERS","CUSTOMER_ID",$customer_id);

	  $driver_id=$driver_finder['DRIVE_ID'];
	  $customer_name=$form_man->cleanString($driver_finder['CUSTOMER_NAME']);
	  $customer_contact=$driver_finder['CUSTOMER_CONTACT'];

	  /*get the total amount to be paid by the customer for the customer for every order*/
	  //for every loop,get the product price of the current item in the array
	  //multiple by current ordered quanity
	  //value obtained is the amount to be paid by the customer for that order

	   for($i=0;$i<sizeof($ordered_quantity);$i++){//looping throufh every order

	   	    $product_id=$item_name[$i];
	   	    $number_of_orders=$i+1;
	        $category_id= $item_category[$i];
	        $ordered_amount=$ordered_quantity[$i];
        
	        //first of all, get product price of the current item in the array.
	        $product_price_finder=$query_guy->find_by_id("PRODUCTS","PRODUCT_ID",$product_id);
	        $product_price=$product_price_finder["PRODUCT_PRICE"];

	        //multiply by the ordered quantity
	        $total_cost_of_order=$ordered_amount*$product_price;
	        $amount_to_be_paid= $total_cost_of_order;

	        //proceed to logging this into the database

				     $order_query="INSERT INTO ORDERS(SELLER_ID,PRODUCT_ID,DR_ID,CA_ID,CUST_ID,CUSTOMER_NAME,CUSTOMER_CONTACT,ORDERED_QUANTITY,";
				     $order_query.="ORDER_DATE,ORDER_YEAR,ORDER_MONTH,ORDER_DAY_FIGURE,ORDER_WEEK_DAY,TOTAL_COST_OF_ORDER,AMOUNT_TO_BE_PAID,AMOUNT_PAID,INVOICE_NUMBER,REMARKS,PAYMENT_STATUS) ";
                     $order_query.="VALUES( ";
                     $order_query.="'{$seller_id}', ";
                     $order_query.="'{$product_id}', ";
                     $order_query.="'{$driver_id}', ";
                     $order_query.="'{$category_id}', ";
                     $order_query.="'{$customer_id}', ";
                     $order_query.="'{$customer_name}', ";
                     $order_query.="'{$customer_contact}', ";
                     $order_query.="'{$ordered_amount}', ";
                     $order_query.="'{$order_date}', ";
                     $order_query.="'{$order_year}', ";
                     $order_query.="'{$order_month}', ";
                     $order_query.="'{$order_day_figure}', ";
                     $order_query.="'{$order_week_day}', ";
                     $order_query.="'{$total_cost_of_order}', ";
                     $order_query.="'{$amount_to_be_paid}', ";
                     $order_query.="'0', ";
                     $order_query.="'no invoice', ";
                     $order_query.="'no remarks', ";
                     $order_query.="'0'";
                     $order_query.=")";

					 $process_order=mysqli_query(DB_Connection::$connection,$order_query);

					 if($process_order){
					 	$log_error="<h4 style='background-color:#00B16A;'>".$number_of_orders." Orders For ".ucfirst($customer_name)." Processed Successfully <i class='fa fa-check'></i></h4>";
					 }
					/* else{
					 	echo mysqli_error(DB_Connection::$connection);
					 }
	  	 */

	  }//end for loop


/*	  echo "Year-".$order_year."<br/>";
	  echo "Month-".$order_month."<br/>";
	  echo "day figure-".$order_day_figure."<br/>";
	  echo "weekday-".$order_week_day."<br/>";*/

	
}//end main if

?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/order.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Customer Order</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="index" class="navbar-brand">Sales Center</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<li><a href="new-product-category"> <i class="fa fa-plus"></i> New Product Category</a></li>
        	<li><a href="new-product"> <i class="fa fa-plus"></i> Add New Item</a></li>
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
<!--products display-->
	<div class="col-md-12 details">
		<?php echo $log_error; ?>

	    <div class="col-md-3">
			<div class="product-box">
				<img src="images/vehicle.jpg" class="img img-responsive"/>
			    <form role="form" method="post" action="order.php" class="driver-customer-form">

                 <div class="row">
			       <div class="form-group">
					  <label class="col-lg-3 control-label">Driver Name</label>
					  <div class="col-lg-9">
						<input type="text" name="driver_name" class="form-control" />
					  </div>
				   </div>
				 </div>

				 <div class="row">
			       <div class="form-group">
					  <label class="col-lg-3 control-label">Driver Contact</label>
					  <div class="col-lg-9">
						<input type="text" name="driver_contact" class="form-control" />
					  </div>
				   </div>
				 </div>

				  <div class="row">
				  <div class="form-group" style="margin-top:16%;">
					  <div class="col-lg-12">
						<input type="submit" class="btn btn-info btn-block" name="driver_submit" value="Add Driver">
					  </div>
				  </div>
				  </div>

				   
			    </form>
				<!-- <p><a href="sales?category=<?php echo $product_category; ?>" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back To List</a></p> -->
			</div>
		</div>

<!--customer addition form-->
        <div class="col-md-3">
			<div class="product-box">
				<img src="images/customer_order.png" class="img img-responsive"/>
				     <form role="form" method="post" action="#" class="driver-customer-form">

	                 <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Customer Name</label>
						  <div class="col-lg-9">
							<input type="text" name="customer_name" class="form-control" />
						  </div>
					   </div>
					 </div>

					  <div class="row">
				       <div class="form-group">
						  <label class="col-lg-3 control-label">Customer Contact</label>
						  <div class="col-lg-9">
							<input type="text" name="customer_contact" class="form-control" />
						  </div>
					   </div>
					 </div>

					 <div class="row">
					      <div class="form-group">
						  <label class="col-lg-3 control-label">Driver Name</label>
						  <div class="col-lg-9">
							<select name="customer_driver" class="form-control">
								<option value="default">Customer's Driver</option>
								<option value="0">No Driver</option>
								<?php echo $drivers; ?>
							</select>
						  </div>
					       </div>
					 </div>

					<div class="row">
					  <div class="form-group">
						  <div class="col-lg-12">
							<input type="submit" class="btn btn-info btn-block" name="customer_submit" value="Add Customer">
						  </div>
					  </div>
				    </div>

					   
				    </form>
				
			</div>
		</div>


		<div class="col-md-6 order-form">
			<form action="order.php" role="form" method="post">
                  <span class="hidden-input" style="display:none;"></span><!-- to store categories list-->

				  <div class="form-group">
					  <label class="col-lg-3 control-label">Customer Name</label>
					  <div class="col-lg-9">
						<select name="customer_id" class="form-control">
							<option value="default">Choose Customer</option>
							<?php echo $customers; ?>
						</select>
					  </div>
				  </div>


				  <div class="form-group">
					<h3 class="form-legend">Order Details</h3>
					<hr/>
				  </div>

                 <div class="item_category">
					  <div class="form-group category_form_group">
						  <label class="col-lg-3 control-label">Item Category</label>
						  <div class="col-lg-9">
							<select name="item_category[]" class="form-control" id="item_category">
								<option value="default">Please Choose The Item Category</option>
								<?php echo $category_list; ?>
							</select>
						  </div>
					  </div>


					  <div class="form-group">
						  <label class="col-lg-3 control-label">Specific Item</label>
						  <div class="col-lg-9">
							<select name="item_name[]" class="form-control" id="specific_item">
								<option value="default">Please Choose The Specific Item</option>
							</select>
						  </div>
					  </div>




					  <div class="form-group">
						  <label class="col-lg-3 control-label">Ordered Quantity</label>
						  <div class="col-lg-9">
							<input type="text" name="ordered_quantity[]" class="form-control" />
						  </div>
					  </div>
                </div>

	                <div class="dynamic-form">
	                </div><!--end dynamic form-->

				  <div class="form-group">
					  <a class="btn btn-danger add-order" href="#"><i class="fa fa-plus-circle"></i> Another Order</a>
				  </div>

				  <div class="form-group">
					  <div class="col-lg-12">
						<input type="submit" class="btn btn-info pull-right" name="order_submit" value="Place Order">
					  </div>
				  </div>

				</form>
			
		</div>

		
	

	</div>
</div>
</div>


 	


<?php include("inc/footer.php"); ?>
<script type="text/javascript" src="js/order.js"></script>
</body>
</html>