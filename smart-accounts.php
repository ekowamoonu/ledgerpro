<?php ob_start();

if(!isset($_COOKIE['acclog'])){header("Location: index");}


include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();

/*select all product categories plus the corresponding number of orders*/
/*$categories_plus_orders="SELECT *, COUNT(CA_ID) AS NUMBER_OF_ORDERS FROM PRODUCT_CATEGORY ";
$categories_plus_orders.="LEFT JOIN ORDERS ON(CATEGORY_ID=CA_ID) GROUP BY CATEGORY_NAME ORDER BY CATEGORY_NAME ASC";
$categories_plus_orders_query=mysqli_query(DB_Connection::$connection,$categories_plus_orders);
$category_list="";
*/

//pick first name of this user from database
$emp_id=$_COOKIE['acclog'];
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];


/**************************************get all details to be used for filter*************************************************/
//get all customers
$customers="";
				$read_customers=mysqli_query(DB_Connection::$connection,"SELECT * FROM CUSTOMERS ORDER BY CUSTOMER_NAME ASC");
				while($customer_records=mysqli_fetch_assoc($read_customers)){

					    $customer_id=$customer_records['CUSTOMER_ID'];
					    $customer_name=$customer_records['CUSTOMER_NAME'];

					    $customers.='<option value="'.$customer_id.'">'.ucfirst($customer_name).'</option>';

				  }//end while

/*select all drivers*/
$drivers="";
				$read_drivers=mysqli_query(DB_Connection::$connection,"SELECT * FROM DRIVERS ORDER BY DRIVER_NAME ASC");
				while($driver_records=mysqli_fetch_assoc($read_drivers)){
							$driver_id=$driver_records['DRIVER_ID'];
							$driver_name=$driver_records['DRIVER_NAME'];

							 $drivers.='<option value="'.$driver_id.'">'.ucfirst($driver_name).'</option>';

					 }//end while

/*select all years*/
$years="";
				 $years_query="SELECT DISTINCT ORDER_YEAR FROM ORDERS ORDER BY ORDER_YEAR DESC";
				 $years_query_process=mysqli_query(DB_Connection::$connection,$years_query);

					   while($fetch_years=mysqli_fetch_assoc($years_query_process)){

                            $year=$fetch_years['ORDER_YEAR'];
					   		$years.='<option value="'.$year.'">'.$year.'</option>';	
					   }


/*select all invoices*/
$invoices="";
				$read_invoices=mysqli_query(DB_Connection::$connection,"SELECT INVOICE_NUMBER FROM ORDERS WHERE INVOICE_NUMBER!='no invoice'");
				while($invoice_records=mysqli_fetch_assoc($read_invoices)){
							$invoice_number=$invoice_records['INVOICE_NUMBER'];

							$invoices.='<option value="'.$invoice_number.'">'.$invoice_number.'</option>';

					 }//end while


//select and display all product categories from the database
$categories=$query_guy->find_all("PRODUCT_CATEGORY");
$category_list="";

//select and display all product categories from the database
while($category_results=mysqli_fetch_assoc($categories)){
     $cat_name=$category_results['CATEGORY_NAME'];
     $cat_id=$category_results['CATEGORY_ID'];

     $category_list.='<option value="'.$cat_id.'">'.ucfirst($cat_name).'</option>';

}


/******************************************************/


$order_rows="";
/*run filter code*/
if(isset($_POST['filter_submit'])){

	$filter_results=$query_guy->run_accounts_order_search();//filtering function..my special function

	//if filter returns null
	if(mysqli_num_rows($filter_results)==0){ $order_rows='<tr><td><b>No orders found for your filter</b></td></tr>';$cash_sales=0;$credit_sales=0;}
	else{

          $cash_sales=0;
	      $credit_sales=0;
		  while($orders=mysqli_fetch_assoc($filter_results)){

		  	        $order_id=$orders['ORDER_ID'];
		            $seller_id=$orders['SELLER_ID'];
		            $product_id=$orders['PRODUCT_ID'];
		            $driver_id=$orders['DR_ID'];
		            $customer_name=$orders['CUSTOMER_NAME'];
		            $order_date=date("D d M, Y",strtotime($orders['ORDER_DATE']));
		            $ordered_quantity=$orders['ORDERED_QUANTITY'];
		            $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID'];
		            $amount_paid=$orders['AMOUNT_PAID'];
		            $cash_sales+=$orders['AMOUNT_PAID'];
		            $credit_sales+=$orders['AMOUNT_TO_BE_PAID'];

					//get name and product code
                    $order_product=$query_guy->find_by_id("PRODUCTS","PRODUCT_ID",$product_id);
                    $item_name=$order_product['PRODUCT_NAME'];
                    $item_code=$order_product['PRODUCT_CODE'];

                    //get names of customer's driver
	 				$driver_finder=$query_guy->find_by_id("DRIVERS","DRIVER_ID",$driver_id);
	 				$driver_name=$driver_finder['DRIVER_NAME'];

	 				if($driver_name==""){$driver_name="No driver";}

		            $order_rows.="<tr>
										<td>".$customer_name."</td>
										<td>".$item_code."</td>
										<td>".$item_name."</td>
									    <td>".$order_date."</td>
										<td>".$amount_to_be_paid."</td>
										<td>".$amount_paid."</td>
										<td><input type='text' class='form-control' id='".$order_id."'/></td>
										<td><button class='btn btn-danger' onclick='process_payment(\"{$order_id}\");'><i class='fa fa-check'></i> Make Payment</button></td>
										
									</tr>";

		  }//end wile loop

	}//end reading


}//end if filter_sibmit
else{
	   $cash_sales=0;
	   $credit_sales=0;
	   $order_query="SELECT * FROM ORDERS ORDER BY ORDER_DATE DESC";
	   $order_query_process=mysqli_query(DB_Connection::$connection,$order_query);

	   while($orders=mysqli_fetch_assoc($order_query_process)){
            
		            $order_id=$orders['ORDER_ID'];
		            $seller_id=$orders['SELLER_ID'];
		            $driver_id=$orders['DR_ID'];
		            $product_id=$orders['PRODUCT_ID'];
		            $customer_name=$orders['CUSTOMER_NAME'];
		            $order_date=date("D d M, Y",strtotime($orders['ORDER_DATE']));
		            $ordered_quantity=$orders['ORDERED_QUANTITY'];
		            $amount_to_be_paid=$orders['AMOUNT_TO_BE_PAID'];
		            $amount_paid=$orders['AMOUNT_PAID'];
		            $cash_sales+=$orders['AMOUNT_PAID'];
		            $credit_sales+=$orders['AMOUNT_TO_BE_PAID'];
		            /*$cash_sales=$orders['CASH_SALES'];
		            $credit_sales=$orders['CREDIT_SALES'];*/

					//get name and product code
                    $order_product=$query_guy->find_by_id("PRODUCTS","PRODUCT_ID",$product_id);
                    $item_name=$order_product['PRODUCT_NAME'];
                    $item_code=$order_product['PRODUCT_CODE'];

                    //get names of customer's driver
	 				$driver_finder=$query_guy->find_by_id("DRIVERS","DRIVER_ID",$driver_id);
	 				$driver_name=$driver_finder['DRIVER_NAME'];

	 				if($driver_name==""){$driver_name="No driver";}


		            $order_rows.="<tr>
										<td>".$customer_name."</td>
										<td>".$item_code."</td>
										<td>".$item_name."</td>
									    <td>".$order_date."</td>
										<td>".$amount_to_be_paid."</td>
										<td>".$amount_paid."</td>
										<td><input type='text' class='form-control' id='".$order_id."'/></td>
										<td><button class='btn btn-danger' onclick='process_payment(\"{$order_id}\");'><i class='fa fa-check'></i> Make Payment</button></td>

									</tr>";
	              }//end while

}//end if filter is not triggered


?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/smart-accounts.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Smart Accounts</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="#" class="navbar-brand">Order Reviews</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<li><a href="#" onclick="location.reload();"> <i class="fa fa-refresh"></i> Refresh Page</a></li>
        	<li><a href="accounts-order-review"> <i class="fa fa-file"></i> Check Payments</a></li>
        	<li><a href="expenses"> <i class="fa fa-file"></i> Expenses &amp; Profits</a></li>
            <li><a href="#"  class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $firstname; ?><span class="caret"></span></a>
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
	<h4>Credit &amp; Cash Sales For Filter</h4>	
	<h4>Credit Sales: <b>GHS <?php echo number_format($credit_sales,2); ?></b></h4>	
	<h4>Cash Sales: <b>GHS <?php echo number_format($cash_sales,2); ?></b></h4>

	<?php $total_sales=$credit_sales+$cash_sales;?>

	<h4>Total Sales (credit+cash): <b>GHS <?php echo number_format($total_sales,2); ?></b></h4>
	  <div class="sidebox">
		<form role="form" class="filter-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">


			         <div class="row">
				       <div class="form-group">
						  <label class="col-lg-3 control-label">Customer Name</label>
						  <div class="col-lg-9">
							<select name="customer_id" class="form-control">
							<option value="default">Choose Customer</option>
							<?php echo $customers; ?>
						   </select>
						  </div>
					   </div>
					 </div>

				     <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Driver</label>
						  <div class="col-lg-9">
							<select name="driver_id" class="form-control">
							<option value="default">Choose Driver</option>
							<?php echo $drivers; ?>
						   </select>
						  </div>
					   </div>
					</div>

					<div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Payment Status</label>
						  <div class="col-lg-9">
							<select name="payment_status" class="form-control">
							<option value="default">Choose Order Payement Satus</option>
							<option value="0">Still Owing</option>
							<option value="1">Fully Paid</option>
						   </select>
						  </div>
					   </div>
					</div>

					 <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Invoice Number</label>
						  <div class="col-lg-9">
							<select name="invoices" class="form-control">
							<option value="default">Choose Invoice</option>
							<?php echo $invoices; ?>
						   </select>
						  </div>
					   </div>
					</div>

				<div class="row" style="margin-top:4%;">
				       <div class="form-group">
						 <input type="submit" class="btn btn-danger btn-block" name="filter_submit" value="Sort Orders"/>
					   </div>
				  </div>


							<div class="row filter-details">
							<div class="form-group">
							<h5 class="form-legend">Sort By Item Details</h3>
						    </div>
						    </div>

					 <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Item Category</label>
						  <div class="col-lg-9">
							<select name="item_category" class="form-control" id="item_category">
							<option value="default">Choose Item Category</option>
							<?php echo $category_list; ?>
						   </select>
						  </div>
					   </div>
					</div>


					 <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Item Name <span class="loader"></span></label>
						  <div class="col-lg-9">
							<select name="item_name" class="form-control" id="item_name">
							<option value="default">Choose Specific Item</option>
						   </select>
						  </div>
					   </div>
					</div>
                   
                   <div class="row" style="margin-top:4%;">
				       <div class="form-group">
						 <input type="submit" class="btn btn-danger btn-block" name="filter_submit" value="Sort Orders"/>
					   </div>
				  </div>

							<div class="row filter-details">
							<div class="form-group">
							<h5 class="form-legend">Sort By Date Details</h3>
						    </div>
						    </div>

					 <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Year</label>
						  <div class="col-lg-9">
							<select name="year" class="form-control">
							<option value="default">Choose Year</option>
							<?php echo $years; ?>
						   </select>
						  </div>
					   </div>
					</div>

					 <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Month</label>
						  <div class="col-lg-9">
							<select name="month" class="form-control">
							<option value="default">Choose Month</option>
							<option value="January">January</option>
							<option value="Febrauary">Febrauary</option>
							<option value="March">March</option>
							<option value="April">April</option>
							<option value="May">May</option>
							<option value="June">June</option>
							<option value="July">July</option>
							<option value="August">August</option>
							<option value="September">September</option>
							<option value="October">October</option>
							<option value="November">November</option>
							<option value="December">December</option>
						   </select>
						  </div>
					   </div>
					</div>

				    <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Date</label>
						  <div class="col-lg-9">
							<select name="date" class="form-control">
							<option value="default">Choose Date</option>
							<script type="text/javascript">
							  var i=1;
							  for(i=1;i<32;i++){

							  	  document.write('<option value="'+i+'">'+i+'</option>');
							  }
							</script>
						   </select>
						  </div>
					   </div>
				  </div>

				  <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Day Of The Week</label>
						  <div class="col-lg-9">
							<select name="day_of_the_week" class="form-control">
							<option value="default">Choose Day Of The Week</option>
							<option value="Monday">Monday</option>
							<option value="Tuesday">Tuesday</option>
							<option value="Wednesday">Wednesday</option>
							<option value="Thursday">Thursday</option>
							<option value="Friday">Friday</option>
							<option value="Saturday">Saturday</option>
							<option value="Sunday">Sunday</option>
						   </select>
						  </div>
					   </div>
				  </div>

				  <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						 <input type="submit" class="btn btn-danger btn-block" name="filter_submit" value="Sort Orders"/>
					   </div>
				  </div>
		</form>
	  </div>
	</div>

	<!--products display-->
	<div class="col-md-9 details">
		<h4>Order List (Arranged from the latest order processed)</h4>	

		<table class="table table-striped table-hover">
			<thead>
			<tr style="color:#1E8BC3;">
				<th>Customer Name</th>
				<th>Item Code</th>
				<th>Item Name</th>
				<th>Order Date</th>
				<th>To Be Paid (GHS)</th>
				<th>Amount Paid(GHS)</th>
				<th>New Payment (GHS) </th>
				<th>Process Payment</th>
			</tr>
			</thead>

			<tbody>
			
			<?php echo $order_rows; ?>
			</tbody>
		</table>
	

	</div>
</div>
</div>


 	


<?php include("inc/footer.php"); ?>
<script type="text/javascript">
   $(function(){
   	
   	   $(".clickable-row").click(function(){
   	   	 window.document.location=$(this).data("href");
   	   });
   });
</script>
<script type="text/javascript" src="js/item_searcher.js"></script>
<script type="text/javascript">
  var process_payment;
  $(function(){
     
     //smart payment processing
     process_payment=function(a){

          //a- order id
     	  var amount=$("#"+a).val();
     	  var order_id=a;
     	 // var button=$(this)[0];

     	  //alert(button);

     	  if(amount==""){alert("no amount specified!");}
     	  else{

     	  	   $(this).html("processing...");

     	  	   $.post("inc/smart-parser.php",{order_id:order_id,amount:amount},function(data){
                      
                      if(data==1){
                                alert("Payment Processed!! Changes Will Be Seen On Page Refresh");
                                 //button.html("<i class='fa fa-check'></i> Complete");
                                 //after that, clear input field
			                  	setTimeout(function(){
			                  		$("#"+a).val("");
			                  		 //button.text("<i class='fa fa-check'></i> Make Payment");
			                  	},2000);

                      }
                      //alert(data);
     	     });

     	  }

     	

     }
     
  });

</script>
</body>
</html>