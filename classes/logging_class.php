<?php

  class Log extends DB_Connection{
             
             public function log_this($order_id,$person_logging,$logging_info){

             	   //get time that changes were made
					$logging_date=strftime("%Y-%m-%d %H:%M:%S", time());
					$logging_year=date("Y",strtotime($logging_date));
					$logging_month=date("F",strtotime($logging_date));//full representation of month
					$logging_day_figure=date("j",strtotime($logging_date));
					$logging_week_day=date("l",strtotime($logging_date));//full representation of day of the week

				  	$log_query="INSERT INTO LOGS(ORD_ID,PERSON_LOGGING,LOGGING_INFO,LOGGING_DATE,LOGGING_YEAR,LOGGING_MONTH,LOGGING_DAY_FIGURE,LOGGING_WEEK_DAY) VALUES(";
				  	$log_query.="'{$order_id}',";
				  	$log_query.="'{$person_logging}',";
				  	$log_query.="'{$logging_info}',";
				  	$log_query.="'{$logging_date}',";
				  	$log_query.="'{$logging_year}',";
				  	$log_query.="'{$logging_month}',";
				  	$log_query.="'{$logging_day_figure}',";
				  	$log_query.="'{$logging_week_day}')";

				    $run_log_query=mysqli_query(parent::$connection,$log_query);




             }

  }

  class Expenses extends DB_Connection{

  	        public function add_to_expense($order_id,$expenses_name,$expenses_amount,$person_logging){

             	   //get time that changes were made
					$expenses_date=strftime("%Y-%m-%d %H:%M:%S", time());
					$expenses_year=date("Y",strtotime($expenses_date));
					$expenses_month=date("F",strtotime($expenses_date));//full representation of month
					$expenses_day_figure=date("j",strtotime($expenses_date));
					$expenses_week_day=date("l",strtotime($expenses_date));//full representation of day of the week

				  	$expenses_query="INSERT INTO EXPENSES(ORD_ID,EXPENSES_NAME,EXPENSES_AMOUNT,EXPENSES_DATE,EXPENSES_YEAR,EXPENSES_MONTH,EXPENSES_DATE_FIGURE,EXPENSES_WEEK_DAY) VALUES(";
				  	$expenses_query.="'{$order_id}',";
				  	$expenses_query.="'{$expenses_name}',";
				  	$expenses_query.="'{$expenses_amount}',";
				  	$expenses_query.="'{$expenses_date}',";
				  	$expenses_query.="'{$expenses_year}',";
				  	$expenses_query.="'{$expenses_month}',";
				  	$expenses_query.="'{$expenses_day_figure}',";
				  	$expenses_query.="'{$expenses_week_day}')";

				    $run_expenses_query=mysqli_query(parent::$connection,$expenses_query);

				    /*echo mysqli_error(parent::$connection);*/



             }

  }


?>