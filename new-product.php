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
$category_select_options="";

while($category_results=mysqli_fetch_assoc($categories)){
     $cat_name=$category_results['CATEGORY_NAME'];
     $cat_id=$category_results['CATEGORY_ID'];

     $category_list.="<li><a href='warehouse-view?category=".$cat_id."'><i class='fa fa-arrow-right'></i> ".ucfirst($cat_name)."</a></li>";
     $category_select_options.="<option value='".$cat_id."'>".ucfirst($cat_name)."</option>";
}
/******************************************************/

$emp_id=$_COOKIE['saleslog'];
$log_error="<h4 style='background-color:#00B16A;'>Add A Brand New Item</h4>";

//pick first name of this user from database
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];

/*add a new item*/
if(isset($_POST['submit'])){

	 if($form_man->emptyField($_POST['item_category'])||
                    $form_man->emptyField($_POST['item_code'])||
                    $form_man->emptyField($_POST['item_name'])||
                    $form_man->emptyField($_POST['item_type'])||
                    $form_man->emptyField($_POST['item_price'])

                   ){
                      $log_error="<h4 style='background-color:red;'>There were errors or ommitted spaces in your form submission!</h4>";

                 }//end second if
                 else{

                 	  $item_category=$form_man->cleanString($_POST['item_category']);
                      $item_code=$form_man->cleanString($_POST['item_code']);
                      $item_name=$form_man->cleanString($_POST['item_name']);	
                      $item_type=$form_man->cleanString($_POST['item_type']);	
                      $item_price=$form_man->cleanString($_POST['item_price']);	

                    
                              //register the employee in database
                      	      $insert_query="INSERT INTO PRODUCTS(CAT_ID,PRODUCT_NAME,PRODUCT_CODE,PRODUCT_TYPE,PRODUCT_PRICE) VALUES(";
                              $insert_query.="'{$item_category}',";
                              $insert_query.="'{$item_name}',";
                              $insert_query.="'{$item_code}',";
                              $insert_query.="'{$item_type}',";
                              $insert_query.="'{$item_price}')";
         
							  $rex=mysqli_query(DB_Connection::$connection,$insert_query);

							  if($rex){
							  			$log_error="<h4 style='background-color:#00B16A;'>".$item_name." added to the inventory of your products <i class='fa fa-check'></i></h4>";
							  }//end if query successful
							  /*else{echo mysqli_error(DB_Connection::$connection);}*/

                      
                 }

}//end main if

?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/new-product.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Add A New Item</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="index" class="navbar-brand">Add New Product</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<li><a href="order"> <i class="fa fa-file"></i> Make An Order</a></li>
        	<li><a href="new-product-category"> <i class="fa fa-plus"></i> Add New Product Category</a></li>
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
				<img src="images/new-product.png" class="img img-responsive"/>
				<p style="text-decoration:underline;">Example</p>
				<p>Item Category: <span>Bread</span></p>
				<p>Item Code: <span>CPB67361</span></p>
				<p>Item Name: <span>Brown Bread</span></p>
				<p>Item Type: <span>Large Size</span></p>
				<p>Price Per Item: <span>GHS 10.00</span></p>
			</div>
		</div>

		<div class="col-md-8 order-form">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" role="form" method="post">

				 <div class="form-group">
					  <label class="col-lg-3 control-label">Item Category</label>
					  <div class="col-lg-9">
						<select name="item_category" class="form-control">
							<option value="default">Please Choose The Item Category</option>
							<?php echo $category_select_options; ?>
						</select>
					  </div>
				  </div>

				   <div class="form-group">
					  <label class="col-lg-3 control-label">Item Code</label>
					  <div class="col-lg-9">
						<input type="text" name="item_code" class="form-control"/>
					  </div>
				  </div>

				   <div class="form-group">
					  <label class="col-lg-3 control-label">Item Name</label>
					  <div class="col-lg-9">
						<input type="text" name="item_name" class="form-control" />
					  </div>
				  </div>

				   <div class="form-group">
					  <label class="col-lg-3 control-label">Item Type</label>
					  <div class="col-lg-9">
						<input type="text" name="item_type" class="form-control"/>
					  </div>
				  </div>

				  <div class="form-group">
					  <label class="col-lg-3 control-label">Price Per Item</label>
					  <div class="col-lg-9">
						<input type="text" name="item_price" class="form-control" />
					  </div>
				  </div>

				  <div class="form-group">
					  <div class="col-lg-12">
						<input type="submit" class="btn btn-info pull-right" name="submit" value="Add Item">
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