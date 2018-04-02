$(document).ready(function (){

	$('#print_btn').click(function(){
		// alert();
		var printDivCSS = new String ('<link href="scripts/jtable.2.3.1/themes/metro/blue/jtable.css" rel="stylesheet" type="text/css" />'+'<link href="themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet">'+'<link href="print_style.css" rel="stylesheet" type="text/css" />');
	    var styles = '<style> td{text-decoration:underline;} </style>';
	    window.frames["print_frame"].document.body.innerHTML=printDivCSS + document.getElementById("main").innerHTML;
	    window.frames["print_frame"].window.focus();
	    window.frames["print_frame"].window.print();
	});
});
	