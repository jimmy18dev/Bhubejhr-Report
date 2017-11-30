<?php
require_once '../autoload.php';
// header('Access-Control-Allow-Origin: *');
header("Content-type: text/json");

// $apiArgArray = explode('/', substr(@$_SERVER['PATH_INFO'], 1));
// $headers = getallheaders();
// $token = $headers['Token'] or ['AuthKey'];
// $returnObject = (object) array();
$returnObject = array(
	"apiVersion"  	=> 1.0,
	"method" 		=> $_SERVER['REQUEST_METHOD'],
	// "header"      => $headers,
	"execute"     	=> floatval(round(microtime(true)-StTime,4)),
);

$signature 	= new Signature;

switch ($_SERVER['REQUEST_METHOD']){
	case 'GET':
		// switch ($_GET['request']){
		// 	case 'list':
		// 		$dataset = $app->listAll();

		// 		$returnObject['items'] = $dataset;
		// 		$returnObject['message'] = 'list all apps';
		// 		break;
		// 	default:
		// 		$returnObject['message'] = 'GET API Not found!';
		// 	break;
		// }
    	break;
    case 'POST':
    	switch ($_POST['request']){
    		case 'register':
				$fullname 	= $_POST['fullname'];
				$email 		= $_POST['email'];
				$password 	= $_POST['password'];

				$user_id = $user->register($fullname,$email,$password);

				if(true){
					$state = $user->login($email,$password);
				}

				$returnObject['message'] 	= 'New Account Created!';
				$returnObject['account_id'] = $user_id;

				break;
			case 'login':
				$username = $_POST['username'];
				$password = $_POST['password'];

				$state = $user->login($_POST['username'],$_POST['password']);

				if($state == 1) $message = 'login success';
				else if($state == 1) $message = 'Login fail';
				else if($state == -1) $message = 'Account Locked';
				else $message = 'n/a';

				$returnObject['message'] 	= $message;
				$returnObject['state'] 		= $state;
				
				break;
			case 'edit_profile':
				$username 	= $_POST['username'];
				$email 		= $_POST['email'];
				$name 		= $_POST['name'];
				$company 	= $_POST['company'];
				$position 	= $_POST['position'];

				$user->editProfile($user->id,$username,$email,$name,$company,$position);

				$returnObject['message'] 	= 'Profile edited.';
				break;
			case 'change_password':
				$newpassword = $_POST['newpassword'];

				$user->changePassword($user->id,$newpassword);

				$returnObject['message'] 	= 'Password changed.';
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