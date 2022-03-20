<?php ob_start();

if(!isset($_COOKIE['acclog'])){header("Location: index");}


include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();

/*select all product categories*/
$categories=$query_guy->find_all("PRODUCT_CATEGORY");
$category_list="";

//pick first name of this user from database
$emp_id=$_COOKIE['acclog'];
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];


//select and display all product categories from the database
while($category_results=mysqli_fetch_assoc($categories)){
     $cat_name=$category_results['CATEGORY_NAME'];
     $cat_id=$category_results['CATEGORY_ID'];

     $category_list.="<li><a href='accounts?category=".$cat_id."'><i class='fa fa-arrow-right'></i> ".ucfirst($cat_name)."</a></li>";

}
/******************************************************/

/*get all product details of products belonging to a certain category*/

$product_boxes="";
if(isset($_GET['category'])){

	$category_id=$form_man->cleanString($_GET['category']);
	$all_products=$query_guy->find_by_col("PRODUCTS","CAT_ID",$category_id);

	/*get name of product category*/
	$category_name_finder=$query_guy->find_by_id("PRODUCT_CATEGORY","CATEGORY_ID",$category_id);
	$cat_name=$category_name_finder['CATEGORY_NAME'];

    
    //check if the category has no items
	if(mysqli_num_rows($all_products)==0){

            //output when the category has no items
            $product_boxes.='<div class="col-md-4">
							<div class="product-box">
								<p>No Items For This Category</p>
							</div>
						    </a>
						    </div>';
	}
	else{

			while($products=mysqli_fetch_assoc($all_products)){
            
		            $product_id=$products['PRODUCT_ID'];
		            $product_name=$products['PRODUCT_NAME'];
		            $product_type=$products['PRODUCT_TYPE'];
		            $product_code=$products['PRODUCT_CODE'];
		            $product_price=$products['PRODUCT_PRICE'];

		            $product_boxes.='<div class="col-md-4">
									<a href="price-update?product_id='.$product_id.'">
									<div class="product-box">
										<img src="images/ledger.png" class="img img-responsive"/>
										<p>Name: <span>'.$product_name.'</span></p>
										<p>Type: <span>'.$product_type.'</span></p>
										<p>Code: <span>'.$product_code.'</span></p>
										<p>Current Price Per Item: <span>GHS '.$product_price.'</span></p>
										<div class="overlay text-center"><a href="price-update?product_id='.$product_id.'" class="btn btn-default">Update Price</a></div>
									</div>
								    </a>
								    </div>';
	              }//end while



	/*select overall total stock and overall available stock for that category*/


	}//end else no items for this category



}//end main if

?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/accounts.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Accounts</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="index" class="navbar-brand">Accounts &amp; Prices Review</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<li><a href="accounts-order-review"> <i class="fa fa-file"></i> Check Payments</a></li>
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
		<h4>Product Details</h4>
		<?php echo $product_boxes; ?>
	</div>
</div>
</div>


 	


<?php include("inc/footer.php"); ?>
<script>
        new WOW().init();
</script>
</body>
</html>