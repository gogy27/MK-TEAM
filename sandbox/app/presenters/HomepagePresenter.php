<?php

namespace App\Presenters;

use Nette,
    App\Model,
    Nette\Forms\Form;
;

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
	    die("to nejde");
	    if ($user->isInRole(STUDENT)) {
		$this->redirect('Homepage:student');
	    } else if ($user->isInRole(TEACHER)) {
		$this->redirect('Homepage:teacher');
	    }
	}
    }

    protected function createComponentNewLoginForm() {
	$form = new Form();
        $form->getElementPrototype()->class('form-horizontal');
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
	$form->onSuccess[] = array($this, 'newLoginFormSubmitted');

	return $form;
    }

    public function newLoginFormSubmitted(Form $form) {
	$user = $this->getUser();
	$values = $form->getValues();
	$username = $values['name'];
	$password = $values['password'];
	$successful = true;
	try {
	    $user->login($username, $password);
	} catch (Nette\Security\AuthenticationException $e) {
	    $successful = false;
	    $this->flashMessage($e->getMessage());
	}
	if ($successful) {
	    $this->redirect('Homepage:default');
	}
    }

}
