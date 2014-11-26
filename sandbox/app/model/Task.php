<?php

namespace App\Model;

class Task {
	
	private $unit, $value, $exp, $id;
	
	public function __construct($unit) {
		$this->unit = $unit;
		$this->value = rand(1,999);
		$this->exp = rand(-3,2);
	}
	
	public function toReal() {
		switch(true) {
			case ($this->value < 10):
				$digits = 1;
				break;
			case ($this->value < 100):
				$digits = 2;
				break;
			default:
				$digits = 3; 
		}
		return ($this->value / pow(10, $digits-1)) * pow(10, $this->exp); 
	}
	
	public function getUnit(){
		return $this->unit;
	}
	
	public function getValue(){
		return $this->value;
	}
	
	public function getExp(){
		return $this->exp;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
}
