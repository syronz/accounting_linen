<?php
require_once 'database.class.php';


class cat extends database{
	private static $TABLE = 'cat';

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

			$sql = "INSERT INTO ".self::$TABLE."(id,name,detail,less) VALUES(:id,:name,:detail,:less);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->bindParam(':less',$data['less'],PDO::PARAM_STR);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','write data to cat',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [cat.class.php/function create]'.$e->getMessage().'<br>';
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
			
			$sql = "UPDATE ".self::$TABLE." SET name = :name,detail = :detail, less = :less WHERE id = :id;";

			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->bindParam(':less',$data['less'],PDO::PARAM_STR);
			$stmt->execute();
			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on cat','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [cat.class.php/function update]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'name'=>array('state'=>'self','field'=>'name'),
				'detail'=>array('state'=>'self','field'=>'detail')
			);
			
			self::record('read',"search cat's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'cat');
		}
		catch(exception $e){
			echo 'Error: [cat.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function catAsOptions(){
		try{
			$sql = "SELECT * FROM ".self::$TABLE." WHERE less = 1 ORDER BY name ASC";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$str = '';
			foreach ($rows as $key => $value) {
				$str .= '<option value="'.$value['id'].'" m2="'.$value['detail'].'" >'.$value['name'].'</option>';
			}
			return $str;
		}
		catch(PDOException $e){
			echo 'Error: [cat.class.php/function catAsOptions]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function getM2($idCat){
		try{
			$cat = self::rowInfoObject('cat',$idCat);

			return $cat->detail;
		}
		catch(PDOException $e){
			echo 'Error: [cat.class.php/function getM2]'.$e->getMessage().'<br>';
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
// dsh(cat::create($data));
// dsh(cat::update($data));
// dsh(cat::login('a','a'));

?>