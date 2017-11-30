<a class="report-items" href="goto/<?php echo $var['report_id'];?>" target="_blank">
	<div class="detail">
		<?php if(!empty($var['report_category_id'])){?>
		<div class="info"><i class="fa fa-folder" aria-hidden="true"></i><?php echo $var['report_category_name'];?></div>
		<?php }?>
		<h2><?php echo $var['report_name'];?></h2>
		<p><?php echo $var['report_desc'];?></p>

		<?php if(!empty($var['report_image_file'])){?>
		<img src="image/upload/normal/<?php echo $var['report_image_file'];?>" alt="">
		<?php }else{?>
		<div class="icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></div>
		<?php }?>
	</div>
</a>