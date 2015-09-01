<?
require_once "base_file.php";
/**
 * добавлЯет запись в телефонную книгу
 * @param $name имЯ
 * @param $phone елефон
 * @return 0 в случае некорректного ввода телефона
 * @return 1 в случае некорректного ввода телефона
 */
function base_add_item($name, $phone) 
{
	if(!isset($name))
		return 0;
	if(!is_numeric($phone) || !isset($phone))
		return 1;

	$data = array();
	$max = 1;


	if (read_base() == 0)
		$max = 1;
		else {
			$data = read_base();
			foreach($data as $rows){
				if($rows[2] == $phone)
					return 2;
				if($rows[0] > $max)
				$max = $rows[0];
			$max++;
			}
		}
	$data[$max]['id'] = $max;
	$data[$max]['name'] = $name;
	$data[$max]['phone'] = $phone;
	return write_base($data);
}


/**
 * редактирует запись с идентификатором $id
 * @param $id идентификатор редактируемой записи
 * @param $name имЯ которое нужно отредактировать
 * @param $phone телефон который нужно отредактировать
 * @return 0 в случе если база пуста или не существует
 * 		   1 в случе если id введен некорректно или отсутствует
 * 		   2 в случе если имЯ не введено
 *		   3 в случе если телефон введен некорректно или отсутствует
 * 		   4 в случе если запись с таким телефоном уже существует
 *		   5 в случе если запись с таким id отсутствует
 */
function base_edit_item($id, $name, $phone)
{
	$index;
	$flag = 0;
	$data = read_base();
	if($data == 0)
		return 0;
	
		
	if (!is_numeric($id) || !isset($id)) 
		return 1;
	if (!isset($name))
		return 2;
	if(!is_numeric($phone) || !isset($phone))
		return 3;
	
	foreach ($data as $key=>$rows) {
		if($rows[2] == $phone)
			return 4;
		if ($rows[0] == $id) {
			$index = $key;
			$flag = 1;
		}
	}
	$data[$index][1] = $name;	 
	$data[$index][2] = $phone;
	if($flag == 0) 
		return 5;
	return write_base($data);	
}


/**
 * Џолучить ассоциативный массив с данными записи с идентификатором $id
 * @param $id идентификатор записи
 * @return 0 в случае если база пуста или не существует
 * @return 1 в случае если id введен некорректно или отсутствует
 * @return 2 в случае если запись с таким id не найдена
 * @return массив [индентификатор, имемЯ, телефон]
 */
function base_get_item_by_id($id)
{
	$data = read_base();
	$flag = 0;
	if($data == 0)
		return 0;
	
	if (!is_numeric($id) || !isset($id)) 
		return 1;
	
	$index = NULL;
	foreach ($data as $key=>$rows)
		if($rows[0] == $id) {
			$index = $key;
			$flag = 1;
		}
		
	if($flag == 0)
		return 2;  	
		                  
	$id = $data[$index][0];
	$name = $data[$index][1];
	$phone = $data[$index][2];
	
	return array($id, $name, $phone);
}


/**
 * удалЯет запись с идентификатором $id
 * @param $id идентификатор записи которую нужно удалить
 * @return 0 в случае если база пуста или отсутствует
 * @return 1 в случае если id введен некорректно или отсутствует
 * @return 2 в случае если записи с таким id не существует
 */		
function base_del_item($id)
{
	$data = read_base();
	if($data == 0)
		return 0;
	
	if (!is_numeric($id) || !isset($id)) 
		return 1;
	
	$index;
	$flag = 0;
	foreach($data as $key=>$rows)
		if($rows[0] == $id){
			$index = $key;
			$flag = 1;	
		}
	if ($flag == 0) 
		return 2; 
	unset($data[$index]);
	return write_base($data);	
}



/**
 * проверЯет соответствиЯ маски и строки,
 * когда маска начало строки
 * $pattern строка
 * $str маска
 * @return результат сравнениЯ(0 - соответствует, !0 не соответсвует)
 */
function check_mask_first($pattern, $str)
{ 
	$pattern = substr($pattern, 0, strlen($str));
	return strcmp($pattern, $str);
}


/**
 * проверЯет соответствие маски и строки,
 * когда маска конец строки
 * $pattern строка
 * $str маска
 * @return результат сравнениЯ(0 - соответствует, !0 не соответсвует)
 */
function check_mask_last($pattern, $str)
{
	$start_pos = strlen($pattern) - strlen($str); 
	$pattern = substr($pattern, $start_pos, strlen($pattern)-1);
	return strcmp($pattern, $str);
}


/** 
 * $pattern  строка
 * $str маска
 * @return 1 вслучае успешного результата сравнениЯ
 */
function check_for_mask($pattern, $str)
{
	$star = "*";
	$str = trim($str);
	if(strcmp($str, $star) == 0)
		return 1;

	$length_str = strlen($str);
	$last_str_char = $length_str-1; 
	$pos = strpos($str, '*');
	
	switch ($pos) {
	case "0":
		$str = substr($str, 1, $length_str);
		if (check_mask_last($pattern, $str) == 0)	
			return 1;
		break;
			
	case $last_str_char:
		$str = substr($str, 0, $length_str-1);
		if (check_mask_first($pattern, $str) == 0)
			return 1;
		break;
	
	default:
		$str1 = substr($str, 0, $pos);
		$str2 = substr($str, $pos+1, $length_str );
		if (check_mask_first($pattern, $str1) == 0)	
			if (check_mask_last($pattern, $str2) == 0)
				return 1;
	} 
}	



/**
 * ищет записи в басе по искомой маске телефона и имени
 * $name_mask - маска имени
 * $phone_mask - маска телефона
 * @return 0 в случае если база пуста или отсутствует
 * @return 1 в случае если запись с именем не найдена
 * @return 2 в случае если запись с телефоном не найдена
 * @return массив массивов[идентивикатор, имЯ, телефон]
 */
function base_get_list_by_mask($name_mask, $phone_mask)
{
	$base = read_base();
	$result = array();
	if($base == 0)
		return  0;
		
	$flag1 = 0;
	$flag2 = 0;
	
	foreach($base as $row) {
		 if(check_for_mask($row[1], $name_mask)) {
		 	$flag1++;
		 	if(check_for_mask($row[2], $phone_mask)) {
		 		$flag2++;
		 		$result[] = $row;
			}	
	 	}
	}
	if ($flag1 == 0)
		return 1;
	if ($flag2 == 0)
		return 2;

	return $result;
}	

?>