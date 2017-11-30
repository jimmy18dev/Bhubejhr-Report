<?php
class Category{

	private $db;
    public function __construct() {
    	global $wpdb;
    	$this->db = $wpdb;
    }

    public function create($name){
        $this->db->query('INSERT INTO category(name) VALUE(:name)');
        $this->db->bind(':name'         ,$name);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function listAll(){
    	$this->db->query('SELECT id,name FROM category ORDER BY id DESC');
    	$this->db->bind(':user_id',$user_id);
		$this->db->execute();
		$dataset = $this->db->resultset();

		foreach ($dataset as $k => $var){
			$dataset[$k]['id'] = floatval($var['id']);
		}
		return $dataset;
    }
}
?>
