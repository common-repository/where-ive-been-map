<?php
include_once "dbcontroller.class.php";

class WhereIveBeenModel{
	var $user_id;
	var $con;
	var $settings;
	var $countries;
	var $default = -1;
	var $goingto = 0;
	var $visited = 1;
	var $user_countries;
	var $table_prefix;


	function __construct($user_id){
		global $wpdb;
	
		$this->table_prefix = $wpdb->prefix . "whereivebeen_";
		$this->user_id = $user_id;		
		// TODO : create a connection with the WP DB model
		$this->con = new DbController();
		// Settings
		$this->settings = array();
		// Load countries
		$this->countries = array();
		$this->user_countries = array();

		$this->load_settings();
		$this->load_user_countries();	
		$this->load_countries();
		
	}

	function load_settings(){
		$this->countries = array();

		// These are all the countries available
		$sql = "select id,setting,setting_value from " . $this->table_prefix . "settings where user_id=" . $this->user_id ;
		$re_settings = $this->con->select($sql, ARRAY_A );		
		
		foreach ( $re_settings as $setting ){ 			
			$this->settings[$setting['setting']] =  $setting["setting_value"];
		}		

	}

	function load_countries(){
		$this->countries = array();

		// These are all the countries available
		$sql = "select id,title from " . $this->table_prefix . "countries where id NOT IN (select country_id from " . $this->table_prefix . "user_countries where user_id=" . $this->user_id . ")";
		$re_countries = $this->con->select($sql, ARRAY_A );		
		
		foreach ( $re_countries as $country ){ 			
			$this->countries[] =  array(
											"country_id"=>$country["id"],
											"name"=>$country["title"]
											);
		}

	}


	function load_user_countries(){
		// The format of the data is like this
		/**
		*	array(
		*			"id"=> "int",
		*			"name"=> "string",
		*			"status" => 1 or 0,
		*			"url"	=> "string",
		*			"new_window" => 1 or 0,
		*
		*
		*/

		$this->user_countries = array();

		$sql = "select " . $this->table_prefix . "user_countries.id," . $this->table_prefix . "user_countries.country_id," . $this->table_prefix . "countries.title," . $this->table_prefix . "user_countries.status_id," . $this->table_prefix . "user_countries.url," . $this->table_prefix . "user_countries.new_window from " . $this->table_prefix . "user_countries inner join " . $this->table_prefix . "countries on " . $this->table_prefix . "user_countries.country_id=" . $this->table_prefix . "countries.id where " . $this->table_prefix . "user_countries.user_id=" . $this->user_id . " ORDER BY " . $this->table_prefix . "countries.title";
		$re_user_countries = $this->con->select($sql, ARRAY_A );		
		
		foreach ( $re_user_countries as $user_country ){ 
			$this->user_countries[] = array(
											"id"=>$user_country["id"],
											"country_id"=>$user_country["country_id"],
											"name"=>$user_country["title"],
											"status"=>$user_country["status_id"],
											"url"=>$user_country["url"],
											"new_window"=>$user_country["new_window"]
											);
		}

	}

	function get_settings(){
		return $this->settings;
	}

	function get_countries(){
		return $this->countries;
	}

	function get_user_countries(){
		return $this->user_countries;
	}

	function get_user_id(){
		return $this->user_id;
	}

	function set_user_id($user_id){
		$this->user_id = $user_id;		
	}

	function update_settings($user_id,$visited_color, $tovisit_color, $inactive_color, $message){
		$sql = "update " . $this->table_prefix . "settings set setting_value='".$visited_color."' where user_id=" . $user_id . " AND setting='color_visited';";
		$id = $this->con->run($sql);
		$sql = "update " . $this->table_prefix . "settings set setting_value='".$tovisit_color."' where user_id=" . $user_id . " AND setting='color_tovisit';";
		$id = $this->con->run($sql);
		$sql = "update " . $this->table_prefix . "settings set setting_value='".$inactive_color."' where user_id=" . $user_id . " AND setting='color_inactive';";
		$id = $this->con->run($sql);
		$sql = "update " . $this->table_prefix . "settings set setting_value='".$message."' where user_id=" . $user_id . " AND setting='message';";
		$id = $this->con->run($sql);

		// Reload settings
		$this->load_settings();
	}	

	function add_country($user_id,$country_id,$status_id,$url,$new_window){
		$sql = "insert into " . $this->table_prefix . "user_countries(user_id,country_id,status_id,url,new_window) VALUES(".$user_id.",".$country_id.",".$status_id.",'".$url."',".$new_window.");";
		$id = $this->con->insert($sql);
		// Reload countries
		$this->load_countries();
		$this->load_user_countries();
	}

	function update_country($id,$user_id,$country_id,$status_id,$url,$new_window){
		$sql = "update " . $this->table_prefix . "user_countries set user_id=".$user_id.",country_id=".$country_id.",status_id=".$status_id.",url='".$url."',new_window=".$new_window." where id=" . $id. ";";
		$id = $this->con->run($sql);
		// Reload countries
		$this->load_countries();
		$this->load_user_countries();
	}

	function delete_country($id){
		$sql = "delete from " . $this->table_prefix . "user_countries  where id=" . $id;
		$id = $this->con->run($sql);
		// Reload countries
		$this->load_countries();
		$this->load_user_countries();
	}

}
?>