<?php
require_once 'database.class.php';
require_once 'cat.class.php';
require_once 'account.class.php';
require_once 'payin.class.php';


class sell_facture_customer extends database{
	private static $TABLE = 'sell_facture_customer';

	public static function create($data){
		try{
			// file_put_contents('a.txt', print_r($data,true));

			if(!self::perm('write',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to Create '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$customer = $data['customer'];
			if(!$customer['name'] && !$customer['id']){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = dic_return("Please Enter Name For Customer");
				self::record('write','WARNING','Try to Create '.self::$TABLE.' Table But No Customer');
				return json_encode($jTableResult);
			}

			if(!isset($data['data'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = dic_return("Please Select Stuff");
				self::record('write','WARNING','Try to Create '.self::$TABLE.' But dont select any thing');
				return json_encode($jTableResult);
			}

			if($customer['name']){
				$customerData = array(
					'name'=>$customer['name'],
					'phone'=>$customer['phone'],
					'type'=>'customer',
					'address'=>$customer['address']);
				account::create($customerData);
				$data['idCustomer'] = self::indexId('account') - 1;
			}
			else
				$data['idCustomer'] = $customer['id'];

			$id = self::indexId(self::$TABLE);
			$data['id_user'] = $_SESSION['user']['id'];
			$data['date'] = date('Y-m-d H:i:s',time());
			$money = $data['money'];
			if($money['prePayment'] < $money['totalPrice']){
				$data['type'] = 'loan';
				$data['state'] = 'progress';
			}
			else{
				$data['type'] = 'cash';
				$data['state'] = 'OK';
			}
			

			$sql = "INSERT INTO ".self::$TABLE."(id,id_account,id_user,date,type,state,pre_payment,total_price) VALUES(:id,:id_account,:id_user,:date,:type,:state,:pre_payment,:total_price);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':id_account',$data['idCustomer'],PDO::PARAM_STR);
			$stmt->bindParam(':id_user',$data['id_user'],PDO::PARAM_STR);
			$stmt->bindParam(':date',$data['date'],PDO::PARAM_STR);
			$stmt->bindParam(':type',$data['type'],PDO::PARAM_STR);
			$stmt->bindParam(':state',$data['state'],PDO::PARAM_STR);
			$stmt->bindParam(':pre_payment',$money['prePayment'],PDO::PARAM_STR);
			$stmt->bindParam(':total_price',$money['totalPrice'],PDO::PARAM_STR);
			$stmt->execute();


			$stuff = $data['data'];
			// dsh($stuff);
			$idSellFacture = $id;
			foreach ($stuff as $key => $value) {
				if($value['idCat']){
					$id = self::indexId('sell_facture_detail');
					$sql = "INSERT INTO sell_facture_detail(id,id_sfc,id_cat,id_stuff,detail,width,height,qty,m2,price,total_price,date) VALUES(:id,:id_sfc,:id_cat,:id_stuff,:detail,:width,:height,:qty,:m2,:price,:total_price,:date_time);";
					$stmt = self::$PDO->prepare($sql);
					$stmt->bindParam(':id',$id,PDO::PARAM_STR);
					$stmt->bindParam(':id_sfc',$idSellFacture,PDO::PARAM_STR);
					$stmt->bindParam(':id_cat',$value['idCat'],PDO::PARAM_STR);
					$stmt->bindParam(':id_stuff',$value['idStuff'],PDO::PARAM_STR);
					$stmt->bindParam(':detail',$value['detail'],PDO::PARAM_STR);
					$stmt->bindParam(':width',$value['width'],PDO::PARAM_STR);
					$stmt->bindParam(':height',$value['height'],PDO::PARAM_STR);
					$stmt->bindParam(':qty',$value['qty'],PDO::PARAM_STR);
					$stmt->bindParam(':m2',$value['m2'],PDO::PARAM_STR);
					$stmt->bindParam(':price',$value['price'],PDO::PARAM_STR);
					$stmt->bindParam(':total_price',$value['totalPrice'],PDO::PARAM_STR);
					@$stmt->bindParam(':date_time',substr($data['date'],0,10),PDO::PARAM_STR);
					$stmt->execute();
				}
			}

			$payinData = array('id_account'=>$data['idCustomer'],'dollar' => $money['prePayment'],'dinar' => '0','detail' => 'Pre Paymnet', 'id_facture'=>$idSellFacture);
			payin::create($payinData);
			
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','write data to sell_facture_customer',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_customer.class.php/function create]'.$e->getMessage().'<br>';
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

			$data['id_user'] = $_SESSION['user']['id'];

			$catInfo = self::rowInfoObject('cat',$data['id_cat']);
			$data['m2'] = $data['width'] * $data['height'] / 10000;
			if($data['m2'] < $catInfo->detail){
				$data['total_price'] = $data['qty'] * $catInfo->detail * $data['price'];
				$data['detail'] .= ' - Less than Minimum';
			}
			else
				$data['total_price'] = $data['qty'] * $data['m2'] * $data['price'];
			
			$sql = "UPDATE ".self::$TABLE." SET id_account = :id_account, id_user = :id_user, id_cat=:id_cat, id_stuff = :id_stuff, detail = :detail, width = :width,height = :height,qty = :qty,m2 = :m2,price = :price, total_price = :total_price WHERE id = :id;";

			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$data['id'],PDO::PARAM_STR);
			$stmt->bindParam(':id_account',$data['id_account'],PDO::PARAM_STR);
			$stmt->bindParam(':id_user',$data['id_user'],PDO::PARAM_STR);
			$stmt->bindParam(':id_cat',$data['id_cat'],PDO::PARAM_STR);
			$stmt->bindParam(':id_stuff',$data['id_stuff'],PDO::PARAM_STR);
			$stmt->bindParam(':detail',$data['detail'],PDO::PARAM_STR);
			$stmt->bindParam(':width',$data['width'],PDO::PARAM_STR);
			$stmt->bindParam(':height',$data['height'],PDO::PARAM_STR);
			$stmt->bindParam(':qty',$data['qty'],PDO::PARAM_STR);
			$stmt->bindParam(':m2',$data['m2'],PDO::PARAM_STR);
			$stmt->bindParam(':price',$data['price'],PDO::PARAM_STR);
			$stmt->bindParam(':total_price',$data['total_price'],PDO::PARAM_STR);
			$stmt->execute();
			
			$jTableResult = array();
			$jTableResult['Result'] = 'OK';
			self::record('write','Edit data on sell_facture_customer','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_customer.class.php/function update]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'user'=>array('state'=>'foreign','table'=>'user','field'=>'name','source'=>'id_user'),
				'account'=>array('state'=>'foreign','table'=>'account','field'=>'name','source'=>'id_account'),
				'date'=>array('state'=>'self','field'=>'date'),
				'type'=>array('state'=>'self','field'=>'type'),
				'state'=>array('state'=>'self','field'=>'state'),
				'pre_payment'=>array('state'=>'self','field'=>'pre_payment'),
				'total_price'=>array('state'=>'self','field'=>'total_price')
			);
			
			self::record('read',"search sell_facture_customer's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'sell_facture_customer');
		}
		catch(exception $e){
			echo 'Error: [sell_facture_customer.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function loanList($sorting,$startIndex,$pageSize){
		try{
			if(!self::perm('read',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			self::hack_pageSize($startIndex,$pageSize);
			$sorting = self::hack_sorting($sorting);
			$sql = "SELECT * FROM ".self::$TABLE." WHERE state = 'progress' ORDER BY $sorting LIMIT $startIndex, $pageSize;";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT count(id) AS count FROM ".self::$TABLE." WHERE state = 'progress' ;";
			$result = self::$PDO->query($sql);
			$row = $result->fetchObject();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $row->count;
			$jTableResult['Records'] = $rows;
			self::record('read','View '.self::$TABLE.' Table [progress List]',"sorting = $sorting, startIndex = $startIndex,pageSize = $pageSize");
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_customer.class.php/function loanList]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function sellFactureCustomerInfo($idSellFacture){
		try{
			if(!self::perm('read',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$sql = "SELECT * FROM ".self::$TABLE." WHERE id = '$idSellFacture'";
			$result = self::$PDO->query($sql);
			$head = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT * FROM sell_facture_detail WHERE id_sfc = '$idSellFacture'";
			$result = self::$PDO->query($sql);
			$stuffs = $result->fetchALL(PDO::FETCH_ASSOC);

			$sql = "SELECT * FROM payin WHERE id_facture = '$idSellFacture'";
			$result = self::$PDO->query($sql);
			$payins = $result->fetchALL(PDO::FETCH_ASSOC);

			$totalPayin = 0;
			foreach ($payins as $key => $value) {
				$totalPayin += $value['dollar'];
			}
			$head['totalPayin'] = $totalPayin;
			$head['remained'] = $head['total_price'] - $totalPayin;



			$result = array('head'=>$head,'stuffs'=>$stuffs,'payins'=>$payins);

			
			// self::record('read','View '.self::$TABLE.' Table [Print View]',"id = $idSellFacture");
			return $result;
		}
		catch(PDOException $e){
			echo 'Error: [sell_facture_customer.class.php/function sellFactureCustomerInfo]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function changeState($id,$state){
		try{
			if(!self::perm('read',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to edit '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$sql = "UPDATE ".self::$TABLE." SET state = :state WHERE id = :id";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
			$stmt->bindParam(':state',$state,PDO::PARAM_STR);
			$stmt->execute();
			
			return true;
		}
		catch(PDOException $e){
			echo 'Error: [sell_facture_customer.class.php/function changeState]'.$e->getMessage().'<br>';
			die();
		}
	}

	// public static function updateDate(){
	// 	try{
	// 		$sql = "SELECT id,date FROM sell_facture_daily";
	// 		$result = self::$PDO->query($sql);
	// 		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

	// 		foreach ($rows as $key => $value) {
	// 			dsh($value);
	// 			$newDate = date('Y-m-d',$value['date']);
	// 			$sql = "UPDATE sell_facture_daily SET date= :date WHERE id = :id";
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


	// public static function setState(){
	// 	try{
	// 		$sql = "SELECT id,date FROM sell_facture_daily";
	// 		$result = self::$PDO->query($sql);
	// 		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

	// 		foreach ($rows as $key => $value) {
	// 			dsh($value);
	// 			$newDate = date('Y-m-d',$value['date']);
	// 			$sql = "UPDATE sell_facture_daily SET date= :date WHERE id = :id";
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
	
	public static function delete($id){
		try{
			if(!self::perm('delete',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to Delete '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$oldData = self::rowInfoArray(self::$TABLE,$id);

			$sql = "DELETE FROM ".self::$TABLE." WHERE id = :id";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$_POST['id'],PDO::PARAM_INT);
			$stmt->execute();

			$sql = "DELETE FROM sell_facture_detail WHERE id_sfc = :id";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$_POST['id'],PDO::PARAM_INT);
			$stmt->execute();

			// $sql = "DELETE id FROM payin WHERE  id_facture = :id";
			// $stmt = self::$PDO->prepare($sql);
			// $stmt->bindParam(':id',$_POST['id'],PDO::PARAM_INT);
			// $stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			self::record('write','WARNING : Delete data in '.self::$TABLE,'OLD DATA : '.self::httpBuildQuery($oldData));
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


}

// $data = array(
//     'id_cat' => '3',
//     'name' => 'testsell_facture_customer',
//     'qty' => '36',
//     'price' => '16',
//     'detail' => 'diako test',
//     'id' => '463');
// // dsh(sell_facture_customer::create($data));
// dsh(sell_facture_customer::update($data));
// dsh(sell_facture_customer::login('a','a'));
// dsh(sell_facture_customer::delete(4725));
?>