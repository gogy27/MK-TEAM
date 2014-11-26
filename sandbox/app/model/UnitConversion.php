<?php
namespace App\Model;

use Nette,
		Nette\Database;

class UnitConversion extends Nette\Object {
	
	const UNIT_TABLE_NAME = 'unit',
				UNIT_COLUMN_ID = 'id',
				UNIT_COLUMN_DIFFICULTY = 'nb_level',
				UNIT_COLUMN_BASE_UNIT = 'fl_base_unit',
				UNIT_BASE_UNIT_TRUE = 'A',
				UNIT_BASE_UNIT_FALSE = 'N',
				TASK_TABLE_NAME = 'task',
				TASK_COLUMN_USER_ID = 'id_user',
				TASK_COLUMN_UNIT_ID = 'id_unit',
				TASK_COLUMN_CREATED = 'dt_created',
				TASK_COLUMN_VALUE_FROM = 'nb_value_from',
				TASK_COLUMN_POWER_FROM = 'nb_power_from';
	
	/** @var Nette\Database\Context */
	private $database;
	
	public function __construct(Nette\Database\Connection $connection) {
		$this->database = new Database\Context($connection);
	}
	
	public function generateConversion($userID, $difficulty = 1) {
		$unit = $this->getRandomUnit($difficulty);
		$task = new Task($unit);
		$insertData = array(
										self::TASK_COLUMN_USER_ID => $userID, 
										self::TASK_COLUMN_UNIT_ID => $unit->getPrimary(),
										self::TASK_COLUMN_CREATED => date('Y-m-d H:i:s', time()),
										self::TASK_COLUMN_VALUE_FROM => $task->getValue(),
										self::TASK_COLUMN_POWER_FROM => $task->getExp());
		$this->database->table(self::TASK_TABLE_NAME)->insert($insertData);
		$task->setId($this->database->getInsertId());
		return $task;
	}
	
	private function getRandomUnit($difficulty) {
		$offset_result = $this->database->table(self::UNIT_TABLE_NAME)->select("FLOOR(RAND() * COUNT(*)) AS `offset`")->where(array(self::UNIT_COLUMN_BASE_UNIT => self::UNIT_BASE_UNIT_FALSE, self::UNIT_COLUMN_DIFFICULTY => $difficulty));
		$offset = $offset_result->fetch()->offset;
		return $this->database->table(self::UNIT_TABLE_NAME)->where(array(self::UNIT_COLUMN_BASE_UNIT => self::UNIT_BASE_UNIT_FALSE, self::UNIT_COLUMN_DIFFICULTY => $difficulty))->order(self::UNIT_COLUMN_ID)->limit(1, $offset)->fetch();
	}
}
