<?php
@session_start();
error_reporting(E_ALL);
date_default_timezone_set("Asia/Baghdad");
require_once 'dictionary.ku.class.php';
require_once 'setting.class.php';
require_once 'user_activity.class.php';
require_once 'search.class.php';
require_once 'permission.class.php';

function dsh($v){
	echo '<pre style="color:red">';
	ob_start();
	var_dump($v);
	$result = ob_get_clean();
	$result = str_replace(">\n", '>', $result);
	echo $result;
	echo '</pre>';
}

function dsh_money($money,$decimal_check = 2,$symbol = null){
	$negative = false;
	if($money < 0){
		$negative = true;
		$money = abs($money);
	}
	if($money == 0)
		return '0';
	$decimal = $money - intval($money);
	$arr = array();
	if($money !== 0)
		while($money){
			$part = strval($money % 1000);
			$len = strlen($part);
			if($len == 1)
				$part = '00'.$part;
			else if($len == 2)
				$part = '0'.$part;
			$money =intval($money/1000);
			array_push($arr, $part);
		}
	else
		$arr = array(0);
	$arr = array_reverse($arr);
	
	$str = implode(',', $arr);
	if(strlen($str)>1){
		if($str[0]=='0')
			$str = substr($str, 1);
		if($str[0]=='0')
			$str = substr($str, 1);
	}
	
	if($decimal_check)
		if(round($decimal,$decimal_check))
			$str .= substr(strval(round($decimal,2)),1);
	if($symbol)
		$str .= ' '.$symbol;

	if($negative)
		$str = '-'.$str;
	return $str;
}

function dsh_convert_number($string) {
    $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');

    $num = range(0, 9);
    $string = str_replace($persian, $num, $string);
    return str_replace($arabic, $num, $string);
}


class database{
	public $pdo;
	public static $PDO;
	function __construct(){
		try {
		// if(strpos($_SERVER['HTTP_USER_AGENT'],'Linux') === false || strpos($_SERVER['HTTP_USER_AGENT'],'Chrome') === false)
		// 	if(strpos($_SERVER['HTTP_USER_AGENT'],'Mozilla/5.0 (iPad; CPU OS 7_1 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) CriOS/26.0.1410.50 Mobile/11D167 Safari/8536.25') === false){
		// 		header('location:'.setting::APP_URL.'/login.php?alert=Try Again!');
		// 		// die('You Havent Permission'); 
		// 	}
	    // $this->pdo = new PDO('mysql:host=localhost;dbname=laboratory', 'root', setting::MYSQL_PASSWORD);
	    // $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    // $this->pdo->query('SET NAMES utf8');

		$this->pdo = new PDO('sqlite:../p.db');
	    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    // $this->pdo->query('SET NAMES utf8');

	    /*static mode*/
	    self::$PDO = $this->pdo;
		} 
		catch (PDOException $e) {
		    print "Error!: " . $e->getMessage() . "<br/>";
		    die();
		}
	}

	public static function hack_pageSize($startIndex,$pageSize){
		$startIndex = intval($startIndex);
		$pageSize = intval($pageSize);
		if(!$pageSize)
			die('Hack Detection!');
	}

	public static function hack_sorting($sorting){
		$sorting = str_replace("'", "", $sorting);
		$sorting = str_replace('"', '"', $sorting);
		return $sorting;
	}

	private static function check_column_exist($column,$table){
		try{
			$sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			if($rows)
				return true;
			return NULL;
		}
		catch(PDOException $e){
			return null;
		}
		
	}


	public static function record($type,$action,$detail=null){
		switch ($type) {
			case 'read':
				if(setting::USER_ACTIVITY_READ)
					@user_activity::record_activity($_SERVER['REMOTE_ADDR'],$_SESSION['user']['id'],$action,$detail);
				break;
			case 'write':
				if(setting::USER_ACTIVITY_WRITE)
					user_activity::record_activity(@$_SERVER['REMOTE_ADDR'],@$_SESSION['user']['id'],@$action,@$detail);
				break;
			
			default:
				user_activity::record_activity($_SERVER['REMOTE_ADDR'],$_SESSION['user']['id'],$action,$detail.$type);
				break;
		}
	}

	public static function search($sorting,$startIndex,$pageSize,$search_str,$arr_table,$table,$other_table = null){
		try{
			return search::make_search_list($sorting,$startIndex,$pageSize,$search_str,$arr_table,$table,$other_table);
		}
		catch(exception $e){
			echo 'Error: [database.class.php/function search]'.$e->getMessage().'<br>';
			die();
		}
	}

//for jtable
	public static function calculate_rows($table){
		try{
			$sql = "SELECT count(id) AS count FROM $table";
			$result = self::$PDO->query($sql);
			$count = $result->fetchObject();
			return $count->count;
		}
		catch(PDOException $e){
			echo 'Error: [database.class.php/function calculate_row]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function last_id_data($table){
		try{
			$sql = "SELECT * FROM $table ORDER BY id DESC LIMIT 1";
			$stmt = self::$PDO->query($sql);
			$row = $stmt->fetchObject();
			return $row;
		}
		catch(PDOException $e){
			echo 'Error: [database.class.php/function last_id_data]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function indexId($table){
		try{
			$lastId = self::last_id_data($table);
			if($lastId)
				return $lastId->id + 1;
			else
				return 1;
		}
		catch(PDOException $e){
			echo 'Error: [database.class.php/function indexId]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function go_to_login(){
		// header('location:'.SETTING::APP_URL.'/login.php');
		echo '<script>window.location.hash = "logout"</script>';
	}

	public static function calculateRow($table){
		try{
			$sql = "SELECT count(id) AS count FROM ".$table;
			$result = self::$PDO->query($sql);
			$count = $result->fetchObject();
			return $count->count;
		}
		catch(PDOException $e){
			echo 'Error: [database.class.php/function calculate_row]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function defaultLists($sorting,$startIndex,$pageSize,$table){
		try{
			//var_dump($where);
			if(!self::perm('read',$table,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View '.$table.' Table But havent permission');
				return json_encode($jTableResult);
			}

			self::hack_pageSize($startIndex,$pageSize);
			$sorting = self::hack_sorting($sorting);
			$sql = "SELECT * FROM ".$table." ORDER BY $sorting LIMIT $startIndex, $pageSize;";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = self::calculateRow($table);
			$jTableResult['Records'] = $rows;
			self::record('read','View '.$table.' Table',"sorting = $sorting, startIndex = $startIndex,pageSize = $pageSize");
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [database.class.php/function lists]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function defaultDelete($id,$table){
		try{
			if(!self::perm('delete',$table,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to Delete '.$table.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$oldData = self::rowInfoArray($table,$id);

			$sql = "DELETE FROM ".$table." WHERE id = :id";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$_POST['id'],PDO::PARAM_INT);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			self::record('write','WARNING : Delete data in '.$table,'OLD DATA : '.self::httpBuildQuery($oldData));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [database.class.php/function defaultDelete]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function rowInfoArray($table,$id){
		try{
			$sql = "SELECT * FROM ".$table." WHERE id = :id";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			return $row;
		}
		catch(PDOException $e){
			echo 'Error: [user.class.php/function get_user_info]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function rowInfoObject($table,$id){
		try{
			$sql = "SELECT * FROM ".$table." WHERE id = :id";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetchObject();

			return $row;
		}
		catch(PDOException $e){
			echo 'Error: [user.class.php/function get_user_info]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function httpBuildQuery($str){
		if(is_object($str) || is_array($str))
			$str = urldecode(http_build_query($str));
		
		$str = str_replace('&', ' ', $str);
		return $str;
	}


	public static function json_list($table){
		try{
			$sql = "SELECT id AS Value,name AS DisplayText FROM $table ORDER BY name" ;
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Options'] = $rows;
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [user.class.php/function json_list]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function perm($state,$table,$idUser=null,$idPerm=null){
		try{
			return permission::checkPerm($state,$table,$idUser,$idPerm);
		}
		catch(exception $e){
			echo 'Error: [database.class.php/function perm]'.$e->getMessage().'<br>';
			die();
		}
	}

	// public static function vJtable($value,$type){
	// 	try{
	// 		$jTableResult['Result'] = "NO";
			
	// 		if($type == 'number'){
	// 			if(is_numeric($value))
	// 				return false;
	// 			else{
	// 				$jTableResult['Message'] = "You Havent Permission";
	// 				self::record('write','WARNING','problem in validate '.$value.' is not number');
	// 				return json_encode($jTableResult);
	// 			}
	// 		}
	// 	}
	// 	catch(exception $e){
	// 		echo 'Error: [database.class.php/function validateJtable]'.$e->getMessage().'<br>';
	// 		die();
	// 	}
	// }

}

$db = new database();
//dsh(database::last_id_data('stuff'));
//$db->return_company_list();
//echo $db->return_company_list();
// database::check_perm_manage('diako',225);
?>