<?php 
	require_once 'database.class.php';
	class user_activity extends database{
		private static $TABLE = 'user_activity';
		public static function record_activity($ip,$id_user=1,$action,$detail=null){
			try{
				// return true;
				$lastId = self::indexId(self::$TABLE);

				// dsh($lastId);
				$date = date('Y-m-d H:i:s',time());
				$sql = "INSERT INTO ".self::$TABLE."(id,ip,id_user,action,detail,date) VALUES(:id,:ip,:id_user,:action,:detail,:date);";
				$stmt = self::$PDO->prepare($sql);
				$stmt->bindParam(':ip',$ip,PDO::PARAM_STR);
				$stmt->bindParam(':id_user',$id_user,PDO::PARAM_STR);
				$stmt->bindParam(':action',$action,PDO::PARAM_STR);
				$stmt->bindParam(':detail',$detail,PDO::PARAM_STR);
				$stmt->bindParam(':date',$date,PDO::PARAM_STR);
				$stmt->bindParam(':id',$lastId,PDO::PARAM_STR);
				$stmt->execute();
				return true;
			}
			catch(PDOException $e){
				echo 'Error: [user_activity.class.php/function record_activity]'.$e->getMessage().'<br>';
				die();
			}
		}

	public static function search_list($sorting,$startIndex,$pageSize,$search_str){
		try{
			$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'ip'=>array('state'=>'self','field'=>'ip'),
				'user'=>array('state'=>'foreign','table'=>'user','field'=>'name','source'=>'id_user'),
				'action'=>array('state'=>'self','field'=>'action'),
				'detail'=>array('state'=>'self','field'=>'detail'),
				'date'=>array('state'=>'self','field'=>'date')
			);
			
			self::record('read',"search user_activity's","SEARCH: $search_str");
			return self::search($sorting,$startIndex,$pageSize,$search_str,$arr_table,'user_activity');
		}
		catch(exception $e){
			echo 'Error: [user_activity.class.php/function search_list]'.$e->getMessage().'<br>';
			die();
		}
	}
	}

//user_activity::record_activity('write',5,'ddd','no');
		
?>