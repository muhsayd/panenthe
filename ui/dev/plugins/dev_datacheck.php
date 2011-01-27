<?php
/**
 * Panenthe VPS Management
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

if(!defined("IS_INCLUDED")){
	exit;
}

class dev_dataCheck {

	//Class Settings
	private $settings = array();
	private $okay = true;
	private $output = '';
	private $inc = '';

	public function __construct($settings=array()){

		//Apply Hot Settings
		$this->settings = array_merge($this->settings,$settings);

	}

	public function addChecks($checks=array()){

		foreach($checks AS $name => $check){
			$this->addCheck($name,$check);
		}

	}

	public function addCheck($name,$check){
		$this->checks[$name] = $check;
	}

	public function removeCheck($name){
		unset($this->checks[$name]);
	}

	public function executeChecks(){

		foreach($this->checks AS $name => $check){
			if(is_array($check->failures) && count($check->failures) > 0){
				$this->okay = false;
				foreach($check->failures AS $message){
					$this->output .= $this->inc.$message;
					if($this->inc == ''){
						$this->inc = '<br />';
					}
				}
			}
		}

	}

	public function isOkay(){
		return $this->okay;
	}

	public function getOutput(){
		return $this->output;
	}

}

class dev_dataCheckField {

	public $fieldName;
	public $fieldDesc;
	public $fieldData;
	public $failures = array();

	public function __construct($fieldName,$fieldDesc,$fieldData){
		$this->fieldName = $fieldName;
		$this->fieldDesc = $fieldDesc;
		$this->fieldData = $fieldData;
	}

	public function manualFailure($message){
		
		$this->failures[] = preg_replace(
			'@\[\['.preg_quote($this->fieldName).'\]\]@si',
			$this->fieldDesc,
			$message
		);

	}

	public function equals($answer){
		if($this->fieldData != $answer){
			$this->failures[] = $this->fieldDesc." does not equal required value.";
		}
	}

	public function notEquals($answer){
		if($this->fieldData != $answer){
			$this->failures[] = $this->fieldDesc." must not equal the ".$answer.".";
		}
	}

	public function notEmpty(){
		if(empty($this->fieldData)){
			$this->failures[] = $this->fieldDesc." cannot be emtpy.";
		}
	}

	public function notBlank(){
		if($this->fieldData == ''){
			$this->failures[] = $this->fieldDesc." cannot be left blank.";
		}
	}

	public function minLength($length=0){
		if(strlen($this->fieldData) < $length){
			$this->failures[] = $this->fieldDesc." must contain a minimum of ".$length." character(s).";
		}
	}

	public function maxLength($length=1){
		if(strlen($this->fieldData) > $length){
			$this->failures[] = $this->fieldDesc." must contain a maximum of ".$length." character(s).";
		}
	}

	public function greaterThan($floor){
		if($this->fieldData < $floor){
			$this->failures[] = $this->fieldDesc." must be greater than ".$floor.".";
		}
	}

	public function lessThan($ceiling){
		if($this->fieldData > $ceiling){
			$this->failures[] = $this->fieldDesc." must be less than ".$ceiling.".";
		}
	}

	public function isType($type){
		switch($type){
			case 'num':
				$this->allowChars('0-9','numeric');
				break;

			case 'alnum':
				$this->allowChars('0-9a-zA-Z','alphanumeric');
				break;

			case 'alnums':
				$this->allowChars('0-9a-zA-Z\s','alphanumeric with spaces');
				break;

			case 'alpha':
				$this->allowChars('a-zA-Z','alphabetical');
				break;

			case 'alphas':
				$this->allowChars('a-zA-Z\s','alphabetical with spaces');
				break;

		}
	}

	public function isNotType($type){
		switch($type){
			case 'num':
				$this->denyChars('0-9','numeric');
				break;

			case 'alnum':
				$this->denyChars('0-9a-zA-Z','alphanumeric');
				break;

			case 'alnums':
				$this->denyChars('0-9a-zA-Z\s','alphanumeric with no spaces');
				break;

			case 'alpha':
				$this->denyChars('a-zA-Z','alphabetical');
				break;

			case 'alphas':
				$this->denyChars('a-zA-Z\s','alphabetical with now spaces');
				break;

		}
	}

	public function allowChars($rule,$ruleDesc){
		if(!preg_match('@^['.preg_quote($rule,'@').']*$@si',$this->fieldData)){
			$this->failures[] = $this->fieldDesc." must be ".$ruleDesc.".";
		}
	}

	public function allowRawChars($rule,$ruleDesc){
		if(!preg_match('@^['.$rule.']*$@si',$this->fieldData)){
			$this->failures[] = $this->fieldDesc." must be ".$ruleDesc.".";
		}
	}

	public function denyChars($rule,$ruleDesc){
		if(preg_match('@['.preg_quote($rule,'@').']+@si',$this->fieldData)){
			$this->failures[] = $this->fieldDesc." must be non ".$ruleDesc.".";
		}
	}

	public function denyRawChars($rule,$ruleDesc){
		if(preg_match('@['.$rule.']+@si',$this->fieldData)){
			$this->failures[] = $this->fieldDesc." must be non ".$ruleDesc.".";
		}
	}

}

?>
