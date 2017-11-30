<?php
class User{
    public $id;
    public $username;
    public $name;
    public $email;
    public $company;
    public $position;
    public $type;
    public $permission;
    public $status;
    public $ip;
    public $register_time;
    public $visit_time;
    public $edit_time;
    public $total_app;
    public $app_limit;

    private $password;
    private $salt;
    private $db;
    private $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
    }

    private function updateVisitTime($user_id){
        $this->db->query('UPDATE user SET visit_time = :visit_time WHERE id = :user_id');
        $this->db->bind(':user_id' ,$user_id);
        $this->db->bind(':visit_time' ,date('Y-m-d H:i:s'));
        $this->db->execute();
    }

    public function getUser($user_id){
        $this->db->query('SELECT id,username,name,email,company,position,password,salt,type,permission,status,ip,register_time,edit_time,visit_time FROM user WHERE id = :user_id');
        $this->db->bind(':user_id',$user_id);
        $this->db->execute();
        $dataset = $this->db->single();

        $this->id             = $dataset['id'];
        $this->username       = $dataset['username'];
        $this->name           = $dataset['name'];
        $this->email           = $dataset['email'];
        $this->company           = $dataset['company'];
        $this->position           = $dataset['position'];
        $this->password       = $dataset['password'];
        $this->salt           = $dataset['salt'];
        $this->permission     = $dataset['permission'];
        $this->ip             = $dataset['ip'];
        $this->type           = $dataset['type'];
        $this->status         = $dataset['status'];
        $this->register_time  = $dataset['register_time'];
        $this->visit_time     = $dataset['visit_time'];
        $this->edit_time     = $dataset['edit_time'];

         if($this->permission == 'admin'){
            $this->app_limit = 10;
        }else{
            $this->app_limit = 3;
        }
    }

    public function register($name,$email,$password){
        $email      = filter_var(strip_tags(trim($email)),FILTER_SANITIZE_EMAIL);
        // Random password if password is empty value
        $salt       = hash('sha512',uniqid(mt_rand(1,mt_getrandmax()),true));
        // Create salted password
        $password   = hash('sha512',$password.$salt);

        if($this->already($username,$name)){
            
            $this->db->query('INSERT INTO user(email,name,password,salt,permission,ip,register_time,status) VALUE(:email,:name,:password,:salt,:permission,:ip,:register_time,:status)');
            $this->db->bind(':email'        ,$email);
            $this->db->bind(':name'         ,$name);
            $this->db->bind(':password'     ,$password);
            $this->db->bind(':salt'         ,$salt);
            $this->db->bind(':permission'   ,'guest');
            $this->db->bind(':ip'           ,$this->db->GetIpAddress());
            $this->db->bind(':register_time',date('Y-m-d H:i:s'));
            $this->db->bind(':status'       ,'disable');
            $this->db->execute();

            $user_id = $this->db->lastInsertId();

        }else{
            return 0;
        }

        return $user_id;
    }
    public function already($username,$name){
        $this->db->query('SELECT id FROM user WHERE username = :username OR name = :name');
        $this->db->bind(':username',$username);
        $this->db->bind(':name',$name);
        $this->db->execute();
        $dataset = $this->db->single();

        if(empty($dataset['id'])){
            return true;
        }else{
            return false;
        }
    }

    public function sec_session_start() {
        $session_name   = 'sec_session_id';   // Set a custom session name
        $secure         = false;
        // session.cookie_secure specifies whether cookies should only be sent over secure connections. (https)

        // This stops JavaScript being able to access the session id.
        $httponly = true;

        // Forces sessions to only use cookies.
        if(ini_set('session.use_only_cookies', 1) === FALSE) {
            header("Location: ../error.php?err=Could_not_initiate_a_safe_session");
            exit();
        }

        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params(600,$cookieParams["path"],$cookieParams["domain"],$secure,$httponly);
        // session_set_cookie_params('600'); // 10 minutes.

        // Sets the session name to the one set above.
        session_name($session_name);
        session_start();             // Start the PHP session
        // session_regenerate_id(true); // regenerated the session, delete the old one.
    }

    public function loginChecking(){
        // READ COOKIES
        if(!empty($_COOKIE['user_id']) && empty($_SESSION['user_id']))
            $_SESSION['user_id'] = $_COOKIE['user_id'];
        if(!empty($_COOKIE['login_string']) && empty($_SESSION['login_string']))
            $_SESSION['login_string'] = $_COOKIE['login_string'];

        // Check if all session variables are set
        if(isset($_SESSION['user_id'],$_SESSION['login_string'])){

            $user_id        = $_SESSION['user_id'];
            $login_string   = $_SESSION['login_string'];

            // Get the user-agent string of the user.
            $user_browser   = $_SERVER['HTTP_USER_AGENT'];

            $this->getUser($this->Decrypt($user_id));

            if(!empty($this->id)){
                $login_check = hash('sha512',$this->password.$user_browser);

                if($login_check == $login_string){
                    $this->updateVisitTime($this->id);
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function login($username,$password){
        $username       = strip_tags(trim($username));
        $password       = trim($password);
        $cookie_time    = time() + 3600 * 24 * 12; // Cookie Time (1 year)

        // GET USER DATA BY EMAIL
        $this->db->query('SELECT id,password,salt FROM user WHERE email = :username');
        $this->db->bind(':username',$username);
        $this->db->execute();
        $user_data = $this->db->single();

        if($this->checkBrute($user_data['id'])){
            if((hash('sha512',$password.$user_data['salt']) == $user_data['password'])){
                // PASSWORD IS CORRECT!
                $user_browser = $_SERVER['HTTP_USER_AGENT'];

                // XSS protection as we might print this value
                $user_id = preg_replace("/[^0-9]+/",'',$user_data['id']);
                // Encrypt UserID before send to cookie.
                $user_id = $this->Encrypt($user_id);

                // SET SESSION AND COOKIE
                $_SESSION['user_id'] = $user_id;
                setcookie('user_id',$user_id,$cookie_time);
                $_SESSION['login_string'] = hash('sha512',$user_data['password'].$user_browser);
                setcookie('login_string',hash('sha512',$user_data['password'].$user_browser),$cookie_time);

                // Save log to attempt : [successful]
                // $this->db->recordAttempt($user_data['id'],'successful');

                return 1; // LOGIN SUCCESS
            }else{
                // Save log to attempt : [fail]
                if(!empty($user_data['id'])){
                    $this->recordAttempt($user_data['id']); // Login failure!
                }

                return 0; // LOGIN FAIL!
            }
        }else{
            return -1; // ACCOUNT LOCKED!
        }
        // Note: crypt — One-way string hashing (http://php.net/manual/en/function.crypt.php)
    }

    private function checkBrute($user_id){
        // First step clear attempt log.
        // $this->db->clearAttempt();
        // return ($this->db->countAttempt($user_id) >= 5 ? true : false);

        return true;
    }

    private function userAlready($email){
        $this->db->query('SELECT id FROM user WHERE email = :email');
        $this->db->bind(':email',$email);
        $this->db->execute();
        $dataset = $this->db->single();
        
        if(empty($dataset['id'])) return true;
        else return false;
    }

    private function Encrypt($data){
        $key = $this->key;
        $password = $this->cookie_salt;
        $encryption_key = base64_decode($key.$password);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    private function Decrypt($data){
        $key = $this->key;
        $password = $this->cookie_salt;
        $encryption_key = base64_decode($key.$password);
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }


    // LOGIN ATTEMPTS
    private function recordAttempt($user_id){
        $this->db->query('INSERT INTO login_attempts(user_id,time,ip) VALUE(:user_id,:time,:ip)');
        $this->db->bind(':user_id' ,$user_id);
        $this->db->bind(':time'     ,time());
        $this->db->bind(':ip'       ,$this->db->GetIpAddress());

        $this->db->execute();
        return $this->db->lastInsertId();
    }
    // public function countAttempt($member_id){
    //  $this->db->query('SELECT COUNT(member_id) total FROM login_attempts WHERE (member_id = :member_id) AND (status = "fail")');
    //  $this->db->bind(':member_id', $member_id);
    //  $this->db->execute();
    //  $data = $this->db->single();
    //  return $data['total'];
    // }

    private function clearAttempt(){
        $this->db->query('DELETE FROM login_attempts WHERE time < :limittime');
        $this->db->bind(':limittime', time() - 60);
        $this->db->execute();
    }

    public function editProfile($user_id,$username,$email,$name,$company,$position){
        $this->db->query('UPDATE user SET username = :username,email = :email, name = :name,company = :company,position = :position, edit_time = :edit_time WHERE id = :user_id');
        $this->db->bind(':user_id'  ,$user_id);
        $this->db->bind(':username' ,$username);
        $this->db->bind(':email'    ,$email);
        $this->db->bind(':name'     ,$name);
        $this->db->bind(':company'  ,$company);
        $this->db->bind(':position' ,$position);
        $this->db->bind(':edit_time' ,date('Y-m-d H:i:s'));
        $this->db->execute();
    }
    public function changePassword($user_id,$newpassword){
        // Random password if password is empty value
        $salt = hash('sha512',uniqid(mt_rand(1,mt_getrandmax()),true));
        // Create salted password
        $password   = hash('sha512',$newpassword.$salt);

        $this->db->query('UPDATE user SET password = :password, salt = :salt, edit_time = :edit_time WHERE id = :user_id');
        $this->db->bind(':user_id' ,$user_id);
        $this->db->bind(':password' ,$password);
        $this->db->bind(':salt' ,$salt);
        $this->db->bind(':edit_time' ,date('Y-m-d H:i:s'));
        $this->db->execute();
    }
}
?>