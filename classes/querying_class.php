 <?php

      /*main querying class*/
     class DataQuery extends DB_Connection{
          
          //find all returns the whole table record
     	   public function find_all($table){//selects everything from table
     	   	  $query="SELECT * FROM ".$table." ORDER BY CATEGORY_NAME ASC";
     	   	  $results=mysqli_query(parent::$connection,$query);
               
     	   	  return $results;
     	   }
             
          //find by id returns just one row as an associative array
     	   public function find_by_id($table,$column,$id){//selects everything from table by id
     	   	$query="SELECT * FROM ".$table." WHERE ".$column."=".$id;
     	   	$results=mysqli_query(parent::$connection,$query);

         /* if(!$results){echo mysqli_error(parent::$connection);}*/

     	   	$results_set=mysqli_fetch_assoc($results);

           return $results_set;
     	   }

         //find by col returns all records
         public function find_by_col($table,$column,$id){//selects everything from table by id
          $query="SELECT * FROM ".$table." WHERE ".$column."=".$id;
          $results=mysqli_query(parent::$connection,$query);

           return $results;
         }

         //find by col returns all records
         public function find_by_col_order_by_date($table,$column,$id){//selects everything from table by id
          $query="SELECT * FROM ".$table." WHERE ".$column."=".$id." ORDER BY ORDER_DATE DESC";
          $results=mysqli_query(parent::$connection,$query);

           return $results;
         }

        //find by col_and sum returns a summation of a particular column
         public function find_by_col_and_sum($table,$column,$id,$sum_variable,$as_variable){//selects everything from table by id
          $query="SELECT SUM(".$sum_variable.") AS ".$as_variable." FROM ".$table." WHERE ".$column."=".$id;
          $results=mysqli_query(parent::$connection,$query);

           return $results;
         }

         //find by col_and sum returns a summation of a particular column
         public function find_by_col_and_sum_and_date($table,$column,$id,$sum_variable,$as_variable,$order_year,$order_month,$order_day_figure,$order_week_day){//selects everything from table by id
          $query="SELECT SUM(".$sum_variable.") AS ".$as_variable." FROM ".$table." WHERE ".$column."=".$id." AND ORDER_YEAR='{$order_year}' AND ORDER_MONTH='{$order_month}' AND ORDER_DAY_FIGURE='{$order_day_figure}' AND ORDER_WEEK_DAY='{$order_week_day}'";
          $results=mysqli_query(parent::$connection,$query);

           return $results;
         }

  
           //deleting from database
           public function delete_by_id($table,$column,$id){
                $query="DELETE FROM ".$table." WHERE ".$column."=".$id;
                $results=mysqli_query(parent::$connection,$query);

               return $results?true:false;

           }

             //running the search filter
           public function run_order_search(){
              $by_customer=$_POST['customer_id'];
              $by_driver=$_POST['driver_id'];
              $by_item_category=$_POST['item_category'];
              $by_item_name=$_POST['item_name'];
              $by_year=$_POST['year'];
              $by_month=$_POST['month'];
              $by_date=$_POST['date'];
              $by_day_of_the_week=$_POST['day_of_the_week'];

              $query="SELECT * FROM ORDERS ";
              $conditions=array();

              if($by_customer!="default"){
                $conditions[]="CUST_ID =".$by_customer;
              }
              if($by_driver!="default"){
                $conditions[]="DR_ID =".$by_driver;
              }
              if($by_item_category!="default"){
                 $conditions[]="CA_ID=".$by_item_category;

              }
               if($by_item_name!="default"){
                 $conditions[]="PRODUCT_ID='{$by_item_name}'";
              }
              if($by_year!="default"){
                 $conditions[]="ORDER_YEAR='{$by_year}'";
              }

              if($by_month!="default"){
                 $conditions[]="ORDER_MONTH='{$by_month}'";
              }
              if($by_date!="default"){
                $conditions[]="ORDER_DAY_FIGURE='{$by_date}' ";
              }

               if($by_day_of_the_week!="default"){
                $conditions[]="ORDER_WEEK_DAY='{$by_day_of_the_week}' ";
              }

              $sql=$query;

              if(count($conditions)>0){
                $sql.="WHERE ".implode(' AND ',$conditions)." ORDER BY ORDER_DATE DESC";
              }

            $result=mysqli_query(parent::$connection,$sql);

            //echo $result?"setttt":"not settt".mysqli_error(parent::$connection);
            
            return $result;

           }//function ends here



           /**************accounts filtering function*****************/
               //running the search filter
           public function run_accounts_order_search(){
              $by_customer=$_POST['customer_id'];
              $by_driver=$_POST['driver_id'];
              $by_item_category=$_POST['item_category'];
              $by_item_name=$_POST['item_name'];
              $by_year=$_POST['year'];
              $by_month=$_POST['month'];
              $by_date=$_POST['date'];
              $by_day_of_the_week=$_POST['day_of_the_week'];
              $by_payment_status=$_POST['payment_status'];
              $by_invoices=$_POST['invoices'];

              $query="SELECT * FROM ORDERS ";
              $conditions=array();

              if($by_customer!="default"){
                $conditions[]="CUST_ID =".$by_customer;
              }
              if($by_driver!="default"){
                $conditions[]="DR_ID =".$by_driver;
              }
              if($by_item_category!="default"){
                 $conditions[]="CA_ID=".$by_item_category;

              }
               if($by_item_name!="default"){
                 $conditions[]="PRODUCT_ID='{$by_item_name}'";
              }
              if($by_year!="default"){
                 $conditions[]="ORDER_YEAR='{$by_year}'";
              }

              if($by_month!="default"){
                 $conditions[]="ORDER_MONTH='{$by_month}'";
              }
              if($by_date!="default"){
                $conditions[]="ORDER_DAY_FIGURE='{$by_date}' ";
              }

               if($by_day_of_the_week!="default"){
                $conditions[]="ORDER_WEEK_DAY='{$by_day_of_the_week}' ";
              }

               if($by_payment_status!="default"){
                $conditions[]="PAYMENT_STATUS=".$by_payment_status;
              }

               if($by_invoices!="default"){
                $conditions[]="INVOICE_NUMBER='{$by_invoices}'";
              }

              $sql=$query;

              if(count($conditions)>0){
                $sql.="WHERE ".implode(' AND ',$conditions)." ORDER BY ORDER_DATE DESC";
              }

            $result=mysqli_query(parent::$connection,$sql);

            //echo $result?"setttt":"not settt".mysqli_error(parent::$connection);
            
            return $result;

           }//function ends here


            /**************expenses filtering function*****************/
               //running the search filter
           public function expenses_search(){
              $by_year=$_POST['year'];
              $by_month=$_POST['month'];
              $by_date=$_POST['date'];
              $by_day_of_the_week=$_POST['day_of_the_week'];

              $query="SELECT * FROM EXPENSES ";
              $conditions=array();

              if($by_year!="default"){
                 $conditions[]="EXPENSES_YEAR='{$by_year}'";
              }

              if($by_month!="default"){
                 $conditions[]="EXPENSES_MONTH='{$by_month}'";
              }
              if($by_date!="default"){
                $conditions[]="EXPENSES_DATE_FIGURE='{$by_date}' ";
              }

               if($by_day_of_the_week!="default"){
                $conditions[]="EXPENSES_WEEK_DAY='{$by_day_of_the_week}' ";
              }

              $sql=$query;

              if(count($conditions)>0){
                $sql.="WHERE ".implode(' AND ',$conditions)." ORDER BY EXPENSES_DATE DESC";
              }

            $result=mysqli_query(parent::$connection,$sql);

            //echo $result?"setttt":"not settt from expenses search".mysqli_error(parent::$connection);
            
            return $result;

           }//function ends here

             /**************corresponding cash and credit sales for same perios filtering function*****************/
               //running the search filter
           public function cash_credit_sales_search(){
              $by_year=$_POST['year'];
              $by_month=$_POST['month'];
              $by_date=$_POST['date'];
              $by_day_of_the_week=$_POST['day_of_the_week'];

              $query="SELECT SUM(AMOUNT_TO_BE_PAID) AS CREDIT_SALES, SUM(AMOUNT_PAID) AS CASH_SALES FROM ORDERS ";
              $conditions=array();
              
              if($by_year!="default"){
                 $conditions[]="ORDER_YEAR='{$by_year}'";
              }

              if($by_month!="default"){
                 $conditions[]="ORDER_MONTH='{$by_month}'";
              }
              if($by_date!="default"){
                $conditions[]="ORDER_DAY_FIGURE='{$by_date}'";
              }

               if($by_day_of_the_week!="default"){
                $conditions[]="ORDER_WEEK_DAY='{$by_day_of_the_week}'";
              }

              $sql=$query;

              if(count($conditions)>0){
                $sql.="WHERE ".implode(' AND ',$conditions);
              }

            $result=mysqli_query(parent::$connection,$sql);

            //echo $result?"setttt":"not settt from sales search".mysqli_error(parent::$connection);
            
            return $result;

           }//function ends here


                     /**************logs filtering function*****************/
               //running the search filter
           public function logs_search(){
              $by_year=$_POST['year'];
              $by_month=$_POST['month'];
              $by_date=$_POST['date'];
              $by_day_of_the_week=$_POST['day_of_the_week'];

              $query="SELECT * FROM LOGS ";
              $conditions=array();

              if($by_year!="default"){
                 $conditions[]="LOGGING_YEAR='{$by_year}'";
              }

              if($by_month!="default"){
                 $conditions[]="LOGGING_MONTH='{$by_month}'";
              }
              if($by_date!="default"){
                $conditions[]="LOGGING_DAY_FIGURE='{$by_date}' ";
              }

               if($by_day_of_the_week!="default"){
                $conditions[]="LOGGING_WEEK_DAY='{$by_day_of_the_week}' ";
              }

              $sql=$query;

              if(count($conditions)>0){
                $sql.="WHERE ".implode(' AND ',$conditions)." ORDER BY LOGGING_DATE DESC";
              }

            $result=mysqli_query(parent::$connection,$sql);

            //echo $result?"setttt":"not settt from expenses search".mysqli_error(parent::$connection);
            
            return $result;

           }//function ends here


              //for production managert
           public function run_order_sum_search(){
              $by_customer=$_POST['customer_id'];
              $by_driver=$_POST['driver_id'];
              $by_item_category=$_POST['item_category'];
              $by_item_name=$_POST['item_name'];
              $by_year=$_POST['year'];
              $by_month=$_POST['month'];
              $by_date=$_POST['date'];
              $by_day_of_the_week=$_POST['day_of_the_week'];

              $query="SELECT SUM(ORDERED_QUANTITY) AS ORDERED_TOTAL FROM ORDERS ";
              $conditions=array();

              if($by_customer!="default"){
                $conditions[]="CUST_ID =".$by_customer;
              }
              if($by_driver!="default"){
                $conditions[]="DR_ID =".$by_driver;
              }
              if($by_item_category!="default"){
                 $conditions[]="CA_ID=".$by_item_category;

              }
               if($by_item_name!="default"){
                 $conditions[]="PRODUCT_ID='{$by_item_name}'";
              }
              if($by_year!="default"){
                 $conditions[]="ORDER_YEAR='{$by_year}'";
              }

              if($by_month!="default"){
                 $conditions[]="ORDER_MONTH='{$by_month}'";
              }
              if($by_date!="default"){
                $conditions[]="ORDER_DAY_FIGURE='{$by_date}' ";
              }

               if($by_day_of_the_week!="default"){
                $conditions[]="ORDER_WEEK_DAY='{$by_day_of_the_week}' ";
              }

              $sql=$query;

              if(count($conditions)>0){
                $sql.="WHERE ".implode(' AND ',$conditions);
              }

            $result=mysqli_query(parent::$connection,$sql);

            //echo $result?"setttt":"not settt".mysqli_error(parent::$connection);
            
            return $result;

           }//function ends here


     

     }


     ?>