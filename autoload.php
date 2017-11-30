<?php
session_start();
// session_regenerate_id(true); // regenerated the session, delete the old one.
ob_start();
define('StTime', microtime(true));

date_default_timezone_set('Asia/Bangkok');
error_reporting(E_ALL ^ E_NOTICE);

define("VERSION" 	,'1.0');
define("SITENAME" 	,'Bhubejhr Report');

include_once'config/config.php';
require_once'config/image.config.php';

include_once'class/database.class.php';
include_once'class/image.class.php';
include_once'class/report.class.php';
include_once'class/category.class.php';
include_once'class/keyword.class.php';
include_once'class/signature.class.php';
include_once'class/user.class.php';

$wpdb = new Database(DB_HOST,DB_NAME,DB_USER,DB_PASS);
$user = new User;

$user->sec_session_start();
$user_online = $user->loginChecking();
?>