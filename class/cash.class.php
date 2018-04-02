<?php
require_once 'database.class.php';
require_once 'dollar_rate.class.php';


class cash extends database{
	private static $TABLE = 'cash';

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
			$data['date'] = date('Y-m-d H:i:s',time());
			$data['id_user'] = $_SESSION['user']['id'];

			$preCash = self::last_id_data(self::$TABLE);

			$data['box_dollar'] = @$preCash->box_dollar + @$data['dollar'];
			$data['box_dinar'] = @$preCash->box_dinar + @$data['dinar'];

			$data['dollar_rate'] = dollar_rate::lastDollarRate();

			$sql = "INSERT INTO ".self::$TABLE."(id,id_user,date,type,id_f,dollar,dinar,detail,box_dollar,box_dinar,dollar_rate) VALUES(:id,:id_user,:date,:type,:id_f,:dollar,:dinar,:detail,:box_dollar,:box_dinar,:dollar_rate);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':id_user',$data['id_user'],PDO::PARAM_STR);
			$stmt->bindParam(':date',$data['date'],PDO::PARAM_STR);
			$stmt->bindParam(':type',$data['type'],PDO::PARAM_STR);
			$stmt->bindParam(':id_f',$data['id_f'],PDO::PARAM_STR);
			$stmt->bindParam(':dollar',$data['dollar'],PDO::PARAM_STR);
			$stmt->bindParam(':dinar',$data['dinar'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->bindParam(':box_dollar',$data['box_dollar'],PDO::PARAM_STR);
			$stmt->bindParam(':box_dinar',$data['box_dinar'],PDO::PARAM_STR);
			$stmt->bindParam(':dollar_rate',$data['dollar_rate'],PDO::PARAM_STR);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','Write data to cash',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [cash.class.php/function create]'.$e->getMessage().'<br>';
				file_put_contents('d.txt','ddd');
				die();
			}
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
			
			$sql = "UPDATE ".self::$TABLE." SET id_user = :id_user,type = :type,id_f = :id_f,dollar = :dollar,dinar = :dinar,detail = :detail WHERE id = :id;";
			$data['id_user'] = $_SESSION['user']['id'];

			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':id_user',$data['id_user'],PDO::PARAM_STR);
			$stmt->bindParam(':type',$data['type'],PDO::PARAM_STR);
			$stmt->bindParam(':id_f',$data['id_f'],PDO::PARAM_STR);
			$stmt->bindParam(':dollar',$data['dollar'],PDO::PARAM_STR);
			$stmt->bindParam(':dinar',$data['dinar'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->execute();

			$offset_dollar = $data['dollar'] - $oldData['dollar'];
			$offset_dinar = $data['dinar'] - $oldData['dinar'];
			self::update_box($data['id'],$offset_dollar,$offset_dinar);
			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on cash','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [cash.class.php/function update]'.$e->getMessage().'<br>';
				die();
			}
		}
	}


	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'user'=>array('state'=>'foreign','table'=>'user','field'=>'name','source'=>'id_user'),
				'date'=>array('state'=>'self','field'=>'date'),
				'type'=>array('state'=>'self','field'=>'type'),
				'dollar'=>array('state'=>'self','field'=>'dollar'),
				'dinar'=>array('state'=>'self','field'=>'dinar'),
				'box_dollar'=>array('state'=>'self','field'=>'box_dollar'),
				'box_dinar'=>array('state'=>'self','field'=>'box_dinar'),
				'detail'=>array('state'=>'self','field'=>'detail')
			);
			
			self::record('read',"search cash's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'cash');
		}
		catch(exception $e){
			echo 'Error: [cash.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function update_box($id,$offset_dollar,$offset_dinar){
		try{
			$sql = "UPDATE ".self::$TABLE." SET box_dollar = box_dollar + (:offset_dollar), box_dinar = box_dinar + (:offset_dinar)  WHERE id >= :id";
			// dsh($sql);
			// var_dump($id_user,$id,$offset_dollar,$offset_dinar,$offset_tman);
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':offset_dollar',$offset_dollar,PDO::PARAM_INT);
			$stmt->bindParam(':offset_dinar',$offset_dinar,PDO::PARAM_INT);
			// $stmt->bindParam(':id_user',$id_user,PDO::PARAM_INT);
			$stmt->bindParam(':id',$id,PDO::PARAM_INT);
			$stmt->execute();
			return true;
		}
		catch(PDOException $e){
			echo 'Error: [cash.class.php/function update_box]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function delete($id){
		try{
			if(!self::perm('delete',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to delete '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$oldData = self::rowInfoArray(self::$TABLE,$id);

			$sql = "DELETE FROM ".self::$TABLE." WHERE id = :id";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_INT);
			$stmt->execute();

			$offset_dollar = 0 - $oldData['dollar'];
			$offset_dinar = 0 - $oldData['dinar'];
			self::update_box($id,$offset_dollar,$offset_dinar);

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			self::record('write','WARNING : Delete data in '.self::$TABLE.' but havent permission','OLD DATA : '.self::httpBuildQuery($oldData));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [fund.class.php/function delete]'.$e->getMessage().'<br>';
			die();
		}
	}


}

// $data = array(
//     'type' => 'payin',
//     'dollar' => '200',
//     'dinnar' => '',
//     'detail' => 'good',
//     // 'box_dollar' => '',
//     // 'box_dinnar' => '',
//     'id' => '16');
// // dsh(cash::create($data));
// dsh(cash::update($data));
// dsh(cash::login('a','a'));

?>