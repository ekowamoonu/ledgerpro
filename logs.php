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

/*select all product categories plus the corresponding number of orders*/
/*$categories_plus_orders="SELECT *, COUNT(CA_ID) AS NUMBER_OF_ORDERS FROM PRODUCT_CATEGORY ";
$categories_plus_orders.="LEFT JOIN ORDERS ON(CATEGORY_ID=CA_ID) GROUP BY CATEGORY_NAME ORDER BY CATEGORY_NAME ASC";
$categories_plus_orders_query=mysqli_query(DB_Connection::$connection,$categories_plus_orders);
$category_list="";
*/

$log_error="<h4 style='background-color:#00B16A;'>Log List</h4>";


/**************************************get all details to be used for filter*************************************************/


/*select all years*/
$years="";
				 $years_query="SELECT DISTINCT ORDER_YEAR FROM ORDERS ORDER BY ORDER_YEAR DESC";
				 $years_query_process=mysqli_query(DB_Connection::$connection,$years_query);

					   while($fetch_years=mysqli_fetch_assoc($years_query_process)){

                            $year=$fetch_years['ORDER_YEAR'];
					   		$years.='<option value="'.$year.'">'.$year.'</option>';	
					   }


$order_rows="";
//insert code here

/*run filter code*/
if(isset($_POST['filter_submit'])){

	$filter_results=$query_guy->logs_search();//filtering function..my special function


	//if filter returns null
	if(mysqli_num_rows($filter_results)==0){ $order_rows='<tr><td><b>No Logs found for your filter</b></td></tr>';}
	else{

		  $i=1;

	     while($logs=mysqli_fetch_assoc($filter_results)){

		  	        $log_id=$logs['LOG_ID'];
		  	        $person_logging=ucfirst($logs['PERSON_LOGGING']);
		  	        $logging_info=$logs['LOGGING_INFO'];
		  	        $order_id=$logs['ORD_ID'];
		            $logging_date=date("D d M, Y h:m:s a",strtotime($logs['LOGGING_DATE']));

//check whether expense is an order expense or general expense
		            if($order_id!=0){
		            	$source_of_log="Log was made on an order. "."<a href='statistics-detail?order=".$order_id."'>Click to view this order </a>";
		            }
		            else{
		            	$source_of_log="Log is a general";
		            }


		            $order_rows.='    <div class="row" style="background-color:white;">
							       	  <div class="col-md-12">
											<h3 class="topic">'. $i.'. Recorded log of '.$person_logging.' on '.$logging_date.' </h3>
									        <div class="blk">
									        <blockquote>
									        '. $logging_info.'
									        <p>'.$source_of_log.'</p>
									        </blockquote>
									        </div>
									       </div>
								       </div>';

								       $i++;

		  }//end wile loop

	}//end reading


}//end if filter_sibmit
else{
	   
	  
	   $log_query="SELECT * FROM LOGS ORDER BY LOGGING_DATE DESC";
	   $log_query_process=mysqli_query(DB_Connection::$connection,$log_query);
	   $i=1;

	     while($logs=mysqli_fetch_assoc($log_query_process)){

		  	        $log_id=$logs['LOG_ID'];
		  	        $person_logging=ucfirst($logs['PERSON_LOGGING']);
		  	        $logging_info=$logs['LOGGING_INFO'];
		  	        $order_id=$logs['ORD_ID'];
		            $logging_date=date("D d M, Y h:m:s a",strtotime($logs['LOGGING_DATE']));

//check whether expense is an order expense or general expense
		            if($order_id!=0){
		            	$source_of_log="Log was made on an order. "."<a href='statistics-detail?order=".$order_id."'>Click to view this order </a>";
		            }
		            else{
		            	$source_of_log="Log is a general";
		            }


		            $order_rows.='    <div class="row" style="background-color:white;">
							       	  <div class="col-md-12">
											<h3 class="topic">'. $i.'. Recorded log of '.$person_logging.' on '.$logging_date.' </h3>
									        <div class="blk">
									        <blockquote>
									        '. $logging_info.'
									        <p>'.$source_of_log.'</p>
									        </blockquote>
									        </div>
									       </div>
								       </div>';

								       $i++;

		  }//end wile loop


}//end if filter is not triggered


?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/logs.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Expenses Review</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="#" class="navbar-brand">Expenses Reviews</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<li><a href="accounts-order-review"> <i class="fa fa-arrow-left"></i> Back To Order Reviews</a></li>
            <li><a href="#"  class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Boss<span class="caret"></span></a>
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
	<div class="col-md-4 list">
	<h4>Filter Logs</h4>	
	  <div class="sidebox">
		<form role="form" class="filter-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

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
						 <input type="submit" class="btn btn-danger btn-block" name="filter_submit" value="Filter Logs"/>
					   </div>
				  </div>
		</form>

						
	  </div>
	</div>

	<!--products display-->
	<div class="col-md-8 details">
		<?php echo $log_error; ?>
		<div class="container-fluid">

		   <?php echo $order_rows; ?>
		       

      </div>
	</div>
</div>
</div>


 	


<?php include("inc/footer.php"); ?>
<script type="text/javascript">
   $(function(){


            $(".blk").hide();

                $(".topic").click(function(){

                $(this).next().slideToggle();

                });

   	
   	  
   });
</script>
<script type="text/javascript" src="js/item_searcher.js"></script>
</body>
</html>