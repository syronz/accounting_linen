<?php
require_once 'database.class.php';


class user extends database{
	private static $TABLE = 'user';
	public static $FIELDS = array();
	private static $PASSKEY = 'bZhiDiak0~m|rPr0grAmm3r';

	public static function create($data){
		try{
			// file_put_contents('a.txt', print_r($data));

			if(!self::perm('write',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to Create '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$id = self::indexId(self::$TABLE);
			$register_date = date('Y-m-d H:i:s',time());

			$sql = "INSERT INTO ".self::$TABLE."(id,name,id_permission,phone,username,password,image_url,register_date) VALUES(:id,:name,:id_permission,:phone,:username,:password,:image_url,:register_date);";
			$data['password'] = md5(self::$PASSKEY.$data['password']);
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':id_permission',$data['id_permission'],PDO::PARAM_STR);
			$stmt->bindParam(':phone',$data['phone'],PDO::PARAM_STR);
			$stmt->bindParam(':username',$data['username'],PDO::PARAM_STR);
			$stmt->bindParam(':password',$data['password'],PDO::PARAM_STR);
			$stmt->bindParam(':register_date',$register_date,PDO::PARAM_STR);
			$stmt->bindParam(':image_url',$data['image_url'],PDO::PARAM_STR);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','write data to userm',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [user.class.php/function create]'.$e->getMessage().'<br>';
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
			
			$sql = "UPDATE ".self::$TABLE." SET name = :name,id_permission = :id_permission,phone = :phone,username = :username,password = :password,image_url = :image_url WHERE id = :id;";


			if(strlen($data['password']) < 25)
				$password = md5(self::$PASSKEY.$data['password']);
			else
				$password = $data['password'];
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':id_permission',$data['id_permission'],PDO::PARAM_STR);
			$stmt->bindParam(':phone',$data['phone'],PDO::PARAM_STR);
			$stmt->bindParam(':username',$data['username'],PDO::PARAM_STR);
			$stmt->bindParam(':password',$password,PDO::PARAM_STR);
			$stmt->bindParam(':image_url',$data['image_url'],PDO::PARAM_STR);
			$stmt->execute();
			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on userm','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [user.class.php/function update]'.$e->getMessage().'<br>';
			die();
		}
	}

	



	public static function select_list($part){
		try{
			$where = self::return_user_range($part);
			$sql = "SELECT id,name FROM ".self::$TABLE." WHERE {$where['user']} " ;
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		}
		catch(PDOException $e){
			echo 'Error: [user.class.php/function select_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'name'=>array('state'=>'self','field'=>'name'),
				// 'permission'=>array('state'=>'foreign','table'=>'permission','field'=>'name','source'=>'id_permission'),
				'phone'=>array('state'=>'self','field'=>'phone'),
				'date'=>array('state'=>'self','field'=>'register_date'),
				'username'=>array('state'=>'self','field'=>'username')
			);
			
			self::record('read',"search user's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'user');
		}
		catch(exception $e){
			echo 'Error: [user.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function login($username,$password){
		try{
			
			$sql = "SELECT * FROM 'user' WHERE username = :username AND password = :password";
			$password = md5(self::$PASSKEY.$password);
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':username',$username,PDO::PARAM_STR);
			$stmt->bindParam(':password',$password,PDO::PARAM_STR);
			$stmt->execute();
			$row['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

			// $row['permission'] = permission::return_one_permission($row['user']['id_permission']);
			//if($row->id_permission)
			self::record('write','login to system',"DATA : username = $username ");
			return $row;

		}
		catch(PDOException $e){
			echo 'Error: [user.class.php/function login]'.$e->getMessage().'<br>';
			die();
		}
	}


}

// $data = array(
//     'name' => 'b3',
//     'id_permission' => '3',
//     'username' => 'b',
//     'password' => 'b',
//     'phone' => 'b',
//     'register_date' => '2014-06-17',
//     'id' => '3');
// dsh(user::create($data));
// dsh(user::update($data));
// dsh(user::login('a','a'));

?>