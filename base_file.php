<?php 

/**
 * Считывает записи из файла в ассоциативный массив
 * @return массив с записямя из файла
 */
function read_base() 
{
	$data = array();	
	$filename = "base.txt";
	if (!file_exists($filename))
		return 0;
	
	$content = file_get_contents($filename);
	$rows = explode("\n", $content);
	$rows = array_diff($rows, array(''));
	foreach ($rows as $row) {
		$row = trim($row);
		$columns = explode(';', $row);
		$columns = array_diff($columns, array(''));
		$data[] = $columns;
	}	
	return $data;
}


/**
 * записывает данные из двумерного массива в файл
 * @param $array - двумерный массив
 */
function write_base($array)
{
	$content = '';
	foreach ($array as $row) {
		foreach ($row as $column) 
			$content .= trim($column) . ';'; 
		$content .= "\n";
	}
	return file_put_contents("base.txt", $content);
}


?>