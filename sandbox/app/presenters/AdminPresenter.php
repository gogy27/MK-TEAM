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
		$this->template->teachers = $this->userRepository->getTeachers();
	}

	public function handleDeleteUser($id) {
		$result['accepted'] = true;


		$this->payload->accepted = $result['accepted'];
		
		$this->payload->message = "Success";
		
		if (!$this->isAjax()) {
      $this->redirect('this');
    }
		else {
			$this->terminate();
		}
	}

	protected function createComponentTeachersForm() {
		$form = new Form;
		$container = $form->addContainer('teacher');
		foreach ($this->template->teachers as $teacher) {
			$col_id = $teacher->{Model\UserRepository::COLUMN_ID};
			$container->addHidden($col_id, $col_id);
		}

		$form->onSuccess[] = $this->teachersFormSubmitted;

		return $form;
	}

	public function teachersFormSubmitted() {
		
	}

}
