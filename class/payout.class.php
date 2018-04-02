<?php
require_once 'database.class.php';
require_once 'dollar_rate.class.php';
require_once 'cash.class.php';


class payout extends database{
	private static $TABLE = 'payout';

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
			$data['id_cash'] = self::indexId('cash');
			$data['date'] = date('Y-m-d H:i:s',time());
			$data['id_user'] = $_SESSION['user']['id'];

			$prepayout = self::last_id_data(self::$TABLE);

			$data['dollar_rate'] = dollar_rate::lastDollarRate();

			$sql = "INSERT INTO ".self::$TABLE."(id,id_user,id_account,date,dollar,dinar,dollar_rate,id_cash,detail) VALUES(:id,:id_user,:id_account,:date,:dollar,:dinar,:dollar_rate,:id_cash,:detail);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':id_user',$data['id_user'],PDO::PARAM_STR);
			$stmt->bindParam(':id_account',$data['id_account'],PDO::PARAM_STR);
			$stmt->bindParam(':date',$data['date'],PDO::PARAM_STR);
			$stmt->bindParam(':dollar',$data['dollar'],PDO::PARAM_STR);
			$stmt->bindParam(':dinar',$data['dinar'],PDO::PARAM_STR);
			$stmt->bindParam(':dollar_rate',$data['dollar_rate'],PDO::PARAM_STR);
			$stmt->bindParam(':id_cash',$data['id_cash'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->execute();

			$accountInfo = self::rowInfoObject('account',$data['id_account']);
			$detailCash = $accountInfo->name.' / '.$data['detail'];
			if(@$data['not_add_to_cash'])
				cash::create(array('dollar'=>0,'dinar'=>0,'type'=>'payout','id_f'=>$id,'detail'=>'NOT ADD - '.$detailCash));
			else
				cash::create(array('dollar'=>-$data['dollar'],'dinar'=>-$data['dinar'],'type'=>'payout','id_f'=>$id,'detail'=>$detailCash));


			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','Write data to payout',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [payout.class.php/function create]'.$e->getMessage().'<br>';
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
			
			$sql = "UPDATE ".self::$TABLE." SET id_user = :id_user,id_account = :id_account,dollar = :dollar,dinar = :dinar,dollar_rate = :dollar_rate,detail = :detail WHERE id = :id;";
			$data['id_user'] = $_SESSION['user']['id'];

			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':id_user',$data['id_user'],PDO::PARAM_STR);
			$stmt->bindParam(':id_account',$data['id_account'],PDO::PARAM_STR);
			$stmt->bindParam(':dollar',$data['dollar'],PDO::PARAM_STR);
			$stmt->bindParam(':dinar',$data['dinar'],PDO::PARAM_STR);
			$stmt->bindParam(':dollar_rate',$data['dollar_rate'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->execute();

			$accountInfo = self::rowInfoObject('account',$data['id_account']);
			$detailCash = $accountInfo->name.' / '.$data['detail'];
			if(@$data['not_add_to_cash'])
				cash::update(array('dollar'=>0,'dinar'=>0,'type'=>'payout','id_f'=>$id,'detail'=>'NOT ADD - '.$detailCash, 'id'=>$oldData['id_cash']));
			else
				cash::update(array('dollar'=>-$data['dollar'],'dinar'=>-$data['dinar'],'type'=>'payout','id_f'=>$id,'detail'=>$detailCash, 'id'=>$oldData['id_cash']));

			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on payout','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [payout.class.php/function update]'.$e->getMessage().'<br>';
				die();
			}
		}
	}


	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'user'=>array('state'=>'foreign','table'=>'user','field'=>'name','source'=>'id_user'),
				'account'=>array('state'=>'foreign','table'=>'account','field'=>'name','source'=>'id_account'),
				'date'=>array('state'=>'self','field'=>'date'),
				'dollar'=>array('state'=>'self','field'=>'dollar'),
				'dinar'=>array('state'=>'self','field'=>'dinar'),
				'dollar_rate'=>array('state'=>'self','field'=>'dollar_rate'),
				'detail'=>array('state'=>'self','field'=>'detail')
			);
			
			self::record('read',"search payout's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'payout');
		}
		catch(exception $e){
			echo 'Error: [payout.class.php/function search_list]'.$e->getMessage().'<br>';
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

			cash::delete($oldData['id_cash']);

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
//     'type' => 'payout',
//     'dollar' => '200',
//     'dinnar' => '',
//     'detail' => 'good',
//     // 'box_dollar' => '',
//     // 'box_dinnar' => '',
//     'id' => '16');
// // dsh(payout::create($data));
// dsh(payout::update($data));
// dsh(payout::login('a','a'));

?>