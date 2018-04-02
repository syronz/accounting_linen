<?php
require_once '../class/user.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'user';
	//file_put_contents('a.txt', print_r($_GET, true));
switch ($_GET['action']) {

	// case 'list':
	// 	echo user::lists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"]);
	// 	break;
	case 'list':
		echo user::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	file_put_contents('b.txt', print_r($_POST, true));
		echo user::create($_POST);
		break;
	case 'update':
		echo user::update($_POST);
		break;
	case 'delete':
		echo user::defaultDelete($_POST['id'],$table);
		break;

	case 'search_list':
		echo user::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo user::json_list($table);
		break;

	 
	default:
		
		break;
}




?>