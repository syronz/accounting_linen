<?php
require_once '../class/sell_facture_customer.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'sell_facture_customer';
switch ($_GET['action']) {

	case 'list':
		echo sell_facture_customer::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo sell_facture_customer::create($_GET);
		break;
	case 'update':
		echo sell_facture_customer::update($_POST);
		break;
	case 'delete':
		echo sell_facture_customer::delete($_POST['id']);
		break;

	case 'search_list':
		echo sell_facture_customer::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo sell_facture_customer::json_list($table);
		break;

	case 'loanList':
		echo sell_facture_customer::loanList($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"]);
		break;

	

	

	 
	default:
		
		break;
}




?>