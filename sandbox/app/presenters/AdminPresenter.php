<?php

namespace App\Presenters;

use Nette,
		App\Model,
		Nette\Application\UI\Form;

class AdminPresenter extends BasePresenter {

	private $userRepository, $classRepository, $testRepository;

	protected function startup() {
		parent::startup();
		$this->userRepository = $this->context->userRepository;
		$this->classRepository = $this->context->classRepository;
		$this->testRepository = $this->context->testRepository;

		if ($this->user->isLoggedIn()) {
			if ($this->user->isInRole(Model\UserRepository::STUDENT)) {
				$this->redirect('Student:');
			} else if ($this->user->isInRole(Model\UserRepository::TEACHER)) {
				$this->redirect('Teacher:');
			}
		} else {
			$this->redirect('Auth:');
		}
	}

	public function actionDefault() {
		
	}

	public function actionShowTeachers() {
		$this->template->users = $this->userRepository->getTeachers();
	}
        
        public function actionShowGroups($teacher_id = NULL) {
            if (!is_null($teacher_id)) {
                $this->template->groups = $this->classRepository->getTeacherGroups($teacher_id);
                $this->template->teacher = $this->userRepository->getUser($teacher_id);
            } else {
                $this->template->groups = $this->classRepository->getAllGroupsWithAllInfo();
            }
        }
        
        public function actionShowStudents($group_id = NULL) {
            if (!is_null($group_id)) {
                $this->template->users = $this->userRepository->getStudentsByGroup($group_id);
                $this->template->group = $this->classRepository->getGroup($group_id);
            } else {
                $this->template->users = $this->userRepository->getStudents();
            }
        }

        public function handleDeleteUser($id) {
            $result['accepted'] = (bool) $this->userRepository->removeUser($id);
            
            $this->payload->accepted = $result['accepted'];
            
            $this->payload->message = 'Užívateľ bol vymazaný';

            if (!$this->isAjax()) {
                $this->redirect('this');
            } else {
                $this->terminate();
            }
        }
        
        public function handleDeleteGroup($id) {
            $this->payload->accepted = (bool) $this->classRepository->removeGroup($id);
            
            $this->payload->message = 'Skupina bola vymazaná';
            
            if (!$this->isAjax()) {
                $this->redirect('this');
            } else {
                $this->terminate();
            }
        }
        
        public function handleDeleteTasks($date) {
            //$this->payload->accepted = (bool) $this->tas
        }

}
