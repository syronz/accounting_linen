<?php
	require_once 'database.class.php';
	class search extends database{
		//private static $TABLE = 'search';
		public static function make_search_list($sorting,$startIndex,$pageSize,$search_str,$arr_table,$table,$other_table  = null){
		try{
			// $where = self::check_permission_view($table);
			self::hack_pageSize($startIndex,$pageSize);
			$sorting = self::hack_sorting($sorting);
			// $where = str_replace('WHERE', 'AND', $where);

			/*$arr_table = array(
				'id'=>array('state'=>'self','field'=>'id'),
				'company'=>array('state'=>'foreign','table'=>'company','field'=>'name','source'=>'id_company'),
				'category'=>array('state'=>'foreign','table'=>'category','field'=>'name','source'=>'id_category'),
				//'name'=>array('state'=>'self','field'=>'name'),
				'model'=>array('state'=>'self','field'=>'name'),
				//'cd'=>array('state'=>'foreign','table'=>'code','field'=>'code','source'=>'code'),
				'code'=>array('state'=>'foreign','table'=>'code','field'=>'code','source'=>'code'),
				//'cr'=>array('state'=>'foreign','table'=>'color','field'=>'color','source'=>'color'),
				'color'=>array('state'=>'foreign','table'=>'color','field'=>'color','source'=>'color'),
				'date_created'=>array('state'=>'self','field'=>'date_created'),
				'detail'=>array('state'=>'self','field'=>'detail')
			);*/

			/*$search_str = 'category: Bed room; model:syro; 6001';
			$search_str = 'category: Bed room; category:dining room';
			$search_str = 'Bed room;  dining room';*/
			//$search_str = 'category:carpet;901';

			if($table == 'gate_seller_view' || $table == 'reduced_gate'){
				// return self::search_in_list($sorting,$startIndex,$pageSize,$search_str,$arr_table,$table,$other_table  = null);
				return self::special_search($sorting,$startIndex,$pageSize,$search_str,$arr_table,$table,$other_table  = null);
			}

			$search_explode = explode(';', $search_str);
			$count = count($search_explode);
			$sql_intent = '';
			$sql_extended = '';
			for($i=0;$i<$count;$i++){
				$search_part[$i] = explode(':', $search_explode[$i]);
				if(!is_null(@$search_part[$i][1]))
					$search_part[$i][1] = trim(@$search_part[$i][1]);
				$search_part[$i][0] = strtolower(trim($search_part[$i][0]));
// dsh($search_part[$i]);
				if(!is_null(@$search_part[$i][1]))
					$search_like = strtoupper($search_part[$i][1]);
				else
					$search_like = strtoupper($search_part[$i][0]);

				if(strrpos($search_like,'"'))
					$search_str = str_replace('"', '', $search_like);
				else
					$search_str = '%'.$search_like.'%';
				if(count($search_part[$i]) == 2){
					if($search_part[$i][0][0] == '*'){
						$or_and = 'AND';
						$search_part[$i][0] = substr($search_part[$i][0], 1);
					}
					else
						$or_and = 'OR';

					if(array_key_exists($search_part[$i][0],$arr_table)){
						if($arr_table[$search_part[$i][0]]['state']=='foreign'){
							$sql = "SELECT id FROM ".$arr_table[$search_part[$i][0]]['table']." WHERE upper(".$arr_table[$search_part[$i][0]]['field'].") LIKE '$search_str'";
							//dsh($sql);
							$result = self::$PDO->query($sql);
							$rows = $result->fetchAll(PDO::FETCH_ASSOC);
							
							foreach ($rows as $key => $value) {
								$sql_extended .= " $or_and $table.{$arr_table[$search_part[$i][0]]['source']} = {$value['id']}";
							}
						}
						else{
								$sql_intent .= " $or_and upper($table.".$arr_table[$search_part[$i][0]]['field'].") LIKE '$search_str'";
						}
							
					}
				}
				else{
					foreach ($arr_table as $key => $value) {
						if($value['state']=='foreign'){
							$sql = "SELECT id FROM ".$value['table']." WHERE upper(".$value['field'].") LIKE '$search_str'";
							//dsh($sql);
							$result = self::$PDO->query($sql);
							$rows = $result->fetchAll(PDO::FETCH_ASSOC);
							
							foreach ($rows as $key2 => $value2) {
								$sql_intent .= " OR $table.{$value['source']} = {$value2['id']}";
							}
						}
						else{
							$sql_extended .= " OR upper($table.".$value['field'].") LIKE '$search_str'";
						}
					}
				}

			}
			if(!$other_table)
				$sql_final = "SELECT * FROM $table WHERE 0 $sql_intent $sql_extended ORDER BY $sorting LIMIT $startIndex,$pageSize";
			else
				$sql_final = "SELECT $other_table WHERE 0 $sql_intent $sql_extended ORDER BY $table.$sorting LIMIT $startIndex,$pageSize";
// dsh($sql_final);
// die();
			$result = self::$PDO->query($sql_final);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			$sql_count = "SELECT count(id) AS count FROM $table WHERE 0 $sql_intent $sql_extended";
			$result = self::$PDO->query($sql_count);
			$count = $result->fetchObject();
//dsh($count);
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $count->count;
			$jTableResult['Records'] = $rows; 
			//self::record('read','Search: ');
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [search_in_list.class.php/function lists]'.$e->getMessage().'<br>';
			die();
		}
	}


	static function search_in_list($sorting,$startIndex,$pageSize,$search_str,$arr_table,$table,$other_table  = null){
		try{
// $time_start = microtime(true); 
			switch ($table) {
				case 'gate_seller_view':
					$sql = "SELECT gate.*,sum(qty) As qty,model.id_company,model.id_category FROM `gate` LEFT JOIN model ON gate.id_model = model.id WHERE gate.qty > 0 AND (gate.state = 1 OR gate.state = 2) GROUP BY id_stuff,id_code,id_color,id_location,id_department HAVING state = 1 OR state = 2 OR state = '-1'";
					$result = self::$PDO->query($sql);
					$rows = $result->fetchAll(PDO::FETCH_ASSOC);
					break;
				
				default:
					# code...
					break;
			}
			$count = count($rows);

			$search_explode = explode(';', $search_str);
			// dsh($count);

			$arr_table2 =array();
			foreach ($arr_table as $key => $value) {
				if($value['state'] == 'self' )
					$arr_table2[$key] = $value;
				else
					$arr_table2[$value['source']] = $value;
			}

			$search_result = array();

			// foreach ($rows as $key => $value) {
			$search_count = 0;
			$i=0;
			$count_search = 0;
			do{
				$value = $rows[$i];
				// dsh($value);
				
				$i++;
				$check = false;
				foreach ($value as $key2 => $value2) {
					if(isset($arr_table2[$key2])){
						
						if($arr_table2[$key2]['state'] == 'self'){
							// echo 'self<br>';
							if(stripos($value2,$search_str) !== false)
								$check = true;
						}
						else if($arr_table2[$key2]['state'] == 'foreign'){
							// echo $arr_table2[$key2]['table'].'<br>';
							$sql = "SELECT {$arr_table2[$key2]['field']} FROM {$arr_table2[$key2]['table']} WHERE id = '{$value2}'";
							$result = self::$PDO->query($sql);
							$row = $result->fetch(PDO::FETCH_ASSOC);
							// dsh($row);
							if(stripos($row[$arr_table2[$key2]['field']],$search_str) !== false)
								$check = true;
						}
					}
					
					
					}
					if($check){
						$count_search++;
						if($startIndex)
							$startIndex--;
						else if($search_count < $pageSize){
							array_push($search_result, $value);
							$search_count++;
						}
						
					}
						
				// dsh($check);
			}while(/*$search_count < $pageSize &&*/ $i < $count);
			// }
			// dsh($search_result);
			// echo '---------------';
			// dsh($arr_table2);
			// dsh($table);
			// dsh($arr_table);
			// dsh($rows);
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $count_search + 1;
			$jTableResult['Records'] = $search_result;
			//self::record('read','Search: ');
// $time_end = microtime(true);
// dsh(($time_end - $time_start));
			return json_encode($jTableResult);
		}
		catch(PDOException $e){
			echo 'Error: [search.class.php/function search_in_list]'.$e->getMessage().'<br>';
			die();
		}
	}

	static function special_search($sorting,$startIndex,$pageSize,$search_str,$arr_table,$table,$other_table  = null){
		try{
// $time_start = microtime(true); 

			// dsh($arr_table);
			// $search_str = '23204';
			$search_str = strtoupper($search_str);
			// $search_str = str_replace('"', '', $search_str);
			$arr_search = array();
			$str = $search_str[0];
			for($i = 1; $i<strlen($search_str); $i++){
				$ch = $search_str[$i];
				if($ch == '>' || $ch == ';'){
					array_push($arr_search,$str);
					array_push($arr_search,$ch);
					$str = '';
					continue;
				}
				$str .= $search_str[$i];
			}
			array_push($arr_search,$str);
			// foreach ($arr_search as $key => &$value) {
			// 	$value = trim($value);
			// }
			for($i=0;$i<count($arr_search);$i++){
				$arr_search[$i] = trim($arr_search[$i]);
			}
			// dsh($arr_search);
			$sql_intent = '';
			$sql_outent = '';
			$final_inner = ' AND(';
			foreach ($arr_search as $key0 => $value0) {
				// dsh($value0);
				if($value0 != '>' && $value0 != ';'){
					$search_str = $value0;
					if(strrpos($search_str,'"'))
						$search_str = str_replace('"', '', $search_str);
					else
						$search_str = "%$search_str%";

					foreach ($arr_table as $key => $value) {
						if($value['state'] == 'foreign'){
							// dsh($search_str);
							$sql = "SELECT id FROM {$value['table']} WHERE UPPER({$value['field']}) LIKE '$search_str';";
							$result = self::$PDO->query($sql);
							$rows = $result->fetchAll(PDO::FETCH_ASSOC);
							foreach ($rows as $key2 => $value2) {
								$sql_intent .= $value['source'].' = '.$value2['id'].' OR ';
							}
							// dsh($value['field']);
							// dsh($sql_intent);
							//if($rows)
							// dsh($rows);
						}
						else if($value['state'] == 'self'){
							$sql_outent .= " OR gate.{$value['field']} LIKE '$search_str' ";
						}
						else if($value['state'] == 'extra'){
							$sql = "SELECT id FROM {$value['table']} WHERE UPPER({$value['field']}) LIKE '$search_str';";
							$result = self::$PDO->query($sql);
							$rows = $result->fetchAll(PDO::FETCH_ASSOC);

							foreach ($rows as $key4 => $value4) {
								$sql = "SELECT id FROM {$value['extra_table']} WHERE UPPER({$value['extra_source']}) = {$value4['id']};";
								$result = self::$PDO->query($sql);
								$rows = $result->fetchAll(PDO::FETCH_ASSOC);
								foreach ($rows as $key5 => $value5) {
									$sql_intent .= $value['source'].' = '.$value5['id'].' OR ';
								}
							}
						}

					}
				}
					
				else{
					if($value0 == '>')
						$check = 'AND';
					else
						$check = 'OR';
					// dsh($sql_intent);
					// if($sql_intent){
						if(!$sql_intent)
							$sql_intent = '00000';
						$inner_search = '('.substr($sql_intent,0,-4).') ';
						$final_inner .= " $inner_search $check ";
						$sql_intent = '';
					// }
					
				}
			}
			if(!$sql_intent)
				$sql_intent = '00000';
			$inner_search = '('.substr($sql_intent,0,-4).') ';
			$final_inner .= "$inner_search )";
			// $inner_search = ' AND ('.substr($sql_intent,0,-4).') ';

			

			// dsh($sql_outent);
			// dsh($final_inner);
			
			switch ($table) {
				case 'gate_seller_view':
					$sql = "SELECT gate.*,sum(qty) As qty,model.id_company,model.id_category FROM `gate` LEFT JOIN model ON gate.id_model = model.id WHERE gate.qty > 0 AND (gate.state = 1 OR gate.state = 2) $final_inner $sql_outent GROUP BY id_stuff,id_code,id_color,id_location,id_department HAVING state = 1 OR state = 2 OR state = '-1' ORDER BY $sorting LIMIT $startIndex, $pageSize;"; 

					//$sql = "SELECT gate.*,sum(qty) As qty,model.id_company,model.id_category FROM `gate` LEFT JOIN model ON gate.id_model = model.id WHERE gate.qty > 0 AND (gate.state = 1 OR gate.state = 2) GROUP BY id_stuff,id_code,id_color,id_department HAVING state = 1 OR state = 2 ORDER BY $sorting LIMIT $startIndex, $pageSize;";
			
					// dsh($sql);
					$result = self::$PDO->query($sql);
					$rows = $result->fetchAll(PDO::FETCH_ASSOC);

					
					$sql = "SELECT gate.id,gate.state,gate.id_model,count(Distinct id_stuff,id_department,id_location,id_code,id_color) AS count,model.id_company,model.id_category FROM `gate` LEFT JOIN model ON gate.id_model = model.id WHERE  gate.qty > 0 AND (gate.state = 1 OR gate.state = 2)  $final_inner $sql_outent ";
					$result = self::$PDO->query($sql);
					$row = $result->fetchAll(PDO::FETCH_ASSOC);
					// dsh($row);
					$jTableResult = array();
					$jTableResult['Result'] = "OK";
					$jTableResult['TotalRecordCount'] = $row[0]['count'];
					$jTableResult['Records'] = $rows;
					return json_encode($jTableResult);
					break;

				case 'reduced_gate':
					$sql = "SELECT gate.*,sum(qty) As qty,model.id_company,model.id_category FROM `gate` LEFT JOIN model ON gate.id_model = model.id WHERE (gate.state = 1 OR gate.state = 2) $final_inner $sql_outent GROUP BY id_stuff,id_code,id_color,id_location,id_department HAVING state = 1 OR state = 2 OR state = '-1' ORDER BY $sorting LIMIT $startIndex, $pageSize;";
					// dsh($sql);
					$result = self::$PDO->query($sql);
					$rows = $result->fetchAll(PDO::FETCH_ASSOC);

					
					$sql = "SELECT gate.id,gate.state,gate.id_model,count(Distinct id_stuff,id_department,id_location,id_code,id_color) AS count,model.id_company,model.id_category FROM `gate` LEFT JOIN model ON gate.id_model = model.id WHERE  (gate.state = 1 OR gate.state = 2) $final_inner $sql_outent";
					$result = self::$PDO->query($sql);
					$row = $result->fetchAll(PDO::FETCH_ASSOC);
					// dsh($row);
					$jTableResult = array();
					$jTableResult['Result'] = "OK";
					$jTableResult['TotalRecordCount'] = $row[0]['count'];
					$jTableResult['Records'] = $rows;
					return json_encode($jTableResult);
					break;
				break;
				
				default:
					# code...
					break;
			}
// dsh(count($rows));
// $time_end = microtime(true);
// dsh(($time_end - $time_start));
		}
		catch(PDOException $e){
			echo 'Error: [search.class.php/function special_search]'.$e->getMessage().'<br>';
			die();
		}
	}
}

//search::record_activity('write',5,'ddd','no');
//dsh(search::make_search_list('id ASC',0,3,@$search_str,@$arr_table,'model'));
?>