<?php

class Model {

	//private $connection;
	public function __construct()
	{
		global $config;
		
	//	$this->connection = mysql_pconnect($config['db_host'], $config['db_username'], $config['db_password']) or die('MySQL Error: '. mysql_error());
	//	mysql_select_db($config['db_name'], $this->connection);
	
	//$this->open('aprsbot.db');
	
	}

	public function escapeString($string)
	{
		return mysql_real_escape_string($string);
	}

	public function escapeArray($array)
	{
	    array_walk_recursive($array, create_function('&$v', '$v = mysql_real_escape_string($v);'));
		return $array;
	}
	
	public function to_bool($val)
	{
	    return !!$val;
	}
	
	public function to_date($val)
	{
	    return date('Y-m-d', $val);
	}
	
	public function to_time($val)
	{
	    return date('H:i:s', $val);
	}
	
	public function to_datetime($val)
	{
	    return date('Y-m-d H:i:s', $val);
	}
	
	public function query($qry)
	{
		//$result = query($qry) or die('SQL Error: '. lastErrorMsg());
		$resultObjects = array();

		//while($row = fetchArray($result)) $resultObjects[] = $row;

		return $resultObjects;
	}

	public function execute($qry)
	{
		//$exec = query($qry) or die('SQL Error: '. lastErrorMsg());
		return $exec;
	}
    
}
?>
