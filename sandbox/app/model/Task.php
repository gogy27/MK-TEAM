<?php

namespace App\Model;

class Task {

	private $unit, $value, $exp, $id;

	public function __construct($unit) {
		$this->unit = $unit;
		$this->value = rand(1, 999);
		$this->exp = rand(-3, 2);
	}
	
	public function setConstruct($id, $values){
	    $this->unit = $values;
	    $this->value = $values->nb_value_from;
	    $this->exp = $values->nb_power_from;
	    $this->id = $id;
	}

	public function __toString() {
		return strval(($this->value / pow(10, floor(log($this->value, 10)))) * pow(10, $this->exp));
	}
	
	public function getUnit() {
		return $this->unit;
	}

	public function getUnitName() {
		return $this->getUnit()->{UnitConversion::UNIT_COLUMN_NAME};
	}

	public function getValue() {
		return $this->value;
	}

	public function getExp() {
		return $this->exp;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}
	
	public static function toBaseValue($value) {
		$value = str_replace('.', '', strval($value));
			
		return intval($value);
	}
	
	public static function toBaseExp($unit, $exp) {
		return $unit->{UnitConversion::UNIT_COLUMN_MULTIPLE} + $exp;
	}

}
