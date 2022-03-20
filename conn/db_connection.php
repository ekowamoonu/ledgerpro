<?php   
      
    
     /*Database Connection Class*/ 
     class DB_Connection{
          
        public static $connection;
        private  $host="localhost";
        private  $user="regi";
        private  $password="iluvmummy123";
        private  $name="bakerystock";

     	function __construct(){
     		self::$connection=mysqli_connect($this->host,$this->user,$this->password,$this->name);
     		//echo $this->connection?"successful":"failed";
     	}


        //specifically for building tables not running other database queries
     	public function run_table_query($query,$table_name){
     		$results=mysqli_query(self::$connection,$query);
     		echo $results?"<h1 style='color:green;'>".$table_name." TABLE CREATED</h1>":"<h1 style='color:red;'>FAILED!</h1>".mysqli_error(self::$connection);
     	}
     }

   
    $connection=new DB_Connection();


?>