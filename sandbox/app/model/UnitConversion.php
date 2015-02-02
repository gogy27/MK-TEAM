<?php

namespace App\Model;

use Nette,
		Nette\Database;

class UnitConversion extends Nette\Object {

	/** @internal tables and columns names in database  */
	const UNIT_TABLE_NAME = 'unit',
					UNIT_COLUMN_ID = 'id',
					UNIT_COLUMN_DIFFICULTY = 'nb_level',
					UNIT_COLUMN_BASE_UNIT = 'fl_base_unit',
					UNIT_COLUMN_MULTIPLE = 'nb_multiple',
					UNIT_COLUMN_CATEGORY = 'nb_category',
					UNIT_COLUMN_NAME = 'str_unit_name',
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
					TASK_COLUMN_TEST_ID = 'id_test',
					TASK_COLUMN_HINT = 'nb_hints';

	/** boolean values represented in database */
	const TRUE_VALUE = 'A',
					FALSE_VALUE = 'N';

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Connection $connection) {
		$this->database = new Database\Context($connection);
	}

	/**
	 * Checks if solution of task is correct.
	 * 
	 * @param int $userID ID of the user, which is "owner" of the task 
	 * @param int $taskID ID of the task we check
	 * @param array $data Array, which containe user's solution: value, exp, expBase
	 * @return boolean Returns if task was marked as correct/uncorrect
	 */
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
			$userValue = Task::toHumanValue($data['value'], $data['exp']);
			$taskValue = Task::toHumanValue($task->{self::TASK_COLUMN_VALUE_FROM}, $task->{self::TASK_COLUMN_POWER_FROM});
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

	/**
	 * Creates a new task
	 * 
	 * @param int $userID ID of user who invoke generator
	 * @param int $difficulty Difficulty level of the task
	 * @param null|int $test_id ID of the test if the task is created for the specific test, otherwise NULL
	 * @return \App\Model\Task
	 */
	public function generateConversion($userID, $difficulty = 1, $test_id = NULL) {
		if ($difficulty <= 0) {
			$difficulty = 1;
		}
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

	/**
	 * Reloads task from database
	 *  
	 * @param Nette\Database\Table\ActiveRow $values Values of the task and its unit from database
	 * @return \App\Model\Task
	 */
	public function reGenerateTask($values) {
		$task = new Task($values);
		$task->setConstruct($values->idcko, $values);
		return $task;
	}

	/**
	 * Returns base unit from $unit category
	 * 
	 * @param Nette\Database\Table\ActiveRow $unit Values of the unit from database
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function getBaseUnit($unit) {
		return $this->database->table(self::UNIT_TABLE_NAME)
										->where([
												self::UNIT_COLUMN_CATEGORY => $unit->{self::UNIT_COLUMN_CATEGORY},
												self::UNIT_COLUMN_BASE_UNIT => self::TRUE_VALUE
										])->fetch();
	}

	private function getRandomUnit($difficulty) {
		$offset_result = $this->database->table(self::UNIT_TABLE_NAME)->select("FLOOR(RAND() * COUNT(*)) AS `offset`")->where(self::UNIT_COLUMN_BASE_UNIT, self::FALSE_VALUE)->where(self::UNIT_COLUMN_DIFFICULTY . " <= " . $difficulty);
		$offset = $offset_result->fetch()->offset;
		return $this->database->table(self::UNIT_TABLE_NAME)->where(self::UNIT_COLUMN_BASE_UNIT, self::FALSE_VALUE)->where(self::UNIT_COLUMN_DIFFICULTY . " = " . $difficulty)->order(self::UNIT_COLUMN_ID)->limit(1, $offset)->fetch();
	}

	/**
	 * Returns task from database
	 * 
	 * @param id $taskID ID of the task
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function getTask($taskID) {
		return $this->database->table(self::TASK_TABLE_NAME)->where('id', $taskID)->fetch();
	}

	/**
	 * Returns id of the task owner (user)
	 * 
	 * @param Nette\Database\Table\ActiveRow $task Row from database
	 * @return int ID of the user who "own" task
	 */
	public function getTaskOwner($task) {
		return $task->{self::TASK_COLUMN_USER_ID};
	}

	/**
	 * Returns row from unit database
	 * 
	 * @param int $unitID ID of the unit
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function getUnit($unitID) {
		return $this->database->table(self::UNIT_TABLE_NAME)->where('id', $unitID)->fetch();
	}

	/**
	 * Returns all evaluated tasks for specific user.
	 * 
	 * @param type $userID ID of the user
	 * @return Nette\Database\Table\Selection
	 */
	public function getUserTasks($userID) {
		return $this->database->table(self::TASK_TABLE_NAME)->where(self::TASK_COLUMN_USER_ID, $userID)->where(self::TASK_COLUMN_UPDATED . " IS NOT NULL");
	}

	private function isTaskOpen($task) {
		return is_null($task->{self::TASK_COLUMN_UPDATED});
	}

	private function insertResult($task, $data) {
		$data[self::TASK_COLUMN_UPDATED] = date('Y-m-d H:i:s', time());
		$task->update($data);
	}

	/**
	 * Returns full listing of result
	 * 
	 * @param Nette\Database\Table\ActiveRow $task Row from database
	 * @return array result in array structure.
	 */
	public function getTableResult($task) {
		$unit = $this->getUnit($task->{UnitConversion::TASK_COLUMN_UNIT_ID});
		$baseUnit = $this->getBaseUnit($unit);
		$result = array(
				'date' => $task->{UnitConversion::TASK_COLUMN_CREATED},
				'test' => $task->{UnitConversion::TASK_COLUMN_TEST_ID},
				'prescription' => Task::toHumanValue($task->{UnitConversion::TASK_COLUMN_VALUE_FROM}, $task->{UnitConversion::TASK_COLUMN_POWER_FROM}) . " " . $unit->{UnitConversion::UNIT_COLUMN_NAME},
				'correctAnswer' => Task::toRealValue($task->{UnitConversion::TASK_COLUMN_VALUE_FROM}) . " &times; 10 <sup>" . $task->{UnitConversion::TASK_COLUMN_POWER_FROM} . "</sup> " . $unit->{UnitConversion::UNIT_COLUMN_NAME} . " <span class='equal-to'> = </span> " . Task::toRealValue($task->{UnitConversion::TASK_COLUMN_VALUE_FROM}) . " &times; 10 <sup>" . Task::toBaseExp($unit, $task->{UnitConversion::TASK_COLUMN_POWER_FROM}) . "</sup> " . $baseUnit->{UnitConversion::UNIT_COLUMN_NAME},
				'userAnswer' => Task::toRealValue($task->{UnitConversion::TASK_COLUMN_VALUE_TO}) . " &times; 10 <sup>" . $task->{UnitConversion::TASK_COLUMN_POWER_TO} . "</sup> " . $unit->{UnitConversion::UNIT_COLUMN_NAME} . " <span class='equal-to'> = </span> " . Task::toRealValue($task->{UnitConversion::TASK_COLUMN_VALUE_TO}) . " &times; 10 <sup>" . $task->{UnitConversion::TASK_COLUMN_POWER_BASE_TO} . "</sup> " . $baseUnit->{UnitConversion::UNIT_COLUMN_NAME},
				'hint' => $task->{UnitConversion::TASK_COLUMN_HINT},
				'isCorrect' => ($task->{UnitConversion::TASK_COLUMN_CORRECT} == UnitConversion::TRUE_VALUE)
		);
		return $result;
	}

	/**
	 * Returns all possible levels (difficulty value) of the units
	 * @return Nette\Database\Table\Selection
	 */
	public function getDistinctLevels() {
		//return $this->database->table(self::UNIT_TABLE_NAME)->select('DISTINCT ' . self::UNIT_COLUMN_DIFFICULTY . ' as level')->fetchAll();
		return $this->database->query('SELECT DISTINCT nb_level as level FROM unit;');
	}

	/**
	 * Removes all the task, which are older than specific $date
	 * 
	 * @param string $date
	 * @return int number of affected rows
	 */
	public function removeTasks($date) {
		$formatedDate = date('Y-m-d H:i:s', strtotime($date));
		return $this->database->table(self::TASK_TABLE_NAME)->where(self::TASK_COLUMN_UPDATED . "< '" . $formatedDate . "'")->delete();
	}

	/**
	 * Removes tasks from datbase, which haven't been sended yet
	 * 
	 * @param type $userID
	 */
	public function removeUnpostedTasks($userID) {
		$this->database->table(self::TASK_TABLE_NAME)->where(self::TASK_COLUMN_USER_ID, $userID)->where(self::TASK_COLUMN_UPDATED . " IS NULL")->where(self::TASK_COLUMN_TEST_ID, null)->delete();
	}

}
