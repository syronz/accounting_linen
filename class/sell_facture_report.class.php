<?php
require_once 'database.class.php';
require_once 'cat.class.php';
ini_set('memory_limit', '-1');

class sell_facture_report extends database{
	private static $TABLE = 'sell_facture_daily';

	public static function findStartDate(){
		try{
			$sql = "SELECT date FROM sell_facture_daily ORDER BY date ASC LIMIT 1";
			$result = self::$PDO->query($sql);
			$minDaily = $result->fetchObject();

			$sql = "SELECT date FROM sell_facture_detail ORDER BY date ASC LIMIT 1";
			$result = self::$PDO->query($sql);
			$minCustomer = $result->fetchObject();

			if($minDaily->date < $minCustomer->date)
				return $minDaily->date;
			return $minCustomer->date;
		}
		catch(PDOException $e){
			echo 'Error: [sell_facture_report.class.php/function findStartDate]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function reportListDay($sorting,$startIndex,$pageSize){
		try{
			//var_dump($where);
			if(!self::perm('read',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			self::hack_pageSize($startIndex,$pageSize);
			$sorting = self::hack_sorting($sorting);


			$sql = "SELECT SUM(total_price) AS total,date FROM sell_facture_daily GROUP BY date";
			$result = self::$PDO->query($sql);
			$rowsDaily = $result->fetchAll(PDO::FETCH_ASSOC);

			$arrDaily = [];
			foreach ($rowsDaily as $key => $value) {
				$arrDaily[$value['date']] = $value['total'];
			}

			$sql = "SELECT SUM(total_price) AS total,date FROM sell_facture_detail GROUP BY date";
			$result = self::$PDO->query($sql);
			$rowsCustomer = $result->fetchAll(PDO::FETCH_ASSOC);

			// dsh($rowsCustomer);

			$arrCustomer = [];
			foreach ($rowsCustomer as $key => $value) {
				$arrCustomer[$value['date']] = $value['total'];
			}

			$arrCombine = [];
			$startDate = setting::START_DATE;
			$nowDate = date('Y-m-d',time());
			$pDate = $startDate;


			// dsh($startDate);
			// dsh($startDate);
			// die();
			$i=0;
			do{
				@$arrCombine[$i] = ['id'=>$i,'date'=>$pDate,'sell_branch'=>$arrDaily[$pDate],'sell_customer'=>$arrCustomer[$pDate],'total_sell'=>$arrDaily[$pDate]+$arrCustomer[$pDate]];
				$pTime = strtotime($pDate);
				$pTime += 86400;
				$pDate = date('Y-m-d',$pTime);
				$i++;
			}while($pDate <= $nowDate);

			if($sorting == 'id DESC')
				$arrCombine = array_reverse($arrCombine);

			$count = $i;
			// return $arrCombine;
			$arrFinal = [];
			for($i = $startIndex; $i < $startIndex + $pageSize; $i++){
				@$arrFinal[$i] = $arrCombine[$i];
			}


			$jTableResult = [];
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $count;
			$jTableResult['Records'] = $arrFinal;
			// self::record('read','View '.$table.' Table',"sorting = $sorting, startIndex = $startIndex,pageSize = $pageSize");
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_report.class.php/function reportList]'.$e->getMessage().'<br>';
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
				'detail'=>array('state'=>'self','field'=>'detail')
			);
			
			// self::record('read',"search sell_facture_report's","SEARCH: $search_str");
			return null;
		}
		catch(exception $e){
			echo 'Error: [sell_facture_report.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function reportListDayDetail($date){
		try{
			//var_dump($where);
			if(!self::perm('read','sell_facture_daily',$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View Report '.$table.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$sql = "SELECT * FROM sell_facture_daily WHERE date = '$date' ORDER BY id_cat";
			$result = self::$PDO->query($sql);
			$rowsDaily = $result->fetchAll(PDO::FETCH_ASSOC);


			$sql = "SELECT * FROM  sell_facture_detail WHERE date = '$date' ORDER BY id_cat";
			$result = self::$PDO->query($sql);
			$rowsCustomer = $result->fetchAll(PDO::FETCH_ASSOC);



			$arrCombine = array_merge($rowsDaily,$rowsCustomer);
			foreach ($arrCombine as $key => &$value) {
				$value['id'] = ($key+1);
			}

			// return $arrCombine;

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = count($arrCombine);
			$jTableResult['Records'] = $arrCombine;
			self::record('read','View Sell Report Table',"date : $date");
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_report.class.php/function reportListDayDetail]'.$e->getMessage().'<br>';
			die();
		}
	}


	public static function stuffReportMonth($sorting,$startIndex,$pageSize){
		try{
			//var_dump($where);
			if(!self::perm('read','sell_facture_daily',$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View Report '.$table.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$sql = "select d as date,sum(zebra) as zebra,sum(store) as store,sum(madani) as madani,sum(slight) as slight from (
SELECT substr(date,1,7) d, sum(m2) AS zebra, null as store, null as madani, null as slight FROM 'sell_facture_daily' sfd  WHERE  sfd.id_cat = 1 group by d
union
SELECT substr(date,1,7) d, null AS zebra, sum(m2) as store, null as madani, null as slight FROM 'sell_facture_daily' sfd  WHERE  sfd.id_cat = 2 group by d
union
SELECT substr(date,1,7) d, null AS zebra, null as store, sum(m2) as madani, null as slight FROM 'sell_facture_daily' sfd  WHERE  sfd.id_cat = 3 group by d
union
SELECT substr(date,1,7) d, null AS zebra, null as store, null as madani, sum(m2) as slight FROM 'sell_facture_daily' sfd  WHERE  sfd.id_cat = 4 group by d

union
SELECT substr(date,1,7) d, sum(width * height / 10000.0 * qty) AS zebra, null as store, null as madani, null as slight FROM 'sell_facture_detail' sfd  WHERE  sfd.id_cat = 1 group by d
union
SELECT substr(date,1,7) d, null AS zebra, sum(width * height / 10000.0 * qty) as store, null as madani, null as slight FROM 'sell_facture_detail' sfd  WHERE  sfd.id_cat = 2 group by d
union
SELECT substr(date,1,7) d, null AS zebra, null as store, sum(width * height / 10000.0 * qty) as madani, null as slight FROM 'sell_facture_detail' sfd  WHERE  sfd.id_cat = 3 group by d
union
SELECT substr(date,1,7) d, null AS zebra, null as store, null as madani, sum(width * height / 10000.0 * qty) as slight FROM 'sell_facture_detail' sfd  WHERE  sfd.id_cat = 4 group by d
) group by d order by $sorting LIMIT $startIndex, $pageSize;";
// dsh($sql);
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);



			$sql = "select count(*) as count from ( SELECT count(*),substr(date,1,7) d FROM 'sell_facture_daily'  group by d )";
			$result = self::$PDO->query($sql);
			$count = $result->fetchObject();

			// return $arrCombine;

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $count->count;
			$jTableResult['Records'] = $rows;
			self::record('read','View Sell Stuff Report Table',"date : ");
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_report.class.php/function stuffReportMonth]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function stuffReportDay($date){
		try{
			//var_dump($where);
			if(!self::perm('read','sell_facture_daily',$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('read','WARNING','Try to View Report '.$table.' Table But havent permission');
				return json_encode($jTableResult);
			}

			$sql = "select d as date,sum(zebra) as zebra,sum(store) as store,sum(madani) as madani,sum(slight) as slight from (
SELECT substr(date,1,10) d, sum(m2) AS zebra, null as store, null as madani, null as slight FROM 'sell_facture_daily' sfd  WHERE  sfd.id_cat = 1 AND date like '$date%' group by d
union
SELECT substr(date,1,10) d, null AS zebra, sum(m2) as store, null as madani, null as slight FROM 'sell_facture_daily' sfd  WHERE  sfd.id_cat = 2 AND date like '$date%' group by d
union
SELECT substr(date,1,10) d, null AS zebra, null as store, sum(m2) as madani, null as slight FROM 'sell_facture_daily' sfd  WHERE  sfd.id_cat = 3 AND date like '$date%' group by d
union
SELECT substr(date,1,10) d, null AS zebra, null as store, null as madani, sum(m2) as slight FROM 'sell_facture_daily' sfd  WHERE  sfd.id_cat = 4 AND date like '$date%' group by d

union
SELECT substr(date,1,10) d, sum(width * height / 10000.0 * qty) AS zebra, null as store, null as madani, null as slight FROM 'sell_facture_detail' sfd  WHERE  sfd.id_cat = 1 AND date like '$date%' group by d
union
SELECT substr(date,1,10) d, null AS zebra, sum(width * height / 10000.0 * qty) as store, null as madani, null as slight FROM 'sell_facture_detail' sfd  WHERE  sfd.id_cat = 2 AND date like '$date%' group by d
union
SELECT substr(date,1,10) d, null AS zebra, null as store, sum(width * height / 10000.0 * qty) as madani, null as slight FROM 'sell_facture_detail' sfd  WHERE  sfd.id_cat = 3 AND date like '$date%' group by d
union
SELECT substr(date,1,10) d, null AS zebra, null as store, null as madani, sum(width * height / 10000.0 * qty) as slight FROM 'sell_facture_detail' sfd  WHERE  sfd.id_cat = 4 AND date like '$date%' group by d
) group by d order by date";
			$result = self::$PDO->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

// dsh($rows);

			// $sql = "select count(*) as count from ( SELECT count(*),substr(date,1,7) d FROM 'sell_facture_daily'  group by d )";
			// $result = self::$PDO->query($sql);
			// $count = $result->fetchObject();

			// return $arrCombine;

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = 31;
			$jTableResult['Records'] = $rows;
			self::record('read','View Sell Stuff Report Table',"date : ");
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [sell_facture_report.class.php/function stuffReportDay]'.$e->getMessage().'<br>';
			die();
		}
	}


	


}

// $data = array(
//     'id_cat' => '3',
//     'name' => 'testsell_facture_report',
//     'qty' => '36',
//     'price' => '16',
//     'detail' => 'diako test',
//     'id' => '463');
// // dsh(sell_facture_report::create($data));
// dsh(sell_facture_report::update($data));
// dsh(sell_facture_report::reportListDayDetail('2014-06-13'));

?>