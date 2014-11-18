<?php

namespace App\Presenters;

use Nette,
    App\Model, Nette\Forms\Form;;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    const STUDENT = 'S', TEACHER = 'T';

    public function actionDefault() {
        $user = $this->getUser();
        if ($user->isLoggedIn()) {
            if ($user->isInRole(STUDENT)) {
                $this->redirect('Homepage:student');
            } else if ($user->isInRole(TEACHER)) {
                $this->redirect('Homepage:teacher');
            }
        }
    }

    public function renderDefault() {
        $this->template->anyVariable = 'any value';
    }

    protected function createComponentNewLoginForm() {
        $form = new Form();
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
    
    public function newLoginFormSubmitted(Form $form) {
        /*$user = $this->getUser();
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
        }*/
    }

}
