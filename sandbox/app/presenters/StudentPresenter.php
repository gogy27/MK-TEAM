<?php

namespace App\Presenters;

use Nette,
		App\Model,
		Nette\Application\UI\Form;

class StudentPresenter extends BasePresenter {

	private $unitConversion, $testRepository;
	private $tasks, $unFilledTasks = NULL;
	private $numberOfTasks = 10, $difficulty = 1, $test_id = NULL;

	protected function startup() {
		parent::startup();
		$this->unitConversion = $this->context->unitConversion;
		$this->testRepository = $this->context->testRepository;
		if ($this->user->isLoggedIn()) {
			if ($this->user->isInRole(Model\UserRepository::TEACHER)) {
				$this->redirect('Teacher:');
			}
		} else {
			$this->redirect('Auth:');
		}

		$this->tasks = array();
	}

	public function actionDefault() {
		
	}

	public function actionNewTask($test) {
		if (!$this->getRequest()->isPost()) {
			if ($test == 1) {
				$test_row = $this->testRepository->getTestForUser($this->user->getId());
				if ($this->testRepository->getFilledTaskInTest($test_row->id, $this->user->getId())) {
					$this->flashMessage('Už ste vyplnili test', self::FLASH_MESSAGE_DANGER);
					$this->redirect('Student:');
				} else if ($tasks = $this->testRepository->getUnfilledTaskInTest($test_row->id, $this->user->getId())) {
					$this->unFilledTasks = array();
					foreach ($tasks as $value) {
						$this->unFilledTasks[] = $this->unitConversion->reGenerateTask($value);
					}
				}
				$this->numberOfTasks = $test_row->nb_count;
				$this->difficulty = $test_row->nb_level;
				$this->test_id = $test_row->id;
			}
			$this->template->form = $this['newTaskForm'];
			$this->template->tasks = $this->tasks;
			$this->template->unitConversion = $this->unitConversion;
		}
	}
	
	private function getResults($from) {
		$results = array();
		$this->tasks = $this->unitConversion->getUserTasks($this->user->getId())->order(Model\UnitConversion::UNIT_COLUMN_ID . " DESC")->limit(20, $from);
		foreach ($this->tasks as $task) {
			$unit = $this->unitConversion->getUnit($task->{Model\UnitConversion::TASK_COLUMN_UNIT_ID});
			$baseUnit = $this->unitConversion->getBaseUnit($unit);
			$results[] = array(
					'date' => $task->{Model\UnitConversion::TASK_COLUMN_CREATED},
					'test' => $task->{Model\UnitConversion::TASK_COLUMN_TEST_ID},
					'prescription' => Model\Task::toHumanValue($task->{Model\UnitConversion::TASK_COLUMN_VALUE_FROM}, $task->{Model\UnitConversion::TASK_COLUMN_POWER_FROM}) . " " . $unit->{Model\UnitConversion::UNIT_COLUMN_NAME},
					'correctAnswer' => Model\Task::toRealValue($task->{Model\UnitConversion::TASK_COLUMN_VALUE_FROM}) . " &times; 10 <sup>" . $task->{Model\UnitConversion::TASK_COLUMN_POWER_FROM} . "</sup> " . $unit->{Model\UnitConversion::UNIT_COLUMN_NAME} . " <span class='equal-to'> = </span> " . Model\Task::toRealValue($task->{Model\UnitConversion::TASK_COLUMN_VALUE_FROM}) . " &times; 10 <sup>" . Model\Task::toBaseExp($unit, $task->{Model\UnitConversion::TASK_COLUMN_POWER_FROM}) . "</sup> " . $baseUnit->{Model\UnitConversion::UNIT_COLUMN_NAME},
					'userAnswer' => Model\Task::toRealValue($task->{Model\UnitConversion::TASK_COLUMN_VALUE_TO}) . " &times; 10 <sup>" . $task->{Model\UnitConversion::TASK_COLUMN_POWER_TO} . "</sup> " . $unit->{Model\UnitConversion::UNIT_COLUMN_NAME} . " <span class='equal-to'> = </span> " . Model\Task::toRealValue($task->{Model\UnitConversion::TASK_COLUMN_VALUE_TO}) . " &times; 10 <sup>" . $task->{Model\UnitConversion::TASK_COLUMN_POWER_BASE_TO} . "</sup> " . $baseUnit->{Model\UnitConversion::UNIT_COLUMN_NAME},
					'isCorrect' => ($task->{Model\UnitConversion::TASK_COLUMN_CORRECT} == Model\UnitConversion::TRUE_VALUE)
			);
		}
		return $results;
	}

	public function actionResults() {
		$this->template->tasks = $this->getResults(0);
	}

	public function actionTest() {
		if ($this->testRepository->getTestForUser($this->user->getId())->id == NULL) {
			$this->flashMessage('Momentálne pre Vás neexistuje test', self::FLASH_MESSAGE_WARNING);
			$this->redirect('Student:');
		} else {
			$this->redirect('Student:newTask', 1);
		}
	}

	protected function createComponentNewTaskForm() {
		$this->tasks = array();

		$form = new Form;
		$form->getElementPrototype()->class('form-horizontal task-list');
		if (!$this->getRequest()->isPost()) {
			for ($i = 0; $i < $this->numberOfTasks; $i++) {
				if ($this->unFilledTasks) {
					$singleTask = $this->unFilledTasks[$i];
				} else {
					$singleTask = $this->unitConversion->generateConversion($this->user->getId(), $this->difficulty, $this->test_id);
				}
				$this->tasks[$singleTask->getId()] = $singleTask;

				$singleTaskInput = $form->addText("task" . $singleTask->getId(), $singleTask . " " . $singleTask->getUnitName());
				$singleTaskInput->addCondition(Form::FILLED)->addRule(Form::FLOAT, "Základný tvar " . ($i + 1) . ". príkladu má neplatný číselný zápis");

				$singleTaskInput->getLabelPrototype()->setHtml($singleTaskInput->getLabelPrototype()->getHtml() . "<span class='equal-to'>=</span>");
				$singleTaskInput->getLabelPrototype()->class = 'control-label';
				$singleTaskInput->setAttribute('class', 'form-control input-sm base-number-format-input');
				$singleTaskInput->setAttribute('placeholder', 'Zákl. tvar');
				$form->addText("taskExp" . $singleTask->getId())->setAttribute('class', 'form-control input-sm exp-input')
								->addCondition(Form::FILLED)->addRule(Form::FLOAT, "Prvý exponent " . ($i + 1) . ". príkladu má neplatný číselný zápis");
				$form->addText("taskBaseExp" . $singleTask->getId())->setAttribute('class', 'form-control input-sm expBase-input')
								->addCondition(Form::FILLED)->addRule(Form::FLOAT, "Druhý exponent " . ($i + 1) . ". príkladu má neplatný číselný zápis");
			}
			$form->addSubmit("send", "Vyhodnotiť")->setAttribute('class', 'btn btn-primary');
		}
		$form->onSuccess[] = $this->taskFormSubmitted;

		return $form;
	}

	public function taskFormSubmitted($form, $values) {
		$this->tasks = array();
		$values = $form->getHttpData();
		foreach ($values as $key => $value) {
			if (preg_match("/^task([0-9]+)$/", $key, $matches) > 0) {
				$data['value'] = floatval($value);
				$data['exp'] = (array_key_exists('taskExp' . $matches[1], $values)) ? intval($values['taskExp' . $matches[1]]) : 0;
				$data['expBase'] = (array_key_exists('taskBaseExp' . $matches[1], $values)) ? intval($values['taskBaseExp' . $matches[1]]) : 0;
				$this->unitConversion->checkConversion($this->user->getId(), $matches[1], $data);
			}
		}
		$this->redirect('results');
	}

	public function handleGetHint($id) {
		$this->payload->accepted = true;

		$task = $this->unitConversion->getTask(intval($id));

		if ($task && ($this->unitConversion->getTaskOwner($task) == $this->user->getId())) {
			$rand = rand(0, 2);
			if ($rand == 0) {
				$this->payload->part = "base-number";
				$this->payload->value = Model\Task::toRealValue($task->{Model\UnitConversion::TASK_COLUMN_VALUE_FROM});
			} elseif ($rand == 1) {
				$this->payload->part = "exp";
				$this->payload->value = $task->{Model\UnitConversion::TASK_COLUMN_POWER_FROM};
			} else {
				$this->payload->part = "expBase";
				$this->payload->value = Model\Task::toBaseExp($this->unitConversion->getUnit($task->{Model\UnitConversion::TASK_COLUMN_UNIT_ID}), $task->{Model\UnitConversion::TASK_COLUMN_POWER_FROM});
			}
		} else {
			$this->payload->accepted = false;
			$this->payload->value = 0;
		}

		if (!$this->isAjax()) {
			$this->redirect('this');
		} else {
			$this->terminate();
		}
	}
	
	public function handleGetNextResults($count) {
		$this->template->tasks = $this->getResults(intval($count));
		$this->payload->accepted = sizeOf($this->template->tasks) > 0;

		$this->redrawControl('results');
		
		if (!$this->isAjax()) {
			$this->redirect('this');
		}
	}
}
