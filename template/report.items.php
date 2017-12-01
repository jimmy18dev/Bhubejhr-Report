<a class="report-items style3" href="goto/<?php echo $var['report_id'];?>" target="_blank">
	<div class="image">
		<?php if(!empty($var['report_image_file'])){?>
		<img src="image/upload/square/<?php echo $var['report_image_file'];?>" alt="<?php echo $var['report_name'];?>">
		<?php }else{?>
		<i class="fa fa-file-text" aria-hidden="true"></i>
		<?php }?>
	</div>

	<div class="detail">
		<h2><?php echo $var['report_name'];?></h2>
		<?php if(!empty($var['report_category_id'])){?>
		<div class="info"><?php echo $var['report_category_name'];?></div>
		<?php }?>
		<p><?php echo $var['report_desc'];?></p>
	</div>
	<div class="navicon"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
</a>