<?php
require_once '../autoload.php';
// header('Access-Control-Allow-Origin: *');
header("Content-type: text/json");

$returnObject = array(
	"apiVersion"  	=> 1.1,
	// "method" 		=> $_SERVER['REQUEST_METHOD'],
	"execute"     	=> floatval(round(microtime(true)-StTime,4)),
);

// if($user->permission != 'admin' || $user->status != 'active'){
// 	$returnObject['message'] = 'user permission error!';
// 	echo json_encode($returnObject);
// 	exit();
// }

$category = new Category();

switch ($_SERVER['REQUEST_METHOD']){
	case 'GET':
		switch ($_GET['request']){
			case 'list_all':
				$dataset = $category->listAll();
				$returnObject['dataset'] = $dataset;
				$returnObject['message'] = 'list all category';
				break;
			default:
				$returnObject['message'] = 'GET API Not found!';
			break;
		}
    	break;
    case 'POST':
    	switch ($_POST['request']){
			case 'create':
				$name = $_POST['name'];
				$category_id = $category->create($name);
				$returnObject['category_id'] = $category_id;
				$returnObject['message'] = 'Category created';
				break;
			case 'create_and_set':
				$report 		= new Report();
				$name 			= $_POST['name'];
				$report_id 		= $_POST['report_id'];
				$category_id 	= $category->create($name);

				$report->changeCategory($report_id,$category_id);
				
				$returnObject['category_id'] = $category_id;
				$returnObject['message'] = 'Category created';
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