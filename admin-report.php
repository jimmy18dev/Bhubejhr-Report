<?php
include_once 'autoload.php';
if(!$user_online || $user->permission != 'admin'){
	header('Location: login.php');
	die();
}
?>
<!doctype html>
<html lang="en-US" itemscope itemtype="http://schema.org/Blog" prefix="og: http://ogp.me/ns#">
<head>

<!--[if lt IE 9]>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<![endif]-->

<!-- Meta Tag -->
<meta charset="utf-8">

<!-- Viewport (Responsive) -->
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="user-scalable=no">
<meta name="viewport" content="initial-scale=1,maximum-scale=1">

<?php include'favicon.php';?>
<title>Bhubejhr Report</title>

<base href="<?php echo DOMAIN;?>">
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<link rel="stylesheet" type="text/css" href="plugin/font-awesome/css/font-awesome.min.css"/>
</head>
<body>
<?php
$report = new Report();
$reports = $report->lists('all',NULL,NULL);

include_once'header.php';
?>

<div class="container">
	<div class="topic">
		<div class="text">รายงานทั้งหมด</div>
		<div class="btn" id="btnCreateReport"><i class="fa fa-plus" aria-hidden="true"></i>เพิ่มรายงานใหม่</div>
	</div>
	<div class="table">
		<div class="content">
			<?php foreach ($reports as $var) {?>
			<div class="row" data-id="<?php echo $var['report_id'];?>" data-category="<?php echo $var['report_category_id'];?>">
				<div class="icon <?php echo ($var['report_status'] == 'active'?'btn-deactive':'btn-active');?>"><i class="fa fa-circle" aria-hidden="true"></i></div>
				<div class="detail">
					<h2><?php echo $var['report_name'];?></h2>

					<?php if(!empty($var['report_desc'])){?>
					<p><?php echo $var['report_desc'];?></p>
					<?php }?>
					
					<div class="info">
						<div class="category">
							<div class="current"><?php echo (!empty($var['report_category_name'])?$var['report_category_name']:'เพิ่มหมวดหมู่');?></div>
							
							<div class="category-menu">
								<div class="loading">กำลังโหลด<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i></div>
							</div>
						</div>

						<a href="goto/<?php echo $var['report_id'];?>" target="_blank"><?php echo $var['report_url'];?><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
					</div>
				</div>
				<div class="counter">
					<?php echo number_format($var['report_count_open']);?>
				</div>
				
				<div class="edit">
					<i class="fa fa-ellipsis-v" aria-hidden="true"></i>

					<div class="edit-menu">
						<div class="items btn-editor" data-op="edit"><i class="fa fa-cog" aria-hidden="true"></i>แก้ไข</div>
						<div class="items op-items delete" data-op="delete"><i class="fa fa-times" aria-hidden="true"></i>ลบออก</div>
					</div>
				</div>
			</div>
			<?php }?>
		</div>
	</div>
</div>

<form action="report.process.php" class="form-dialog" id="reportForm" method="POST" enctype="multipart/form-data">
	<div id="formProgress"></div>
	<div class="topic">
		<span class="text">รายงาน</span>
		<span class="btn-close-dialog" id="btnCloseDialog"><i class="fa fa-times" aria-hidden="true"></i></span>
	</div>
	<label for="name">Name</label>
	<input type="text" name="name" id="name" placeholder="Name">
	<label for="description">Description</label>
	<input type="text" name="desc" id="desc" placeholder="Description">
	<label for="url">URL</label>
	<input type="text" name="url" id="url" placeholder="Report URL">
	<input type="file" name="image">
	<input type="text" name="report_id" id="report_id">
	<button id="btnSubmitReport" type="submit">สร้าง</button>
</form>

<script type="text/javascript" src="js/lib/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/lib/jquery-form.min.js"></script>
<script type="text/javascript" src="js/lib/autosize.js"></script>
<script type="text/javascript" src="js/init.js"></script>
<script type="text/javascript" src="js/report.js"></script>
</body>
</html>