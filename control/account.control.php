<?php
require_once '../class/account.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'account';
switch ($_GET['action']) {

	case 'list':
		echo account::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo account::create($_POST);
		break;
	case 'update':
		echo account::update($_POST);
		break;
	case 'delete':
		echo account::defaultDelete($_POST['id'],$table);
		break;

	case 'search_list':
		echo account::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo account::json_list($table);
		break;

	case 'json_list_branch':
		echo account::json_list_branch();
		break;

	case 'json_listWithType':
		echo account::jsonListWithType();
		break;

	case 'branchLists':
		echo account::branchLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"]);
		break;

	case 'customerId':
		echo account::customerId($_GET['data']);
		break;

	case 'accountBalanceReport':
		echo account::accountBalanceReport($_GET['idAccount']);
		break;

	 
	default:
		
		break;
}




?>