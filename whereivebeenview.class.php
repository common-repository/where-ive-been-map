<?php


class WhereIveBeenView{
	var $model;
	var $modes;
	var $width;
	var $height;

	function __construct($model){
		$this->model = $model;
		$this->modes 	= array("public_profile","admin_panel","admin_list","admin_new","admin_edit","admin_create","admin_update","admin_delete","load_data_country","load_data_link","load_settings");
	}

	function is_valid_mode($mode){
		
		foreach($this->modes as $tmp_mode){
			if($mode == $tmp_mode ){
				return true;
				break;
			}
		}
		return false;
	}

	function view_public_profile($width,$height){
		global $current_user;

		$this->width 	= $width;
		$this->height 	= $height;

		$output = "";
		// Loading the data from the profile of the user
		// Display his selected countries
		// Add the hover and click functionality

		$output .= "<div id='map_div' style='width:" . $this->width . ";height:" . $this->height . ";'></div>";

		return $output;
	}


	function view_admin_panel($width,$height){	

		$output = "";

		$output .= $this->view_public_profile($width,$height);

		$output .= "<div><a href=\"#\" onclick=\"$( '#dlg_whereivebeen_admin' ).dialog('open');return false;\">Open Admin Panel</a></div>";
		
		$output .= "<b><a href=\"#\" onclick=\"$('#map_settings').toggle();return false;\">Map Settings</a></b>";
		$output .= "<div id=\"map_settings\" style=\"display:none;width:400px;\"><form name=\"frmSettings\" method=\"post\" onsubmit=\"return false;\">";
		$output .= "<input type=\"hidden\" id=\"user_id\" name=\"user_id\" value=\"". $this->model->get_user_id() ."\">";
		$output .= "<div class=\"form_row\">Visited Color: <input type=\"text\" id=\"visited_color\" name=\"visited_color\" value=\"".$this->model->settings["color_visited"]."\" style=\"width:200px;\"></div>";
		$output .= "<div class=\"form_row\">To Visit Color: <input type=\"text\" id=\"tovisit_color\" name=\"tovisit_color\" value=\"".$this->model->settings["color_tovisit"]."\" style=\"width:200px;\"></div>";
		$output .= "<div class=\"form_row\">Inactive Color: <input type=\"text\" id=\"inactive_color\" name=\"inactive_color\" value=\"".$this->model->settings["color_inactive"]."\" style=\"width:200px;\"></div>";
		$output .= "<div class=\"form_row\">Custom Text: <input type=\"text\" id=\"message\" name=\"message\" value=\"".$this->model->settings["message"]."\" style=\"width:200px;\"></div>";
		$output .= "<div class=\"form_row\"><input type=\"button\" value=\"Save\" onclick=\"update_settings();\" style=\"width:80px;\"></div>";
		$output .= "</form>";
		$output .= "</div>";

		$output .= "<div id=\"dlg_whereivebeen_admin\" title=\"Admin Panel\">";
		$output .= "	<div id=\"admin_content\">";
		$output .= $this->view_admin_list();
		$output .= "	</div>";
		$output .= "</div>";
		$output .= "<script type=\"text/javascript\">";
		$output .= "$( \"#dlg_whereivebeen_admin\" ).dialog({ autoOpen: false , width: 300, height:300 }); ";
		$output .= "</script>";

		return $output;
	}

	function view_admin_list(){
		$output = "";

		$output .= "<form name=\"frmAddCountry\" method=\"post\" onsubmit=\"return false;\">";
		$output .= "<input type=\"hidden\" id=\"user_id\" name=\"user_id\" value=\"". $this->model->get_user_id() ."\">";
		$output .= "<div class=\"form_row\">Country: <select id=\"country_id\" name=\"country_id\" style=\"width:200px;\">";

			$countries = $this->model->get_countries();
			//var_dump($countries);
			foreach ($countries as $country) {
				$output .= "<option value=\"" . $country["country_id"] . "\">" . $country["name"] . "</option>";
			}

		
		$output .= "</select></div>";
		$output .= "<div class=\"form_row\">Status: <select id=\"status_id\" name=\"status_id\" style=\"width:200px;\">";
		$output .= "<option value=\"1\">Visited</option>";
		$output .= "<option value=\"0\">Going to</option>";		
		$output .= "</select></div>";
		$output .= "<div class=\"form_row\">URL: <input type=\"text\" id=\"url\" name=\"url\" value=\"\" style=\"width:200px;\"></div>";
		$output .= "<div class=\"form_row\">Open in new Window: <input type=\"checkbox\" id=\"new_window\" name=\"new_window\" value=\"1\"><input type=\"button\" value=\"Add\" onclick=\"add_country();\" style=\"width:80px;\"></div>";
		$output .= "</form>";
		$output .= "<div style=\"font-weight:bold;text-align:center;background-color:#dddddd;\">Your Countries</div>";

		$output .= "";

		$countries = $this->model->get_user_countries();
		foreach ($countries as $country) {
			$output .= "<div class=\"country_row\">" . $country["name"] ." (";
			if($country["status"]=="1"){
				$output .=  "Visited";
			}else{
				$output .= "Going to";
			}
			$output .= ") [<a href=\"#\" onclick=\"if( confirm('Are you sure') ){delete_country(" .  $country["id"] . ");}return false;\" class=\"delete_link\" style=\"color:#ff0000;\">x</a>]</div>";
		}

		return $output;
	}


	function view_admin_load_data_country(){
		?>[['Country', 'in', 'Message'],
				<?php
				$countries = $this->model->get_user_countries();
				//var_dump($countries);
				foreach ($countries as $country) {
					?>
					['<?php echo $country["name"];?>', <?php echo $country["status"];?>,'<?php echo $this->model->settings["message"];?>'],
					<?php
				}
				?>]<?php

	}

	function view_admin_load_data_links(){
		?>[		
				['Country', 'Url'],
				<?php
				$countries = $this->model->get_user_countries();
				//var_dump($countries);
				foreach ($countries as $country) {
					?>
					['<?php echo $country["name"];?>', '<?php echo $country["url"];?>'],
					<?php
				}
				?>]<?php

	}	


	function view_admin_load_settings(){
		?>options= {
			
			legend: 'none',
			colorAxis: {colors:['<?php echo $this->model->settings["color_tovisit"];?>','<?php echo $this->model->settings["color_visited"];?>']},
			datalessRegionColor: '<?php echo $this->model->settings["color_inactive"];?>'
			
        }<?php

	}	

	function get_head_js(){
		?>
		<script type="text/javascript">
		var chart ;
		var data;
		var links;		


		// Set options
		//width: 556,height: 347
		var options =  {
			
			legend: 'none',
			colorAxis: {colors:['<?php echo $this->model->settings["color_tovisit"];?>','<?php echo $this->model->settings["color_visited"];?>']},
			datalessRegionColor: '<?php echo $this->model->settings["color_inactive"];?>'
			
        };


		function drawRegionsMap(){				
				// Get data
				data = google.visualization.arrayToDataTable([
					['Country', 'in', 'Message'],
				<?php
				$countries = $this->model->get_user_countries();

				foreach ($countries as $country) {
					?>
					['<?php echo $country["name"];?>', <?php echo $country["status"];?>,'<?php echo $this->model->settings["message"];?>'],
					<?php
				}
				?>
		        ]);		

		        links = google.visualization.arrayToDataTable([		
				['Country', 'Url', 'new_window'],
				<?php
				$countries = $this->model->get_user_countries();
				//var_dump($countries);
				foreach ($countries as $country) {
					?>
					['<?php echo $country["name"];?>', '<?php echo $country["url"];?>', <?php echo $country["new_window"];?>],
					<?php
				}
				?>
		        ]);				        

			    // customize the tooltip
			    var formatter = new google.visualization.PatternFormat('{0}');
			    formatter.format( data, [0,1], 1 );
			    var formatter = new google.visualization.PatternFormat('{2}');
			    formatter.format( data, [0,1,2], 0 );	
	 

				// Draw the map
        		chart = new google.visualization.GeoChart( document.getElementById('map_div') );

        		// Handle Events
        		google.visualization.events.addListener(chart, 'select', handleSelect);
        		//google.visualization.events.addListener(chart, 'onmouseover', handleOnmouseOver);
        		//google.visualization.events.addListener(chart, 'onmouseout', handleOnmouseOut);

				// defines a view
			    var view = new google.visualization.DataView(data);
			    view.setColumns([0, 1]);    

				chart.draw(view, options);					
			}	
		</script>
		<?php
	}
}
?>