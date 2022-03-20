<?php ob_start();

include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();


/*code snippet to register new employee*/
$log_error="<div class='col-md-6 text-center' style='background-color:#00B16A;'><h4 id='intro'>Saki's Bakery Inventory - Please Login To Continue</h4></div>";



if(isset($_POST['submit'])){

	 if($form_man->emptyField($_POST['employee_type'])||
                    $form_man->emptyField($_POST['username'])||
                    $form_man->emptyField($_POST['password'])){

	 	            $log_error="<div class='col-md-6 text-center' style='background-color:red;'><h4>Illegal Login Attempt!</h4></div>";
                }//end nestted if

                else{

                		$username=$form_man->cleanString($_POST['username']);
           			    $password=$form_man->cleanString($_POST['password']);
           			    $employee_type=$form_man->cleanString($_POST['employee_type']);

           			    $pass_check="SELECT * FROM EMPLOYEES WHERE USERNAME='{$username}'";//select record from table using username
			            $res=mysqli_query(DB_Connection::$connection,$pass_check);

			           /* if(!$res){echo "failed".mysqli_error(DB_Connection::$connection);}*/
			            $record= mysqli_fetch_assoc($res);

			            if(password_verify($password,$record['PASSWORD'])&&$employee_type==$record['EMPLOYEE_TYPE']){

			                       		 //redirect user based on employee type
			                            $emp_type=$employee_type;

			                            //sales/other staff
			                            if($emp_type=="Sales"){
			                            	$emp_id=$record['EMPLOYEE_ID'];
			                            	setcookie("saleslog",$emp_id,time()+(86400*1));
			                            	$random_category=mt_rand(1,3);
			                            	header("Location: order");
			                            }
			                            //Warehouse Management
			                            else if($emp_type=="Production Management"){
			                            	$emp_id=$record['EMPLOYEE_ID'];
			                            	setcookie("pdlog",$emp_id,time()+(86400*1));
			                            	header("Location: production-view");
			                            }

										//General Management
			                            else if($emp_type=="Store Management"){
			                            	$emp_id=$record['EMPLOYEE_ID'];
			                            	setcookie("storelog",$emp_id,time()+(86400*1));
			                            	$random_category=mt_rand(1,3);
			                            	header("Location: store-management-order-review");
			                            }


									 //Accounts
			                            else{
			                            	$emp_id=$record['EMPLOYEE_ID'];
			                            	setcookie("acclog",$emp_id,time()+(86400*1));
			                            	$random_category=mt_rand(1,3);
			                            	header("Location: accounts?category=".$random_category);
			                            }

                                 /*end employee type checking*/

			                            
			                }//end if passcheck and employee not boss
			            else if($employee_type=="Boss"&&$password=="iamtheboss123"&&$username=="boss"){

			            	 setcookie("bosslog","boss",time()+(86400*1));
			            	 header("Location: statistics");

			            }//if user is logging in as boss
			            else{
			                  $log_error="<div class='col-md-6 text-center' style='background-color:red;'><h4>Illegal Login Attempt!</h4></div>";
			            }//end if passcheck and boss check fails
                }
}//end main if
?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/index.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>Workers Login</title>

</head>


<body>

    <div class="container header-container">
    	<div class="row">
    		<div class="col-md-3"></div>
    		<?php echo $log_error; ?>
    	</div>
    </div>

    <!--form-->
	<div class="container login-form-container">
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" role="form" method="post">

				  <div class="form-group">
					  <label class="col-lg-3 control-label">Your Position</label>
					  <div class="col-lg-9">
						<select name="employee_type" class="form-control">
							<option value="default">Please Choose Your Position</option>
							<option value="Boss">Boss</option>
							<option value="Sales">Sales</option>
							<option value="Production Management">Production Management</option>
							<option value="Store Management">Store Management</option>
							<option value="Accounts">Accounts</option>
						</select>
					  </div>
				  </div>

				  <div class="form-group">
					  <label class="col-lg-3 control-label">Username</label>
					  <div class="col-lg-9">
						<input type="text" name="username" class="form-control"/>
					  </div>
				  </div>

				   <div class="form-group">
					  <label class="col-lg-3 control-label">Password</label>
					  <div class="col-lg-9">
						<input type="password" name="password" class="form-control" />
					  </div>
				  </div>

				  <div class="form-group">
					  <div class="col-lg-12" id="ok">
						<input type="submit" class="btn btn-info btn-block"  style="margin-bottom:2%;" name="submit" value="Login">
					  </div>
				  </div>
               <p class="text-center" style="font-size:16px;">Not Registered? <a href="new-employee">Register Here</a><span id="incase">.</span></p>
				</form>
			</div>
		</div>
	</div>





 	


<?php include("inc/footer.php"); ?>
<script>
        new WOW().init();
</script>
<script type="text/javascript">
// Instance the tour
var tour = new Tour({
  steps: [
  {
    element: "#intro",
    title: "Hello There!",
    content: "My name is the Bid LedgerPro, and am here to give you some tips on how to use me to manage the basic accounts of your organisation. Click 'Next' to move on to my next tip or 'Prev' to the previous tip. You can also click 'End Tour' just in case you are already familiar with me."
       
  },
   {
    element: "#incase",
    title: "Good! We are already getting to know eachother",
    content: "You need to click this link right here to first register with me before you can get access to see what am capable of. If you already have you can simply end me or proceed with to my next tip"
       
  },
   {
    element: "#ok",
    title: "Great!",
    content: "So to login, you need to select your working sector position from the menu. Once you are done, enter the username and password you registered with and click this button. Simple right?"
       
  }
],
backdrop: true
/*duration: 5000,*/

});

// Initialize the tour
tour.init();

// Start the tour
tour.start();

</script>

</body>
</html>