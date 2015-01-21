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
                $this->setTitleSection('Študent');
		$this->tasks = array();
	}

	/**
	 * Handle default action for logged in student
	 */
	public function actionDefault() {
            $this->setTitle('Home');
            $this->setVisibleHeadline(false);
		$this->template->levels = $this->unitConversion->getDistinctLevels();
	}

	/**
	 * Manage creating and showing new tasks
	 * 
	 * @param boolean $test If tasks are created for test.
	 * @param int $diff Difficulty level of the task
	 */
	public function actionNewTask($test = NULL, $diff = 1) {
		if (!$this->getRequest()->isPost() && !$this->isSignalReceiver($this)) {
			$this->unitConversion->removeUnpostedTasks($this->user->getId());
			if ($test) {
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
                        
                        $this->setTitle(is_null($test) ? 'Riešenie príkladov' : 'Riešenie päťminútovky');
                        
			$this->difficulty = $diff;
			$this->template->form = $this['newTaskForm'];
			$this->template->tasks = $this->tasks;
			$this->template->unitConversion = $this->unitConversion;
			$this->template->test = ($test) ? true : false;
		}
	}
	
	private function getResults($from) {
		$results = array();
		$this->tasks = $this->unitConversion->getUserTasks($this->user->getId())->order(Model\UnitConversion::UNIT_COLUMN_ID . " DESC")->limit(20, $from);
		foreach ($this->tasks as $task) {
			$results[] = $this->unitConversion->getTableResult($task);
		}
		return $results;
	}

	/**
	 * Shows results of the last student tasks
	 */
	public function actionResults() {
            $this->setTitle('Moje výsledky');
		$this->template->tasks = $this->getResults(0);
	}

	/**
	 * Checks if student has unsolved and/or opened test
	 */
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
					$singleTask = $this->unitConversion->generateConversion($this->user->getId(), intval($this->difficulty), $this->test_id);
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

	/**
	 * Handle submitted solutions of the tasks
	 * 
	 * @param Nette\Application\UI\Form $form
	 * @param Nette\Utils\ArrayHash|array $values The values submitted by the $form
	 */
	public function taskFormSubmitted($form, $values) {
		$this->tasks = array();
		$values = $form->getHttpData();
		$checked = false;
		foreach ($values as $key => $value) {
			if (preg_match("/^task([0-9]+)$/", $key, $matches) > 0) {
				$data['value'] = floatval(str_replace(",", ".", $value));
				$data['exp'] = (array_key_exists('taskExp' . $matches[1], $values)) ? intval($values['taskExp' . $matches[1]]) : 0;
				$data['expBase'] = (array_key_exists('taskBaseExp' . $matches[1], $values)) ? intval($values['taskBaseExp' . $matches[1]]) : 0;
				if(!$checked){
				    if($this->testRepository->getTestOfTask($matches[1])->{Model\TestRepository::COLUMN_CLOSED} == Model\TestRepository::TRUE_VALUE){
					$this->flashMessage('Test bol už uzatvorený, nestihli ste odovzdať', self::FLASH_MESSAGE_DANGER);
					$this->redirect('Student:');
					break;
				    }
				    $checked = true;
				}
				$this->unitConversion->checkConversion($this->user->getId(), $matches[1], $data);
			}
		}
		$this->redirect('results');
	}

	/**
	 * Ajax handler for the task's hint
	 * 
	 * @param int $id ID of the task
	 */
	public function handleGetHint($id) {
		$this->payload->accepted = true;
		$this->payload->close = false;
		$task = $this->unitConversion->getTask(intval($id));

		if ($task && ($this->unitConversion->getTaskOwner($task) == $this->user->getId()) && ($task->{Model\UnitConversion::TASK_COLUMN_HINT} < 2)) {
			if ($task->{Model\UnitConversion::TASK_COLUMN_HINT} == 0) {
				$this->payload->part = "base-number";
				$this->payload->value = Model\Task::toRealValue($task->{Model\UnitConversion::TASK_COLUMN_VALUE_FROM});
			} elseif ($task->{Model\UnitConversion::TASK_COLUMN_HINT} == 1) {
				$this->payload->part = "exp";
				$this->payload->value = $task->{Model\UnitConversion::TASK_COLUMN_POWER_FROM};
				$this->payload->close = true;
			}
			
			/* 
			 * * * * HINT PRE EXPBASE * * * *
				$this->payload->part = "expBase";
				$this->payload->value = Model\Task::toBaseExp($this->unitConversion->getUnit($task->{Model\UnitConversion::TASK_COLUMN_UNIT_ID}), $task->{Model\UnitConversion::TASK_COLUMN_POWER_FROM});
			 * * * * * * * * * * * * * * * * *
			*/
			
			$task->update(array(Model\UnitConversion::TASK_COLUMN_HINT => $task->{Model\UnitConversion::TASK_COLUMN_HINT}+1));
		} else {
			$this->payload->accepted = false;
			$this->payload->close = true;
			$this->payload->value = 0;
		}

		if (!$this->isAjax()) {
			$this->redirect('this');
		} else {
			$this->terminate();
		}
	}
	
	/**
	 * Ajax handler for loading more students results (older tasks)
	 * 
	 * @param int $count Count of the actual shown tasks
	 */
	public function handleGetNextResults($count) {
		$this->template->tasks = $this->getResults(intval($count));
		$this->payload->accepted = sizeOf($this->template->tasks) > 0;

		$this->redrawControl('results');
		
		if (!$this->isAjax()) {
			$this->redirect('this');
		}
	}
}
