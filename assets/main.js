/* 
 * This should be loaded after jQuery
 */

$(document).ready(function(){
	
})


function add_line_click(){
	var line = $('#first_line').html();
	$('#function_args_table').append("<tr> " + line + "</tr>");
}

function redirect(location)
{
	window.location.href= location;
}

