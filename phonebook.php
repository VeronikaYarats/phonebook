<?php
require_once "phone_base_file.php";

$base_file_name = 'base.txt';
switch($argv[1]) {
case "--help":
	print_help();
	break;

case "add":
	//$argv[2] - имя
	//$argv[3] - телефон
	$err = base_add_item($argv[2], $argv[3]);
	switch ($err) {
	case 0:
		echo "Absent name\n";
		break;
	case 1: 
		echo "Incorect or absent phone\n";
		break;
	case 2: 
		echo "Number " . $argv[3] . " is already in phonebook\n";
		break;
	default:
		echo "Item was added\n";
		break;
	}
	break;
	
case "del":
	//$argv[2] - id
	$err = base_del_item($argv[2]);
	switch ($err) {
	case 0:
		echo "Phonebook is empty\n";
		break;
	case 1:
		echo "Id is incorrect or absent\n";
		break;
	case 2:
		echo "Item with such id is not found\n";
		break;
	default:
		echo "Item was deleted\n";
		break;
	}
	break;
	
case "edit":
	//$argv[2] - идентификатор редактируемой записи
	//$argv[3] - имя
	//$argv[4] - телефон
	$err = base_edit_item($argv[2], $argv[3], $argv[4]);
	switch ($err) {
	case 0:
		echo "Phonebook is empty\n";
		break;
	case 1:
		echo "Id is incorrect or absent\n";
		break;
	case 2:
		echo "Name is absent\n";
		break;
	case 3: 
		echo "Phone is incorrect or absent\n";
		break;
	case 4:
		echo "Item with number " . $argv[4] . " is already exist\n";
		break;
	case 5:
		echo "Item with such id is not found\n";
		break;
	default:
		echo "Item was adited\n";
		break;
	}
	break;
	
case "print":
	//$argv[2] - маска имени
	//$argv[3] - маска телефона
	$err = base_get_list_by_mask($argv[2], $argv[3]);
	switch ($err) {
	case 0:
		echo "Phonebook is empty\n";
		break;
	case 1:
		echo "Name is not found\n";
		break;
	case 2:
		echo "Phone is not found\n";
		break;
	case 3:
		echo "Enter name and phone\n";
		break;
	default:
		foreach($err as $row)
			echo "Id: " . $row[0] . "\nContact name: " . $row[1] . "\nPhone Number: " . $row[2] . "\n";
	}
	break;
	
case "id":	
	//$argv[2] - идентификатор 
	$err = base_get_item_by_id($argv[2]);
	switch ($err) {
	case 0:
		echo "Phonebook is empty\n";
		break;
	case 1:
		echo "Id is incorrect or absent\n";
		break;
	case 2:
		echo "Item with such id is not found\n";
		break;
	default:
		echo "Id: " . $err[0] . "\nContact name: " . $err[1] . "\nPhone Number: " . $err[2] . "\n";
	}
	break;
	
case  isset($argv[1]):
	echo "Please enter a command\n";
	print_help();
	break;
	
default:
	echo "command not found\n";
	print_help();
	break;
}


/**
 * выводит все команды
 */
function print_help()
{
	echo "\nprint \"add name phone\" to add an item\n" .
	"Example: \tadd Veronika 5365072\n\n";
	echo "print \"edit id name phone \" to edit an item by id\n" .
	"Example: \tedit 1 Veronika 80295365072\n\n";
	echo "print \"id \"to get item by id\n" .
	"Example: \tid 1\n\n";
	echo "print \"del id\" to delete item by id\n" .
	"Example: \tdel 1\n\n";
	echo "print \"print phone name\" to get an item\n" .
	"Example: \tprint Ve* *72\n\t\tprint '*' 34*\n";
}

