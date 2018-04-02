<?php
require_once '../class/cat.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'cat';
switch ($_GET['action']) {

	case 'list':
		echo cat::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo cat::create($_POST);
		break;
	case 'update':
		echo cat::update($_POST);
		break;
	case 'delete':
		echo cat::defaultDelete($_POST['id'],$table);
		break;

	case 'search_list':
		echo cat::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo cat::json_list($table);
		break;

	case 'getM2':
		echo cat::getM2($_GET['idCat']);
		break;
	 
	default:
		
		break;
}




?>