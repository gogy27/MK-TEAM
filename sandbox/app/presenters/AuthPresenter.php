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
	$form->getElementPrototype()->class('form-horizontal login-form');
	$form->addText('name', 'Meno:')
		->addRule(Form::FILLED, 'Musíte zadať svoje prihlasovacie meno')
		->setAttribute('placeholder', 'Zadajte meno')
		->setAttribute('class', 'col-sm-4 control-label')
		->setAttribute('class', 'form-control');
	$form['name']->getLabelPrototype()->class('col-sm-4 control-label');
	$form->addPassword('password', 'Heslo:')
		->addRule(Form::FILLED, 'Musíte zadať svoje prihlasovacie heslo')
		->setAttribute('placeholder', 'Zadajte heslo')
		->setAttribute('class', 'form-control');
	$form['password']->getLabelPrototype()->class('col-sm-4 control-label');
	$form->addSubmit('login', 'Prihlásiť')
		->setAttribute('class', 'btn btn-primary');
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
