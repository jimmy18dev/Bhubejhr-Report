<?php
include_once 'autoload.php';
$report_id = $_GET['id'];
$report = new Report();
$report_data = $report->get($report_id);
$report->updateCounter($report_id);

header('Location:'.$report_data['report_url']);
die();
?>