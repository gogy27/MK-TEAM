<?php

namespace App\Presenters;

use Nette,
		App\Model;

class StudentPresenter extends BasePresenter {
	
	private $unitConversion;

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
	}

	public function actionDefault() {
		$tasks = array();
		for($i = 0; $i < 100; $i++) {
			$tasks[] = $this->unitConversion->generateConversion($this->user->getId());
		}
		$this->template->tasks = $tasks;
	}
}
