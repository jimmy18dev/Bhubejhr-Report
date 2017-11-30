<?php
require_once '../autoload.php';
// header('Access-Control-Allow-Origin: *');
header("Content-type: text/json");

$returnObject = array(
	"apiVersion"  	=> 1.1,
	"method" 		=> $_SERVER['REQUEST_METHOD'],
	"data" 			=> $_POST,
	"execute"     	=> floatval(round(microtime(true)-StTime,4)),
);

// if($user->permission != 'admin' || $user->status != 'active'){
// 	$returnObject['message'] = 'user permission error!';
// 	echo json_encode($returnObject);
// 	exit();
// }

$report = new Report();

switch ($_SERVER['REQUEST_METHOD']){
	case 'GET':
		switch ($_GET['request']){
			case 'get':
				$report_id = $_GET['report_id'];
				$dataset = $report->get($report_id);
				$returnObject['data'] = $dataset;
				break;
			default:
				$returnObject['message'] = 'GET API Not found!';
			break;
		}
    	break;
    case 'POST':
    	switch ($_POST['request']){
			// case 'submit':
			// 	$report_id 		= $_POST['report_id'];
			// 	$name 			= $_POST['name'];
			// 	$desc 			= $_POST['desc'];
			// 	$url 			= $_POST['url'];

			// 	if(empty($report_id)){
			// 		$report_id 	= $report->create($name,$desc,$url);
			// 		$returnObject['report_id'] = $report_id;
			// 	}else if(!empty($report_id) && isset($report_id)){
			// 		$report->edit($report_id,$name,$desc,$url);
			// 		$returnObject['message'] = 'Report edited.';
			// 	}
			// 	break;
			case 'change_category':
				$report_id 		= $_POST['report_id'];
				$category_id 	= $_POST['category_id'];
				$report_id 		= $report->changeCategory($report_id,$category_id);
				$returnObject['report_id'] = $report_id;
				$returnObject['message'] = 'Report category changed.';
				break;
			case 'active':
				$report_id 		= $_POST['report_id'];
				$report_id 		= $report->setStatus($report_id,'active');
				$returnObject['message'] = 'Report actived.';
				break;
			case 'deactive':
				$report_id 		= $_POST['report_id'];
				$report_id 		= $report->setStatus($report_id,'deactive');
				$returnObject['message'] = 'Report deactived.';
				break;
			case 'delete':
				$report_id 		= $_POST['report_id'];
				$report_id 		= $report->delete($report_id);
				$returnObject['message'] = 'Report deleted.';
				break;
			default:
				$returnObject['message'] = 'POST API Not found!';
			break;
		}
    	break;
    default:
    	$returnObject['message'] = 'METHOD API Not found!';
    	break;
}

echo json_encode($returnObject);
exit();
?>