<?php
require_once 'database.class.php';
require_once 'cat.class.php';


class sell_facture_daily extends database{
	private static $TABLE = 'sell_facture_daily';

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
			$data['id_user'] = $_SESSION['user']['id'];
			$data['date'] = substr(date('Y-m-d H:i:s',time()),0,10);

			$catInfo = self::rowInfoObject('cat',$data['id_cat']);
			$data['m2'] = $data['width'] * $data['height'] / 10000;
			if($data['m2'] < $catInfo->detail){
				$data['m2'] = $catInfo->detail;
				$data['detail'] .= ' - Less than Minimum';
			}

			$data['m2'] *= $data['qty'];
			$data['total_price'] = $data['m2'] * $data['price'];


			$sql = "INSERT INTO ".self::$TABLE."(id,id_account,id_user,id_cat,id_stuff,detail,width,height,qty,m2,price,total_price,date) VALUES(:id,:id_account,:id_user,:id_cat,:id_stuff,:detail,:width,:height,:qty,:m2,:price,:total_price,:date);";
			$stmt = self::$PDO->prepare($sql);
			$stmt->bindParam(':id',$id,PDO::PARAM_STR);
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
			$stmt->bindParam(':date',$data['date'],PDO::PARAM_STR);
			$stmt->execute();

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = self::last_id_data(self::$TABLE);
			self::record('write','write data to sell_facture_daily',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_daily.class.php/function create]'.$e->getMessage().'<br>';
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
				$data['m2'] = $catInfo->detail;
				$data['detail'] .= ' - Less than Minimum';
			}

			$data['m2'] *= $data['qty'];
			$data['total_price'] = $data['m2'] * $data['price'];
			
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
			self::record('write','Edit data on sell_facture_daily','OLD DATA : '.self::httpBuildQuery($oldData).' NEW DATA :'.self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_daily.class.php/function update]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'date'=>array('state'=>'self','field'=>'date'),
				'detail'=>array('state'=>'self','field'=>'detail'),
				'user'=>array('state'=>'foreign','table'=>'user','field'=>'name','source'=>'id_user'),
				'cat'=>array('state'=>'foreign','table'=>'cat','field'=>'name','source'=>'id_cat'),
				'account'=>array('state'=>'foreign','table'=>'account','field'=>'name','source'=>'id_account'),
				'total_price'=>array('state'=>'self','field'=>'total_price')
			);
			
			self::record('read',"search sell_facture_daily's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'sell_facture_daily');
		}
		catch(exception $e){
			echo 'Error: [sell_facture_daily.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}


	


}

// $data = array(
//     'id_cat' => '3',
//     'name' => 'testsell_facture_daily',
//     'qty' => '36',
//     'price' => '16',
//     'detail' => 'diako test',
//     'id' => '463');
// // dsh(sell_facture_daily::create($data));
// dsh(sell_facture_daily::update($data));
// dsh(sell_facture_daily::login('a','a'));

?>