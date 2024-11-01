<?php 
class DbController{
	

	var $con 			= null;

	function __construct(){
		global $wpdb;

		$this->con = @$wpdb;
	}

	function run($sql){	
		global $wpdb;	

		$query = $this->con->query($sql);
		return $query;
	}	

	function insert($sql){
		global $wpdb;			
		$id = $this->con->query($sql);	
		//$id = $this->con->insert_id;
		return $id;		
	}	

	function select($sql){		
		global $wpdb;			
		$query = $this->con->get_results($sql, ARRAY_A );
		return $query;
	}		
}
?>