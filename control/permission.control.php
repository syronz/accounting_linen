<?php
require_once '../class/permission.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'permission';

switch ($_GET['action']) {
	case 'list':
		echo permission::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo permission::create($_POST);
		break;
	case 'update':
		echo permission::update($_POST);
		break;
	case 'delete':
		echo permission::defaultDelete($_POST['id'],$table);
		break;

	case 'search_list':
		echo permission::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo permission::json_list($table);
		break;

	 
	default:
		
		break;
}




?>