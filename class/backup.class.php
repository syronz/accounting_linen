<?php
require_once 'database.class.php';

class backup extends database{
	private static $TABLE = 'backup';





	public static function lists($sorting=null,$startIndex=null,$pageSize=null){
		try{

			$directory = '../backups/';

			$arr_backups = array();
			$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
			$i = 1;
			while($it->valid()) {
			    if (!$it->isDot()) {
			        // echo 'SubPathName: ' . $it->getSubPathName() . "<br>";
			        // echo 'SubPath:     ' . $it->getSubPath() . "<br>";
			        // echo 'Key:         ' . $it->key() . "<br><br>";
			        $field = array('id'=>$i,
			        	'name'=>$it->getSubPathName(),
			        	'restore'=>'<a name="'.$it->getSubPathName().'" class="doRestore" >Restore</a>',
			        	'target'=>$it->key(),
			        	'date'=>date ("Y-m-d H:i:s.", filemtime($directory.$it->getSubPathName())),
			        	'intDate' => filemtime($directory.$it->getSubPathName()),
			        	'download' => '<a href="backups/'.$it->getSubPathName().'" >Download Link</a>'
			        );
			        array_unshift($arr_backups,$field);
			        $i++;
			    }
			    $it->next();
			}
			// if($sorting == 'id ASC')
			// 	$arr_backups = array_reverse($arr_backups);

			$newArr = array();
			foreach ($arr_backups as $key => $value) {
				$newArr[$value['intDate']] = $value;
			}
			// dsh($newArr);
			krsort($newArr);
			// if($sorting == 'date ASC' || $sorting == 'id ASC')
			// 	ksort($newArr);
			// dsh($newArr);
			$arr_backups = array();
			$i = 0;
			foreach ($newArr as $key => $value) {
				$arr_backups[$i]['id'] = $i;
				$arr_backups[$i]['name'] = $value['name'];
				$arr_backups[$i]['date'] = $value['date'];
				$arr_backups[$i]['restore'] = $value['restore'];
				$arr_backups[$i]['download'] = $value['download'];
				$i++;
			}
			// $arr_backups = $newArr;
			// sort($arr_backups);
			// dsh($arr_backups);

			// array
// dsh($arr_backups);
			$count = count($arr_backups);
			$rows = array();
			for($i=0; $i<$count; $i++){
				if($i >= $startIndex && $i < ($startIndex + $pageSize))
					array_unshift($rows,$arr_backups[$i]);
			}
			$rows = array_reverse($rows);
	// dsh($rows);		
			// self::hack_pageSize($startIndex,$pageSize);
			// $sorting = self::hack_sorting($sorting);
			// $sql = "SELECT * FROM ".self::$TABLE." ORDER BY $sorting LIMIT $startIndex, $pageSize;";
			// $result = self::$PDO->query($sql);
			// $rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $count;
			$jTableResult['Records'] = $rows;
			// self::record('read','View All User Activity');
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [backup_page.class.php/function lists]'.$e->getMessage().'<br>';
			die();
		}
	}

	public static function create($data){
		try{
			// file_put_contents('a.txt', print_r($data,true));

			if(!self::perm('write',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to Create '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			if(file_exists("../backups/{$data['name']}.db")){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "This name registered before<br>Try another name";
				self::record('write','WARNING','Try to Create '.self::$TABLE.' Table But dont accept name');
				return json_encode($jTableResult);
			}

			copy('../'.setting::DATABASE_NAME,"../backups/{$data['name']}.db");
			// copy('../index0.php','../backups/sss.txt');
			// dsh(setting::DATABASE_NAME);
			// copy('../p.db','../backups/sss.dp');

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = array('name'=>$data['name']);
			self::record('write','Write data to backup',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [backup.class.php/function create]'.$e->getMessage().'<br>';
				file_put_contents('d.txt','ddd');
				die();
			}
		}
	}

	public static function restore($fileName){
		try{
			// file_put_contents('a.txt', print_r($data,true));

			if(!self::perm('edit',self::$TABLE,$_SESSION['user']['id'])){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = "You Havent Permission";
				self::record('write','WARNING','Try to restore '.self::$TABLE.' Table But havent permission');
				return json_encode($jTableResult);
			}

			unlink('../'.setting::DATABASE_NAME);
			copy("../backups/$fileName","../".setting::DATABASE_NAME);
			// copy('../index0.php','../backups/sss.txt');
			// dsh(setting::DATABASE_NAME);
			// copy('../p.db','../backups/sss.dp');

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = array('name'=>$data['name']);
			self::record('write','Write data to backup',"DATA : ".self::httpBuildQuery($data));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			else{
				echo 'Error: [backup.class.php/function create]'.$e->getMessage().'<br>';
				file_put_contents('d.txt','ddd');
				die();
			}
		}
	}

	

	public static function findNameById($id){
		try{
			$directory = '../backups/';

			$arr_backups = array();
			$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
			$i = 1;
			while($it->valid()) {
			    if (!$it->isDot()) {
			        // echo 'SubPathName: ' . $it->getSubPathName() . "<br>";
			        // echo 'SubPath:     ' . $it->getSubPath() . "<br>";
			        // echo 'Key:         ' . $it->key() . "<br><br>";
			        $field = array('id'=>$i,
			        	'name'=>$it->getSubPathName(),
			        	'restore'=>'<a name="'.$it->getSubPathName().'" class="doRestore" >Restore</a>',
			        	'target'=>$it->key(),
			        	'date'=>date ("Y-m-d H:i:s.", filemtime($directory.$it->getSubPathName())),
			        	'intDate' => filemtime($directory.$it->getSubPathName())
			        );
			        array_unshift($arr_backups,$field);
			        $i++;
			    }
			    $it->next();
			}

			$newArr = array();
			foreach ($arr_backups as $key => $value) {
				$newArr[$value['intDate']] = $value;
			}
			krsort($newArr);
			$arr_backups = array();
			$i = 0;
			foreach ($newArr as $key => $value) {
				$arr_backups[$i]['id'] = $i;
				$arr_backups[$i]['name'] = $value['name'];
				$arr_backups[$i]['date'] = $value['date'];
				$arr_backups[$i]['restore'] = $value['restore'];
				if($id == $i)
					return $value['name'];
				$i++;
			}
		}
		catch(PDOException $e){
			echo 'Error: [backup.class.php/function findNameById]'.$e->getMessage().'<br>';
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

			$fileName = self::findNameById($id);

			unlink('../backups/'.$fileName);


			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			self::record('write','WARNING : Delete data in '.self::$TABLE.' ','OLD DATA : '.self::httpBuildQuery($fileName));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			if(setting::ERROR_JTABLE_MESSAGE){
				$jTableResult['Result'] = "NO";
				$jTableResult['Message'] = $e->getMessage();
				return json_encode($jTableResult);
			}
			echo 'Error: [backup.class.php/function delete]'.$e->getMessage().'<br>';
			die();
		}
	}



}

// $data = array(
//     'type' => 'backup',
//     'dollar' => '200',
//     'dinnar' => '',
//     'detail' => 'good',
//     // 'box_dollar' => '',
//     // 'box_dinnar' => '',
//     'id' => '16');
// dsh(backup::create(253));
// dsh(backup::update($data));
// dsh(backup::login('a','a'));

// echo backup::findNameById(7);

?>