<?php ob_start();

if(!isset($_COOKIE['bosslog'])){header("Location: index");}


include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();

$log_error="<h4 style='background-color:#00B16A;'>Work Force Changes</h4>	";

/*select all employees from database*/
$employees=mysqli_query(DB_Connection::$connection,"SELECT * FROM EMPLOYEES");
$employee_list="";



while($employee_results=mysqli_fetch_assoc($employees)){

	 $employee_id=$employee_results['EMPLOYEE_ID'];
     $employee_fname=ucfirst($employee_results['FIRSTNAME']);
     $employee_lname=ucfirst($employee_results['LASTNAME']);
     $employee_contact=ucfirst($employee_results['CONTACT']);
     $employee_type=ucfirst($employee_results['EMPLOYEE_TYPE']);

     $employee_list.="<tr>
							
							<td>".$employee_id."</td>
							<td>".$employee_fname."</td>
							<td>".$employee_lname."</td>
							<td>".$employee_contact."</td>
							<td>".$employee_type."</td>
							
				   </tr>";

}

//all drivers
  /*select all drivers*/
  				$drivers="";
				$read_drivers=mysqli_query(DB_Connection::$connection,"SELECT * FROM DRIVERS ORDER BY DRIVER_NAME ASC");
				while($driver_records=mysqli_fetch_assoc($read_drivers)){

					    $driver_id=$driver_records['DRIVER_ID'];
					    $driver_name=$driver_records['DRIVER_NAME'];

					    $drivers.='<option value="'.$driver_id.'">'.ucfirst($driver_name).'</option>';

				  }//end while

/*select all customers*/
					$customers="";
					$read_customers=mysqli_query(DB_Connection::$connection,"SELECT * FROM CUSTOMERS ORDER BY CUSTOMER_NAME ASC");
					while($customer_records=mysqli_fetch_assoc($read_customers)){

						    $customer_id=$customer_records['CUSTOMER_ID'];
						    $customer_name=$customer_records['CUSTOMER_NAME'];

						    $customers.='<option value="'.$customer_id.'">'.ucfirst($customer_name).'</option>';

					  }//end while


/*if isset driver change*/
if(isset($_POST['driver_change'])){

	if($form_man->emptyField($_POST['customer_id'])||$form_man->emptyField($_POST['driver_id'])){
      $log_error="<h4 style='background-color:red'>Oops! Some Expense Fields Left Blank!</h4>";
	}else{
            $customer_id=$_POST['customer_id'];
            $driver_id=$_POST['driver_id'];

            $update_query=mysqli_query(DB_Connection::$connection,"UPDATE CUSTOMERS SET DRIVE_ID=".$driver_id." WHERE CUSTOMER_ID=".$customer_id);
            $log_error="<h4 style='background-color:#00B16A;'>Driver Changed <i class='fa fa-check'></i></h4>";
	}

}//end main if

?>




<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/employee-list.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Employee List</title>

</head>


<body>

 <nav class="navbar navbar-inverse navbar-fixed-right">
	     <div class="navbar-header"> 
	        <button type="button" class="navbar-toggle collapsed" data-target="#collapsemenu" data-toggle="collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a href="#" class="navbar-brand">Work Force Management</a>
	       <!--  <a href="#" class="navbar-brand"><span><i class="fa fa-barcode fa-fw"></i></span>Nelson Storm</a> -->
	       <!-- <img src="logo.jpg" class="img-responsive img-circle"/> -->
	     </div>
	    <div class="collapse navbar-collapse pull-right" id="collapsemenu">
        <ul class="nav navbar-nav">
        	<li><a href="statistics"> <i class="fa fa-arrow-left"></i> Back To Orders</a></li>
        	<li><a href="logs"> <i class="fa fa-pencil"></i> Logs</a></li>
        	<li><a href="boss-expenses"> <i class="fa fa-file"></i> Expenses &amp; Profits</a></li>
            <li><a href="#" title="Notifications" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Boss<span class="caret"></span></a>
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
	<?php echo $log_error; ?>
	  <div class="sidebox">
			<form role="form" class="filter-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

		                  <div class="row filter-details">
									<div class="form-group">
									<h5 class="form-legend">Change Customer Driver</h3>
								    </div>
						  </div>  
			         
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
					  <div class="form-group" style="margin-top:2%;">
						  <label class="col-lg-3 control-label">New Driver</label>
						  <div class="col-lg-9">
							<select name="driver_id" class="form-control">
							<option value="default">Choose Driver</option>
							<?php echo $drivers; ?>
						   </select>
						  </div>
					   </div>
					 </div>


				<div class="row" style="margin-top:4%;">
				       <div class="form-group">
						 <input type="submit" class="btn btn-danger btn-block" name="driver_change" value="Change Driver"/>
					   </div>
				  </div>
				  
		</form>
	  </div>
	</div>

	<!--products display-->
	<div class="col-md-9 details">
		<h4 style="background-color:#00B16A;">Employee List</h4>	

		<table class="table table-striped table-hover">
			<thead>
			<tr style="color:#1E8BC3;">
				<th>Registration ID</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Contact</th>
				<th>Working Sector</th>
			</tr>
			</thead>

			<tbody>
			
			<?php echo $employee_list; ?>
			</tbody>
		</table>
	

	</div>
</div>
</div>


 	


<?php include("inc/footer.php"); ?>
<script>
        new WOW().init();
</script>
</body>
</html>