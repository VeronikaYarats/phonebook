<?
require_once "base_file.php";
/**
 * ��������� ������ � ���������� �����
 * @param $name ���
 * @param $phone ������
 * @return 0 � ������ ������������� ����� ��������
 * @return 1 � ������ ������������� ����� ��������
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
 * ����������� ������ � ��������������� $id
 * @param $id ������������� ������������� ������
 * @param $name ��� ������� ����� ���������������
 * @param $phone ������� ������� ����� ���������������
 * @return 0 � ����� ���� ���� ����� ��� �� ����������
 * 		   1 � ����� ���� id ������ ����������� ��� �����������
 * 		   2 � ����� ���� ��� �� �������
 *		   3 � ����� ���� ������� ������ ����������� ��� �����������
 * 		   4 � ����� ���� ������ � ����� ��������� ��� ����������
 *		   5 � ����� ���� ������ � ����� id �����������
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
 * �������� ������������� ������ � ������� ������ � ��������������� $id
 * @param $id ������������� ������
 * @return 0 � ������ ���� ���� ����� ��� �� ����������
 * @return 1 � ������ ���� id ������ ����������� ��� �����������
 * @return 2 � ������ ���� ������ � ����� id �� �������
 * @return ������ [��������������, �����, �������]
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
 * ������� ������ � ��������������� $id
 * @param $id ������������� ������ ������� ����� �������
 * @return 0 � ������ ���� ���� ����� ��� �����������
 * @return 1 � ������ ���� id ������ ����������� ��� �����������
 * @return 2 � ������ ���� ������ � ����� id �� ����������
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
 * ��������� ������������ ����� � ������,
 * ����� ����� ������ ������
 * $pattern ������
 * $str �����
 * @return ��������� ���������(0 - �������������, !0 �� ������������)
 */
function check_mask_first($pattern, $str)
{ 
	$pattern = substr($pattern, 0, strlen($str));
	return strcmp($pattern, $str);
}


/**
 * ��������� ������������ ����� � ������,
 * ����� ����� ����� ������
 * $pattern ������
 * $str �����
 * @return ��������� ���������(0 - �������������, !0 �� ������������)
 */
function check_mask_last($pattern, $str)
{
	$start_pos = strlen($pattern) - strlen($str); 
	$pattern = substr($pattern, $start_pos, strlen($pattern)-1);
	return strcmp($pattern, $str);
}


/** 
 * $pattern  ������
 * $str �����
 * @return 1 ������� ��������� ���������� ���������
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
 * ���� ������ � ���� �� ������� ����� �������� � �����
 * $name_mask - ����� �����
 * $phone_mask - ����� ��������
 * @return 0 � ������ ���� ���� ����� ��� �����������
 * @return 1 � ������ ���� ������ � ������ �� �������
 * @return 2 � ������ ���� ������ � ��������� �� �������
 * @return ������ ��������[�������������, ���, �������]
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