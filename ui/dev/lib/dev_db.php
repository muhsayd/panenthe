<?php
/**
 * Panenthe
 *
 * Very light PHP Framework
 *
 * This is NOT Free Software
 * This software is NOT Open Source.
 * Please see panenthe.com for more information.
 *
 * Use of this software is binding of a license agreement.
 * This license agreeement may be found at panenthe.com
 *
 * Panenthe DOES NOT offer this software with any WARRANTY whatsoever.
 * Panenthe DOES NOT offer this software with any GUARANTEE whatsoever.
 *
 * @copyright Panenthe, Nullivex LLC. All Rights Reserved.
 * @author Nullivex LLC <contact@nullivex.com>
 * @license http://www.panenthe.com
 * @link http://www.panenthe.com
 *
 */

final class dev_db {
	
	//db environment
	private $config;
	private $pdo;
	private $connected;
	private $query_count;
	
	public function __construct($config){

		//Save Config
		$this->config = $config;

		//Set Query Count
		$this->query_count = 0;
		
		//Get Connection
		$this->connect();
		
	}
	
	private function connect(){

		//Connection String
		$dsn = $this->config['db_driver'].':dbname='.$this->config['db_name'].';host='.$this->config['db_host'].';port='.$this->config['db_port'];
		$user = $this->config['db_user'];
		$pass = $this->config['db_pass'];
		
		try{
			$this->pdo = new PDO($dsn,$user,$pass);
			$this->connected = true;
		}
		catch(PDOException $error){
			echo "Database Connection Failed: ".$error->getMessage();
			$this->connected = false;
		}
		
		//Set Driver Properties
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		
	}
	
	public function beginTransaction(){
		return $this->pdo->beginTransaction();
	}
	
	public function commit(){
		return $this->pdo->commit();
	}
	
	public function errorCode(){
		return $this->pdo->errorCode();
	}
	
	public function errorInfo(){
		return $this->pdo->errorInfo();
	}
	
	public function exec($statement){
		$this->query_count++;
		return $this->pdo->exec($statement);
	}
	
	public function getAttribute($attribute){
		return $this->pdo->getAttribute($attribute);
	}
	
	public static function getAvailableDrivers(){
		return PDO::getAvailableDrivers();
	}
	
	public function lastInsertId($name=null){
		return $this->pdo->lastInsertId($name);
	}
	
	public function prepare($statement,$driver_options=array()){
		$this->query_count++;
		try{
			$query = $this->pdo->prepare($statement,$driver_options);
		}
		catch(PDOException $error){
			echo "Query Prepare Failed: ".$error->getMessage();
		}

		return $query;
		
	}
	
	public function query($statement){
		$this->query_count++;
		return $this->pdo->query($statement);
	}
	
	public function quote($string,$paramater_type=false){
		if($paramater_type){
			return $this->pdo->quote($string,$parameter_type);
		}
		else
		{
			return $this->pdo->quote($string);
		}
	}
	
	public function rollBack(){
		return $this->pdo->rollBack();
	}
	
	public function setAttribute($attribute,$value){
		return $this->pdo->setAttribute($attribute,$value);
	}

	public function get_query_count(){
		return $this->query_count;
	}
	
}

?>
