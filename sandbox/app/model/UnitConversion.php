<?php

namespace App\Model;

use Nette,
		Nette\Database;

class UnitConversion extends Nette\Object {

	const UNIT_TABLE_NAME = 'unit',
					UNIT_COLUMN_ID = 'id',
					UNIT_COLUMN_DIFFICULTY = 'nb_level',
					UNIT_COLUMN_BASE_UNIT = 'fl_base_unit',
					UNIT_COLUMN_MULTIPLE = 'nb_multiple',
					UNIT_COLUMN_CATEGORY = 'nb_category',
					UNIT_COLUMN_NAME = 'str_unit_name',
					TRUE_VALUE = 'A',
					FALSE_VALUE = 'N',
					TASK_TABLE_NAME = 'task',
					TASK_COLUMN_USER_ID = 'id_user',
					TASK_COLUMN_UNIT_ID = 'id_unit',
					TASK_COLUMN_CREATED = 'dt_created',
					TASK_COLUMN_UPDATED = 'dt_updated',
					TASK_COLUMN_VALUE_FROM = 'nb_value_from',
					TASK_COLUMN_POWER_FROM = 'nb_power_from',
					TASK_COLUMN_VALUE_TO = 'nb_value_to',
					TASK_COLUMN_POWER_TO = 'nb_power_to',
					TASK_COLUMN_POWER_BASE_TO = 'nb_power_base_to',
					TASK_COLUMN_CORRECT = 'fl_correct',
					TASK_COLUMN_TEST_ID = 'id_test';

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Connection $connection) {
		$this->database = new Database\Context($connection);
	}

	public function checkConversion($userID, $taskID, $data) {
		$task = $this->database->table(self::TASK_TABLE_NAME)->where('id', $taskID)->fetch();
		if (!$task) {
			return false;
		}

		if (!($this->isTaskOpen($task) && ($this->getTaskOwner($task) == $userID))) {
			return false;
		}

		$data[self::TASK_COLUMN_CORRECT] = self::FALSE_VALUE;

		$data['value'] = Task::toBaseValue($data['value']);
		$userValue = 0;
		if ($data['value'] > 0) {
			$userValue = ($data['value'] / pow(10, floor(log($data['value'], 10)))) * pow(10, $data['exp']);
			$taskValue = ($task->{self::TASK_COLUMN_VALUE_FROM} / pow(10, floor(log($task->{self::TASK_COLUMN_VALUE_FROM}, 10)))) * pow(10, $task->{self::TASK_COLUMN_POWER_FROM});
			$taskBaseExp = Task::toBaseExp($this->getUnit($task->{self::TASK_COLUMN_UNIT_ID}), $task->{self::TASK_COLUMN_POWER_FROM});
			if (($userValue == $taskValue) && ($data['expBase'] == $taskBaseExp)) {
				$data[self::TASK_COLUMN_CORRECT] = self::TRUE_VALUE;
			}
		} else {
			$data[self::TASK_COLUMN_CORRECT] = null;
		}


		$data[self::TASK_COLUMN_VALUE_TO] = $data['value'];
		$data[self::TASK_COLUMN_POWER_TO] = $data['exp'];
		$data[self::TASK_COLUMN_POWER_BASE_TO] = $data['expBase'];
		unset($data['value']);
		unset($data['exp']);
		unset($data['expBase']);
		$this->insertResult($task, $data);

		return true;
	}

	public function generateConversion($userID, $difficulty = 1, $test_id = NULL) {
		$unit = $this->getRandomUnit($difficulty);
		$task = new Task($unit);
		$insertData = array(
				self::TASK_COLUMN_USER_ID => $userID,
				self::TASK_COLUMN_UNIT_ID => $unit->getPrimary(),
				self::TASK_COLUMN_CREATED => date('Y-m-d H:i:s', time()),
				self::TASK_COLUMN_VALUE_FROM => $task->getValue(),
				self::TASK_COLUMN_POWER_FROM => $task->getExp(),
				self::TASK_COLUMN_TEST_ID => $test_id);
		$row = $this->database->table(self::TASK_TABLE_NAME)->insert($insertData);
		$task->setId($row->id);
		return $task;
	}

	public function reGenerateTask($values) {
		$task = new Task($values);
		$task->setConstruct($values->idcko, $values);
		return $task;
	}

	public function getBaseUnit($unit) {
		return $this->database->table(self::UNIT_TABLE_NAME)
										->where([
												self::UNIT_COLUMN_CATEGORY => $unit->{self::UNIT_COLUMN_CATEGORY},
												self::UNIT_COLUMN_BASE_UNIT => self::TRUE_VALUE
										])->fetch();
	}

	private function getRandomUnit($difficulty) {
		$offset_result = $this->database->table(self::UNIT_TABLE_NAME)->select("FLOOR(RAND() * COUNT(*)) AS `offset`")->where(array(self::UNIT_COLUMN_BASE_UNIT => self::FALSE_VALUE, self::UNIT_COLUMN_DIFFICULTY => $difficulty));
		$offset = $offset_result->fetch()->offset;
		return $this->database->table(self::UNIT_TABLE_NAME)->where(array(self::UNIT_COLUMN_BASE_UNIT => self::FALSE_VALUE, self::UNIT_COLUMN_DIFFICULTY => $difficulty))->order(self::UNIT_COLUMN_ID)->limit(1, $offset)->fetch();
	}

	public function getTask($taskID) {
		return $this->database->table(self::TASK_TABLE_NAME)->where('id', $taskID)->fetch();
	}

	public function getTaskOwner($task) {
		return $task->{self::TASK_COLUMN_USER_ID};
	}

	public function getUnit($unitID) {
		return $this->database->table(self::UNIT_TABLE_NAME)->where('id', $unitID)->fetch();
	}

	public function isTaskOpen($task) {
		return is_null($task->{self::TASK_COLUMN_UPDATED});
	}

	public function insertResult($task, $data) {
		$data[self::TASK_COLUMN_UPDATED] = date('Y-m-d H:i:s', time());
		$task->update($data);
	}

	public function getDistinctLevels() {
		//return $this->database->table(self::UNIT_TABLE_NAME)->select('DISTINCT ' . self::UNIT_COLUMN_DIFFICULTY . ' as level')->fetchAll();
		return $this->database->query('SELECT DISTINCT nb_level as level FROM unit;');
	}

}
