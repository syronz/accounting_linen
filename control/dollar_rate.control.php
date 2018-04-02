<?php
require_once '../class/dollar_rate.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'dollar_rate';
switch ($_GET['action']) {

	case 'list':
		echo dollar_rate::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo dollar_rate::create($_POST);
		break;
	case 'update':
		echo dollar_rate::update($_POST);
		break;
	case 'delete':
		echo dollar_rate::defaultDelete($_POST['id'],$table);
		break;

	case 'search_list':
		echo dollar_rate::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo dollar_rate::json_list($table);
		break;

	 
	default:
		
		break;
}




?>