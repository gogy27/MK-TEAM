<?php

class TeacherPresenter {

	protected function startup() {
		parent::startup();
		if ($this->user->isLoggedIn()) {
			if ($this->user->isInRole(Model\UserRepository::Student)) {
				$this->redirect('Teacher:');
			}
		} else {
			$this->redirect('Auth:');
		}
	}

}
