<?php 
//include database connection
include('../functions.php'); 
include('../conn'.DS.'db_connection.php'); 
include('../classes'.DS.'querying_class.php');
include('../classes'.DS.'form_class.php');
include('../classes'.DS.'logging_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();
$form_man=new FormDealer();



 ?>

 <?php

 //getting the list of all institutions from a country
 if(isset($_POST['order_id'])&&isset($_POST['amount'])){

 	if(!empty($_POST['order_id'])&&!empty($_POST['amount'])){

 		  $amount_paid=$form_man->cleanString($_POST['amount']);
 		  $order_id=$_POST['order_id'];

		  //update ordered quanity
		  $update_query="UPDATE ORDERS SET AMOUNT_PAID=AMOUNT_PAID+".$amount_paid;
		  $update_query.=" WHERE ORDER_ID=".$order_id;
		  $run_update=mysqli_query(DB_Connection::$connection,$update_query);

		  if($run_update){

			 //update amount to be paid
			 $update_query="UPDATE ORDERS SET AMOUNT_TO_BE_PAID=AMOUNT_TO_BE_PAID-".$amount_paid;
			 $update_query.=" WHERE ORDER_ID=".$order_id;
			 $run_update=mysqli_query(DB_Connection::$connection,$update_query);
		      
		    
		     echo 1;//mysqli_error(DB_Connection::$connection);
			  

 	}
 }

}





 ?>