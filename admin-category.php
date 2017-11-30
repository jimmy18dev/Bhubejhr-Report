<?php
include_once 'autoload.php';
if(!$user_online || $user->permission != 'admin'){
	header('Location: login.php');
	die();
}
$category = new Category();
$categories = $category->listAll();

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
<?php include_once'header.php'; ?>

<div class="container">
	<div class="topic">
		<div class="text">หมวดหมู่</div>
		<div class="btn" id="btnCreate"><i class="fa fa-plus" aria-hidden="true"></i>เพิ่มหมวดหมู่ใหม่</div>
	</div>
	<div class="table">
		<div class="content">
			<?php foreach ($categories as $var) {?>
			<div class="row" data-id="<?php echo $var['category_id'];?>">
				<div class="icon <?php echo ($var['category_status'] == 'active'?'btn-deactive':'btn-active');?>"><i class="fa fa-folder" aria-hidden="true"></i></div>
				<div class="detail">
					<h2><?php echo $var['category_name'];?></h2>
					<p>Category ID : <?php echo $var['category_id'];?> <?php echo $var['category_desc'];?></p>
				</div>
				<div class="counter"><?php echo number_format($var['category_count']);?></div>
				<div class="edit">
					<i class="fa fa-ellipsis-v" aria-hidden="true"></i>

					<div class="edit-menu">
						<div class="items btn-editor" data-op="edit"><i class="fa fa-cog" aria-hidden="true"></i>แก้ไข</div>
						<div class="items btn-delete" data-op="delete"><i class="fa fa-times" aria-hidden="true"></i>ลบออก</div>
					</div>
				</div>
			</div>
			<?php }?>
		</div>
	</div>
</div>

<form action="category.process.php" class="form-dialog" id="dialogForm" method="POST" enctype="multipart/form-data">
	<div id="formProgress"></div>
	<div class="topic">
		<span class="text">หมวดหมู่</span>
		<span class="btn-close-dialog" id="btnCloseDialog"><i class="fa fa-times" aria-hidden="true"></i></span>
	</div>

	<label for="name">ชื่อหมวดหมู่</label>
	<input type="text" name="name" id="name" placeholder="Name">

	<label for="description">รายละเอียด</label>
	<input type="text" name="desc" id="desc" placeholder="Description">

	<input type="hidden" name="category_id" id="category_id">
	<button id="btnSubmit" type="submit">สร้าง</button>
</form>

<script type="text/javascript" src="js/lib/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/lib/jquery-form.min.js"></script>
<script type="text/javascript" src="js/lib/autosize.js"></script>
<script type="text/javascript" src="js/init.js"></script>
<script type="text/javascript" src="js/category.js"></script>
</body>
</html>