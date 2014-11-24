<?php

namespace App\Presenters;

use Nette,
		App\Model;

class TeacherPresenter extends BasePresenter {

	protected function startup() {
		parent::startup();
		if ($this->user->isLoggedIn()) {
			if ($this->user->isInRole(Model\UserRepository::STUDENT)) {
				$this->redirect('Student:');
			}
		} else {
			$this->redirect('Auth:');
		}
	}
        
        public function actionDefault (){
            
        }
        
        public function actionShowStudentsInGroup($group_id){
            
        }

}
