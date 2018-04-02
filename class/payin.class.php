<?php
require_once 'database.class.php';
require_once 'dollar_rate.class.php';
require_once 'cash.class.php';
require_once 'sell_facture_customer.class.php';


class payin extends database{
	private static $TABLE = 'payin';

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

			$prePayin = self::last_id_data(self::$TABLE);

			$data['dollar_rate'] = dollar_rate::lastDollarRate();

			$sql = "INSERT INTO ".self::$TABLE."(id,id_user,id_account,date,dollar,dinar,dollar_rate,id_cash,detail,id_facture) VALUES(:id,:id_user,:id_account,:date,:dollar,:dinar,:dollar_rate,:id_cash,:detail,:id_facture);";
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
			$stmt->bindParam(':id_facture',$data['id_facture'],PDO::PARAM_STR);
			$stmt->execute();

			$accountInfo = self::rowInfoObject('account',$data['id_account']);
			$detailCash = $accountInfo->name.' / '.$data['detail'];
			if(@$data['not_add_to_cash'])
				cash::create(array('dollar'=>0,'dinar'=>0,'type'=>'payin','id_f'=>$id,'detail'=>'NOT ADD - '.$detailCash));
			else
				cash::create(array('dollar'=>$data['dollar'],'dinar'=>$data['dinar'],'type'=>'payin','id_f'=>$id,'detail'=>$detailCash));

			sleep(1);

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','Write data to payin',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [payin.class.php/function create]'.$e->getMessage().'<br>';
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
				cash::update(array('dollar'=>0,'dinar'=>0,'type'=>'payin','id_f'=>$id,'detail'=>'NOT ADD - '.$detailCash, 'id'=>$oldData['id_cash']));
			else
				cash::update(array('dollar'=>$data['dollar'],'dinar'=>$data['dinar'],'type'=>'payin','id_f'=>$id,'detail'=>$detailCash, 'id'=>$oldData['id_cash']));

			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on payin','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [payin.class.php/function update]'.$e->getMessage().'<br>';
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
			
			self::record('read',"search payin's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'payin');
		}
		catch(exception $e){
			echo 'Error: [payin.class.php/function search_list]'.$e->getMessage().'<br>';
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

	public static function listSellFactureCustomerPayin($sorting,$startIndex,$pageSize,$idSellFacture){
		try{
			if(!self::perm('read',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View '.$table.' Table But havent permission');
				return json_encode($jTableResult);
			}

			self::hack_pageSize($startIndex,$pageSize);
			$sorting = self::hack_sorting($sorting);
			$sql = "SELECT * FROM ".self::$TABLE." WHERE id_facture = '$idSellFacture' ORDER BY $sorting LIMIT $startIndex, $pageSize;";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT count(id) AS count FROM ".self::$TABLE." WHERE id_facture = '$idSellFacture'";
			$result = self::$PDO->query($sql);
			$row = $result->fetchObject();

			// foreach ($rows as $key => &$value) {
			// 	$value['balance'] = self::calculateBalance($value['id'])['result'];
			// }

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $row->count;
			$jTableResult['Records'] = $rows;
			self::record('read','View '.self::$TABLE.' [Payin] Table',"sorting = $sorting, startIndex = $startIndex,pageSize = $pageSize, id_facture = $idSellFacture");
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [account.class.php/function listSellFactureCustomerPayin]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function createForFacture($data,$idSellFacture,$idAccount){
		try{
			file_put_contents('a.txt', print_r($data,true));

			$data['id_facture'] = $idSellFacture;
			$data['id_account'] = $idAccount;


			if(!self::perm('write',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to createForFacture '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$id = self::indexId(self::$TABLE);
			$data['id_cash'] = self::indexId('cash');
			$data['date'] = date('Y-m-d H:i:s',time());
			$data['id_user'] = $_SESSION['user']['id'];

			$prePayin = self::last_id_data(self::$TABLE);

			$data['dollar_rate'] = dollar_rate::lastDollarRate();

			$sql = "INSERT INTO ".self::$TABLE."(id,id_user,id_account,date,dollar,dinar,dollar_rate,id_cash,detail,id_facture) VALUES(:id,:id_user,:id_account,:date,:dollar,:dinar,:dollar_rate,:id_cash,:detail,:id_facture);";
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
			$stmt->bindParam(':id_facture',$data['id_facture'],PDO::PARAM_STR);
			$stmt->execute();

			$accountInfo = self::rowInfoObject('account',$data['id_account']);
			$detailCash = $accountInfo->name.' / '.$data['detail'];
			if(@$data['not_add_to_cash'])
				cash::create(array('dollar'=>0,'dinar'=>0,'type'=>'payin','id_f'=>$id,'detail'=>'NOT ADD - '.$detailCash));
			else
				cash::create(array('dollar'=>$data['dollar'],'dinar'=>$data['dinar'],'type'=>'payin','id_f'=>$id,'detail'=>$detailCash));

			$factureInfo = sell_facture_customer::sellFactureCustomerInfo($data['id_facture']);
			if($factureInfo['head']['remained'] < 1)
				sell_facture_customer::changeState($data['id_facture'],'OK');

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','Write data to payin [sell facture]',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [payin.class.php/function createForFacture]'.$e->getMessage().'<br>';
				die();
			}
		}
	}

	public static function lists($sorting,$startIndex,$pageSize,$table){
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
			$sql = "SELECT id,id_user,id_account,date,dollar,detail FROM ".$table." ORDER BY $sorting LIMIT $startIndex, $pageSize;";
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

	// public static function updateDate(){
	// 	try{
	// 		$sql = "SELECT id,date FROM ".self::$TABLE;
	// 		$result = self::$PDO->query($sql);
	// 		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

	// 		foreach ($rows as $key => $value) {
	// 			dsh($value);
	// 			$newDate = date('Y-m-d',$value['date']);
	// 			$sql = "UPDATE ".self::$TABLE." SET date= :date WHERE id = :id";
	// 			$stmt = self::$PDO->prepare($sql);
	// 			$stmt->bindParam(':id',$value['id'],PDO::PARAM_STR);
	// 			$stmt->bindParam(':date',$newDate,PDO::PARAM_STR);
	// 			$stmt->execute();
	// 		}
			
	// 		return true;
	// 	}
	// 	catch(PDOException $e){
	// 		echo 'Error: [account.class.php/function customerId]'.$e->getMessage().'<br>';
	// 		die();
	// 	}
	// }


}

// $data = array(
//     'type' => 'payin',
//     'dollar' => '200',
//     'dinnar' => '',
//     'detail' => 'good',
//     'id_account' => 257,
//     // 'box_dollar' => '',
//     // 'box_dinnar' => '',
//     'id' => '257');
// dsh(payin::create($data));
// dsh(payin::update($data));
// dsh(payin::login('a','a'));

?>