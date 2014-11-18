<?php

namespace App\Presenters;

use Nette,
		App\Model,
		Nette\Application\UI\Form;

/**
 * Homepage presenter.
 */
class AuthPresenter extends BasePresenter {

	private $userManager;

	protected function startup() {
		parent::startup();
		$this->userManager = $this->context->authorizator;
	}

	public function actionDefault() {
		$user = $this->getUser();
		if ($user->isLoggedIn()) {
			if ($user->isInRole(Model\UserManager::STUDENT)) {
				$this->redirect('Student:default');
			} else if ($user->isInRole(Model\UserManager::TEACHER)) {
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

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = 'div class=form-horizontal';
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['label']['container'] = 'div class="col-sm-4 control-label text-right"';
		$renderer->wrappers['control']['container'] = 'div class=col-sm-8';
		$renderer->wrappers['control']['.text'] = 'form-control';
		$renderer->wrappers['control']['.password'] = 'form-control';
		$renderer->wrappers['control']['.submit'] = 'btn btn-primary';
		
		return $form;
  }

	public function newLoginFormSubmitted($form, $values) {
		try {
			$this->getUser()->login($values->name, $values->password);
			$this->redirect('Auth:default');
		} catch (Nette\Security\AuthenticationException $e) {
			$this->flashMessage($e->getMessage());
		}
	}
}
