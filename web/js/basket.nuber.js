var base = [],
    basket = $("#cart-table"),
    sendBas = "";
    

function add(item){ 
   var boo = false,
       foo = false,
       x = 0,
       tmp = {};
   for(var i in base ) {    
       if(base.hasOwnProperty(i)){if(i == item.groupId){boo = true; break;}}
   };
   if(!boo){base[item.groupId] = [];}
   
   for(var j in base[item.groupId] ) { 
       
       if(base[item.groupId].hasOwnProperty(j)){ 
           x = (item.isDis == 1)?0:1;
          
            if(j == item.id && (base[item.groupId][j][x] != undefined && base[item.groupId][j][x].count > 0)){
               if(item.isDis == 1 && base[item.groupId][j][0].max >= base[item.groupId][j][0].count+item.count){
                   base[item.groupId][j][0].count = base[item.groupId][j][0].count + item.count; 
               }else{

                    item.price = item.oldPrice;
                    item.isDis = 0;
                    if(base[item.groupId][j][1].count > 0){
                     
                        base[item.groupId][j][1].count = base[item.groupId][j][1].count + item.count; 
                    }else{
                        base[item.groupId][item.id][1] = item;
                    }
                }
                foo = true; 
                break; 
            }
       }   
   }
   if(!foo){
       if(base[item.groupId][item.id] == undefined){                               
           base[item.groupId][item.id] = [{},{}];
       }
      
       if(item.isDis == 1){
           base[item.groupId][item.id][0] = item;
       }else{   
           base[item.groupId][item.id][1] = item;  
       //    alert('add'+item.bid);
       }
       base[item.groupId]["group"] = item.group;
   }
    setTotalPrice($("#total-price"));
    setTotalPrice($("#cart-sum"));
    setTotalPrice($("#checkout-price"));
   
};

function getRandom(min, max)
{
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function initBasket(){
    var item = {},
        pid =0, 
        gid = 0,
        rnd = 0,
        gName = "";
        
    $("#cart-table").find(".group-title").each(function(i,val){
        gid = $(this).data("group-id");
        gName = $(this).find("h3").html();
        
        $("#cart-table").find('.border[data-group-id="'+gid+'"]').each(function(j,v){
            pid = $(this).attr("data-item-id");
            rnd = getRandom(1,100);
            item = {
                price   : parseInt($(this).find(".price").html()),
                oldPrice: parseInt($(this).find(".price").attr("data-old-price")),
                name    : $("#cart-table").find('.item-name[data-item-id="'+pid+'"] > a').html(),
                rid     : rnd,
                id      : pid*rnd,
                bid     : $(this).attr("data-basket-id"),
                max     : $(this).attr("data-max-count"),
                isDis   : $(this).attr("data-is-discount"),
                count   : parseInt($(this).find(".quantity").html()),
                url     : $("#cart-table").find('.item-name[data-item-id="'+pid+'"] > a').attr("href"),
                group   : gName,
                groupId : gid
            } 
                
            add(item);            
        });
        $("#cart-table").find('.cart-mod[data-parent-id="'+item.bid+'"]').each(function(j,v){
            mid = $(this).attr("data-mod");
            rnd = getRandom(1,100);
            item = {
                price   : parseInt($(this).find(".mod-price").html()),
                name    : $(this).find(".mod-name").html(),
                rid     : rnd,
                id      : mid * rnd, 
                bid     : $(this).attr("data-basket-id"),                         
                isDis   : 0,
                parent  : $(this).attr("data-parent-id"),
                max     : $(this).attr("data-max-count"),
                count   : parseInt($(this).find(".mod-q").html()),
                url     : '',
                group   : 'modifier',
                groupId :  $(this).attr("data-prod")
            }              
            add(item);
        });    
            
    });

    render(0);    
}

function setTotalPrice(place){
    var total = 0,send = "",isDis = 0,b=0;
  //  alert(base.length);
    for(var i in base ) {
       if(base.hasOwnProperty(i) && base[i].length > 0){ 
           for(var j in base[i] ) {
               if(base[i].hasOwnProperty(j) && j > 0 && ((base[i][j][0] != undefined && base[i][j][0].count > 0) || (base[i][j][1] != undefined && base[i][j][1].count > 0))){
                   for(var x in base[i][j]){
                       if(base[i][j][x].count > 0){
                           isDis =(base[i][j][x].isDis == 1)?base[i][j][x].isDis:0;
                           total += parseInt(base[i][j][x].price)*parseInt(base[i][j][x].count);     
                           b = ( base[i][j][x].bid == undefined)?0:base[i][j][x].bid;

                           send += base[i][j][x].id/base[i][j][x].rid+"x"+isDis+"x"+base[i][j][x].count+"x"+b+"x"+base[i][j][x].rid+"-";
                       }
                   }
               }
           }
       }
    }
    if(!$("#cart-button").hasClass("active") && total>0){
        $("#cart-button").addClass("active");
    }    
    place.html(total + ' <i class="fa fa-rub">');
    setGiftPrice(total);
    sendBas = (send == "")?false:send;  
}

function setGiftPrice(total){
    $(".about-delivery").find(".col-sm-4").each(function(i,val){ 
        
    });       
}

function setRecomendedBlock(id,pid,gcount,icount){
    $.ajax({
        type: "POST",
        url: '/ajax/group/recomended',
        data: 'id=' + id+'-'+gcount+'-'+icount,
        dataType: "json",
        success : function (res) {
            var 
                ind = $('.getProduct[data-group-nuber-id="'+id+'"]').find('div[data-product-id="'+pid+'"]').index() ;
            if(ind % 2 == 0){
                $('.getProduct[data-group-nuber-id="'+id+'"] > div:eq('+(ind+1)+')').after(res);            
            }else {
                $('.getProduct[data-group-nuber-id="'+id+'"] > div:eq('+ind+')').after(res);  
            }       
        },
   });    
}

function sendBasket(f){
    $.ajax({
        type: "POST",
        url: '/ajax/basket/set',
        data: 'basket=' + sendBas,
        dataType: "json",
        success : function (res) { 
            if(res.w == 1){
                $("#registration").show();
            }    
            if(res.gift != ""){
                $('#cart-table > tbody > tr:last').after(res.gift);
            }
            if(f != 0 ){
                base[f][res.inf.id][res.inf.isDis].bid = res.inf.bid;
                base[f][res.inf.id][res.inf.isDis].parent = res.inf.parent;
                render(0);
            }
            
        },
   });
}

function render(f){
    var pes = "";
    for(var i in base ) {
        if(base.hasOwnProperty(i) && base[i].length > 0){
            if(base[i]["group"] != 'modifier'){
                pes += "<tr class='group-title' data-group-id='"+i+"'><td colspan='5'><h3>"+base[i]["group"]+"</h3></td></tr><tr><td colspan='2'><table class='in-tbl'>";
                for(var j in base[i] ) {
                    if(base[i].hasOwnProperty(j) && j > 0 && ((base[i][j][0] != undefined && base[i][j][0].count > 0) || (base[i][j][1] != undefined && base[i][j][1].count > 0))){
                        for(var x in base[i][j]){
                            if(base[i][j][x].count > 0){
                                var mod_txt = '', nid = base[i][j][x].id/base[i][j][x].rid;
                              
                                for(var m in base[nid]){
                                    var mod = base[nid][m][1];
                                 //  alert(mod.id);  
                                    if(mod.id !=  undefined && (mod != undefined && mod.count > 0) && mod.parent == base[i][j][x].bid){ 
                                  //      alert('add mod -' + base[i][j][x].bid + '='+mod.parent + '<'+mod.bid); 
                                        mod_txt +="<tr class='cart-mod' data-parent='"+mod.parent+"' data-prod='"+base[i][j][x].id/base[i][j][x].rid+"' data-mod='"+mod.id/mod.pid+"'>"
                                            +"<td colspan='5'>"
                                                +"<span class='mod-name'>"+mod.name+"</span>"
                                                +"<i class='fa fa-minus-circle minus' data-is-dis='1' data-group-id='"+nid+"' data-item-id='"+mod.id+"' data-min-count='"+mod.min+"'></i>"
                                                +"<span class='mod-q'>"+mod.count+"</span>"
                                                +"<i data-is-dis='0' data-group-id='"+nid+"' data-item-id='"+mod.id+"' data-max-count='"+mod.max+"' class='fa fa-plus-circle plus'></i>"
                                                +"<span class='mod-price'>"+mod.price+"<i class='fa fa-rub'></i>"
                                                +"</span>"
                                            +"</td>"
                                        +"</tr>";            
                                    }
                                }
                             //   alert('next'); 
                                pes += "<tbody><tr data-max-count='"+base[i][j][x].max+"' data-is-discount='"+x+"' data-group-id='"+base[i][j][x].groupId+"' data-item-id='"+base[i][j][x].id+"'>"
                                            +"<td class='item-name'><a href='"+base[i][j][x].url+"'>"+base[i][j][x].name+"</a></td>"
                                            +"<td class='minus' data-is-dis='"+x+"' data-group-id='"+base[i][j][x].groupId+"' data-item-id='"+base[i][j][x].id+"'><i class='fa fa-minus-circle'></i></td>"
                                            +"<td class='quantity'>"+base[i][j][x].count+"</td>"
                                            +"<td class='plus' data-is-dis='"+x+"' data-group-id='"+base[i][j][x].groupId+"' data-item-id='"+base[i][j][x].id+"'><i class='fa fa-plus-circle'></i></td>"
                                            +"<td class='price'>"+base[i][j][x].price+"<i class='fa fa-rub'></i></td>"
                                            +"<td class='delete' data-is-discount='"+x+"' data-group-id='"+base[i][j][x].groupId+"' data-item-id='"+base[i][j][x].id+"' rowspan='2'><i class='fa fa-times-circle'></i></td>"
                                       +"</tr></tbody>" + mod_txt;      
                            }
                        }            
                    }
                }
            }
            pes += "</table></td></tr>";
        }    
    }
    if(pes == ''){
        pes = 'Корзина пуста';
        $('#cart-order').css('display','none')   
    } else {
        $('#cart-order').css('display','')
    }

    basket.html(pes);
    setTotalPrice($("#total-price"));
    setTotalPrice($("#cart-sum"));
    

    sendBasket(f);

};

function chCount(g,i,c,dis){                                
    if( c > 0 || (dis == 0 && base[g][i][0].count > 1) || (dis == 1 && base[g][i][1].count > 1))
       {  
        if(dis == 0 && (c == -1 || base[g][i][0].count + c <= base[g][i][0].max)){
            base[g][i][0].count += c;
        } else {
            if(base[g][i][1].count > 0){
                base[g][i][1].count += c;
            }else{
                base[g][i][1] = ObjClone(base[g][i][0]);  
                base[g][i][1].price = base[g][i][1].oldPrice;
                base[g][i][1].count = 1;
                base[g][i][1].isDis = 0;
            }
        }    
    }    
}    
function ObjClone(obj) {
    var copy = {};
    for (var key in obj) {
        copy[key] = obj[key];
    }
    return copy;
};  

function delItem(g,i,isDis){
    var x = (isDis == 1)?1:0;
    base[g][i][x] = {};
   
    if(basket.find('tr[data-group-id="'+g+'"]').length == 2){
        base[g] = [];
    }
    
}

 
$(document).ready(function(){
    $("#checkout-detail").on("click",".minus", function (e) {
         var 
            group = $(this).data("group-id"),
            isDis = $(this).data("is-discount"),
            id    = $(this).data("item-id");    
        chCount(group,id,-1,isDis);
        $(this).parent().find(".quantity").html(base[group][id][isDis].count);
        setTotalPrice($("#checkout-price"));
        render(0);       
    });
    
    $("#checkout-detail").on("click",".delete", function (e) {
        var 
            group = $(this).data("group-id"),
            isDis = $(this).data("is-discount"),
            id    = $(this).data("item-id");
              
        
        delItem(group,id,isDis);
        $(this).parent().remove();
        setTotalPrice($("#checkout-price"));
        render(0);        
    });
    
    

    $(".getRec").on("click",".basket-buy", function (e) {
        var id = $(this).attr('id'),
            rnd = getRandom(1,100),
            c = $(this).parents(".rec-list").find(".rec[data-product-id='"+id+"']");
        var item = {
            price    : $(this).find(".rec-price").html(),
            name     : $(this).parent().find('.item-name').html(),
            rid      : rnd,
            id       : id*rnd,
            group    : $(this).attr("data-group-name"),
            groupId  : $(this).attr("data-group-id"),
            url      : $(this).parent().find('a').attr("href"),
            count    : 1,
            isDis    : 0,      
        }
        item.isDis = (item.isDis == undefined)?0:item.isDis;
        add(item); 
        render(item.groupId);         
    });
    
    $("#checkout-detail").on("click",".plus", function (e) {
        var 
            group = $(this).data("group-id"),
            isDis = $(this).data("is-discount"),
            id    = $(this).data("item-id");
          
        chCount(group,id,1,isDis);
        $(this).parent().find(".quantity").html(base[group][id][isDis].count);
        setTotalPrice($("#checkout-price"));
        render(0);
        
    });
    
    $(basket).on("click",".minus", function (e) {
        var 
            group = $(this).data("group-id"),
            dis = $(this).data("is-dis"),
            id    = $(this).data("item-id");
        
     
        chCount(group,id,-1,dis);
        render(false);    
    });
    
    $(basket).on("click",".delete", function (e) {
        var 
            group = $(this).data("group-id"),
            isDis = $(this).data("is-discount"),
            id    = $(this).data("item-id");  
       
        delItem(group,id,isDis);
        render(0);    
                 
    });
    $(basket).on("click",".plus", function (e) {
        var 
            group = $(this).data("group-id"),
            dis = $(this).data("is-dis"),
            id    = $(this).data("item-id");
            
        chCount(group,id,1,dis);
        render(0);    
    });

    $(".getProduct").on('click','.mod_add',function(e){
            mid = $(this).attr('id');
            var rnd = getRandom(1,100);
            item = {
                price   : parseInt($(this).parent().parent().find('h5').html()),
                name    : $(this).parent().parent().find('h6').html(),
                rid      : rnd,
                id       : mid*rnd,                         
               // bid     : $(this).attr("data-basket-id"),                         
                isDis   : 0,
              //  parent  : $(this).attr("data-parent-id"),
                count   : 1,
                url     : '',
                max      : $(this).attr("data-max-count"),
                group   : 'modifier',
                groupId :  $(this).attr("data-prod")
            }               
            add(item);  
            
            render(item.groupId); 
    });
    
    $(".getMoment").on('click','.cart-buy',function(e){
                                                                   
        var id = $(this).attr("id"),
            rnd = getRandom(1,100);
        var item = {
            price    : $(this).parent().find(".price-moment > span").html(),
            oldPrice : $(this).parent().find(".old-price").html(),
            name     : $(this).parent().find("a > h4").html(),
            rid      : rnd,
            id       : id*rnd,
            max      : $(this).parents(".item-moment").attr("data-max-basket-count"),
            isDis    : $(this).parents(".item-moment").attr("data-is-discount"),
            count    : 1,
            url      : $(this).parent().find("a").attr("href"),
            group    : $(this).attr("data-group-name"),
            groupId  : $(this).attr("data-group-id"),
            reqcount : $(this).parents(".item-moment").attr("data-recommended-count"),
            reqitem  : $(this).parents(".item-moment").attr("data-recommended-count-item"),
        }
        item.isDis = (item.isDis == undefined)?0:item.isDis;    
        add(item); 
            
        render(item.groupId); 
 
    });
    
    $(".getProduct").on('click','.buy',function(e){

        $(this).parent().parent('.goods-item').addClass('active');
        var id = $(this).parents("article").attr("data-product-id"),
            rnd = getRandom(1,100);
        var item = {
            price    : $(this).find(".price").html(),
            oldPrice : $(this).parent().find(".old-price").html(),
            name     : $(this).parent().find("a.product-name").html(),
            rid      : rnd,
            id       : id*rnd,
            max      : $(this).parents(".item").attr("data-max-basket-count"),
            isDis    : $(this).parents(".item").attr("data-is-discount"),
            count    : 1,
            url      : $(this).parent().find("a").attr("href"),
            group    : $(this).parents(".container").find(".group-title").html(),
            groupId  : $(this).parents(".getProduct").attr("data-group-nuber-id"),
            reqcount : $(this).parents(".getProduct").attr("data-recommended-count"),
            reqitem  : $(this).parents(".getProduct").attr("data-recommended-count-item"),
        }
        item.isDis = (item.isDis == undefined)?0:item.isDis;    
        add(item); 
            
        render(item.groupId); 
 
    }); 
    
    $(".row").on("click",".rec-buy", function (e) {

        var id = $(this).attr('id'),
            rnd = getRandom(1,100),
            c = $(this).parents(".rec-list").find(".rec[data-product-id='"+id+"']");
        var item = {
            price    : $(this).find(".rec-price").html(),
            name     : c.find('h6').html(),
            rid      : rnd,
            id       : id*rnd,
            group    : $(this).attr("data-group-name"),
            groupId  : $(this).attr("data-group-id"),
            url      : c.find('a').attr("href"),
            count    : 1,
            isDis    : 0,      
        }
        item.isDis = (item.isDis == undefined)?0:item.isDis;
        add(item); 
        render(item.groupId);         
    });
    
    $(".rec").click(function(){
        var rnd = getRandom(1,100);
        var item = {
            price    : $(this).parent().find(".price").html(),
            name     : $(this).parent().find(".item-name").html(),
            rid      : rnd,
            id       : $(this).parent().attr("data-product-id")*rnd,
            group    : $(this).parent().attr("data-group-name"),
            groupId  : $(this).parent().attr("data-group-id"),
            url      : $(this).parent().find(".item-name").attr("href"),
            count    : 1,
            isDis    : 0,      
        }
        item.isDis = (item.isDis == undefined)?0:item.isDis;
        add(item); 
        render(item.groupId);        
    });
    
    $(".about-item").on("click",".button-price", function (e) {
        var rnd = getRandom(1,100);
        var item = {
            price    : $(this).find(".price").html(),
            name     : $(this).parent().find("h2").html(),
            rid      : rnd,
            id       : $(this).parent().attr("data-product-id")*rnd,
            group    : $(this).parent().attr("data-group-name"),
            groupId  : $(this).parent().attr("data-group-id"),
            url      : "",
            count    : 1,
            max      : $(this).attr("data-max-basket-count"),
            isDis    : $(this).attr("data-is-discount"),     
        }
        item.isDis = (item.isDis == undefined)?0:item.isDis;
        add(item); 
        render(item.groupId);         
    });
    
     
        $('.button-price').click(function(){
                if(!$(this).parent().hasClass('about-item')){
                    $(this).parent().parent().parent('#item-detail').addClass('active');
                }
            }
        );

    
    $(".getProduct").on("click", ".main", function (e) { 
                $(this).parent().parent().parent().parent('.list-with-triggers').addClass('main-active');
                $(this).parent().parent().parent().parent('.list-with-triggers').removeClass('adds-active');
        }
    );
    $(".getProduct").on("click", ".adds", function (e) {  
            $(this).parent().parent().parent().parent('.list-with-triggers').addClass('adds-active');
            $(this).parent().parent().parent().parent('.list-with-triggers').removeClass('main-active');
        }
    );
    
    $(".search-result").on("click",".buy",function(e){
        e.preventDefault();
        var rnd = getRandom(1,100);
        var item = {
            price    : $(this).find('span').html(),
            name     : $(this).parent().find(".search-item").html(),
            rid      : rnd,
            id       : $(this).parent().find(".search-item").attr("data-product-id")*rnd,
            group    : $(this).parent().find(".search-item").attr("data-group-name"),
            groupId  : $(this).parent().find(".search-item").attr("data-group-id"),
            url      : $(this).parent().find(".search-item").attr("href"),
            count    : 1,
            max      : $(this).parent().find(".search-item").attr("data-max-basket-count"),
            isDis    : $(this).parent().find(".search-item").attr("data-is-discount"),    
        }
        item.isDis = (item.isDis == undefined)?0:item.isDis;
        add(item); 
        render(item.groupId); 
        
    })
    
    
   initBasket();  

});