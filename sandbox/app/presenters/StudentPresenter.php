<?php

namespace App\Presenters;

use Nette,
		App\Model;

class StudentPresenter extends BasePresenter {

	protected function startup() {
		parent::startup();
		if ($this->user->isLoggedIn()) {
			if ($this->user->isInRole(Model\UserRepository::TEACHER)) {
				$this->redirect('Teacher:');
			}
		}
		else {
			$this->redirect('Auth:');
		}
	}
	
	public function actionDefault(){
	}

}
