var base = [],
    table = $('#cart-table'),
    totalSum,
    listForTotalPrice = ['#total-price','#cart-sum'],
    listHideOnBasketEmpty = ['#cart-order'],
    date = new Date();

function orderSubmit(){
    var add = '';
    if($('#co-persons').val() !=''){
        add = 'Количество персон: '+$('#co-persons').val()+'; ';
    }                        
    if($('#co-time').val() != ''){
        add += 'Желаемое время доставки: ' + $('#co-time').val()+';';
    }   
    if(add != ''){
        $('#order_comment').val($('#order_comment').val() + ' ' + add);     
    }                                   
    base = [];
    fix();     
    $('#order').slideUp(1);
    $('#cart .checkout-page').slideUp(1);
    $('.checkout-send').slideDown(400);
    $('form[name="order"]').submit();
}
    
$('#checkout-button').click(function(){
    if($('.address-field').is(':visible')){
        if($('#order_user_name').val() != ''){
            if($('#order_user_phone').val() != ''){
                if($('#order_user_street').val().replace(/\s+/g, '') != ''){
                    if($('#order_user_house').val().replace(/\s+/g, '') != ''){
                       orderSubmit();
                    } else { alert('Пожалуйста, укажите дом!'); }    
                } else { alert('Пожалуйста, укажите улицу!'); }
            } else { alert('Пожалуйста, укажите телефон!'); }    
        } else { alert('Пожалуйста, укажите своё имя!'); }    
    } else {
        if($('#order_user_name').val() != ''){
            if($('#order_user_phone').val() != ''){  
               orderSubmit();
            } else { alert('Пожалуйста, укажите телефон!'); }    
        } else { alert('Пожалуйста, укажите своё имя!'); }       
    }
});

function checkBasket(){
    if($.cookie('send') == undefined){$.cookie('send', 'false',{path: '/'});}
    var send = ($.cookie('send') != undefined)?$.cookie('send'):false,
        se = [], s= [], it = '',count = 0; 
    if($.cookie('demo_user_id') == undefined){
        return 1;    
    } 
    if(send != false){
        var sen = send.substring(0, send.length - 1).split('-');
        $.each(sen,function(i,j){
            se = j.split('x');
            if(se[4] == 0){
                s.push(se[0]+'x'+se[1]);
            }
        }); 
        $("#cart-table").find(".group-title").each(function(i,val){
            $("#cart-table").find('.border[data-group-id="'+$(this).data("group-id")+'"]').each(function(j,v){
                it = $(this).attr("data-item-id")+'x'+$(this).find(".quantity").html();
                count++;
                if($.inArray(it,s) == -1){    
                    synx(false);   
                };    
            });    
        });
        if(count != s.length){  
            synx(true); 
        } else { 
            return 1;
        }
    } 
      
    return 0;
}
    
function initBase(){
    if(checkBasket() == 1){ 
        $("#cart-table").find(".group-title").each(function(i,val){
            gid = $(this).data("group-id");
            gName = $(this).find("h3").html();

            $("#cart-table").find('.border[data-group-id="'+gid+'"]').each(function(j,v){
                id = $(this).attr("data-item-id");
                bid = $(this).attr("data-basket-id");
                var mods = [];
                $("#cart-table").find('.cart-mod[data-parent-id="'+bid+'"]').each(function(j,v){
                    mid = $(this).attr("data-mod");
                    mod = {
                        price   : parseInt($(this).find(".mod-price").html()),
                        name    : $(this).find(".mod-name").html(),
                        id      : mid,
                        baseId: $(this).attr("data-base-id"),                        
                        count   : parseInt($(this).find(".mod-q").html()),   
                        itemId  : id,     
                        groupId :  gid
                    }
                    mods.push(mod);              
                });
                
                item = {
                    id:id,
                    groupId: gid,
                    groupName: gName,
                    price: parseInt($(this).find(".price").html()),
                    count: parseInt($(this).find(".quantity").html()),
                    href : $("#cart-table").find('.item-name[data-item-id="'+id+'"] > a').attr("href"),
                    isGift: $("#cart-table").find('.item-name[data-item-id="'+id+'"] > a').attr("data-is-gift"),
                    name: $("#cart-table").find('.item-name[data-item-id="'+id+'"] > a').html(),
                    baseId: $(this).attr("data-base-id"),
                    mod:mods  
                }
                addItem(item,true);
            });
        });  
    } else {
        synx(true)
    } 
}

function draw(){
    var row = '';
        totalSum = 0,
        modText = '',
        curPriceWithMod = 0,
        price = '' ;    
    if(base.length != 0){
        $.each(base,function(i,a){

            $.each(a.items,function(j,b){
                totalSum += b.count*b.price;
                curPriceWithMod = parseInt(b.price);
                modText = '';
                if(b.mod.length > 0){
                    $.each(b.mod,function(q,m){
                        totalSum += m.count * m.price; 
                        modText  += m.name + ', ';
                        curPriceWithMod += parseInt(m.price);    
                    });
                    modText = modText.charAt(0).toUpperCase() + modText.substr(1).toLowerCase();
                }
                modText = (modText != '')?'<br><span class="properties">'+modText.substring(0, modText.length - 2)+'</span>':'';
                if(!b.isGift){
                    row += '<tr data-base-id="'+b.baseId+'" data-item-id="'+b.id+'" data-group-id="'+b.groupId+'">'
                                +'<td class="item-name">'
                                    +'<a href="'+b.href+'">'+b.name+'</a>'
                                    +modText
                                +'</td>'
                                +'<td class="minus">'
                                    +'<i class="fa fa-minus-circle"></i>'
                                +'</td>'
                                +'<td class="quantity">'+b.count+'</td>'
                                +'<td class="plus">'
                                    +'<i class="fa fa-plus-circle"></i>'
                                +'</td>'
                                +'<td class="price">'
                                    +curPriceWithMod
                                    +'<i class="fa fa-rub"></i>'
                                +'</td>'
                                +'<td class="delete" >'
                                    +'<i class="fa fa-times-circle"></i>'
                                +'</td>'
                            +'</tr>';
                } else {
                    row += '<tr data-base-id="'+b.baseId+'" data-item-id="'+b.id+'" data-group-id="'+b.groupId+'">'
                                +'<td class="item-name">'
                                    +'<a href="'+b.href+'">'+b.name+'</a>'
                                    +modText
                                +'</td>'
                                +'<td class="quantity" colspan="3">'+b.count+'</td>'
                                +'<td class="gift">в подарок</td>';
                                +'<td></td>'
                            +'</tr>';                    
                }   
            });    
        }); 
    } else {
        row = 'Корзина пуста';
    }
    table.html(row);
    setTotalPrice(totalSum);        
    synx(false);
}

function fix(){
    var send = sMod = '';  
    if(base.length > 0){
        $.each(base,function(i,a){
            $.each(a.items,function(j,b){
                sMod = '';
                if(b.mod.length > 0){
                    $.each(b.mod,function(q,m){ 
                        sMod += m.id+'x'+b.count+'x'+m.baseId+'x'+b.baseId+'x'+b.id+'-';
                    });  
                }
                send += b.id + 'x'+b.count+'x'+b.baseId+'x0x0'+'-'+sMod;
            });
        });
    } else {
        send = false;
    } 
    date.setTime(date.getTime() + (3 * 60 * 1000));
    $.cookie('send', send,{path: '/',  expires: date});
}

function synx(reload){ 
    var s  = ($.cookie('send') != undefined)?$.cookie('send'):false,
        u  = ($.cookie('demo_user_id') != undefined)?$.cookie('demo_user_id'):false;
        //alert($.cookie('demo_user_id'));   
    $.ajax({
        type: "POST",
        url: '/ajax/basket/set',
        data: 'basket=' + s + '&user='+u,
        dataType: "json",
        success : function (res) { 
            if(res.w == 1 && $.cookie('get_phone') == undefined){
                $("#registration").show();
            }    
            if(res.gift.txt != ""){
                $('#cart-table > tbody > tr:last').after(res.gift.txt);
            }
            if(res.auth > 0){
                date.setTime(date.getTime() + (1 * 30 *24 * 60 * 60 * 1000));
                $.cookie('demo_user_id', res.auth,{path: '/',  expires: date});

            }
            if(reload){ location.reload(); }
            return 1;         
        },
   });
   return 1;       
}

function setTotalPrice(total){       
    $.each(listForTotalPrice,function(i,val){
        if(!$("#cart-button").hasClass("active") && total>0){
            $("#cart-button").addClass("active");
        } else if(total == 0){
            $("#cart-button").removeClass("active");
        }
        $(val).html(Math.round(total)+' <i class="fa fa-rub"></i>'); 
        hideByBasketEmpty(total)   
    });
}

function changeItemCount(item,count){
    $.each(base,function(i,a){
        if(a.id == item.groupId){
            $.each(a.items,function(j,b){        
                if(b.baseId == item.baseId){
                     if((b.count != 1 || count != -1)){
                        b.count = b.count + count;
                        if(b.mod.length > 0){
                            $.each(b.mod, function(q,m){
                                m.count = m.count + count;    
                            });
                        }
                     }
                }
            });
        }
    });
    fix();
    draw();    
}

function hideByBasketEmpty(totalSum){
    if(base.length > 0 && totalSum > 0){
        $.each(listHideOnBasketEmpty,function(i,val){
            $(val).removeClass('hide');
        });        
    }else{
        $.each(listHideOnBasketEmpty,function(i,val){
            $(val).addClass('hide');
        });
    }   
}

function addMod(mod){
    $.each(base,function(i,a){
        if(a.id == mod.groupId){
            $.each(a.items,function(j,b){
                if(b.id == mod.itemId){
                    b.push(mod);    
                }    
            });             
        }        
    }); 
}    

function removeItem(item){
    $.each(base,function(i,a){
        if(a.id == item.groupId){

            var tmp = [];
            $.each(a.items,function(j,b){  
   
                if(b.baseId != item.baseId){
                   
                    tmp.push(b);    
                }    
            });
            a.items = tmp;
        }        
    }); 

    clearBase();
  
    fix();    
} 

function findMaxId(group){
    var max = 0;
    $.each(group.items,function(i,val){
        var cBid = parseInt(val.baseId);
        max = (max < cBid+1)?cBid+1:max;  
    })
    return max;
} 

  
    
function addItem(add,init){
    var isAdd = false;
    $.each(base,function(i,val){
        if(val.id == add.groupId){
            if(add.mod.length > 0){
                add.baseId = (init)?add.baseId:findMaxId(val);
                val.items.push(add);
                isAdd = true;
            } else {
                $.each(val.items,function(j,al){
                    if(al.id == add.id){
                        al.count += 1;
                        isAdd = true;
                    }
                });
                if(!isAdd){
                    add.baseId = (init)?add.baseId:findMaxId(val); 
                    val.items.push(add);  
                    isAdd = true; 
                }
            }
        }
    });
    if(!isAdd){
        var newGroup = {
            id:add.groupId,
            name:add.groupName,
            items:[]    
        };
        add.baseId = (init)?add.baseId:0;
        newGroup.items.push(add);
        addGroup(newGroup);
    } 
    fix(); 
             
}    
    
function addGroup(add){
    base.push(add);
    clearBase();
    fix();
}

function clearBase(){
    var tmp = [];
    $.each(base,function(i,val){
        if(val.items.length != 0){
            tmp.push(val);    
        }    
    });
    
    base = tmp;
}

function getRandom(min, max)
{
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function userRegistration(){
    var phone = $("#reg-form-phone").val(),    
        mail = $("#reg-form-mail").val(),    
        name = $("#reg-form-name").val();    

        $.ajax({
            type: "POST",
            url: '/ajax/reg/',
            data: 'phone=' + phone + '&name='+name + '&mail='+mail,
            dataType: "json",
            success : function (res) {
                if(res.data == 1){
                    var date = new Date();
                    date.setTime(date.getTime() + (1 * 30 *24 * 60 * 60 * 1000));
                    if(res.gift.id > 0){
                        $.cookie('demo_user_id', res.user,{path: '/',  expires: date});
                        var item = {
                            price    : parseInt(res.gift.price),              
                            name     : res.gift.name,
                            id       : res.gift.id,
                            count    : 1,
                            isGift   : 1,
                            href     : res.gift.href,
                            group    : res.gift.group,
                            groupId  : res.gift.groupId,
                            mod : []       
                        } 
                        addItem(item,false);
                        draw(); 
                        alert("Благодарим за регистрацию. Ваш подарок добавлен в корзину!"); 
                    } else {
                        $.cookie('demo_user_id', res.user,{path: '/',  expires: date});
                        alert("Благодарим за регистрацию!");  
                    }

                }else {
                    $.cookie('demo_user_id', res.user,{path: '/',  expires: date});
                    alert("Ранее Вы к нам заходили и мы Вас узнали. Рады видеть Вас снова!"); 
                    $('.success > p').html('Рады видеть вас снова!');   
                } 
            },
        });
    }

$(document).ready(function(){
    
    $("#reg-form-send").click(function(){             
        if($('#reg-form-mail').val().length > 7 && $('#reg-form-mail').val().indexOf('@') > 0){
            userRegistration();  
        } else {
             alert('E-mail адресс указан не корректно!');    
        }  
    });
    
    $("#OrderRepeat").click(function(e){
        $.ajax({
            type: "POST",
            url: '/ajax/last/order/repeat',
            dataType: "json",
            success : function (res) {
                if(res.r == 1){
                    date.setTime(date.getTime() + (3 * 60 * 1000));
                    $.cookie('send', res.send,{path: '/',  expires: date});
                    location.reload();
                } else {
                    alert('К сожалению нам не удалось найти ваш предыдущий заказ');
                }
            }
            
        }); 
    });
    
    $(table).on('click','.delete',function(e){
        var item = {
            id: $(this).parent('tr').attr('data-item-id'),
            groupId:$(this).parent('tr').attr('data-group-id'),
            baseId:$(this).parent('tr').attr('data-base-id')
        };
        removeItem(item);
        draw();    
    });

    $(table).on('click','.plus',function(e){
        var item = {
            id: $(this).parent('tr').attr('data-item-id'),
            groupId:$(this).parent('tr').attr('data-group-id'),
            baseId:$(this).parent('tr').attr('data-base-id')
        };
        changeItemCount(item,1);
        //draw();    
    });

    $(table).on('click','.minus',function(e){
        var item = {
            id: $(this).parent('tr').attr('data-item-id'),
            groupId:$(this).parent('tr').attr('data-group-id'),
            baseId:$(this).parent('tr').attr('data-base-id') 
        };
        changeItemCount(item,-1);
        //draw();    
    });
    
    $(".getProduct,#item-detail,.recommend,#search-box").on('click','.buy',function(e){
        if($(this).hasClass('blink')){
            window.location.href = $(this).attr('data-href');
            return false;
        }
        
        var mods = []; 
        var info = $(this).parent().find('.product-info');
        
        var mod = $(this).parent().parent().find('.mods').find('.active');
            if(mod.attr("data-mod-id") > 0){
                var mod = {
                    id: mod.attr("data-mod-id"),
                    baseId:getRandom(1,10000),
                    itemId:mod.attr("data-product-id"),
                    groupId: mod.attr("data-product-parent-id"),
                    name: mod.attr('data-mod-name'),
                    price: mod.attr('data-mod-price'),
                    count:1
                }
                mods.push(mod);   
            } 
       
            var item = {
                price    : parseInt($(info).attr("data-product-price")),              
                name     :  $(info).attr("data-product-name"),
                id       : $(info).attr("data-product-id"),
                
                count    : 1,
                href      : $(info).attr("data-product-href"),
                group    : $(info).attr("data-product-parent-name"),
                groupId  : $(info).attr("data-product-parent-id"),
                mod : mods       
            } 
            addItem(item,false);
            draw();
    });
    
    setInterval(function() {   
        var u  = ($.cookie('demo_user_id') != undefined)?$.cookie('demo_user_id'):false;
        if(u){
            $.ajax({
                type: "POST",
                url: '/ajax/user/ping',                         
                dataType: "json",
                success : function (res) {
                    
                }
            });  
        }  
    }, 60000);
    
    initBase();
    draw();
    
    $(document).keypress(function(e) {
        if(e.which == 13) {
            if($('#reg-form-phone').is(':focus')){
                if($('#reg-form-phone').val().length == 0){
                    alert('Номер указан не корректно!');
                    $('.icon-paper-plane').removeClass("next");
                } else {
                    $('.email-section').addClass("fold-up");
                    $('.password-section').removeClass("folded");
                    $('#reg-form-name').focus();
                }
            }  
            if($('#reg-form-name').is(':focus')){
                
                if($('#reg-form-name').val().length > 0){
                    $('.password-section').addClass("fold-up");
                    $('.repeat-password-section').removeClass("folded"); 
                    $('#reg-form-mail').focus();                       
                } 
            }
            
            if($('#reg-form-mail').is(':focus')){
                if ($('#reg-form-mail').val().length > 0){
                    if($('#reg-form-mail').val().length > 7 && $('#reg-form-mail').val().indexOf('@') > 0){
                        userRegistration();    
                        $('.repeat-password-section').addClass("fold-up");
                        $('.success').css("marginTop", 0);
                    } else {
                        alert('E-mail адресс указан не корректно!');
                    }   
                } 
            }
        }
    });
    

    $('.email').on("change keyup paste",function(){
        if($(this).val().indexOf('_') > 0){
           $('.icon-paper-plane').removeClass("next");
        } else {
           $('.icon-paper-plane').addClass("next");
        } 
    });

    $('.next-button.email').click(
      function(){
        $('.email-section').addClass("fold-up");
        $('.password-section').removeClass("folded");
      }
    );

    $('.password').on("change keyup paste",
      function(){
        if($(this).val()){
          $('.icon-lock').addClass("next");
        } else {
          $('.icon-lock').removeClass("next");
        }
      }
    );

    $('.next-button').hover(
      function(){
        $(this).css('cursor', 'pointer');
      }
    );

    $('.next-button.password').click(
      function(){
        $('.password-section').addClass("fold-up");
        $('.repeat-password-section').removeClass("folded");
      }
    );

    $('.repeat-password').on("change keyup paste",function(){
        if($('#reg-form-mail').val().length > 7 && $('#reg-form-mail').val().indexOf('@') > 0){
          $('.icon-repeat-lock').addClass("next");
        } else {
          $('.icon-repeat-lock').removeClass("next");
        }
    });

    $('.next-button.repeat-password').click(
      function(){
        $('.repeat-password-section').addClass("fold-up");
        $('.success').css("marginTop", 0);
      }
    );
});