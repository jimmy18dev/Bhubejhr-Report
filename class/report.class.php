<?php
class Report{

	private $db;
    public function __construct() {
    	global $wpdb;
    	$this->db = $wpdb;
    }

    public function get($report_id){
    	$this->db->query('SELECT report.id report_id,report.name report_name,report.description report_desc,report.url report_url,report.create_time report_create_time,report.edit_time report_edit_time,report.visit_time report_visit_time,report.type report_type,report.status report_status,report.tag report_tag,report.c_open report_open FROM report AS report WHERE report.id = :report_id');
    	$this->db->bind(':report_id',$report_id);
		$this->db->execute();
		$dataset = $this->db->single();
		return $dataset;
    }

    public function lists($status,$category_id,$keyword){
        $select = 'SELECT report.id report_id,report.name report_name,report.description report_desc,report.url report_url,report.create_time report_create_time,report.edit_time report_edit_time,report.visit_time report_visit_time,report.type report_type,report.status report_status,report.tag report_tag,report.c_open report_count_open,category.id report_category_id,category.name report_category_name,image.image_file report_image_file,image.format report_image_format FROM report AS report LEFT JOIN category AS category ON report.category_id = category.id LEFT JOIN image AS image ON report.id = image.report_id ';
        $where = 'WHERE 1=1 ';
        $order = 'ORDER BY report.create_time DESC';

        if(!empty($category_id) && isset($category_id)){
            $where_category = 'AND report.category_id = :category_id ';
        }

        if($status == 'active'){
            $where_status = 'AND report.status = "active" ';
        }

        if(!empty($keyword) && isset($keyword)){
            $where_keyword = ' AND (report.name LIKE :keyword OR report.description LIKE :keyword) ';
        }

        $query_string = $select.$where.$where_category.$where_status.$where_keyword.$order;

        $this->db->query($query_string);

        if(!empty($category_id)){
            $this->db->bind(':category_id',$category_id);
        }
        if(!empty($keyword) && isset($keyword)){
            $this->db->bind(':keyword','%'.$keyword.'%');
        }
        $this->db->execute();
        $dataset = $this->db->resultset();
        return $dataset;
    }

    public function create($name,$description,$url){
    	$this->db->query('INSERT INTO report(name,description,url,create_time) VALUE(:name,:description,:url,:create_time)');
    	$this->db->bind(':name' 		,$name);
    	$this->db->bind(':description' 	,$description);
    	$this->db->bind(':url' 			,$url);
    	$this->db->bind(':create_time' 	,date('Y-m-d H:i:s'));
		$this->db->execute();
		return $this->db->lastInsertId();
    }

    public function edit($report_id,$name,$description,$url){
    	$this->db->query('UPDATE report SET name = :name,description = :description,url = :url,edit_time = :edit_time WHERE id = :report_id');
    	$this->db->bind(':report_id' 	,$report_id);
    	$this->db->bind(':name' 		,$name);
    	$this->db->bind(':description' 	,$description);
    	$this->db->bind(':url' 			,$url);
    	$this->db->bind(':edit_time' 	,date('Y-m-d H:i:s'));
		$this->db->execute();
    }
    public function changeCategory($report_id,$category_id){
        if($category_id == 0) $category_id = null;
        $this->db->query('UPDATE report SET category_id = :category_id,edit_time = :edit_time WHERE id = :report_id');
        $this->db->bind(':report_id'    ,$report_id);
        $this->db->bind(':category_id'  ,$category_id);
        $this->db->bind(':edit_time'    ,date('Y-m-d H:i:s'));
        $this->db->execute();
    }

    public function delete($report_id){
        $this->db->query('DELETE FROM report WHERE id = :report_id');
        $this->db->bind(':report_id'    ,$report_id);
        $this->db->execute();
    }

    public function visited($report_id){
    	$this->db->query('UPDATE report SET visit_time = :visit_time WHERE id = :report_id');
    	$this->db->bind(':report_id' 	,$report_id);
    	$this->db->bind(':visit_time' 	,date('Y-m-d H:i:s'));
		$this->db->execute();
    }

    public function updateCounter($report_id){
        $this->db->query('SELECT c_open FROM report WHERE id = :report_id');
        $this->db->bind(':report_id',$report_id);
        $this->db->execute();
        $dataset = $this->db->single();

        $counter = ++$dataset['c_open'];

        $this->db->query('UPDATE report SET c_open = :counter WHERE id = :report_id');
        $this->db->bind(':report_id',$report_id);
        $this->db->bind(':counter',$counter);
        $this->db->execute();        
    }

    public function setStatus($report_id,$status){
    	$this->db->query('UPDATE report SET status = :status,edit_time = :edit_time WHERE id = :report_id');
    	$this->db->bind(':report_id' 	,$report_id);
    	$this->db->bind(':status' 		,$status);
    	$this->db->bind(':edit_time' 	,date('Y-m-d H:i:s'));
		$this->db->execute();
    }
}
?>
