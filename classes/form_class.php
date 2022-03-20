 <?php 

      class FormDealer extends DB_Connection{

          //check whether field is empty
     	  public function emptyField($value){
			  	    if(empty($value)||$value==""||$value=="default"){return true;}
			       else{return false;}
               }

          //clean form input
          public function cleanString($val){
				     $cleaned= mysqli_real_escape_string(parent::$connection,$val);
				     $final=strip_tags($cleaned);

				     return $final;
               }
            
           
        /*function to print out error messages*/
            public function log_error($statement="",$color=""){
    
               if($statement=="login"&&$color==""){return  "<h3 class='form-head' style='background-color:red;'>ILLEGAL LOGIN ATTEMPT</h3>";}
               else if($statement!=""&&$color=="normal"){return "<h3 class='form-head'>".$statement."</h3>"; }
               else if($statement!=""&&$color=="red"){return "<h3 class='form-head' style='background-color:red;'>".$statement."</h3>"; }
          }


     }
   
 ?>