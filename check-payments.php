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
$categories_plus_orders="SELECT *, COUNT(CA_ID) AS NUMBER_OF_ORDERS FROM PRODUCT_CATEGORY ";
$categories_plus_orders.="LEFT JOIN ORDERS ON(CATEGORY_ID=CA_ID) GROUP BY CATEGORY_NAME ORDER BY CATEGORY_NAME ASC";
$categories_plus_orders_query=mysqli_query(DB_Connection::$connection,$categories_plus_orders);
$category_list="";

//pick first name of this user from database
$emp_id=$_COOKIE['acclog'];
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];


//select and display all product categories from the database
while($category_results=mysqli_fetch_assoc($categories_plus_orders_query)){
     $cat_name=$category_results['CATEGORY_NAME'];
     $cat_id=$category_results['CATEGORY_ID'];
     $number_of_orders=$category_results['NUMBER_OF_ORDERS'];;

     $category_list.="<li><a href='check-payments?category=".$cat_id."'><i class='fa fa-arrow-right'></i> ".ucfirst($cat_name)." (".$number_of_orders." orders)</a></li>";

}
/******************************************************/


$order_rows="";
if(isset($_GET['category'])){

	$category_id=$form_man->cleanString($_GET['category']);
	$all_orders=$query_guy->find_by_col_order_by_date("ORDERS","CA_ID",$category_id);

	/*get name of product category*/
	$category_name_finder=$query_guy->find_by_id("PRODUCT_CATEGORY","CATEGORY_ID",$category_id);
	$cat_name=$category_name_finder['CATEGORY_NAME'];

    
    //check if the category has no items
	if(mysqli_num_rows($all_orders)==0){

            //output when the category has no orders
            $order_rows='<tr><td><b>No orders for this item</b></td></tr>';
	}
	else{

			while($orders=mysqli_fetch_assoc($all_orders)){
            
		            $order_id=$orders['ORDER_ID'];
		            $seller_id=$orders['SELLER_ID'];
		            $product_id=$orders['PRODUCT_ID'];
		            $customer_name=$orders['CUSTOMER_NAME'];
		            $customer_contact=$orders['CUSTOMER_CONTACT'];
		            
		            /*get the name of seller who processed this order*/
		            $seller_details=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$seller_id);
		            $seller_fname=ucfirst($seller_details['FIRSTNAME']);
		            $seller_lname=ucfirst($seller_details['LASTNAME']);

		            /*find name of product*/
		            $product_details=$query_guy->find_by_id("PRODUCTS","PRODUCT_ID",$product_id);
                    $product_name=$product_details['PRODUCT_NAME'];

		            $order_rows.="<tr class='clickable-row' data-href='payment-detail?order=".$order_id."'>
										<td>".$order_id."</td>
										<td>".$customer_name."</td>
										<td>".$customer_contact."</td>
										<td>".$seller_fname." ".$seller_lname."</td>
										<td>".$product_name."</td>
										
									</tr>";
	              }//end while



	}//end else no items for this category



}//end main if
else{

	   $cat_name="All Items";
	   $order_query="SELECT * FROM ORDERS ORDER BY ORDER_DATE DESC";
	   $order_query_process=mysqli_query(DB_Connection::$connection,$order_query);

	   while($orders=mysqli_fetch_assoc($order_query_process)){
            
		            $order_id=$orders['ORDER_ID'];
		            $seller_id=$orders['SELLER_ID'];
		            $product_id=$orders['PRODUCT_ID'];
		            $customer_name=$orders['CUSTOMER_NAME'];
		            $customer_contact=$orders['CUSTOMER_CONTACT'];
		            
		            /*get the name of seller who processed this order*/
		            $seller_details=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$seller_id);
		            $seller_fname=ucfirst($seller_details['FIRSTNAME']);
		            $seller_lname=ucfirst($seller_details['LASTNAME']);

		            /*find name of product*/
		            $product_details=$query_guy->find_by_id("PRODUCTS","PRODUCT_ID",$product_id);
                    $product_name=$product_details['PRODUCT_NAME'];

		            $order_rows.="<tr class='clickable-row' data-href='payment-detail?order=".$order_id."'>
										<td>".$order_id."</td>
										<td>".$customer_name."</td>
										<td>".$customer_contact."</td>
										<td>".$seller_fname." ".$seller_lname."</td>
										<td>".$product_name."</td>
										
									</tr>";
	              }//end while

}//end if category dynamic url is not set


?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/check-payments.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Payments</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="#" class="navbar-brand">Check Order Payments</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	 <li><a href="accounts?category=<?php echo mt_rand(1,3); ?>"><i class="fa fa-file"></i> Item Prices</a></li>
        	 <li><a href="check-payments"><i class="fa fa-file"></i> All Orders</a></li>
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
	<h4>Filter Orders</h4>	
	  <div class="sidebox">
		<ul class="nav">
			<?php echo $category_list; ?>

		</ul>
	  </div>
	</div>

	<!--products display-->
	<div class="col-md-9 details">
		<h4><?php echo ucfirst($cat_name); ?> Order List</h4>	

		<table class="table table-striped table-hover">
			<thead>
			<tr style="color:#1E8BC3;">
				<th>Order ID</th>
				<th>Customer Name</th>
				<th>Customer Contact</th>
				<th>Name Of Seller</th>
				<th>Item Ordered</th>
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
<script>
        new WOW().init();
</script>
<script type="text/javascript">
   $(function(){
   	
   	   $(".clickable-row").click(function(){
   	   	 window.document.location=$(this).data("href");
   	   });
   });
</script>
</body>
</html>