<a class="report-items style3" href="goto/<?php echo $var['report_id'];?>" target="_blank">
	<img src="image/upload/normal/<?php echo $var['report_image_file'];?>" alt="">
	<div class="detail">
		<?php if(!empty($var['report_category_id'])){?>
		<div class="info"><i class="fa fa-folder" aria-hidden="true"></i><?php echo $var['report_category_name'];?></div>
		<?php }?>
		<h2><?php echo $var['report_name'];?></h2>
		<p><?php echo $var['report_desc'];?></p>
	</div>
</a>