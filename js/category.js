var api_category 	= 'api/category.php';

$(document).ready(function(){
	var category_id;
	var category_name;

	autosize($('textarea'));

	$dialogForm 		= $('#dialogForm');
	$btnClose 			= $dialogForm.children('.topic').children('.btn-close-dialog');
	$btnCreate 			= $('#btnCreate');
	$btnSubmit 			= $('#btnSubmit');
	$formProgress 		= $('#formProgress');

	$dialogForm.ajaxForm({
        beforeSubmit: function(){
            $formProgress.fadeIn(100);
            $formProgress.width('0%');
            $btnSubmit.prop('disabled',true);
            $btnSubmit.addClass('loading');
            $btnSubmit.html('กำลังบันทึก');
        },
        uploadProgress: function(event,position,total,percentComplete) {
            var percent = percentComplete;
            percent = (percent * 80) / 100;
            $formProgress.animate({width:percent+'%'},100);
        },
        success: function() {
            $formProgress.animate({width:'90%'},300);
        },
        complete: function(xhr) {
        	$formProgress.animate({width:'100%'},300);
            console.log(xhr.responseText);
            console.log(xhr.responseJSON);

            setTimeout(function(){
            	location.reload();
            }, 1000);
        }
    });

	$btnCreate.click(function(){
		$dialogForm.addClass('open');
		$overlay.addClass('open');
	});

	$btnClose.click(function(){
		$dialogForm.removeClass('open');
		$overlay.removeClass('open');

		$('#category_id').val('');
	    $('#name').val('');
	    $('#desc').val('');
	    $('#url').val('');
	});

	$('.edit').click(function(e){
		e.stopPropagation();
		category_id 	= $(this).parent().attr('data-id');
		category_name 	= $(this).parent().children('.detail').children('h2').html();
		closeAllMenu();

		$submenu = $(this).children('.edit-menu');
		$submenu.show();
	});

	$('.edit').on('click','.btn-editor', function(e){

		console.log(category_id);

		$.ajax({
	        url         :api_category,
	        cache       :false,
	        dataType    :"json",
	        type        :"GET",
	        data:{
	            request 	:'get',
	            category_id	:category_id
	        },
	        error: function (request, status, error) {
	            console.log("Request Error",request.responseText);
	        }
	    }).done(function(data){
	    	console.log(data);
	    	$('#category_id').val(data.data.category_id);
	    	$('#name').val(data.data.category_name);
	    	$('#desc').val(data.data.category_desc);

	    	$dialogForm.addClass('open');
	    	$overlay.addClass('open');
	    	closeAllMenu();
	    });

	});

	$('.btn-deactive').click(function(){
		report_id = $(this).parent().attr('data-id');
		$.ajax({
			url         :api_report,
			cache       :false,
			dataType    :"json",
			type        :"POST",
			data:{
				request 	:'deactive',
				report_id	:report_id
			},
			error: function (request, status, error) {
				console.log("Request Error",request.responseText);
			}
		}).done(function(data){
			console.log(data);
			location.reload();
		});
	});

	$('.btn-active').click(function(){
		report_id = $(this).parent().attr('data-id');
		$.ajax({
			url         :api_report,
			cache       :false,
			dataType    :"json",
			type        :"POST",
			data:{
				request 	:'active',
				report_id	:report_id
			},
			error: function (request, status, error) {
				console.log("Request Error",request.responseText);
			}
		}).done(function(data){
			console.log(data);
			location.reload();
		});
	});

	$('.btn-delete').click(function(){
		console.log(category_name);

		if(!confirm('คุณต้องการลบ "'+category_name+'"ใช่หรือไม่ ?')){ return false; }

		$.ajax({
			url         :api_category,
			cache       :false,
			dataType    :"json",
			type        :"POST",
			data:{
				request 	:'delete',
				category_id	:category_id
			},
			error: function (request, status, error) {
				console.log("Request Error",request.responseText);
			}
		}).done(function(data){
			console.log(data);
			location.reload();
		});
	});

	function closeAllMenu(){
		$('.edit-menu').hide();
	}

	$(document).click(function(e){
		if (!$(e.target).closest('.edit-menu').length || !$(e.target).closest('.category-menu').length){
			closeAllMenu();
		}
	});
});