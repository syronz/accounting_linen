<?php
require_once '../class/sell_facture_daily.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'sell_facture_daily';
switch ($_GET['action']) {

	case 'list':
		echo sell_facture_daily::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo sell_facture_daily::create($_POST);
		break;
	case 'update':
		echo sell_facture_daily::update($_POST);
		break;
	case 'delete':
		echo sell_facture_daily::defaultDelete($_POST['id'],$table);
		break;

	case 'search_list':
		echo sell_facture_daily::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo sell_facture_daily::json_list($table);
		break;

	

	 
	default:
		
		break;
}




?>