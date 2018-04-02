<?php
require_once '../class/payin.class.php';
if(!isset($_SESSION['user']))
		die();
$table = 'payin';
switch ($_GET['action']) {

	case 'list':
		// echo payin::defaultLists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$table);
		echo payin::lists($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],'payin');
		break;
	case 'create':
		// dsh($_GET);
	// file_put_contents('b.txt', print_r($_POST, true));
		echo payin::create($_POST);
		break;
	case 'update':
		echo payin::update($_POST);
		break;
	case 'delete':
		echo payin::delete($_POST['id']);
		break;

	case 'search_list':
		echo payin::search_list($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['search_str']);
		break;

	case 'json_list':
		echo payin::json_list($table);
		break;

	case 'listSellFactureCustomerPayin':
		// echo 'ssss';
		echo payin::listSellFactureCustomerPayin($_GET["jtSorting"],$_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET['idSellFacture']);
		break;

	case 'createForFacture':
		echo payin::createForFacture($_POST,$_GET['idSellFacture'],$_GET['idAccount']);
		break;

	 
	default:
		
		break;
}




?>