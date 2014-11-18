<?php

namespace App\Presenters;

use Nette,
		App\Model,
		Nette\Application\UI\Form;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

	const STUDENT = 'S', TEACHER = 'T';

	private $userManager;

	protected function startup() {
		parent::startup();
		$this->userManager = $this->context->authorizator;
	}

	public function actionDefault() {
		$user = $this->getUser();
		if ($user->isLoggedIn()) {
			if ($user->isInRole(self::STUDENT)) {
				$this->redirect('Student:default');
			} else if ($user->isInRole(self::TEACHER)) {
				$this->redirect('Teacher:default');
			}
		}
	}

	protected function createComponentNewLoginForm() {
		$form = new Form;
		$form->addText('name', 'Meno:')
						->addRule(Form::FILLED, 'Musíte zadať svoje prihlasovacie meno')
						->setAttribute('placeholder', 'Zadajte meno');
		$form->addPassword('password', 'Heslo:')
						->addRule(Form::FILLED, 'Musíte zadať svoje prihlasovacie heslo')
						->setAttribute('placeholder', 'Zadajte heslo');
		$form->addSubmit('login', 'Prihlásiť');
		$form->onSuccess[] = $this->newLoginFormSubmitted;

		return $form;
	}

	public function newLoginFormSubmitted($form, $values) {
		try {
			$this->getUser()->login($values->name, $values->password);
			$this->redirect('Homepage:default');
		} catch (Nette\Security\AuthenticationException $e) {
			$this->flashMessage($e->getMessage());
		}
	}

}
