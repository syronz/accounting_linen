<?php
require_once '../class/backup.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'backup';
switch ($_GET['action']) {

	case 'list':
		echo backup::lists($_GET["jtSorting"]=null,$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// 
		echo backup::create($_POST);
		break;
	case 'update':
		echo backup::update($_POST);
		break;
	case 'delete':
		echo backup::delete($_POST['id']);
		break;

	case 'search_list':
		$jTableResult['Result'] = "NO";
		$jTableResult['Message'] = "Search not work in this part";
		echo json_encode($jTableResult);
		break;

	case 'json_list':
		echo backup::json_list($table);
		break;

	case 'restore':
	// file_put_contents('a.txt', print_r($_GET, true));
		echo backup::restore($_GET["fileName"]);
		break;

	 
	default:
		
		break;
}




?>