<?php
require_once 'database.class.php';


class dollar_rate extends database{
	private static $TABLE = 'dollar_rate';

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
			$data['id_user'] = $_SESSION['user']['id'];

			$sql = "INSERT INTO ".self::$TABLE."(id,id_user,price,detail,date) VALUES(:id,:id_user,:price,:detail,:date);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':id_user',$data['id_user'],PDO::PARAM_STR);
			$stmt->bindParam(':price',$data['price'],PDO::PARAM_STR);
			$stmt->bindParam(':date',$register_date,PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','write data to dollar_rate',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [dollar_rate.class.php/function create]'.$e->getMessage().'<br>';
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
			
			$sql = "UPDATE ".self::$TABLE." SET id_user = :id_user,price = :price,detail = :detail WHERE id = :id;";
			$data['id_user'] = $_SESSION['user']['id'];

			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':id_user',$data['id_user'],PDO::PARAM_STR);
			$stmt->bindParam(':price',$data['price'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->execute();
			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on dollar_rate','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [dollar_rate.class.php/function update]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'user'=>array('state'=>'foreign','table'=>'user','field'=>'name','source'=>'id_user'),
				'price'=>array('state'=>'self','field'=>'price'),
				'date'=>array('state'=>'self','field'=>'date'),
				'detail'=>array('state'=>'self','field'=>'detail')
			);
			
			self::record('read',"search dollar_rate's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'dollar_rate');
		}
		catch(exception $e){
			echo 'Error: [dollar_rate.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function lastDollarRate(){
		try{
			return 1320;
			$dollarRateInfo = self::last_id_data(self::$TABLE);
			return $dollarRateInfo->price;
		}
		catch(exception $e){
			echo 'Error: [dollar_rate.class.php/function lastDollarRate]'.$e->getMessage().'<br>';
			die();
		}
	}


}

// $data = array(
//     'price' => '352',
//     'detail' => 'b',
//     'id' => '3');
// dsh(dollar_rate::create($data));
// dsh(dollar_rate::update($data));
// dsh(dollar_rate::login('a','a'));

?>