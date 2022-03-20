<?php 
//include database connection
include('../functions.php'); 
include('../conn'.DS.'db_connection.php'); 
include('../classes'.DS.'querying_class.php');

$connection=new DB_Connection();
$query_guy=new DataQuery();

 ?>

 <?php

 //getting the list of all institutions from a country
 if(isset($_POST['category_id'])){

 	if(!empty($_POST['category_id'])){

 		$rex="";
        $category=$_POST['category_id'];
 		$records=mysqli_query(DB_Connection::$connection,"SELECT * FROM PRODUCTS WHERE CAT_ID=".$category." ORDER BY PRODUCT_NAME ASC");

 		while($cat_array=mysqli_fetch_assoc($records)){

 			    $product_id=$cat_array['PRODUCT_ID'];
                $product_name=$cat_array['PRODUCT_NAME'];
                $product_type=$cat_array['PRODUCT_TYPE'];

 			    $rex.="<option value='{$product_id}'>".ucfirst($product_name)." - ".$product_type."</option>";
 		}

 		echo $rex;

 	}
 }


 //getting the list of all institutions from a country
 if(isset($_POST['categories'])){

 	if(!empty($_POST['categories'])){

 		/*select all product categories*/
		$categories=$query_guy->find_all("PRODUCT_CATEGORY");
		$category_list="";

		//select and display all product categories from the database
		while($category_results=mysqli_fetch_assoc($categories)){
		     $cat_name=$category_results['CATEGORY_NAME'];
		     $cat_id=$category_results['CATEGORY_ID'];

		     $category_list.='<option value="'.$cat_id.'">'.ucfirst($cat_name).'</option>';

		}


 		echo $category_list;

 	}
 }








 ?>