    $(document).ready(function(){
        
    var token = $("[name='_token']").val();
    //todo:image preview
    $(document).on('change','#image', function() {
        $('.error_success_msg_container').html('');
        if (this.files && this.files[0]) {
            let img = document.querySelector('.image_preview');
            img.onload = () =>{
                URL.revokeObjectURL(img.src);
            }
            img.src = URL.createObjectURL(this.files[0]);
            document.querySelector(".image_preview").files = this.files;
        }
    });


/* 
Company module.
*/
 $("#company_profile_submit").click(function(e)
        {
            e.preventDefault();

        var data = $('#company_profile').serialize(),
            form = new FormData($('#company_profile')[0]),
            id = $("[name='id']").val();
            form.append('_token',token);
            $.ajax({url: "https://ilansa.shailtech.com/admin/company/save",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        // $("#div1").html(result);
                     }}).done(function( msg ) {
                var arr = JSON.parse(msg);
                window.location.href = "https://ilansa.shailtech.com/admin/company/view/"+arr.data;
            });
        });
        
        
    /* 
Company module.
*/
 $("#support_chat_submit").click(function(e)
        {
            e.preventDefault();

        var data = $('#support_chat').serialize(),
            form = new FormData($('#support_chat')[0]),
            id = $("[name='id']").val();
            form.append('_token',token);
            $.ajax({url: "https://ilansa.shailtech.com/admin/chat/save",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        // $("#div1").html(result);
                     }}).done(function( msg ) {
                var arr = JSON.parse(msg);
                console.log(arr);
                console.log(arr.status);
                if(arr.status== 'Success'){
                    location.reload();
                }
            });
        });
        
    
    $("#company_delete").click(function(e)
        {
            e.preventDefault();

        var id = $(this).data('id'),
            form = new FormData();
            form.append('_token',token);
            form.append('id',id);
            $.ajax({url: "https://ilansa.shailtech.com/admin/company/delete",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        // $("#div1").html(result);
                     }}).done(function( msg ) {
                window.location.href = "https://ilansa.shailtech.com/admin/company/list";
            });
        });
 

/* 
Coupon module.
*/
 $("#coupon_submit").click(function(e)
        {
            e.preventDefault();

        var data = $('#coupon_create').serialize(),
            form = new FormData($('#coupon_create')[0]);
            form.append('_token',token);
            $.ajax({url: "https://ilansa.shailtech.com/admin/coupon/save",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        
                     }}).done(function( msg ) {
                         var arr = JSON.parse(msg);
                window.location.href = "https://ilansa.shailtech.com/admin/coupon/view/"+arr.data;
            });
        });
    
    $("#coupon_delete").click(function(e)
        {
            e.preventDefault();

        var form = new FormData(),
            id = $(this).data('id');
            form.append('_token',token);
            form.append('id',id);
            
            
            $.ajax({url: "https://ilansa.shailtech.com/admin/coupon/delete",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        // $("#div1").html(result);
                     }}).done(function( msg ) {
                         
                window.location.href = "https://ilansa.shailtech.com/admin/coupon/list";
            });
        });
 

/* 
Service module.
*/
 $("#service_submit").click(function(e)
        {
            e.preventDefault();

        var data = $('#service_form').serialize(),
            form = new FormData($('#service_form')[0]),
            id = $("[name='id']").val();
            form.append('_token',token);
            // data.append('image',image);
            $.ajax({url: "https://ilansa.shailtech.com/admin/service/save",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        // $("#div1").html(result);
                     }}).done(function( msg ) {
                         var arr = JSON.parse(msg);
                window.location.href = "https://ilansa.shailtech.com/admin/service/view/"+arr.data;
            });
        });
    
    $(".service_delete").click(function(e)
        {
            e.preventDefault();

        var form = new FormData(),
            id = $(this).data('id');
            form.append('_token',token);
            form.append('id',id);
            
            
            $.ajax({url: "https://ilansa.shailtech.com/admin/service/delete",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        // $("#div1").html(result);
                     }}).done(function( msg ) {
                window.location.href = "https://ilansa.shailtech.com/admin/service/list";
            });
        });
 


/* 
Sub Service module.
*/
 $("#sub_service_submit").click(function(e)
        {
            e.preventDefault();

        var data = $('#sub_service_form').serialize(),
            form = new FormData($('#sub_service_form')[0]),
            id = $("[name='id']").val();
            form.append('_token',token);
            $.ajax({url: "https://ilansa.shailtech.com/admin/sub-service/save",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        // $("#div1").html(result);
                     }}).done(function( msg ) {
                         var arr = JSON.parse(msg);
                window.location.href = "https://ilansa.shailtech.com/admin/sub-service/view/"+arr.data;
            });
        });
    
    $(".sub_service_delete").click(function(e)
        {
            e.preventDefault();

        var form = new FormData(),
            id = $(this).data('id');
            form.append('_token',token);
            form.append('id',id);
            
            $.ajax({url: "https://ilansa.shailtech.com/admin/sub-service/delete",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        
                     }}).done(function( msg ) {
                window.location.href = "https://ilansa.shailtech.com/admin/sub-service/list";
            });
        });


/* 
Product module.
*/
 $("#product_submit").click(function(e)
        {
            e.preventDefault();

        var data = $('#product_form').serialize(),
            form = new FormData($('#product_form')[0]),
            id = $("[name='id']").val();
            form.append('_token',token);
            // data.append('image',image);
            $.ajax({url: "https://ilansa.shailtech.com/admin/product/save",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        // $("#div1").html(result);
                     }}).done(function( msg ) {
                         var arr = JSON.parse(msg);
                window.location.href = "https://ilansa.shailtech.com/admin/product/view/"+arr.data;
            });
        });
    
    /* 
Product Category module.
*/
 $("#product_category_submit").click(function(e)
        {
            e.preventDefault();

        var data = $('#product_category_form').serialize(),
            form = new FormData($('#product_category_form')[0]),
            id = $("[name='id']").val();
            form.append('_token',token);
            // data.append('image',image);
            $.ajax({url: "https://ilansa.shailtech.com/admin/product-category/save",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                
                     }}).done(function( msg ) {
                         var arr = JSON.parse(msg);
                window.location.href = "https://ilansa.shailtech.com/admin/product-category/view/"+arr.data;
            });
        });
   
    
    $(".product_delete").click(function(e)
        {
            e.preventDefault();

        var form = new FormData(),
            id = $(this).data('id');
            form.append('_token',token);
            form.append('id',id);
            
            
            $.ajax({url: "https://ilansa.shailtech.com/admin/product/delete",
                    data: form,
                    method: "post",
                    cache:false,
                    contentType: false,
                    processData: false,
                     success: function(result){
                        
                     }}).done(function( msg ) {
                window.location.href = "https://ilansa.shailtech.com/admin/product/list";
            });
        });


});