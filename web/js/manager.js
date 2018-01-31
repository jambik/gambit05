$(document).ready(function() {
    $("#addRecBtn").click(function(e){
        var id = $(this).attr('data-select-id');
        var group = $(this).attr("data-g-id");
        $.ajax({
            type: "POST",
            url: "/work/group/"+group+"/set/recommended",
            data: "id="+ id,
            dataType: "json",
            success : function (res) {
                if($(".rec-group > tbody > tr").length == 0){
                    $(".rec-group ").html("<tbody><tr><td width='10%'>"+res.id+"</td>"+
                                                "<td width='80%'>"+res.name+"</td>"+
                                                "<td><i class='del-rec fa fa-times' id='"+res.id+"'></i></td>"+
                                         "</tr></tbody>");
                }else{
                    $(".rec-group tr:last-child").after("<tr><td width='10%'>"+res.id+"</td>"+
                                                                "<td width='80%'>"+res.name+"</td>"+
                                                                "<td><i class='del-rec fa fa-times' id='"+res.id+"'></i></td>"+
                                                         "</tr>"); 
                }  
                $("#base-group-rec-select option:selected").remove(); 
            }
        }); 
        e.preventDefault();   
    });
    
    $("#main-order-tbl").on("click",".order-item > td",function(){ 
        var order = $(this).parent().attr('data-id'),
        user = $(this).parent().attr('data-user-id');
        $("#status-order-id").attr('data-order-id',order);

        $.ajax({
            type: "POST",
            url: '/work/order/'+order+'/getItemList/'+user,
            dataType: "json",               
            success : function (res) {        
                $('#order-table-list > tbody').html(res.list);
                
                $('.order-city').html(res.a.city);
                $('.order-street').html(res.a.street);
                $('.order-house').html(res.a.house);
                $('.order-build').html(res.a.build);
                $('.order-apartment').html(res.a.apartment);
                $('.order-comment').html(res.a.comment);
                
                $("#order-user-name").html(res.u.name);
                $("#order-user-address").html(res.u.address);
                $("#order-user-phone").html(res.u.phone);
                $("#order-is-reg").html(res.u.isReg);
                $("#order-user-reg").html(res.u.createdAt);
                $("#order-user-last-visit").html(res.u.lastVisit);
                                
                $('#order-list').modal('show')
            }
        });    
    });

    $('#product-tree-rec a').click(function(){
        var id = $(this).attr('data-group-id');
        $('#product-tree-rec a').removeClass('activeTreeItem'); 
        $(this).addClass('activeTreeItem');
        $('#addRecBtn').attr('data-select-id',id);   
    });
    
    $("#save-product-prop").click(function(){
        var id = $(this).attr('data-prod-id'),
            item = {
                id: (id == undefined)?0:id,
                seo_title: $('#card-product-seotitle').val(),
                seo_key: $('#card-product-seo-key').val(),
                seo_desc: $('#card-product-seo-desc').val(),
                seo_text: $('#card-product-seo-text').val(),
                energy: $('#card-product-energy').val(),
                carbon:$('#card-product-carbonhydrat').val(),
                fat:$('#card-product-fat').val(),
                fiber:$('#card-product-fiber').val(),
                addInfo:$('#card-product-additional-info').val(),
                name:$('#card-product-name').val(),
                code:$('#card-product-code').val(),
                alias:$('#card-product-alias').val(),
                price:$('#card-product-price').val(),
                weight:$('#card-product-weight').val(),
                desc:$('#card-product-description').val()
            };
        $.ajax({
            type: "POST",
            url: "/work/product/save/",
            dataType: "json",
            data: 'data='+JSON.stringify(item),
            success : function (res) {
                 $('#ProductModal').modal('hide');
                 //refreshProductTree(item.group);      
            }
        });
            
    });
    $('#card-block-product-tree').on("click","a",function(){  
        $('#card-block-product-tree a').removeClass('activeTreeItem');
        $(this).addClass('activeTreeItem');
        
        $('#parent-group').val($(this).html());
        $('#parent-group').attr('data-group-id',$(this).attr('data-group-id'));
        $('#card-block-img').css('display',''); 
        $('#card-block-product-tree').css('display','none');     
    });    
    $('#selected-group').val('Группа не выбрана');
    $("#group-setting").click(function(){
        var id =  $("#selected-group").attr('data-group-id');
        if(id == undefined){
            alert('Группа не выбрана');    
        }else{
            $("#save-prop").attr("data-id",id);
            $('#addRecBtn').attr("data-g-id",id); 
            var tr = "", op = "";
            $.ajax({
                type: "POST",
                url: "/work/group/"+id+"/get/recommended",
                dataType: "json",
                success : function (res) {
                    $.each(res.rec,function(i,val){
                        tr += "<tr><td width='10%'>"+val.id+"</td>"+
                               "<td width='80%'>"+val.name+"</td>"+
                               "<td><i class='del-rec fa fa-times' id='"+val.id+"'></i></td>"+
                            "</tr>";    
                    });  
                    $(".rec-group").html(tr); 
                    $("#base-group-rec-select").html(op); 
                    $('#card-group-name').val(res.data.name);
                    $('#card-group-alias').val(res.data.alias);
                    $('#card-group-description').val(res.data.desc);
                    $('#parent-group').attr('data-group-id',res.data.parent.id);
                    $('#parent-group').val(res.data.parent.name);
                    $('#card-group-additional-info').val(res.data.addInfo);
                 
                    $('#group-hide-alias-by-url').prop('checked',(res.data.hide_by_url == 1)?true:false);
                    $('#card-group-seo-title').val(res.data.seo_title);
                    $('#card-group-seo-text').val(res.data.seo_text);
                    $('#card-group-seo-key').val(res.data.seo_keys);
                    $('#card-group-seo-desc').val(res.data.seo_desc);
                    
                    $('#group-card-img').attr('src',res.data.img[0].src);
                    $('#save-group-prop').attr('data-group-id',res.data.id);
                    
                }
            }); 
            $('#ProductGroupModal').modal('show');
        }    
    });
    
    $('#open-group-tree').click(function(){
        if($('#card-block-product-tree').css('display') == 'none'){
            $('#card-block-img').css('display','none');    
            $('#card-block-product-tree').css('display','');    
        }else{
            $('#card-block-img').css('display','');    
            $('#card-block-product-tree').css('display','none');
        }        
    });
    
    $("#save-group-prop").click(function(){
        var id = $(this).attr('data-group-id'),
            group = {
                id:(id == undefined)?0:id,
                name:$("#card-group-name").val(),
                alias:$("#card-group-alias").val(),
                urlhide:($("#group-hide-alias-by-url").prop('checked') == true)?1:0,
                desc:$("#card-group-description").val(),
                addInfo:$("#card-group-additional-info").val(),
                parentGroup:$('#parent-group').attr('data-group-id'),
                img : $('#group-card-img').attr('src'),
                seo_title:$("#card-group-seo-title").val(),
                seo_keys:$("#card-group-seo-key").val(),
                seo_desc:$("#card-group-seo-desc").val(),
                seo_text:$("#card-group-seo-text").val(),
            };
           
        $.ajax({
            type: "POST",
            url: "/work/group/save/",
            dataType: "json",
            data: 'data='+JSON.stringify(group),
            success : function (res) {
                 $('#ProductGroupModal').modal('hide');    
            }
        });            
    });
    
    $("#all-checked").change(function(){
        if($(this).prop("checked")){     
            $(".ch > input").prop('checked', true);    
        }else{
            $(".ch > input").prop('checked', false);   
        }    
    });
    

    
    
    $('#product-tree a').click(function(){
        var id = $(this).attr('data-group-id');
            
            
        $('a[href$="#d"]').attr('data-toggle','tab');    
        $('a[href$="#d"]').parent().removeClass('disabled');    
        $('a[href$="#m"]').attr('data-toggle','tab');    
        $('a[href$="#m"]').parent().removeClass('disabled');
        
        $('#product-tree a').removeClass('activeTreeItem'); 
        $("#selected-group").val($(this).html()); 
        $("#selected-group").attr('data-group-id',$(this).attr('data-group-id')); 
        $(this).addClass('activeTreeItem');
        refreshProductTree(id);     
    });
    $('#product-file-img').change(function(){
        var files = this.files,    
            data = new FormData();
            
        $.each( files, function( key, value ){
            data.append( key, value );
        });
        $.ajax({
            url: '/work/catalog/upload/',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false, // Не обрабатываем файлы (Don't process the files)
            contentType: false, // Так jQuery скажет серверу что это строковой запрос
            success: function( res ){   
                $('#group-card-img').attr('src',res.files);        
            }
        });  
    });
    function refreshProductTree(id){
        var row = "",synx = "";
        $.ajax({
            type: "POST",
            url: "/work/catalog/group/"+id+"/getProduct",
            dataType: "json",
            success : function (res) {
                if(res.length > 0){
                    $.each(res,function(i,val){
                        
                       synx = (val.synx != null)?"<i class='fa fa-circle' aria-hidden='true'></i>":"<i class='fa fa-circle-o' aria-hidden='true'></i>";
                        row += "<tr data-group-id='"+val.groupId+"' data-id='"+val.id+"'><td class='ch'><input type='checkbox' id='"+val.id+"'></td><td>"+synx+"</td><td>"+val.name+"</td><td>"+val.price+"</td><td>"+val.img+"</td><td class='ch text-center'><i class='fa fa-sort-asc sort-icon' data-sort='1' aria-hidden='true'></i> "+val.orderBy+" <i class='fa fa-sort-desc sort-icon' data-sort='-1' aria-hidden='true'></i></td></tr>";    
                    });
                } else { row = "<tr><td colspan='6'>В группе нет товаров</td></tr>"; }
                $("#base-product-table > tbody").html(row);
            }
        });
    }
       
    $("#base-product-table > tbody").on("click","td:not(td.ch)",function(){
        var id = $(this).parent().attr("data-id"),
            project = $("#project_id").val()
            acc = '',co = '',isIiko='',mod_req = mod = disc = '';
            $('#save-product-prop').attr('data-prod-id',id);
        $.ajax({
            type: "POST",
            url: '/work/get/product/'+id,
            dataType: "json",
            success : function (res) {
                $('#product-card-img').attr('src',res.product.img[0].src);
                $('#card-product-name').val(res.product.name);
                $('#card-product-code').val(res.product.code);
                $('#card-product-alias').val(res.product.alias);
                $('#card-product-price').val(res.product.price);
                $('#card-product-weight').val(res.product.weight);
                $('#card-product-description').val(res.product.desc);
                isIiko = (res.product.isIiko == true)?'Из iiko':'Добавлен вручную';
                $('#isIiko').html(isIiko);
                $('#card-product-created').html(res.product.created);
                $('#card-product-cur-price').html(res.product.curPrice);
                
                $('#card-product-energy').val(res.product.energy);
                $('#card-product-carbonhydrat').val(res.product.carbon);
                $('#card-product-fat').val(res.product.fat);
                $('#card-product-fiber').val(res.product.fiber);
                $('#card-product-additional-info').val(res.product.additional);
                $('#card-product-seotitle').val(res.product.seotitle);
                $('#card-product-seo-key').val(res.product.seokeys);
                $('#card-product-seo-desc').val(res.product.seodesc);
                $('#card-product-seo-text').val(res.product.seotext);
               
                $("#addDiscount").attr("data-product-id",res.product.id);
                if(res.product.mod != 0){
                    if(res.product.mod[1].length > 0){
                        $.each(res.product.mod[1],function(i,val){
                            mod_req += "<tr><td>"+val.id+"</td><td>"+val.name+"</td><td>"+val.price+"</td><td>"+val.minAmount+"</td><td>"+val.maxAmount+"</td><td>"+val.defaultAmount+"</td></tr>";    
                        });
                    }
                    if(res.product.mod[0].length > 0){
                        $.each(res.product.mod[0],function(i,val){
                            mod += "<tr><td>"+val.id+"</td><td>"+val.name+"</td><td>"+val.price+"</td><td>"+val.minAmount+"</td><td>"+val.maxAmount+"</td><td>"+val.defaultAmount+"</td></tr>";    
                        });
                    } 
                } 
                mod_req = (mod_req == "")?'<tr><td colspan="6">Нет модификаторов</td></tr>':mod_req; 
                $('#requared-mod > tbody').html(mod_req);
                mod = (mod == "")?'<tr><td colspan="6">Нет модификаторов</td></tr>':mod;
                $('#not-requared-mod > tbody').html(mod);    
                
                if(res.product.discount.length > 0){   
                    $.each(res.product.discount,function(i,val){
                        st = (val.active == 1)?"Да":"Нет";
                        disc += "<tr class='"+val.isTrue+"'><td>"+val.id+"</td><td>"+st+"</td><td>"+val.title+"</td><td>^</td><td>"+val.percent+"%</td><td><a class='set-discount-condition' data-toggle='modal' data-target='#ProductCardDiscountCondition' data-disc-id='"+val.id+"'>Условия</a></td></tr>";
                    });     
                }
                disc = (disc == "")?'<tr><td colspan="6">Нет скидок</td></tr>':disc;
                $('#discount-table > tbody').html(disc);                                               
            }
        });
        $('#ProductModal').modal('show');
    });
    
                   
    $(".save-discount").click(function(){
        var id = $(this).attr("data-id"),
            pid = $(this).attr("data-product-id"),
            title = $("#disc-title").val(),
            percent = $("#disc-percent").val(),
            sum = $("#disc-sum").val(),
            title = $("#disc-title").val(),
            ac = $('#disc-active').prop("checked"),
            top = $('#disc-in_top').prop("checked");
    });
    
    $("#ProductModal").on("click",".add-discount-condition",function(){
        $(".save-discount-condition").attr("data-discount-id",$(this).attr('data-discount-id')); 
    });
    
    $(".save-discount-condition").click(function(){
         var id = $(this).attr("data-discount-id"),
             func = $("#cond-func").val(),
             start = $("#cond-datetime-start").val(),
             stop = $("#cond-datetime-stop").val(),
             limit = $("#cond-buy-in-basket").val(),
             repeat = $('#cond-repeat').prop("checked");
             
         $.ajax({
            type: "POST",
            url: '/work/discount/'+id+'/setCondition/',
            dataType: "json",
            data:'func='+func+'&start='+start+'&stop='+stop+'&limit='+limit+'&repeat='+repeat,
            success : function (res) {
                refreshDiscountCondList(id);   
            }
        });     
    });
    
    $(".rec-group").on("click",".del-rec",function(){
         var group = $("#save-group-prop").attr("data-group-id");
         var id = $(this).attr("id");
         var it = $(this);
         
         $.ajax({
            type: "POST",
            url: "/work/group/"+group+"/del/recommended",
            dataType: "json",
            data: "id="+ id,
            success : function (res) {
                it.parent().parent().remove();      
            }
         });    
                 
    });
    
    $("#addDiscount").click(function(){
        $("#save-add-discount").attr("data-id",0);    
        $("#save-add-discount").attr("data-product-id",$(this).attr('data-product-id'));    
    })
    
    $("#save-add-discount").click(function(){
        var id = $(this).attr("data-id"),
            pid = $(this).attr("data-product-id"),
            title = $("#card-discount-name").val(),
            percent = $("#card-discount-percent").val(),
            sum = $("#card-discount-summ").val(),
            ac = $('#card-discount-active').prop("checked"),
            top = $('#card-discount-in-top').prop("checked");
        
        $.ajax({
            type: "POST",
            url: '/product/'+pid+'/setDiscount/',
            dataType: "json",
            data:'id='+id+'&title='+title+'&percent='+percent+'&sum='+sum+'&ac='+ac+'&top='+top,
            success : function (res) {
                $('#ProductCardDiscount').modal('hide');   
                refreshDiscountList(pid); 
            }
        });            
    });     
    
    $(".disc-cond-table").on("click",".cond-delete",function(){
        var id = $(this).attr('data-cond-id');

        if(confirm('Уверены?')){
            $.ajax({
                type: "POST",
                url: '/work/condition/'+id+'/delete/',
                dataType: "json",                                                                    
                success : function (res) { 
                    refreshDiscountCondList(res); 
                }
            }); 
        }      
    });
    
    $("#discount-table").on("click",".set-discount-condition",function(){
        var id = $(this).attr('data-disc-id');
        $(".save-discount-condition").attr("data-discount-id",id); 
        $("#cond-func option[value='1']").attr('selected', 'selected');
        refreshDiscountCondList(id);        
    });
    
   
    $("#base-rec-table").on("dblclick",".page-rec-item > td",function(){ 
        var id = $(this).parent().attr('data-id');
        $(".save-page-rec").attr('data-id',id);
        var group = page = prod = '';
        var sel = '';
        $.ajax({
            type: "POST",
            url: '/work/getPbPdata/edit/',
            dataType: "json",
            data: 'id='+id,                                                                    
            success : function (res) {
                if(res.group.length > 0){
                    $.each(res.group,function(i,val){ 
                        sel = (res.setGroup == val.id)?'selected':'';
                        group += '<option '+sel+' value="'+val.id+'">'+val.name+'</option>';
                    });
                }
                $('#group-list-edit').html(group);

                $.each(res.page,function(i,val){
                    sel = (res.setPage== val.id)?'selected':'';
                    page += '<option '+sel+' value="'+val.id+'">'+val.name+'</option>';
                });
                
                $('#page-list-edit').html("<option value='0'>Все страницы</option>" + page); 
                $("#place-edit").val(res.place); 
                
                $.each(res.product,function(i,val){
                    sel = (res.setProduct== val.id)?'selected':'';
                    prod += '<option '+sel+' value="'+val.id+'">'+val.name+'</option>';
                });
                
                $('#product-list-edit').html("<option value='0'>Случайный товар</option>" + prod);                        
            }
        });
        
        
        $('#ProductByPageEdit').modal('show'); 
    });
    
    $('.save-page-rec').click(function(){
        var id = $(this).attr('data-id'),
            prefix = (id == 0)?'':'-edit',
            page = $('#page-list'+prefix).val(),
            place = $('#place'+prefix).val(),
            group = $('#group-list'+prefix).val(),
            prod = $('#product-list'+prefix).val();
    
        $.ajax({
            type: "POST",
            url: '/work/getPbPdata/save/',
            dataType: "json",      
            data:'id='+id+'&page='+page+'&place='+place+'&group='+group+'&prod='+prod,                                                              
            success : function (res) {
                window.location.href = "/work/page/item-rec/";    
            }            
        });
        
            
    });
    
    
    $('#new-page-rec').click(function(){
        var group = page = '';
        $(".save-page-rec").attr('data-id',0);
        $.ajax({
            type: "POST",
            url: '/work/getPbPdata/',
            dataType: "json",                                                                    
            success : function (res) {
                $.each(res.group,function(i,val){ 
                    group += '<option value="'+val.id+'">'+val.name+'</option>';
                });  
                $('#group-list').html(group);

                $.each(res.page,function(i,val){
                    page += '<option value="'+val.id+'">'+val.name+'</option>';
                });
                $('#page-list').html("<option value='0'>Все страницы</option>" + page); 
                $("#place").val(res.place); 
                $('#product-list').html("<option value='0'>Случайный товар</option>");                        
            }
        });
        $('#ProductByPage').modal('show');        
    });
    
    $('.page-item > td').click(function(){
        var id = $(this).parent().attr('data-id');
        
        window.location.href = "/work/page/"+id+"/";   
    });

    $("#base-order-table").on("click",".order-item > td",function(){ 
        var order = $(this).parent().attr('data-id'),
        user = $(this).parent().attr('data-user-id');
        $("#status-order-id").attr('data-order-id',order);

        $.ajax({
            type: "POST",
            url: '/work/order/'+order+'/getItemList/'+user,
            dataType: "json",               
            success : function (res) {        
                $('#order-table-list > tbody').html(res.list);
                
                $('.order-city').html(res.a.city);
                $('.order-street').html(res.a.street);
                $('.order-house').html(res.a.house);
                $('.order-build').html(res.a.build);
                $('.order-apartment').html(res.a.apartment);
                $('.order-comment').html(res.a.comment);
                
                $("#order-user-name").html(res.u.name);
                $("#order-user-address").html(res.u.address);
                $("#order-user-phone").html(res.u.phone);
                $("#order-user-reg").html(res.u.createdAt);
                $("#order-user-last-visit").html(res.u.lastVisit);
                                
                $('#order-list').modal('show')

            }
        });    
    });
     
     
    $('#group-list').change(function(){
        var id = $(this).val(),list = '';
        
        $.ajax({
            type: "POST",
            url: '/work/group/'+id+'/getProduct',
            dataType: "json",                
            success : function (res) {
                if(res != null){
                    $.each(res,function(i,val){
                        list += '<option value="'+val.id+'">'+val.name+'</option>'        
                    });  
                    $("#product-list").html("<option value='0'>Случайный товар</option>" + list);  
                } else {
                    $("#product-list").html("<option>Нет товаров</option>");  
                }
            }
        });
           
    });
    
    
    $('.change-order-status').click(function(){
        var status = $(this).data('status-id'),
            id = $('#status-order-id').attr('data-order-id'); 

            $.ajax({
                type: "POST",
                url: '/work/order/'+id+'/status',
                dataType: "json",
                data:'id='+status,               
                success : function (res) { 
                    if(res == 1){
                        $("#order-item-"+id).addClass('order-status-complete'); 
                        $("#order-item-"+id).removeClass('order-status-cancel'); 
                        $("#order-item-"+id).find('.order-status').html('Обработан');
                    } else {
                        $("#order-item-"+id).addClass('order-status-cancel'); 
                        $("#order-item-"+id).removeClass('order-status-complete');    
                        $("#order-item-"+id).find('.order-status').html('Отменен');                    
                    }
                }
            });    
    });
    
      
});

function setProductByPageWindow(id){
    var group = page = '';
    var sel = 0;
    $(".save-page-rec").attr('data-id',id);
    
        
};

function refreshDiscountCondList(id){
    var cond = '';
    $.ajax({
        type: "POST",
        url: '/work/discount/'+id+'/getDiscountCondList/',
        dataType: "json",                                                                    
        success : function (res) {
            if(res.length > 0){   
                $.each(res,function(i,val){
                    cond += "<tr><td>"+val.id+"</td><td>"+val.func+"</td><td>"+val.start+"</td><td>"+val.stop+"</td><td>"+val.sum+"</td><td>"+val.repeat+"</td><td><a class='cond-delete' data-cond-id='"+val.id+"'>X</a></td></tr>";
                });     
            }
            cond = (cond == "")?'<tr><td colspan="6">Нет условий</td></tr>':cond;
            $('.disc-cond-table > tbody').html(cond); 
        }
    });      
}

function refreshDiscountList(id){
    var disc = '';
    $.ajax({
        type: "POST",
        url: '/work/product/'+id+'/getDiscountList/',
        dataType: "json",                                                                    
        success : function (res) {
            if(res.length > 0){   
                $.each(res,function(i,val){
                    st = (val.active == 1)?"Да":"Нет";
                    disc += "<tr class='"+val.isTrue+"'><td>"+val.id+"</td><td>"+st+"</td><td>"+val.title+"</td><td>^</td><td>"+val.percent+"%</td><td><a class='set-discount-condition' data-toggle='modal' data-target='#ProductCardDiscountCondition' data-disc-id='"+val.id+"'>Условие</a></td></tr>";
                });     
            }
            disc = (disc == "")?'<tr><td colspan="6">Нет скидок</td></tr>':disc;
            $('#discount-table > tbody').html(disc); 
        }
    });    
}

