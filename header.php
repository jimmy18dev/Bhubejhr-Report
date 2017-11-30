<header class="header">
	<a href="index.php" class="logo" title="Version <?php echo VERSION;?>"><img src="image/logo.png" alt="logo"><span><?php echo SITENAME;?></span></a>

	<?php if($user_online){?>
	<div class="btn-profile" id="btnProfile">
		<span><?php echo $user->name;?></span><i class="fa fa-angle-down" aria-hidden="true"></i>

		<div class="more-menu" id="menuProfile">
			<div class="arrow-up"></div>
			<?php if($user->permission == 'admin'){?>
			<a href="admin"><i class="fa fa-id-badge" aria-hidden="true"></i>Admin Panel</a>
			<?php }?>
			<a href="logout" class="btn-logout"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
		</div>
	</div>
	<?php }else{?>
	<a href="signin" class="btn">เข้าระบบ<i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
	<?php }?>
</header>

<div class="overlay"></div>
<div id="progressbar"></div>