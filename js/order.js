$(document).ready(function(){


 
   $("#item_category").change(function(){
       
          var chosen_one=$(this);
          if($(this[this.selectedIndex]).val()!="default"){

                  $(".form-legend").append('<i class="fa fa-spinner fa-spin" style="color:red;"></i>');

                  var category_id=$(this[this.selectedIndex]).val();

                  $.post("inc/order_parser.php",{category_id:category_id},function(data){
                      
                     chosen_one.parent().parent().next().find('div').find('select').html('<option value="default">Please Choose The Specific Item</option>'+data);
                      $(".form-legend").html("Order Details");
                      
                  });


          }//end first if

   });


      var dynamic_form=$(".dynamic-form");
      var cloned_form=$(".item_category").clone(true,true);

       $(".add-order").click(function(e){ //on add input button click
            
            e.preventDefault();
            $(dynamic_form).append('<div class="form-group"><h3 class="form-legend">Order Details</h3><hr/></div>');
            //$(dynamic_form).append('<div class="form-group"><label class="col-lg-3 control-label">Item Category</label><div class="col-lg-9">');

            cloned_form.clone(true,true).appendTo(dynamic_form);


    });

});

