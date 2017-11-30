var article_id;
var youtube_key = 'AIzaSyB5KfCVIK9XviNJ9fNYWwAGhZfRskjGQ_M';
// $files = $();

$(document).ready(function(){
    var editor = new MediumEditor('.input-textarea',{
        placeholder: {
            text: 'เขียนบทความที่นี่...',
            hideOnClick: true,
        }
    });

    // EDITOR CONFIG
    $laodingbar         = $('#loading-bar');
    $saved              = $('#saved-status');
    $insertFilter       = $('#insertFilter');
    $paper              = $('#paper');
    article_id          = $('#article_id').val();
    $savedStatus        = $('#savedStatus');
    $currentCategory    = $('#currentCategory');

    function savedStatus(state){
        $('#swap').removeClass('-toggle');

        if(state)
            $savedStatus.html('<i class="fa fa-keyboard-o" aria-hidden="true"></i>');
        else
            $savedStatus.html('<i class="fa fa-check-circle" aria-hidden="true"></i>');
    }

    // Editor Control
    $btnAddFile         = $('#btn-add-file');
    $btnAddImage        = $('#btn-add-image');
    $btnAddYouTube      = $('#btn-add-youtube');

    // Attach Form.
    $attachForm         = $('#attachForm');
    $attachAlt          = $('#attachAlt');
    $attachPreview      = $('#attachPreview');
    $attachFilename     = $('#attachFilename');
    $attachLoading      = $('#attachLoading');
    $attachProgress     = $('#attachProgress');
    $btnAttachSubmit    = $('#btnAttachSubmit');
    $btnAttachClose     = $('#btnAttachClose');
    $attachFiles        = $('#attachFiles');

    $btnAddFile.click(function(){
        $attachFiles.click();
        $attachForm.addClass('-toggle');
        $insertFilter.fadeIn(100);
    });

    $btnAttachClose.click(closeAttachFile);

    function closeAttachFile(){
        $attachForm.removeClass('-toggle');
        $insertFilter.fadeOut(100);
        $btnAttachSubmit.removeClass('-active');
        $btnAttachSubmit.html('เพิ่มในบทความ');
        $attachFiles.val('');
        $attachFilename.html('เลือกไฟล์เอกสาร...') 
        $attachAlt.val('');
        $attachLoading.fadeOut(100);
        $attachProgress.fadeOut(100);
    }

    $attachPreview.click(function(){
        $attachFiles.focus().click();
    });

    $attachFiles.change(function(){
        var files   = this.files;
        var file    = files[0];
        var filename = file.name.substring(0,file.name.lastIndexOf('.'));

        console.log(file);

        $attachFilename.html(file.name);
        $attachAlt.val(filename);
        $btnAttachSubmit.addClass('-active');
    });
    $btnAttachSubmit.click(function(){
        $attachForm.submit();
    });
    $attachForm.ajaxForm({
        beforeSubmit: function(){
            $attachLoading.fadeIn(100);
            $attachProgress.fadeIn(100);
            $attachProgress.width('0%');
            $btnAttachSubmit.html('กำลังบันทึก...');
            $btnAttachSubmit.removeClass('-active');

            $attachForm.clearForm();
        },
        uploadProgress: function(event,position,total,percentComplete) {
            var percent = percentComplete;
            percent = (percent * 80) / 100;
            $attachProgress.animate({width:percent+'%'},100);
            // console.log('Upload: '+percentComplete+' %');
        },
        success: function() {
            $attachProgress.animate({width:'100%'},300);
        },
        complete: function(xhr) {
            // console.log(xhr.responseText);
            console.log(xhr.responseJSON);

            var file_id     = xhr.responseJSON.file_id;
            var filename    = xhr.responseJSON.filename;
            var alt         = xhr.responseJSON.alt;

            var html = $('<div class="file-items" id="file'+file_id+'" data-id="'+file_id+'"><div class="icon"><i class="fa fa-paperclip" aria-hidden="true"></i></div><div class="info"><input type="text" class="attach-title" value="'+alt+'" placeholder="ชื่อเอกสาร..."><div class="filename">'+filename+'<span class="btn-file-remove" title="ลบไฟล์นี้"><i class="fa fa-close" aria-hidden="true"></i>ลบไฟล์</span></div></div></div>');

            closeAttachFile();
            $('#fileList').append(html);
            $('html,body').animate({ scrollTop: $('#file'+file_id).offset().top },'slow');
        }
    });


    // Image Form
    $photoForm      = $('#photoForm');
    $imagePreview   = $('#imagePreview');
    $btnChooseImage = $('#btnChooseImage');
    $imageFiles     = $('#imageFiles');
    $imageLoading   = $('#imageLoading');
    $imageProgress  = $('#imageProgress');
    $btnImageSubmit = $('#btnImageSubmit');
    $btnImageClose  = $('#btnImageClose');
    $btnImageRotate = $('#btnImageRotate');
    $imageContainer = $('#imageContainer');

    $btnAddImage.click(function(){
        $imageFiles.focus().click();
        $photoForm.addClass('-toggle');
        $insertFilter.fadeIn(100);
    });

    $btnImageClose.click(function(){
        
        var content_id = $('#content_id').val();

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'delete_content',
                content_id  :content_id,
                article_id  :article_id,
            },
            error: function (request, status, error){
                console.log("Request Error");
            }
        }).done(function(data){
            console.log(data);
            closePhotoForm();
        });
    });

    function closePhotoForm(){
        $photoForm.removeClass('-toggle');
        $insertFilter.fadeOut(100);
        $imageLoading.fadeOut();
        $imageProgress.fadeOut();
        $btnImageSubmit.removeClass('-active');
        $btnImageRotate.removeClass('-active');
        $btnImageSubmit.html('เพิ่มในบทความ');
        $imagePreview.html('<span class="btn-choose" id="btnChooseImage"><span class="i"><i class="fa fa-picture-o" aria-hidden="true"></i></span><span class="c">เลือกไฟล์รูปภาพ...</span></span>');
    } 

    $imagePreview.click(function(){
        $imageFiles.focus().click();
    });

    $imageFiles.change(function(){
        console.log('file change!');
        var files   = this.files;
        $imagePreview.html(''); // Clear thumbnail container.

        var file    = files[0];
        var imageType = /image.*/

        console.log('file',file);

        if(!file.type.match(imageType)){
            console.log("Not an Image");
            // continue;
        }

        var image = document.createElement("img");

        image.file = file;
        $imagePreview.append(image);
        var reader = new FileReader();

        reader.onload = (function(aImg){
            return function(e){ aImg.src = e.target.result; };
        }(image));

        var ret = reader.readAsDataURL(file);
        var canvas = document.createElement("canvas");
        ctx = canvas.getContext("2d");

        image.onload= function(){ ctx.drawImage(image,100,100); }
        $photoForm.submit();
    });

    $btnImageSubmit.click(function(){

        var content_id = $('#content_id').val();

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'active_content',
                content_id  :content_id,
            },
            error: function (request, status, error){
                console.log(request, status, error);
                console.log("Request Error");
            }
        }).done(function(data){

            console.log(data);

            var content_id = data.content_id;
            var image_file = data.image_file;

            var html = $('<div class="content-box -edit" id="content'+content_id+'" data-id="'+content_id+'"><img src="image/upload/normal/'+image_file+'"><input type="text" class="input-alt" placeholder="ใส่คำอธิบายภาพ..."><div class="content-controls"><div class="btn btn-content-remove"><i class="fa fa-times" aria-hidden="true"></i></div></div></div>');

            $paper.append(html);
            closePhotoForm();
            $('html,body').animate({ scrollTop: $('#content'+content_id).offset().top },'slow');
        });
    });

    $photoForm.ajaxForm({
        beforeSubmit: function(){
            $imageLoading.fadeIn(100);
            $imageProgress.fadeIn(100);
            $imageProgress.width('0%');
            $btnImageSubmit.removeClass('-active');

            $photoForm.clearForm();
        },
        uploadProgress: function(event,position,total,percentComplete) {
            var percent = percentComplete;
            percent = (percent * 80) / 100;
            $imageProgress.animate({width:percent+'%'},100);
            
            console.log('Upload: '+percentComplete+' %');
        },
        success: function() {
            $imageProgress.animate({width:'100%'},300);
        },
        complete: function(xhr) {
            console.log(xhr.responseText);
            console.log(xhr.responseJSON);

            var image_file  = xhr.responseJSON.image_file;
            var content_id  = xhr.responseJSON.content_id;
            var alt         = xhr.responseJSON.alt;

            $imagePreview.html('<img src="image/upload/normal/'+image_file+'">');
            $('#content_id').val(content_id);
            $imageLoading.fadeOut(100);
            $imageProgress.fadeOut(100);

            $btnImageSubmit.addClass('-active');
            $btnImageRotate.addClass('-active');
        }
    });

    // YouTube Form
    $youtubeForm        = $('#youtubeForm');
    $videoLink          = $('#videoLink');
    $YouTubePreview     = $('#YouTubePreview');
    $YouTubeID          = $('#YouTubeID');
    $YouTubeTitle       = $('#YouTubeTitle');
    $btnYouTubeClose    = $('#btnYouTubeClose');
    $btnYouTubeSubmit   = $('#btnYouTubeSubmit');
    var youtube_url     = '';

    function closeYoutubeDialog(){
        $youtubeForm.removeClass('-toggle');
        $insertFilter.fadeOut(100);
        $YouTubeID.val('');
        $YouTubeTitle.val('');
        $videoLink.val('');
        $YouTubePreview.html('<span><i class="fa fa-youtube-play" aria-hidden="true"></i></span>');
        $btnYouTubeSubmit.removeClass('-active');
    }

    $btnYouTubeClose.click(closeYoutubeDialog);

    $btnAddYouTube.click(function(){
        $youtubeForm.addClass('-toggle');
        $videoLink.focus();
        $insertFilter.fadeIn(300);
    });

    $videoLink.focus(function(){
        youtube_url = $(this).val();
    });

    $videoLink.keyup(function(){
        var link = $(this).val();

        if(link == youtube_url) return false;

        var video_id = youtube_parser(link);
        var embed = '<iframe src="https://www.youtube.com/embed/'+video_id+'?rel=0&amp;controls=0&amp;showinfo=0"></iframe>';

        if(video_id != 0 && video_id.length == 11){
            $YouTubePreview.html(embed);
            $btnYouTubeSubmit.addClass('-active');

            console.log('video_id',video_id);

            $.getJSON('https://www.googleapis.com/youtube/v3/videos?part=snippet&id='+video_id+'&key='+youtube_key,function(data){
                var video_title = data.items[0].snippet.title;
                console.log(video_title);
                $YouTubeTitle.val(video_title);
                $YouTubeID.val(video_id);
            });
        }else{
            $YouTubePreview.html('<span><i class="fa fa-youtube-play" aria-hidden="true"></i></span>');
            $btnYouTubeSubmit.removeClass('-active');
        }
        youtube_url = link;
    });

    function youtube_parser(url){
        var videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);

        if(videoid != null) {
            return videoid[1];
        }else{
            return 0;
        }
    }

    $btnYouTubeSubmit.click(function(){
        var youtube_id      = $YouTubeID.val();
        var youtube_title   = $YouTubeTitle.val();

        console.log(youtube_id,youtube_title);

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'add_youtube',
                article_id  :article_id,
                youtube_id  :youtube_id,
                youtube_title :youtube_title,
            },
            error: function (request, status, error){
                console.log("Request Error");
            }
        }).done(function(data){
            console.log(data);
            closeYoutubeDialog();

            var html = $('<div class="content-box -edit" id="content'+data.content_id+'" data-id="'+data.content_id+'"><iframe class="youtube_player" src="https://www.youtube.com/embed/'+data.youtube_id+'?rel=0&amp;controls=0&amp;showinfo=0"></iframe><input type="text" class="input-alt" placeholder="ใส่คำอธิบายภาพ..." value="'+data.alt+'"><div class="content-controls"><div class="btn btn-content-remove"><i class="fa fa-times" aria-hidden="true"></i></div></div></div>');

            $paper.append(html);
            $('html,body').animate({ scrollTop: $('#content'+content_id).offset().top },'slow');
        });
    });

    var current_content_id;
    $('#paper').on('click','.btn-content-swap',function(e){

        $('#swap').addClass('-toggle');
        $('#swapFilter').fadeIn(300);

        current_content_id  = $(this).parent().parent().attr('data-id');

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'list_content',
                article_id  :article_id,
            },
            error: function (request, status, error){
                console.log(request, status, error);
                console.log("Request Error");
            }
        }).done(function(data){
            console.log(data);
            $('#swap').html('');
            $.each(data.items,function(k,v){
                var current     = '';
                if(current_content_id == v.id) current = '-active';

                if(v.type == 'text')
                    var html = $('<div class="swap-items text '+current+'" data-id="'+v.id+'">'+v.message+'</div>');
                else if(v.type == 'image')
                    var html = $('<div class="swap-items '+current+'" data-id="'+v.id+'"><img src="image/upload/thumbnail/'+v.image_file+'"></div>');
                else if(v.type == 'youtube')
                    var html = $('<div class="swap-items '+current+'" data-id="'+v.id+'"><img src="http://img.youtube.com/vi/'+v.message+'/mqdefault.jpg"></div>');

                $('#swap').append(html);
            });
        });

        $('#swapFilter').click(function(){
            $(this).fadeOut(300);
            $('#swap').removeClass('-toggle');
        });
    });

    $('#swap').on('click','.swap-items',function(e){
        var target_id = $(this).attr('data-id');
        console.log(current_content_id,target_id);

        if(current_content_id == target_id) return false;

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'swap_content',
                current_id  :current_content_id,
                target_id   :target_id,
            },
            error: function (request, status, error){
                console.log(request.responseText);
                console.log("Request Error");
            }
        }).done(function(data){
            location.reload();
        });
    });

    $('#paper').on('click','.btn-content-remove',function(e){
        var content_id = $(this).parent().parent().attr('data-id');

        if(!confirm('คุณต้องการลบเนื้อหาส่วนนี้่ ใช่หรือไม่ ?')){ return false; }

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'delete_content',
                content_id  :content_id,
                article_id  :article_id,
            },
            error: function (request, status, error){
                console.log(request, status, error);
                console.log("Request Error");
            }
        }).done(function(data){
            console.log(data);

            $('#content'+content_id).remove();
        });
    });

    // Edit Alt text.
    $('#paper').on('click','.input-alt',function(e){
        savedStatus(true);
        $old = $(this).val();

        $(this).blur(function(){
            var content_id = $(this).parent().attr('data-id');
            var image_alt = $(this).val();

            if($old == image_alt) return false;

            $laodingbar.width('0%');
            $laodingbar.fadeIn(300);
            $laodingbar.animate({width:'70%'},500);

            console.log(content_id,image_alt,article_id);

            $.ajax({
                url         :'api.content.php',
                cache       :false,
                dataType    :"json",
                type        :"POST",
                data:{
                    action      :'edit_image_alt',
                    content_id  :content_id,
                    article_id  :article_id,
                    image_alt    :image_alt,
                },
                error: function (request, status, error){
                    console.log("Request Error");
                }
            }).done(function(data){
                console.log(data);

                $laodingbar.animate({width:'100%'},500);
                $laodingbar.fadeOut(300);
                savedStatus(false);
            });
        });
    });

    $('#fileList').on('click','.btn-file-remove',function(e){
        var file_id     = $(this).parent().parent().parent().attr('data-id');
        console.log('file_id',file_id);

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'delete_file',
                file_id     :file_id,
            },
            error: function (request, status, error){
                console.log("Request Error");
            }
        }).done(function(data){
            console.log(data);
            $('#file'+file_id).fadeOut(500);
        });
    });

    // CONTENT TEXT EDIT
    $('#paper').on('focus','.input-textarea',function(e){
        savedStatus(true);
        var old = $(this).html();

        $(this).blur(function(){
            var content_id  = $(this).parent().attr('data-id');
            var message     = $(this).html();

            if(old == message) return false;

            $laodingbar.width('0%');
            $laodingbar.fadeIn(300);
            $laodingbar.animate({width:'70%'},500);

            $.ajax({
                url         :'api.content.php',
                cache       :false,
                dataType    :"json",
                type        :"POST",
                data:{
                    action      :'edit_content_text',
                    content_id  :content_id,
                    article_id  :article_id,
                    message     :message,
                },
                error: function (request, status, error){
                    console.log("Request Error");
                }
            }).done(function(data){
                console.log(data.message,message);

                $laodingbar.animate({width:'100%'},500);
                $laodingbar.fadeOut(300);
                savedStatus(false);
            });
        });
    });

    $('#fileList').on('focus','.attach-title',function(e){
        savedStatus(true);
        $old = $(this).val();

        $(this).blur(function(){
            var file_id     = $(this).parent().parent().attr('data-id');
            var file_title  = $(this).val();

            console.log(file_id,file_title);

            if($old == file_title) return false;

            $laodingbar.width('0%');
            $laodingbar.fadeIn(300);
            $laodingbar.animate({width:'70%'},500);

            $.ajax({
                url         :'api.content.php',
                cache       :false,
                dataType    :"json",
                type        :"POST",
                data:{
                    action      :'edit_file_title',
                    file_id     :file_id,
                    file_title  :file_title,
                },
                error: function (request, status, error){
                    console.log("Request Error");
                }
            }).done(function(data){
                console.log(data);

                $laodingbar.animate({width:'100%'},500);
                $laodingbar.fadeOut(300);
                savedStatus(false);
            });
        });
    });

    $('.btn-published-toggle').click(function(){
        $laodingbar.width('0%');
        $laodingbar.fadeIn(300);
        $laodingbar.animate({width:'50%'},500);

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'published_article_toggle',
                article_id  :article_id
            },
            error: function (request, status, error){
                console.log("Request Error");
            }
        }).done(function(data){
            console.log(data.return);

            if(data.return == 'published'){
                $('.btn-published-toggle').html('กำลังเผยแพร่<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');

                $laodingbar.animate({width:'100%'},500);
                setTimeout(function(){
                    // window.location = 'article.php?id='+article_id;
                    location.reload();
                },2000);
            }else{
                $('.btn-published-toggle').html('กำลังดำเนินการ<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');

                $laodingbar.animate({width:'100%'},500);
                setTimeout(function(){
                    location.reload();
                },2000);
            }
        });
    });

    $('#article_title').focus(function(){
        savedStatus(true);
        $old = $(this).val();

        $(this).blur(function(){

            var title = $(this).val();

            if($old == title) return false;

            $laodingbar.width('0%');
            $laodingbar.fadeIn(300);
            $laodingbar.animate({width:'50%'},500);

            $.ajax({
                url         :'api.content.php',
                cache       :false,
                dataType    :"json",
                type        :"POST",
                data:{
                    action      :'edit_article',
                    title       :title,
                    article_id  :article_id
                },
                error: function (request, status, error){
                    console.log("Request Error");
                }
            }).done(function(data){
                console.log(data);
                $laodingbar.animate({width:'100%'},500);
                $laodingbar.fadeOut(300);
                savedStatus(false);
            });
        });
    });

    $('#article_description').focus(function(){
        savedStatus(true);
        $b_description = $(this).val();

        $(this).blur(function(){
            var description = $(this).val();

            if($b_description == description){
                return false;
            }

            $laodingbar.width('0%');
            $laodingbar.fadeIn(300);
            $laodingbar.animate({width:'70%'},300);

            $.ajax({
                url         :'api.content.php',
                cache       :false,
                dataType    :"json",
                type        :"POST",
                data:{
                    action      :'edit_description_article',
                    description :description,
                    article_id  :article_id
                },
                error: function (request, status, error){
                    console.log(request);
                    console.log("Request Error");
                }
            }).done(function(data){
                console.log(data);
                savedStatus(false);

                $laodingbar.animate({width:'100%'},300);
                $laodingbar.fadeOut(300);
            });
        });
    });

	$('#btn-add-text').click(function(){
		console.log('article_id',article_id);

        $laodingbar.width('0%');
        $laodingbar.fadeIn(300);
        $laodingbar.animate({width:'50%'},500);

		$.ajax({
	        url         :'api.content.php',
	        cache       :false,
	        dataType    :"json",
	        type        :"POST",
	        data:{
	            action     	:'create_content',
	            article_id 	:article_id,
	            type 		:'text',
	        },
	        error: function (request, status, error){
	        	console.log(request, status, error);
	            console.log("Request Error");
	        }
	    }).done(function(data){
	        console.log(data);

            $laodingbar.animate({width:'100%'},300);
            $laodingbar.fadeOut(300);

            var content_id = data.content_id;
            var html = $('<div class="content-box -edit" id="content'+content_id+'" data-id="'+content_id+'"><div class="input-textarea" contenteditable="true"></div><div class="content-controls"><div class="btn btn-content-remove"><i class="fa fa-times" aria-hidden="true"></i></div></div></div>');

            $paper.append(html);
            $('html,body').animate({ scrollTop: $('#content'+content_id).offset().top },'slow');
            editor.addElements('.input-textarea');
	    });
	});

    $('.category-choose-items').click(function(){
        var category_id     = $(this).attr('data-id');
        var category_title  = $(this).children('span').html();

        $category           = $(this);

        $laodingbar.width('0%');
        $laodingbar.fadeIn(300);
        $laodingbar.animate({width:'50%'},500);

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'edit_category_article',
                category_id :category_id,
                article_id  :article_id
            },
            error: function (request, status, error){
                console.log(request);
                console.log("Request Error");
            }
        }).done(function(data){
            console.log(data);
            $currentCategory.html(category_title);
            $('.category-choose-items').removeClass('-active');
            $category.addClass('-active');

            $laodingbar.animate({width:'100%'},300);
            $laodingbar.fadeOut(300);
        });
    });

    $btnImageRotate.click(function(){
        var content_id = $('#content_id').val();
        $img = $imagePreview.children('img');

        $imageLoading.fadeIn(100);
        $imageProgress.fadeIn(100);
        $imageProgress.width('0%');
        $imageProgress.animate({width:'50%'},300);

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'rotate_image',
                content_id  :content_id,
            },
            error: function (request, status, error){
                console.log(request, status, error);
                console.log("Request Error");
            }
        }).done(function(data){
            $imageProgress.animate({width:'100%'},300);

            console.log(data);
            $img.attr('src',$img.attr('src')+'?'+Math.random()*100);

            $imageLoading.fadeOut(100);
            $imageProgress.fadeOut(100);
        });
    });

    // CATEGORY
    $('#btnOptions , .choose-category').click(function(e){
        $('#moreOptions').addClass('-toggle');
        $('#moreOptionsFilter').fadeIn(100);

        $('#moreOptionsFilter').click(function(){
            $('#moreOptions').removeClass('-toggle');
            $(this).fadeOut(300);
        });
    });

    $btnDeleteArticle = $('#btnDeleteArticle');

    $btnDeleteArticle.click(function(){

        $.ajax({
            url         :'api.content.php',
            cache       :false,
            dataType    :"json",
            type        :"POST",
            data:{
                action      :'delete_article',
                article_id  :article_id,
            },
            error: function (request, status, error){
                console.log(request, status, error);
                console.log("Request Error");
            }
        }).done(function(data){
            console.log(data);
            window.location = 'profile.php?';
        });
    });
});