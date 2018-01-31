$(document).ready(function(){
    var numKeyStart = 4;
    
    $(document).on("keyup", "#search-input", function () {
        var numKeyCur = $('#search-input').val().length;

        if(numKeyCur >= numKeyStart){  
            $.ajax({
                type: "POST",
                url: '/ajax/product/search',
                data: 'q='+$('#search-input').val(),
                dataType: "json",
                success : function (res) {
                    $(".search-result > ul").html(res);
                    $(".search-result").show();
                }
            });
        } else {
            $(".search-result").hide();    
        }
        
    });      
    
});
