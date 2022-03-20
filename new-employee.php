<?php

include('functions.php'); 
include('conn'.DS.'db_connection.php'); 
include('classes'.DS.'querying_class.php');
include('classes'.DS.'form_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();


/*code snippet to register new employee*/
$log_error="<div class='col-md-6 text-center' style='background-color:#00B16A;'><h4>Showroom Inventory - Please Register Your Details Here</h4></div>";

if(isset($_POST['submit'])){

	 if($form_man->emptyField($_POST['firstname'])||
                    $form_man->emptyField($_POST['lastname'])||
                    $form_man->emptyField($_POST['phone'])||
                    $form_man->emptyField($_POST['employee_type'])||
                    $form_man->emptyField($_POST['username'])||
                    $form_man->emptyField($_POST['password'])||
                    $form_man->emptyField($_POST['cpassword'])

                   ){
                      $log_error="<div class='col-md-6 text-center' style='background-color:red;'><h4>There were errors in your form submission!</h4></div>";

                 }//end second if
                 else{

                      $firstname=$form_man->cleanString($_POST['firstname']);
                      $lastname=$form_man->cleanString($_POST['lastname']);	
                      $phone=$form_man->cleanString($_POST['phone']);	
                      $employee_type=$form_man->cleanString($_POST['employee_type']);
                      $username=$form_man->cleanString($_POST['username']);		
                      $password=$form_man->cleanString($_POST['cpassword']);	

                      //check whether username already exists
                      $query="SELECT * FROM EMPLOYEES WHERE USERNAME='{$username}'";
                      $results=mysqli_query(DB_Connection::$connection,$query);
                      
                      $number=mysqli_num_rows($results);

                      if($number>=1){ $log_error="<div class='col-md-6 text-center' style='background-color:red;'><h4>Your Username Is Already Taken!</h4></div>";}//end if
                      else{
                      	     $final_password=password_hash($password,PASSWORD_BCRYPT,['cost'=>11]);

                              //register the employee in database
                      	      $insert_query="INSERT INTO EMPLOYEES(EMPLOYEE_TYPE,FIRSTNAME,LASTNAME,CONTACT,USERNAME,PASSWORD) VALUES(";
                              $insert_query.="'{$employee_type}',";
                              $insert_query.="'{$firstname}',";
                              $insert_query.="'{$lastname}',";
                              $insert_query.="'{$phone}',";
                              $insert_query.="'{$username}',";
                              $insert_query.="'{$final_password}');";

							  $rex=mysqli_query(DB_Connection::$connection,$insert_query);

							  if($rex){
							  				$log_error="<div class='col-md-6 text-center' style='background-color:#00B16A;'><h4>Registration Successful <i class='fa fa-check'></i> <a href='index' style='color:white;text-decoration:underline;'>Login here</a></h4></div>";
							  }//end if query successful

                      }//emd 
                 }

}//end main if
?>

<?php include("inc/header.php"); ?>
<link rel="stylesheet" href="css/new-employee.css"/>
<link rel="stylesheet" href="css/nav.css"/>
<title>New Employee Registration</title>

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

				 <div class="form-group" >
					  <label class="col-lg-3 control-label" id="firstname">First Name</label>
					  <div class="col-lg-9">
						<input type="text" name="firstname" class="form-control"/>
					  </div>
				  </div>

				   <div class="form-group" >
					  <label class="col-lg-3 control-label" id="lastname">Last Name</label>
					  <div class="col-lg-9">
						<input type="text" name="lastname" class="form-control"/>
					  </div>
				  </div>

				   <div class="form-group">
					  <label class="col-lg-3 control-label">Contact Number</label>
					  <div class="col-lg-9">
						<input type="text" name="phone" class="form-control"/>
					  </div>
				  </div>

				  <div class="form-group" >
					  <label class="col-lg-3 control-label">Your Working Sector</label>
					  <div class="col-lg-9">
						<select name="employee_type" class="form-control" id="sector">
							<option value="default">Please Choose Your Position</option>
							<option value="Sales">Sales</option>
							<option value="Production Management">Production Management</option>
							<option value="Store Management">Store Management</option>
							<option value="Accounts">Accounts</option>
            </select>
					  </div>
				  </div>

				  <div class="form-group">
					  <label class="col-lg-3 control-label">Login Username</label>
					  <div class="col-lg-9">
						<input type="text" name="username" class="form-control"/>
					  </div>
				  </div>

				  <div class="form-group">
					  <label class="col-lg-3 control-label">Login Password</label>
					  <div class="col-lg-9">
						<input type="password" name="password" id="password" class="form-control" />
					  </div>
				  </div>

				  <div class="form-group">
					  <label class="col-lg-3 control-label">Confirm Password</label>
					  <div class="col-lg-9">
						<input type="password" name="cpassword" id="cpassword" class="form-control" />
					  </div>
				  </div>

				  <div class="form-group">
					  <div class="col-lg-12">
						<input type="submit" class="btn btn-info btn-block"  style="margin-bottom:2%;" id="submit"  name="submit" value="Register">
					  </div>
				  </div>

				  <p class="text-center" style="font-size:16px;">Already Registered? <a href="index">Login Here</a></p>
				</form>
			</div>
		</div>
	</div>





 	


<?php include("inc/footer.php"); ?>
<script type="text/javascript">
// Instance the tour
var tour = new Tour({
  steps: [
  {
    element: "#firstname",
    title: "Your Firstname",
    content: "Type your firstname in the given box"
  },
  {
    element: "#lastname",
    title: "Oh! Yes",
    content: "your last name too"
  },
  {
    element: "#sector",
    title: "The section of the organisation you work",
    content: "You can choose this from the dropdown menu here."
  },
  {
    element: "#password",
    title: "Enter your chosen login password",
    content: "You will need this to login everytime you use this software."
  }
  ,
  {
    element: "#cpassword",
    title: "Confirm login password",
    content: "Make sure you enter the same password you chose here."
  }
  ,
  {
    element: "#submit",
    title: "Awesome!",
    content: "Click here once you are done to confirm your registration."
  }
],
storage: false,
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