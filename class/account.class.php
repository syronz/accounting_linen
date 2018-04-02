<?php
require_once 'database.class.php';


class account extends database{
	private static $TABLE = 'account';

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

			$sql = "INSERT INTO ".self::$TABLE."(id,name,phone,detail,date,type,address) VALUES(:id,:name,:phone,:detail,:date,:type,:address);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':phone',$data['phone'],PDO::PARAM_STR);
			$stmt->bindParam(':date',$register_date,PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->bindParam(':type',$data['type'],PDO::PARAM_STR);
			$stmt->bindParam(':address',$data['address'],PDO::PARAM_STR);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','write data to account',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [account.class.php/function create]'.$e->getMessage().'<br>';
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
			
			$sql = "UPDATE ".self::$TABLE." SET name = :name,phone = :phone,detail = :detail, type = :type WHERE id = :id;";

			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':name',$data['name'],PDO::PARAM_STR);
			$stmt->bindParam(':phone',$data['phone'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->bindParam(':type',$data['type'],PDO::PARAM_STR);
			$stmt->execute();
			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on account','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [account.class.php/function update]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'name'=>array('state'=>'self','field'=>'name'),
				'phone'=>array('state'=>'self','field'=>'phone'),
				'date'=>array('state'=>'self','field'=>'date'),
				'type'=>array('state'=>'self','field'=>'type'),
				'detail'=>array('state'=>'self','field'=>'detail')
			);
			
			self::record('read',"search account's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'account');
		}
		catch(exception $e){
			echo 'Error: [account.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function json_list_branch(){
		try{
			$sql = "SELECT id AS Value,name AS DisplayText FROM ".self::$TABLE." WHERE type = 'branch' ORDER BY name" ;
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Options'] = $rows;
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [account.class.php/function json_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function jsonListWithType(){
		try{
			$sql = "SELECT id AS Value,name || ' / ' || type || ' / ' || phone AS DisplayText FROM ".self::$TABLE." ORDER BY name" ;
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Options'] = $rows;
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [account.class.php/function jsonListWithType]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function calculateBalance($idAccount){
		try{
			$sql = "SELECT SUM(dollar) AS payinTotal FROM payin WHERE id_account = $idAccount";
			$result = self::$PDO->query($sql);
			$row = $result->fetchObject();
			$payinTotal = $row->payinTotal;

			$sql = "SELECT SUM(dollar) AS payoutTotal FROM payout WHERE id_account = $idAccount";
			$result = self::$PDO->query($sql);
			$row = $result->fetchObject();
			$payoutTotal = $row->payoutTotal;

			$sql = "SELECT SUM(total_price) AS sellTotal FROM sell_facture_daily WHERE id_account = $idAccount";
			$result = self::$PDO->query($sql);
			$row = $result->fetchObject();
			$sellTotal = $row->sellTotal;

			return array('payinTotal'=>$payinTotal,'payoutTotal'=>$payoutTotal,'sellTotal'=>$sellTotal,'result'=>($sellTotal + $payoutTotal - $payinTotal));
		}
		catch(PDOException $e){
			echo 'Error: [account.class.php/function calculateBalance]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function branchLists($sorting,$startIndex,$pageSize){
		try{
			//var_dump($where);
			if(!self::perm('read',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View '.$table.' Table But havent permission');
				return json_encode($jTableResult);
			}

			self::hack_pageSize($startIndex,$pageSize);
			$sorting = self::hack_sorting($sorting);
			$sql = "SELECT * FROM ".self::$TABLE." WHERE type = 'branch' ORDER BY $sorting LIMIT $startIndex, $pageSize;";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT count(id) AS count FROM ".self::$TABLE." WHERE type = 'branch'";
			$result = self::$PDO->query($sql);
			$row = $result->fetchObject();

			foreach ($rows as $key => &$value) {
				$value['balance'] = round(self::calculateBalance($value['id'])['result'],2);
			}

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $row->count;
			$jTableResult['Records'] = $rows;
			self::record('read','View '.self::$TABLE.' [Branch] Table',"sorting = $sorting, startIndex = $startIndex,pageSize = $pageSize");
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [account.class.php/function lists]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function customerAsOptions(){
		try{
			$sql = "SELECT * FROM ".self::$TABLE." WHERE type = 'customer' ORDER BY name ASC";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$str = '';
			foreach ($rows as $key => $value) {
				$str .= '<option value="'.$value['id'].'">'.$value['name'].' / '.$value['phone'].'</option>';
			}
			return $str;
		}
		catch(PDOException $e){
			echo 'Error: [account.class.php/function customerAsOptions]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function customerId($idCustomer){
		try{
			if($idCustomer){
				$sql = "SELECT name FROM ".self::$TABLE." WHERE id = '$idCustomer' AND type = 'customer'";
				$result = self::$PDO->query($sql);
				$customer = $result->fetchObject();
				if($customer){
					return $customer->name;
				}
			}
			
			return null;
		}
		catch(PDOException $e){
			echo 'Error: [account.class.php/function customerId]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function accountBalanceReport($idAccount){
		try{
			$sql = "select * from (
SELECT 'payin' AS description,id_user,date,dollar as payin,null as payout,detail,id FROM 'payin' WHERE id_account = $idAccount
union
SELECT 'payout' AS description,id_user,date,null as daen,dollar as payout,detail,id FROM 'payout' WHERE id_account = $idAccount
union
SELECT 'sell' AS description,sfd.id_user,sfd.date,null as daen,sfd.total_price as payout,cat.name  || ', W:' || sfd.width || ', H:' || sfd.height as detail,sfd.id  FROM 'sell_facture_daily' as  sfd inner join cat on sfd.id_cat = cat.id WHERE id_account = $idAccount
) order by date ASC";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$count = count($rows);

			$balance = 0;
			for($i=0;$i<$count;$i++){
				$balance = $balance + $rows[$i]['payout'] - $rows[$i]['payin'];
				$rows[$i]['balance'] = round($balance,2);
				$rows[$i]['id'] = $i+1;
			}


			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $count;
			$jTableResult['Records'] = $rows;
			self::record('read','View account Balance Report',"ID: $idAccount");
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
// dsh(account::calculateBalance(93));
// $data = array(
//     'name' => 'b3',
//     'id_permission' => '3',
//     'username' => 'b',
//     'password' => 'b',
//     'phone' => 'b',
//     'register_date' => '2014-06-17',
//     'id' => '3');
// dsh(account::create($data));
// dsh(account::update($data));
// dsh(account::customerId(825));


?>