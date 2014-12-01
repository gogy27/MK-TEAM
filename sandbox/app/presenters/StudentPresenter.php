<?php

namespace App\Presenters;

use Nette,
		App\Model,
		Nette\Application\UI\Form;

class StudentPresenter extends BasePresenter {

	private $unitConversion;
	
	private $tasks;

	protected function startup() {
		parent::startup();
		$this->unitConversion = $this->context->unitConversion;

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

	public function actionNewTask() {
		$this->template->form = $this['newTaskForm'];
		$this->template->tasks = $this->tasks;
		$this->template->unitConversion = $this->unitConversion;
	}

	protected function createComponentNewTaskForm() {
		$this->tasks = array();
		
		$form = new Form;
		$form->getElementPrototype()->class('form-horizontal task-list');
		$rand = rand(1, 3);
		for ($i = 0; $i < $rand; $i++) {
			$singleTask = $this->unitConversion->generateConversion($this->user->getId());
			$this->tasks[$singleTask->getId()] = $singleTask;
			
			$singleTaskInput = $form->addText("task".$singleTask->getId(), $singleTask." ".$singleTask->getUnitName());
			$singleTaskInput->addCondition(Form::FILLED)->addRule(Form::FLOAT, "Základný tvar ".($i+1).". príkladu má neplatný číselný zápis");
			
			$singleTaskInput->getLabelPrototype()->setHtml($singleTaskInput->getLabelPrototype()->getHtml()."<span class='equal-to'>=</span>");
			$singleTaskInput->getLabelPrototype()->class = 'control-label';
			$singleTaskInput->setAttribute('class', 'form-control input-sm base-number-format-input');
			$singleTaskInput->setAttribute('placeholder', 'Zákl. tvar');
			$form->addText("taskExp".$singleTask->getId())->setAttribute('class', 'form-control input-sm')
							->addCondition(Form::FILLED)->addRule(Form::FLOAT, "Prvý exponent ".($i+1).". príkladu má neplatný číselný zápis");
			$form->addText("taskBaseExp".$singleTask->getId())->setAttribute('class', 'form-control input-sm')
							->addCondition(Form::FILLED)->addRule(Form::FLOAT, "Druhý exponent ".($i+1).". príkladu má neplatný číselný zápis");
		}
		$form->addSubmit("send", "Vyhodnotiť")->setAttribute('class', 'btn btn-primary');
		
		$form->onSuccess[] = $this->taskFormSubmitted;
		
		return $form;
	}
	
	public function taskFormSubmitted($form, $values) {
		$this->tasks = array();
		$values = $form->getHttpData();
		foreach($values as $key => $value) {
			if (preg_match("/^task([0-9]+)$/",$key, $matches) > 0) {
				$data['value'] = floatval($value);
				$data['exp'] = (array_key_exists('taskExp'.$matches[1], $values)) ? intval($values['taskExp'.$matches[1]]) : 0;
				$data['expBase'] = (array_key_exists('taskBaseExp'.$matches[1], $values)) ? intval($values['taskBaseExp'.$matches[1]]) : 0;
				if($this->unitConversion->checkConversion($this->user->getId(), $matches[1], $data)) {
					$this->tasks[] = $this->unitConversion->getTask($matches[1]);
				}
			}
		}
		$this->setView('showResult');
	}
	
	public function renderShowResult() {
		$this->template->tasks = $this->tasks;
		$this->template->unitConversion = $this->unitConversion;
	}

}
