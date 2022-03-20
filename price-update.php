<?php ob_start();

if(!isset($_COOKIE['acclog'])){header("Location: index");}


include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();

$log_error="<h4 style='background-color:#00B16A;'>Item Price Update</h4>	";

/*select all product categories*/
$categories=$query_guy->find_all("PRODUCT_CATEGORY");
$category_list="";

//pick first name of this user from database
$emp_id=$_COOKIE['acclog'];
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];
$lastname=$query['LASTNAME'];
$employee_name=ucfirst($firstname)." ".ucfirst($lastname);


//select and display all product categories from the database
while($category_results=mysqli_fetch_assoc($categories)){
     $cat_name=$category_results['CATEGORY_NAME'];
     $cat_id=$category_results['CATEGORY_ID'];

     $category_list.="<li><a href='accounts?category=".$cat_id."'><i class='fa fa-arrow-right'></i> ".ucfirst($cat_name)."</a></li>";

}
/******************************************************/

/*get all product details of products belonging to a certain category*/

$product_boxes="";
if(isset($_GET['product_id'])){

	$product_id=$form_man->cleanString($_GET['product_id']);
	$products=$query_guy->find_by_id("PRODUCTS","PRODUCT_ID",$product_id);

    $product_category=$products['CAT_ID'];
	$product_name=$products['PRODUCT_NAME'];
	$product_type=$products['PRODUCT_TYPE'];
	$product_code=$products['PRODUCT_CODE'];
	$product_price=$products['PRODUCT_PRICE'];


}//end main if


/****************************************************/
//if user chooses to update price
if(isset($_POST['submit'])){
	if($form_man->emptyField($_POST['new_price'])){
      $log_error="<h4 style='background-color:red'>Oops! No updated price specified!</h4>";
	}//end nested if
	else{
		  $new_price=$form_man->cleanString($_POST['new_price']);
		  $old_price=$product_price;

          //update available stock and total stock of particular item with new quantity
		  $update_query="UPDATE PRODUCTS SET PRODUCT_PRICE=".$new_price;
		  $update_query.=" WHERE PRODUCT_ID=".$product_id;
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
					$logging_info=$person_logging." made changes to the price of ".$product_name."(".$product_code.") from ".$old_price." to ".$new_price;



				  	$log_query="INSERT INTO LOGS(ORD_ID,PERSON_LOGGING,LOGGING_INFO,LOGGING_DATE,LOGGING_YEAR,LOGGING_MONTH,LOGGING_DAY_FIGURE,LOGGING_WEEK_DAY) VALUES(";
				  	$log_query.="'0',";
				  	$log_query.="'{$person_logging}',";
				  	$log_query.="'{$logging_info}',";
				  	$log_query.="'{$logging_date}',";
				  	$log_query.="'{$logging_year}',";
				  	$log_query.="'{$logging_month}',";
				  	$log_query.="'{$logging_day_figure}',";
				  	$log_query.="'{$logging_week_day}')";

				    $run_log_query=mysqli_query(DB_Connection::$connection,$log_query);

				  	$log_error="<h4 style='background-color:#00B16A;'>".ucfirst($product_name)." stock price updated! <i class='fa fa-check'></i></h4>";

		            //immediately update display
				  	$products=$query_guy->find_by_id("PRODUCTS","PRODUCT_ID",$product_id);
					$product_price=$products['PRODUCT_PRICE'];
		  }

	}
}


?>
<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/price-update.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Price Update</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="#" class="navbar-brand">Item Price Update Center</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<!-- <li><a href="#"> <i class="fa fa-tags"></i> Process An Order</a></li> -->
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
	<h4>List Of Products</h4>	
	  <div class="sidebox">
		<ul class="nav">
		<?php echo $category_list; ?>
		</ul>
	  </div>
	</div>

	<!--products display-->
	<div class="col-md-9 details">
		<?php echo $log_error; ?>
        <div class="col-md-4">
			<div class="product-box">
				<img src="images/ledger.png" class="img img-responsive"/>
				<p>Name: <span><?php echo $product_name; ?></span></p>
				<p>Type: <span><?php echo $product_type; ?></span></p>
				<p>Code: <span><?php echo $product_code; ?></span></p>
				<p>Current Price Per Item: GHS <span><?php echo $product_price; ?></span></p>
				<p><a href="accounts?category=<?php echo $product_category; ?>" class="btn btn-info btn-block"><i class="fa fa-arrow-left"></i> Back To List</a></p>
			</div>
		</div>

		<div class="col-md-8 order-form">
			<form action="price-update?product_id=<?php echo $product_id; ?>" role="form" method="post">
				
				  <div class="form-group">
					  <label class="col-lg-3 control-label">New Price</label>
					  <div class="col-lg-9">
						<input type="number" name="new_price" class="form-control" />
					  </div>
				  </div>

				  
				  <div class="form-group">
					  <div class="col-lg-12">
						<input type="submit" class="btn btn-info pull-right" name="submit" value="Update Price">
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