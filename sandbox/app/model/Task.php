<?php

namespace App\Model;

class Task {

	private $unit, $value, $exp, $id;

	public function __construct($unit) {
		$this->unit = $unit;
		$this->value = rand(1, 999);
		$this->exp = rand(-3, 2);
	}
	
	/**
	 * Set task values according to existing task
	 * @param type $id ID of the existing task in database
	 * @param Nette\Database\Table\ActiveRow $values Row from database of the existing task
	 */
	public function setConstruct($id, $values){
	    $this->unit = $values;
	    $this->value = $values->nb_value_from;
	    $this->exp = $values->nb_power_from;
	    $this->id = $id;
	}

	/**
	 * Converts task to human readable string
	 * @see toHumanValue
	 * @return string
	 */
	public function __toString() {
		return strval(self::toHumanValue($this->value, $this->exp));
	}
	
	
	/**
	 * Returns unit of the task
	 * 
	 * @return Nette\Database\Table\ActiveRow Row of the task's unit from database
	 */
	public function getUnit() {
		return $this->unit;
	}

	/**
	 * Returns name of the unit of the task.
	 *  
	 * @return string
	 */
	public function getUnitName() {
		return $this->getUnit()->{UnitConversion::UNIT_COLUMN_NAME};
	}

	/**
	 * Returns base value of the task
	 * 
	 * @return int
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Returns exponent of the task
	 * 
	 * @return int
	 */
	public function getExp() {
		return $this->exp;
	}

	/**
	 * Returns ID of the task
	 * 
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Sets ID of the task
	 * 
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * Returns value in a base value - float value in range from 0 to 10 excluded
	 * 
	 * @param type $value
	 * @return float
	 */
	public static function toRealValue($value) {
		$value = intval($value);
		if ($value == 0) {
			return 0;
		}
		$value = $value / pow(10, floor(log($value, 10)));
			
		return $value;
	}
	
	/**
	 * Converts strings base value to internal base value
	 * 
	 * @param string $value
	 * @return int
	 */
	public static function toBaseValue($value) {
		$value = str_replace('.', '', strval($value));
			
		return intval($value);
	}
	
	/**
	 * Calculates base exponent of the task
	 * 
	 * @param Nette\Database\Table\ActiveRow $unit Base unit of the task's unit 
	 * @param int $exp Exponent of the task
	 * @return int
	 */
	public static function toBaseExp($unit, $exp) {
		return $unit->{UnitConversion::UNIT_COLUMN_MULTIPLE} + $exp;
	}

	/**
	 * Converts task to human readable format
	 * 
	 * @param int $value
	 * @param int $exp
	 * @return float
	 */
	public static function toHumanValue($value, $exp) {
		return self::toRealValue($value) * pow(10, $exp);
	}
}
