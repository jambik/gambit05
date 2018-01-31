$(document).ready(function(){

    $(".getProduct").on("click", ".close-similar", function (e) { 
        $(this).parent().parent().remove();    
    });
    
    $("#reg-form-send").click(function(){
        var phone = $("#reg-form-phone").val(),    
            mail = $("#reg-form-mail").val(),    
            name = $("#reg-form-name").val();    
            
            $.ajax({
                type: "POST",
                url: '/ajax/reg/',
                data: 'phone=' + phone + '&name='+name + '&mail='+mail,
                dataType: "json",
                success : function (res) {
                    if(res == 1){
                        alert("Благодарим за регистрацию. Ваш подарок добавлен в корзину!");    
                    }else {
                        alert("Ранее Вы к нам заходили и мы Вас узнали. Рады видеть Вас снова!");    
                    } 
                    $(".info-block").hide('slow');
                           
                },
           });
    });
    
    
    $(".goods").on("click", ".button-price", function (e) { 
        $(this).parent().parent().toggleClass('in-cart');
        $('#cart-button').addClass('active');
    }); 
    
    $("#setPhone").click(function(){
        var phone = $("#user_phone").val();
        $.ajax({
            type: "POST",
            url: '/ajax/auth/',
            data: 'phone=' + phone,
            dataType: "json",
            success : function (res) { 
              //  alert(res);
                $.cookie('demo_user_id_', res);
               // alert($.cookie("demo_user_id"));
                $("#registration").hide();  
                location.reload();   
            },
       });
    }); 
    
     
    $('#cart-button').click(
            function(){
                $('#cart').addClass('active');
                $('#cart-heading').addClass('active');
                $('#cart-bottom').addClass('active');
            }
        );
        $('#close-cart').click(
            function(){
                $('#cart').removeClass('active');
                $('#cart-heading').removeClass('active');
                $('#cart-bottom').removeClass('active');
            }
            );

        $('#nav-button').click(
            function(){
                $('#navmenu').addClass('active');
            }
            );
        $('#navmenu img').click(
            function(){
                $('#navmenu').removeClass('active');
            }
            );
        $('#navmenu li span').click(
            function(){
                $('#navmenu li ul').slideToggle();
            }
            );  
});

