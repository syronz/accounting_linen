<?php
require_once 'database.class.php';


class stuff extends database{
	private static $TABLE = 'stuff';

	public static function create($data){
		try{
			// file_put_contents('a.txt', print_r($data,true));

			if(!self::perm('write',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to Create '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$id = self::indexId(self::$TABLE);
			$register_date = date('Y-m-d H:i:s',time());

			$sql = "INSERT INTO ".self::$TABLE."(id,id_cat,name,code,price,qty,detail) VALUES(:id,:id_cat,:name,:code,:price,:qty,:detail);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':id_cat',$data['id_cat'],PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':code',$data['code'],PDO::PARAM_STR);
			$stmt->bindParam(':price',$data['price'],PDO::PARAM_STR);
			$stmt->bindParam(':qty',$data['qty'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','write data to stuff',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [stuff.class.php/function create]'.$e->getMessage().'<br>';
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
			
			$sql = "UPDATE ".self::$TABLE." SET id_cat=:id_cat, name = :name, code = :code, price = :price, qty = :qty, detail = :detail WHERE id = :id;";

			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':id_cat',$data['id_cat'],PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':code',$data['code'],PDO::PARAM_STR);
			$stmt->bindParam(':price',$data['price'],PDO::PARAM_STR);
			$stmt->bindParam(':qty',$data['qty'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->execute();
			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on stuff','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [stuff.class.php/function update]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'cat'=>array('state'=>'foreign','table'=>'cat','field'=>'name','source'=>'id_cat'),
				'name'=>array('state'=>'self','field'=>'name'),
				'price'=>array('state'=>'self','field'=>'price'),
				'qty'=>array('state'=>'self','field'=>'qty'),
				'detail'=>array('state'=>'self','field'=>'detail')
			);
			
			self::record('read',"search stuff's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'stuff');
		}
		catch(exception $e){
			echo 'Error: [stuff.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function json_list_by_cat($id_cat){
		try{
			$sql = "SELECT id AS Value,name AS DisplayText FROM ".self::$TABLE." WHERE id_cat = $id_cat ORDER BY name" ;
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Options'] = $rows;
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [stuff.class.php/function json_list_by_cat]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function stuffAsOptions($idCat){
		try{
			$sql = "SELECT * FROM ".self::$TABLE." WHERE id_cat = '$idCat' ORDER BY name ASC";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$str = '<option value="0">'.$idCat.'</option>';
			foreach ($rows as $key => $value) {
				$str .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
			}
			return $str;
		}
		catch(PDOException $e){
			echo 'Error: [stuff.class.php/function stuffAsOptions]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function stuffPrice($idStuff){
		try{
			$stuff = self::rowInfoObject('stuff',$idStuff);

			return $stuff->price;
		}
		catch(PDOException $e){
			echo 'Error: [stuff.class.php/function stuffPrice]'.$e->getMessage().'<br>';
			die();
		}
	}


}

// $data = array(
//     'id_cat' => '3',
//     'name' => 'teststuff',
//     'qty' => '36',
//     'price' => '16',
//     'detail' => 'diako test',
//     'id' => '463');
// dsh(stuff::create($data));
// dsh(stuff::update($data));
// dsh(stuff::login('a','a'));

?>