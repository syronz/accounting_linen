<?php
require_once '../class/sell_facture_report.class.php';
if(!isset($_SESSION['user']))
		die();

switch ($_GET['action']) {


	case 'stuffReportDay':
		// dsh($_GET);
		echo sell_facture_report::stuffReportDay($_GET["date"]);
		break;
	case 'stuffReportMonth':
		echo sell_facture_report::stuffReportMonth($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"]);
		break;
	case 'delete':
		echo sell_facture_report::defaultDelete($_POST['id'],$table);
		break;

	case 'search_list':
		echo sell_facture_report::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo sell_facture_report::json_list($table);
		break;
	case 'sellListDay':
		echo sell_facture_report::reportListDayDetail($_GET['date']);
		break;
	

	 
	default:
		
		break;
}




?>