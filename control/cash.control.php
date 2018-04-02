<?php
require_once '../class/cash.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'cash';
switch ($_GET['action']) {

	case 'list':
		echo cash::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo cash::create($_POST);
		break;
	case 'update':
		echo cash::update($_POST);
		break;
	case 'delete':
		echo cash::delete($_POST['id']);
		break;

	case 'search_list':
		echo cash::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo cash::json_list($table);
		break;

	 
	default:
		
		break;
}




?>