$(document).ready(function(){


 
   $("#item_category").change(function(){
       
          var chosen_one=$(this);
          if($(this[this.selectedIndex]).val()!="default"){

                  $(".loader").html('<i class="fa fa-spinner fa-spin" style="color:red;"></i>');

                  var category_id=$(this[this.selectedIndex]).val();

                  $.post("inc/order_parser.php",{category_id:category_id},function(data){
                      
                     $("#item_name").html('<option value="default">Please Choose The Specific Item</option>'+data);
                      $(".loader").html('<i class="fa fa-check" style="color:green;"></i>');
                      
                  });


          }//end first if

   });


});

