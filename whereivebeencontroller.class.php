<?php
// Load Model and view
include_once "whereivebeenview.class.php";
include_once "whereivebeenmodel.class.php";

class WhereIveBeenController{
	var $model;
	var $views;
	var $mode;


	function __construct($user_id, $mode){
		$this->mode 	= $mode;
		$this->model 	= new WhereIveBeenModel($user_id);
		$this->views	= new WhereIveBeenView($this->model);
	}

	function display($width=500,$height=300){
		$output = "";
		if( $this->views->is_valid_mode($this->mode) ){
			if($this->mode == "public_profile"){
				$output = $this->views->view_public_profile($width,$height);
			}elseif($this->mode == "admin_panel"){
				$output = $this->views->view_admin_panel($width,$height);
			}elseif($this->mode == "admin_delete"){
				$output = $this->views->view_admin_list();
			}elseif($this->mode == "admin_create"){
				$output = $this->views->view_admin_list();
			}elseif($this->mode == "load_data_country"){
				$output = $this->views->view_admin_load_data_country();
			}elseif($this->mode == "load_data_link"){
				$output = $this->views->view_admin_load_data_links();
			}elseif($this->mode == "load_settings"){
				$output = $this->views->view_admin_load_settings();
			}else{
				
			}

		}
		return $output;

	}

}
?>