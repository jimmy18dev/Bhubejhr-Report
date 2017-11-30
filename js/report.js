var api_report 		= 'api/report.php';
var api_category 	= 'api/category.php';

$(document).ready(function(){
	var report_id;
	var report_category_id;

	autosize($('textarea'));

	$createReportForm 	= $('#reportForm');
	$btnClose 			= $createReportForm.children('.topic').children('.btn-close-dialog');
	$btnCreateReport 	= $('#btnCreateReport');
	$btnSubmitReport 	= $('#btnSubmitReport');
	$formProgress 		= $('#formProgress');

	$createReportForm.ajaxForm({
        beforeSubmit: function(){
            $formProgress.fadeIn(100);
            $formProgress.width('0%');
            $btnSubmitReport.prop('disabled',true);
            $btnSubmitReport.addClass('loading');
            $btnSubmitReport.html('กำลังบันทึก');

            // $createReportForm.clearForm();
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

	$('#btnCreateReport').click(function(){
		$createReportForm.addClass('open');
		$overlay.addClass('open');
	});

	$btnClose.click(function(){
		$createReportForm.removeClass('open');
		$overlay.removeClass('open');

		$('#report_id').val('');
	    $('#name').val('');
	    $('#desc').val('');
	    $('#url').val('');
	});

	$('.edit').click(function(e){
		e.stopPropagation();
		report_id = $(this).parent().attr('data-id');
		closeAllMenu();

		$submenu = $(this).children('.edit-menu');
		$submenu.show();
	});

	$('.edit').on('click','.btn-editor', function(e){

		$.ajax({
	        url         :api_report,
	        cache       :false,
	        dataType    :"json",
	        type        :"GET",
	        data:{
	            request 	:'get',
	            report_id	:report_id
	        },
	        error: function (request, status, error) {
	            console.log("Request Error",request.responseText);
	        }
	    }).done(function(data){
	    	console.log(data.data);
	    	$('#report_id').val(data.data.report_id);
	    	$('#name').val(data.data.report_name);
	    	$('#desc').val(data.data.report_desc);
	    	$('#url').val(data.data.report_url);

	    	$createReportForm.addClass('open');
	    	$overlay.addClass('open');
	    	closeAllMenu();
	    });

	});

	$('.edit').on('click','.op-items', function(e){
		e.stopPropagation();

		$progressbar.fadeIn(300);
		$progressbar.width('0%');
		$progressbar.animate({width:'70%'},500);

		var op = $(this).attr('data-op');

		console.log(report_id+'/'+op);

		if(op != ''){
			$.ajax({
		        url         :api_report,
		        cache       :false,
		        dataType    :"json",
		        type        :"POST",
		        data:{
		            request 	:op,
		            report_id	:report_id
		        },
		        error: function (request, status, error) {
		            console.log("Request Error",request.responseText);
		        }
		    }).done(function(data){
		    	console.log(data);
		    	location.reload();
		    });
		}
	});

	$('.category-menu').on('click','.category-create', function(e){
		e.stopPropagation();

		$inputtext = $(this).children('input');
		$inputtext.keypress(function(e){
			if(e.which == 13) {
				var name = $(this).val();

				$.ajax({
			        url         :api_category,
			        cache       :false,
			        dataType    :"json",
			        type        :"POST",
			        data:{
			            request 	:'create_and_set',
			            name		:name,
			            report_id 	:report_id
			        },
			        error: function (request, status, error) {
			            console.log("Request Error",request.responseText);
			        }
			    }).done(function(data){
			    	location.reload();
			    });
			}
		});
	});
	$('.category-menu').on('click','.category-items', function(e){
		e.stopPropagation();

		$progressbar.fadeIn(300);
		$progressbar.width('0%');
		$progressbar.animate({width:'70%'},500);

		$current_display 	= $(this).parent().parent().children('div .current');
		var category_id 	= $(this).attr('data-id');
		var category_name 	= $(this).html();

		$.ajax({
	        url         :api_report,
	        cache       :false,
	        dataType    :"json",
	        type        :"POST",
	        data:{
	            request 	:'change_category',
	            report_id	:report_id,
	            category_id	:category_id,
	        },
	        error: function (request, status, error) {
	            console.log("Request Error",request.responseText);
	        }
	    }).done(function(data){
	    	if(category_id == 0){
	    		location.reload();
	    	}else{
	    		closeAllMenu();
	    		$current_display.html(category_name);
	    		$progressbar.animate({width:'100%'},300);
	    		$progressbar.fadeOut();
	    	}
	    });
	});

	$('.category').click(function(e){
		report_id 			= $(this).parent().parent().parent().attr('data-id');
		report_category_id 	= $(this).parent().parent().parent().attr('data-category');

		console.log(report_id,report_category_id);

		e.stopPropagation();
		closeAllMenu();
		$submenu = $(this).children('.category-menu');
		var html = '';

		$.ajax({
	        url         :api_category,
	        cache       :false,
	        dataType    :"json",
	        type        :"GET",
	        data:{
	            request 	:'list_all',
	        },
	        error: function (request, status, error) {
	            console.log("Request Error",request.responseText);
	        }
	    }).done(function(data){
	    	console.log(data);

	    	html +='<div class="items category-create"><input type="text" placeholder="สร้างใหม่..."></div>';

	    	$.each(data.dataset,function(k,v){
	    		if(report_category_id == v.id){
	    			html +='<div class="items category-items selected" data-id="'+v.id+'">'+v.name+'</div>';
	    		}else{
	    			html +='<div class="items category-items" data-id="'+v.id+'">'+v.name+'</div>';
	    		}
	    	});

	    	html +='<div class="items category-items" data-id="0">ลบหมวดออก</div>';

	    	$submenu.html(html);
	    	$submenu.show();
	    });
	});

	function closeAllMenu(){
		$('.category-menu').hide();
		$('.category-menu').html('กำลังโหลด<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>');
		$('.edit-menu').hide();
	}

	$(document).click(function(e){
		if (!$(e.target).closest('.edit-menu').length || !$(e.target).closest('.category-menu').length){
			closeAllMenu();
		}
	});
});