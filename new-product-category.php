<?php ob_start();

if(!isset($_COOKIE['saleslog'])){header("Location: index");}


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
$emp_id=$_COOKIE['saleslog'];
$log_error="<h4 style='background-color:#00B16A;'>Add A New Product Category</h4>";

//pick first name of this user from database
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];

if(mysqli_num_rows($categories)==0){$category_list.="<li><a href='#'><i class='fa fa-arrow-right'></i> You have no item categories.</a></li>";}
else{

	while($category_results=mysqli_fetch_assoc($categories)){
     $cat_name=$category_results['CATEGORY_NAME'];
     $cat_id=$category_results['CATEGORY_ID'];

     $category_list.="<li><a href='warehouse-view?category=".$cat_id."'><i class='fa fa-arrow-right'></i> ".ucfirst($cat_name)."</a></li>";

}

}
/******************************************************/

//category addition
if(isset($_POST['submit'])){

	if($form_man->emptyField($_POST['category_name'])){
      $log_error="<h4 style='background-color:red;'>Oops! You Left The Input Field Empty</h4>";
	}//end nested if
	else{

		$category_name=$form_man->cleanString($_POST['category_name']);

                              //register the employee in database
                      	      $insert_query="INSERT INTO PRODUCT_CATEGORY(CATEGORY_NAME) VALUES('{$category_name}');";
                        	  $rex=mysqli_query(DB_Connection::$connection,$insert_query);

							  if($rex){
							  			$log_error="<h4 style='background-color:#00B16A;'>".ucfirst($category_name)." Added To Product Categories <i class='fa fa-check'></i></h4>";
							 			/*select all product categories*/
										$categories=$query_guy->find_all("PRODUCT_CATEGORY");
										$category_list="";

										while($category_results=mysqli_fetch_assoc($categories)){
										     $cat_name=$category_results['CATEGORY_NAME'];
										     $cat_id=$category_results['CATEGORY_ID'];

										     $category_list.="<li><a href='warehouse-view?category=".$cat_id."'><i class='fa fa-arrow-right'></i> ".ucfirst($cat_name)."</a></li>";

										}
										/******************************************************/
							  }//end if query successful
	}

}//end main if
	

?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/new-product-category.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>New Category</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="index" class="navbar-brand">Product Categories Addition</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<li><a href="order"> <i class="fa fa-file"></i> Make An Order</a></li>
        	<li><a href="new-product"> <i class="fa fa-plus"></i> Add A New Item</a></li>
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
        <div class="col-md-4">
			<div class="product-box">
				<img src="images/new-product-category.png" class="img img-responsive"/>
				<p style="text-decoration:underline;">Quick Help</p>
				<p>All items sold by the company fall under specific categories.</p>
				<p>This section is where you add all those categories of products which are sold in the company</p>
				<p><span>Example:</span> Bread, Doughnut, Cement, etc. To add specific item details, click on <a href="new-product">Add New Item</a></p>
			</div>
		</div>

		<div class="col-md-8 order-form">
			<form  action="<?php echo $_SERVER['PHP_SELF']; ?>" role="form" method="post">

				  <div class="form-group">
					  <label class="col-lg-3 control-label">New Category Name</label>
					  <div class="col-lg-9">
						<input type="text" name="category_name" class="form-control" />
					  </div>
				  </div>

				  <div class="form-group">
					  <div class="col-lg-12">
						<input type="submit" class="btn btn-info pull-right" name="submit" value="Add New Category"/>
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