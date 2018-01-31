$(document).ready(function(){
   $(".getMoment").each(function(){
        var id = $(this).data("sale-id"),
        item = $(this); 
        
        $.ajax({
            type: "POST",
            url: '/ajax/sale/'+id+'/getItem',
            dataType: "json",
            success : function (res) {
                item.html('<h2>Блюдо дня</h2>' + res);    
            }
       });   
            
   });
   
jQuery(function($){
   $("#order_user_phone").mask("8(999) 999-99-99");
});   
   
   $(".getRec").each(function(){
       var id   = $(this).data("page-id"),
           box  = $(this).data("box"),
           item = $(this);
       
       $.ajax({
            type: "POST",
            url: '/ajax/rec-item-page',
            dataType: "json",
            data: 'box=' + box + '&page=' + id,
            success : function (res) {
                item.html('<h3>Попробуйте</h3>' + res);    
            }
       });
           
   }); 
   
   function setImgToSrc(){
       $(".getProduct img, .getProduct-go img").each(function(){
           var src = $(this).attr('data-src');
           $(this).attr('src',src);    
       });
   }
   setImgToSrc(); 
   $(".getProduct-go").each(function(){
        var id         = $(this).data("group-nuber-id"),
            count      = $(this).data("item-count"),
            catch_time = $(this).data("catch-time"),
            item       = $(this),
            more_btn   = $("a.get-more[data-group-nuber-id='"+id+"']"); 
        $.ajax({
            type: "POST",
            url: '/ajax/group/'+id+'/count/'+count+'/catch/'+catch_time,
            dataType: "json",
            success : function (res) {
                item.html(res.group + res.product);
                
                if(res.count > 0){
                    more_btn.html("Показать еще "+ res.count);
                    more_btn.attr("data-item",res.count);
                } else {
                    more_btn.hide();
                }
                
                $(".group-info").html(res.info);
                setImgToSrc();
            }
        });  
   });
   
   $("a.get-more").click(function(e){
       var list = [],
           id = $(this).data("group-nuber-id");
       e.preventDefault();
       if($(this).attr("data-open") > 0){
           if($(this).attr("data-open") == 2){
               $(".getProduct[data-group-nuber-id='"+id+"']").find(".col-sm-4").each(function(i,val){
                  if(i > 2){
                     $(val).hide();
                  }
               }); 
               $(this).attr("data-open","1"); 
               $(this).html("Показать еще "+$(this).data("item"));                 
           }else{
               $(".getProduct[data-group-nuber-id='"+id+"']").find(".col-sm-4").each(function(i,val){
                   $(val).show();
               });
               $(this).attr("data-open","2");
               $(this).html("Свернуть"); 
           }       
       } else {
           $(".getProduct[data-group-nuber-id='"+id+"']").find(".item").each(function(i,val){
                list[i] = ($(this).data("product-id")); 
           });
         
           var last_item_box = $('.item[data-product-id="'+list[list.length - 1]+'"]');
         
           $.ajax({
                type: "POST",
                url: '/ajax/group/'+id+'/get-more',
                data: 'list=' + list,
                dataType: "json",
                success : function (res) {
                    last_item_box.after(res.product);
                    setImgToSrc();
                },
           });
           $(this).attr("data-open","2");
           $(this).html("Свернуть");
       }
   });
   
   $('input:radio').click(function(){
       if($(this).hasClass('pick-up')) {
           $('.address-field').slideUp(400);
       }else{
           $('.address-field').slideDown(400);
       }
   });
   $('.more-fileds').click(function(){
       $('.detail-field').slideToggle();
   });

});