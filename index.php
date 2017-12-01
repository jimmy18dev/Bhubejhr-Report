<?php
include_once 'autoload.php';
$report = new Report();
$category = new Category();
$keyword = new Keyword();

if(!empty($_GET['q'])){
	$keyword->save($_GET['q']);
}

$categories = $category->listAll();
$reports = $report->lists('all',$_GET['category'],$_GET['q']);
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
<div class="searchbox">
	<form action="search" method="GET">
		<i class="fa fa-search" aria-hidden="true"></i>
		<input type="text" class="input-search-text" placeholder="ค้นหารายงานที่คุณต้องการ..." name="q" value="<?php echo $_GET['q'];?>">
	</form>
	<p class="note">กด Enter เพื่อค้นหา</p>

	<div class="category">
		<a class="category-items <?php echo (empty($_GET['category'])?'active':'');?>" href="index.php?">ดูทั้งหมด</a>
		<?php foreach ($categories as $var) {?>
		<a class="category-items <?php echo ($var['category_id'] == $_GET['category']?'active':'');?>" href="category/<?php echo $var['category_id'];?>"><?php echo $var['category_name'];?></a>
		<?php }?>
	</div>
</div>
<div class="list-container">

	<?php if(count($reports) > 0){?>
	<h1><?php echo count($reports);?> รายการ</h1>
	<?php }?>

	<?php if(count($reports) > 0){?>
	<div class="list-content">
		<?php
		foreach ($reports as $var){
			include'template/report.items.php';
		}
		?>
	</div>
	<?php }else{?>
	<div class="empty">ไม่พบรายการ...</div>
	<?php }?>
</div>
<script type="text/javascript" src="js/lib/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/init.js"></script>
</body>
</html>