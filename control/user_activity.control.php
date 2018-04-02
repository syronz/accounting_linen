<?php
require_once '../class/user_activity.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'user_activity';

switch ($_GET['action']) {
	case 'list':
		echo user_activity::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	// case 'create':
	// 	// file_put_contents('b.txt', print_r($_POST, true));
	// 	echo user_activity::create($_POST);
	// 	break;
	// case 'update':
	// 	echo user_activity::update($_POST);
	// 	break;
	// case 'delete':
	// 	echo user_activity::defaultDelete($_POST['id'],$table);
	// 	break;

	case 'search_list':
		echo user_activity::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	 
	default:
		
		break;
}




?>