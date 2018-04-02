<?php
require_once 'database.class.php';


class permission extends database{
	private static $TABLE = 'permission';

	public static function create($data){
		try{
			if(!self::perm('write',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to Create '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$id = self::indexId(self::$TABLE);

			$sql = "INSERT INTO ".self::$TABLE."(id,name,permission,user,user_activity) VALUES(:id,:name,:permission,:user,:user_activity);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':permission',$data['permission'],PDO::PARAM_STR);
			$stmt->bindParam(':user',$data['user'],PDO::PARAM_STR);
			$stmt->bindParam(':user_activity',$data['user_activity'],PDO::PARAM_STR);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','Write data to permission',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [permission.class.php/function create]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function update($data){
		try{

			if(!self::perm('edit',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to Edit '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$oldData = self::rowInfoArray(self::$TABLE,$data['id']);
			
			$sql = "UPDATE ".self::$TABLE." SET name = :name,permission = :permission,user = :user,user_activity = :user_activity WHERE id = :id;";


			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':permission',$data['permission'],PDO::PARAM_STR);
			$stmt->bindParam(':user',$data['user'],PDO::PARAM_STR);
			$stmt->bindParam(':user_activity',$data['user_activity'],PDO::PARAM_STR);
			$stmt->execute();
			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on permission','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [permission.class.php/function update]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'name'=>array('state'=>'self','field'=>'name')
			);
			
			self::record('read',"search permission's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'permission');
		}
		catch(exception $e){
			echo 'Error: [permission.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function checkPerm($state,$table,$idUser=null,$idPerm=null){
		try{
			if(!$idPerm && $idUser){
				$userInfo = self::rowInfoObject('user',$idUser);
				$idPerm = $userInfo->id_permission;
			}

			$row = self::rowInfoArray('permission',$idPerm);
			$check = @$row[$table];

			if($state == 'read')
				return $check[0];
			else if($state == 'write')
				return $check[1];
			else if($state == 'edit')
				return $check[2];
			else if($state == 'delete')
				return $check[3];
			else
				return null;
			

			// $sql = "SELECT $table FROM ".self::$TABLE." WHERE id = :idPerm";
			// $stmt = self::$PDO->prepare($sql);
			// $stmt->bindParam(':idPerm',$idPerm,PDO::PARAM_INT);
			// $stmt->execute();
			// $row = $stmt->fetchObject();

			
			return $row[$table];
		}
		catch(exception $e){
			echo 'Error: [permission.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}


}

// $data = array(
//     'name' => 'b3',
//     'id_permission' => '3',
//     'permissionname' => 'b',
//     'password' => 'b',
//     'phone' => 'b',
//     'register_date' => '2014-06-17',
//     'id' => '3');
// // dsh(permission::create($data));
// dsh(permission::update($data));
// dsh(permission::login('a','a'));
// dsh(permission::checkPerm('read','user_activity',1,2));

?>