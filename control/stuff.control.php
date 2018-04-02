<?php
require_once '../class/stuff.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'stuff';
switch ($_GET['action']) {

	case 'list':
		echo stuff::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	file_put_contents('n.txt', print_r($_POST, true));
		echo stuff::create($_POST);
		break;
	case 'update':
		echo stuff::update($_POST);
		break;
	case 'delete':
		echo stuff::defaultDelete($_POST['id'],$table);
		break;

	case 'search_list':
		echo stuff::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo stuff::json_list($table);
		break;

	case 'json_list_by_cat':
		echo stuff::json_list_by_cat($_GET['id_cat']);
		break;

	case 'stuffPerCat':
		echo stuff::stuffAsOptions($_GET['idCat']);
		break;

	case 'stuffPrice':
		echo stuff::stuffPrice($_GET['idStuff']);
		break;

	 
	default:
		
		break;
}




?>