<?php

namespace App\Presenters;

use Nette,
		App\Model,
		Nette\Application\UI\Form;

/**
 * Maintain and perform admin actions - available in /admin/* folder. Admin
 * has to be logged in to do some actions, otherwise will be redirected to
 * login page
 */
class AdminPresenter extends BasePresenter {

	private $userRepository, $classRepository, $unitConversion;

	protected function startup() {
		parent::startup();
		$this->userRepository = $this->context->userRepository;
		$this->classRepository = $this->context->classRepository;
		$this->unitConversion = $this->context->unitConversion;

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

        /**
         * Handle default action for logged in user
         */
	public function actionDefault() {
		
	}

        /**
         * Show signed up teachers available for removal from DB
         */
	public function actionShowTeachers() {
		$this->template->users = $this->userRepository->getTeachers();
	}
        
        /**
         * Show created groups available for removal. If the parameter $teacher_id is not set, all
         * groups will be shown, otherwise there will be shown just groups
         * which are created by teacher with ID $teacher_id
         * 
         * @param int $teacher_id teacher's unique ID in DB
         */
        public function actionShowGroups($teacher_id = NULL) {
            if (!is_null($teacher_id)) {
                $this->template->groups = $this->classRepository->getTeacherGroups($teacher_id);
                $this->template->teacher = $this->userRepository->getUser($teacher_id);
            } else {
                $this->template->groups = $this->classRepository->getAllGroupsWithAllInfo();
            }
        }
        
        /**
         * Show signed up students available for removal. If is not set $group_id then all students
         * will be shown, otherwise students who have ID $group_id
         * 
         * @param int $group_id gourp's unique ID in the DB
         */
        public function actionShowStudents($group_id = NULL) {
            if (!is_null($group_id)) {
                $this->template->users = $this->userRepository->getStudentsByGroup($group_id);
                $this->template->group = $this->classRepository->getGroup($group_id);
            } else {
                $this->template->users = $this->userRepository->getStudents();
            }
        }

        /**
         * Handles deletion of user (teacher/student) with unique id in DB table
         * with users $id.
         * 
         * @param int $id user's unique ID in DB
         */
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
        
        /**
         * Handles deletion of group which has unique ID in DB table with groups
         * 
         * @param int $id group's unique ID in DB
         */
        public function handleDeleteGroup($id) {
            $this->payload->accepted = (bool) $this->classRepository->removeGroup($id);
            
            $this->payload->message = 'Skupina bola vymazaná';
            
            if (!$this->isAjax()) {
                $this->redirect('this');
            } else {
                $this->terminate();
            }
        }
        
        /**
         * Handles deletion of tasks after given date $date
         * 
         * @param date $date date with format suitable for strtotime() function
         */
        public function handleDeleteTasks($date) {
            $this->payload->accepted = (bool) $this->unitConversion->removeTasks($date);
            
            $this->payload->message = "Príklady po dátume $date boli vymazané";
            
            if (!$this->isAjax()) {
                $this->redirect('this');
            } else {
                $this->terminate();
            }
        }

}
