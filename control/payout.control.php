<?php
require_once '../class/payout.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'payout';
switch ($_GET['action']) {

	case 'list':
		echo payout::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo payout::create($_POST);
		break;
	case 'update':
		echo payout::update($_POST);
		break;
	case 'delete':
		echo payout::delete($_POST['id']);
		break;

	case 'search_list':
		echo payout::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo payout::json_list($table);
		break;

	 
	default:
		
		break;
}




?>