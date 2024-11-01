// Load the Visualization API and the geochart package.
google.load('visualization', '1', {'packages': ['geochart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawRegionsMap);




				

function handleSelect(){
	var selectedItem = chart.getSelection()[0];
	var value = data.getValue(selectedItem.row, 0);
	var link = links.getValue(selectedItem.row, 1);
	var new_window = links.getValue(selectedItem.row, 2);
	if(new_window == 1){
		window.open(link,'_blank','');
	}else{
		window.location.href = link;
	}
}

function handleOnmouseOver(){
	alert('The user handleOnmouseOver ' + value);
}

function handleOnmouseOut(){
	alert('The user handleOnmouseOut ' + value);
}	