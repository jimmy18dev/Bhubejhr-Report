<?php
include_once 'autoload.php';
if($user_online){
	header('Location: index.php');
	die();
}
$signature 	= new Signature;
$currentPage = 'login';
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
<title>Login | <?php echo SITENAME;?></title>

<base href="<?php echo DOMAIN;?>">
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<link rel="stylesheet" type="text/css" href="plugin/font-awesome/css/font-awesome.min.css"/>
</head>
<body class="loginBG">
<div id="progressbar"></div>
<div class="login">
	<a href="index.php" class="back-toggle"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>กลับไปหน้าแรก</a>
	<div class="welcome">
		<a class="logo" href="index.php" target="_parent"><img src="image/logo.png" alt=""></a>
		<div class="head">
			<h1><?php echo SITENAME;?></h1>
			<div class="ver">Version <?php echo VERSION;?></div>
			<p>Chaophya Abhaibhubejhr Hospital Prachinburi</p>
		</div>
	</div>
	<form class="form" action="javascript:login();">
		<p>Please enter your username and password to login or <a href="signup">Create New Account</a></p>
		<input class="inputtext" type="email" id="username" placeholder="Your Email Address" autofocus>
		<input class="inputtext" type="password" id="password" placeholder="Password">
		<input type="hidden" id="sign" name="sign" value="<?php echo $signature->generateSignature('login',SECRET_KEY);?>">
		<button id="btn-submit" class="btn-submit">Login<i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
	</form>
</div>

<script type="text/javascript" src="js/lib/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/user.js"></script>
</body>
</html>