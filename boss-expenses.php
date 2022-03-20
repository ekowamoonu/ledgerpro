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



$log_error="<h4 style='background-color:#00B16A;'>Expenses List</h4>";

//pick first name of this user from database
$emp_id=1;//$_COOKIE['storelog'];
$query=$query_guy->find_by_id("EMPLOYEES","EMPLOYEE_ID",$emp_id);
$firstname=$query['FIRSTNAME'];
$lastname=$query['LASTNAME'];
$employee_name=ucfirst($firstname)." ".ucfirst($lastname);


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

	$filter_results=$query_guy->expenses_search();//filtering function..my special function

	$sales_results=$query_guy->cash_credit_sales_search();//get total cash and credit sales for same period
	$sales_credits=mysqli_fetch_assoc($sales_results);

	$cash_sales=$sales_credits['CASH_SALES'];
	$credit_sales=$sales_credits['CREDIT_SALES'];
	$total_sales=$cash_sales+$credit_sales;
    
    $total_expenses=0;

	//if filter returns null
	if(mysqli_num_rows($filter_results)==0){ $order_rows='<tr><td><b>No expenses found for your filter</b></td></tr>';$total_expenses=0;}
	else{

		  while($expenses=mysqli_fetch_assoc($filter_results)){

		  	        $expenses_id=$expenses['EXPENSES_ID'];
		  	        $expenses_info=$expenses['EXPENSES_NAME'];
		  	        $expenses_amount=$expenses['EXPENSES_AMOUNT'];
		  	        $total_expenses+=$expenses['EXPENSES_AMOUNT'];
		  	        $order_id=$expenses['ORD_ID'];
		            $expenses_date=date("D d M, Y",strtotime($expenses['EXPENSES_DATE']));

		            //check whether expense is an order expense or general expense
		            if($order_id!=0){
		            	$source_of_expense="Expense was made on an order. "."<a href='accounts-order-review-detail?order=".$order_id."'>Click to view this order </a>";
		            }
		            else{
		            	$source_of_expense="This expense is a general expense";
		            }


		            $order_rows.="<tr class='clickable-row'>
										<td>".$expenses_id."</td>
										<td>".$expenses_info."</td>
										<td>".$expenses_amount."</td>
										<td>".$expenses_date."</td>	
									</tr><tr class='tr-hide'><td>".$source_of_expense."</td></tr>";

		  }//end wile loop

	}//end reading


}//end if filter_sibmit
else{
	    $total_expenses=0;

	    $sales_results=mysqli_query(DB_Connection::$connection,"SELECT SUM(AMOUNT_TO_BE_PAID) AS CREDIT_SALES, SUM(AMOUNT_PAID) AS CASH_SALES FROM ORDERS");//get total cash and credit sales for same period
		$sales_credits=mysqli_fetch_assoc($sales_results);

		$cash_sales=$sales_credits['CASH_SALES'];
		$credit_sales=$sales_credits['CREDIT_SALES'];
		$total_sales=$cash_sales+$credit_sales;
	  
	   $expenses_query="SELECT * FROM EXPENSES ORDER BY EXPENSES_DATE DESC";
	   $expenses_query_process=mysqli_query(DB_Connection::$connection,$expenses_query);

	     while($expenses=mysqli_fetch_assoc($expenses_query_process)){

		  	        $expenses_id=$expenses['EXPENSES_ID'];
		  	        $expenses_info=$expenses['EXPENSES_NAME'];
		  	        $expenses_amount=$expenses['EXPENSES_AMOUNT'];
		  	        $total_expenses+=$expenses['EXPENSES_AMOUNT'];
		  	        $order_id=$expenses['ORD_ID'];
		            $expenses_date=date("D d M, Y",strtotime($expenses['EXPENSES_DATE']));

//check whether expense is an order expense or general expense
		            if($order_id!=0){
		            	$source_of_expense="Expense was made on an order. "."<a href='accounts-order-review-detail?order=".$order_id."'>Click to view this order </a>";
		            }
		            else{
		            	$source_of_expense="This expense is a general expense";
		            }


		            $order_rows.="<tr class='clickable-row'>
										<td>".$expenses_id."</td>
										<td>".$expenses_info."</td>
										<td>".$expenses_amount."</td>
										<td>".$expenses_date."</td>	
									</tr><tr class='tr-hide'><td>".$source_of_expense."</td></tr>";

		  }//end wile loop


}//end if filter is not triggered


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
          $expense_guy->add_to_expense("0",$expense_purpose,$expense_amount,$person_logging);


		  $logging_info=$form_man->cleanString($person_logging." recorded an expense amount of: ".$expense_amount." for ".$expense_purpose);
  
         //log this
          $logger->log_this($order_id,$person_logging,$logging_info);

          $log_error="<h4 style='background-color:#00B16A;'>Expense Recorded! <i class='fa fa-check'></i></h4>";
             
		      
	


	}//end nested else
}//end returned if

?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/expenses.css"/>
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
        	<li><a href="statistics"> <i class="fa fa-arrow-left"></i> Back To Order Reviews</a></li>
        	<li><a href="logs"> <i class="fa fa-pencil"></i> Logs</a></li>
        	<li><a href="employee-list"> <i class="fa fa-male"></i> Workforce Management</a></li>
        	<li><a href="#add"> <i class="fa fa-plus"></i> Add General Expenses</a></li>
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
	<div class="col-md-4 list">
	<h4>Credit &amp; Cash Sales For Filter</h4>	
	<h4>Credit Sales: <b>GHS <?php echo number_format($credit_sales,2); ?></b></h4>	
	<h4>Cash Sales: <b>GHS <?php echo number_format($cash_sales,2); ?></b></h4>

	<?php $profit=$total_sales-$total_expenses;?>

	<h4>Total Sales (credit+cash): <b>GHS <?php echo number_format($total_sales,2); ?></b></h4>
	<h4>Total expenses: <b>GHS <?php echo number_format($total_expenses,2); ?></b></h4>
	<h4>Profit (TT Sales- TT Expenses):GHS <b><?php echo number_format($profit,2); ?></b></h4>
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
						 <input type="submit" class="btn btn-danger btn-block" name="filter_submit" value="Sort Expenses"/>
					   </div>
				  </div>
		</form>

							<div class="row filter-details" id="add">
							<div class="form-group">
							<h5 class="form-legend">Add New General Expense</h3>
						    </div>
						    </div>

		<!--add new general expenses-->
			<form role="form" class="filter-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

					 <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						   <label class="col-lg-3 control-label">Expense Purpose</label>
							  <div class="col-lg-9">
								<input type="text" name="expense_purpose" class="form-control"/>
							  </div>
					   </div>
					</div>

					<div class="row">
				       <div class="form-group" style="margin-top:5%;">
						  <label class="col-lg-3 control-label">Expense Amount</label>
							  <div class="col-lg-9">
								<input type="text" name="expense_amount" class="form-control"/>
							  </div>
					   </div>
					</div>

					

				  <div class="row">
				       <div class="form-group" style="margin-top:5%;">
						 <input type="submit" class="btn btn-danger btn-block" name="expense_submit" value="Add New Expense"/>
					   </div>
				  </div>
		</form>
	  </div>
	</div>

	<!--products display-->
	<div class="col-md-8 details">
		<?php echo $log_error; ?>

		<table class="table table-striped table-hover">
			<thead>
			<tr style="color:#1E8BC3;">
				<th>Expenses Id</th>
				<th>Expenses Info</th>
				<th>Expenses Amount</th>
				<th>Expenses Date</th>
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
   	
   	   $('.tr-hide').hide();

   	   $(".clickable-row").click(function(){
   	   	    $(this).next().slideToggle();
   	   });
   });
</script>
<script type="text/javascript" src="js/item_searcher.js"></script>
</body>
</html>