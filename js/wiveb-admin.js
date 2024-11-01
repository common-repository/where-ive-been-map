function redrawRegionsMap(){						        

	// Draw the map
	chart = new google.visualization.GeoChart( document.getElementById('map_div') );

	// Handle Events
	google.visualization.events.addListener(chart, 'select', handleSelect);
	//google.visualization.events.addListener(chart, 'onmouseover', handleOnmouseOver);
	//google.visualization.events.addListener(chart, 'onmouseout', handleOnmouseOut);

    // customize the tooltip
    var formatter = new google.visualization.PatternFormat('{0}');
    formatter.format( data, [0,1], 1 );
    var formatter = new google.visualization.PatternFormat('{2}');
    formatter.format( data, [0,1,2], 0 );	

	// defines a view
    var view = new google.visualization.DataView(data);
    view.setColumns([0, 1]);    

	chart.draw(view, options);				
}

function update_settings(){		
	var user_id 		= $("#user_id").val(); 
	var visited_color 	= $("#visited_color").val(); 
	var tovisit_color  	= $("#tovisit_color").val();
	var inactive_color 	= $("#inactive_color").val(); 
	var message 		= $("#message").val(); 

	$.ajax({
	  type: "POST",
	  url: ajax_url,
	  data: { action: 'wiveb_update_settings',user_id: user_id, visited_color: visited_color,tovisit_color: tovisit_color, inactive_color: inactive_color, message: message }
	}).success(function( content  ) {
		reload_settings(user_id);		
	});				
}	

function reload_settings(user_id){				
	$.ajax({
	  type: "GET",
	  url: ajax_url,
	  data: {action: 'wiveb_reload_settings',user_id: user_id }
	}).success(function( data_arr  ) {
	   eval(data_arr);
	   reload_countries(user_id);
	});					
}

function add_country(){		

	var user_id 	= $("#user_id").val(); 
	var country_id  = $("#country_id").val();
	var status_id 	= $("#status_id").val(); 
	var url 		= $("#url").val(); 
	var new_window = 0;
	if( $("#new_window").is(':checked') ){new_window = 1;}

 	$.ajax({
	  type: "POST",
	  url: ajax_url ,
	  data: { action: 'wiveb_add_country', user_id: user_id, country_id: country_id,status_id: status_id, url: url, new_window: new_window }
	}).success(function( content  ) {
	   $("#admin_content").html(content);
	   reload_countries(user_id);				   
	});				
}	

function delete_country(id){	
	var user_id 	= $("#user_id").val();			

	$.ajax({
	  type: "GET",
	  url: ajax_url,
	  data: {action: 'wiveb_delete_country', user_id: user_id, id: id }
	}).success(function( content  ) {
	   $("#admin_content").html(content);
	   reload_countries(user_id);
	   
	});					
}	

function reload_links(user_id){				
	$.ajax({
	  type: "GET",
	  url:  ajax_url,
	  data: {action: 'wiveb_reload_links', user_id: user_id }
	}).success(function( data_arr  ) {
	   links = google.visualization.arrayToDataTable( eval(data_arr));
	   redrawRegionsMap();
	});					
}

function reload_countries(user_id){				
	$.ajax({
	  type: "GET",
	  url: ajax_url ,
	  data: {action: 'wiveb_reload_countries', user_id: user_id }
	}).success(function( data_arr  ) {
	   data = google.visualization.arrayToDataTable( eval(data_arr));
	   reload_links(user_id);
	});					
}	