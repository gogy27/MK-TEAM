<?php

namespace App\Presenters;

use Nette,
    App\Model,
    Nette\Application\UI\Form;

/**
 * Homepage presenter.
 */
class AuthPresenter extends BasePresenter {

    private $userManager, $userRepository;

    protected function startup() {
        parent::startup();
        $this->userManager = $this->context->authorizator;
        $this->userRepository = $this->context->userRepository;
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

    public function actionRegister() {
        
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

        $this->setFormRenderer($form->getRenderer());

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

    public function createComponentNewRegisterUser() {
        $form = new Form;
        $form->addText('name', 'Meno:')
                ->setRequired('Prosim zadajte Vaše celé meno')
                ->addRule(Form::MIN_LENGTH, 'Vaše meno je príliš krátke', 5)
                ->setAttribute('placeholder', 'Meno Priezvisko');
        $form->addText('email', 'Email:')
                ->setDefaultValue('@')
                ->addRule(Form::EMAIL, 'Zle zadaný email');
        $type = array(Model\UserManager::STUDENT => 'Učiteľ', Model\UserManager::TEACHER => 'Žiak',);
        $form->addRadioList('type', 'Som:', $type);
        $form['type']->getItemLabelPrototype()->addAttributes(array('class' => 'radio'));
        $form['type']->getSeparatorPrototype()->setName(NULL);

        $form->addPassword('password', 'Heslo:')
                ->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', 6);
        $form->addPassword('password2', 'Heslo znova:')
                ->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', 6)
                ->addRule(Form::EQUAL, 'Hesla sa nezhodujú', $form['password']);
        $form->addSubmit('register', 'Zaregistrovať');
        $form->onSuccess[] = $this->newRegisterUserSubmitted;

        $this->setFormRenderer($form->getRenderer());

        return $form;
    }

    public function newRegisterUserSubmitted($form, $values) {
        $registerResult = $this->users->register($values);
        if ($registerResult[0]) {
            $this->flashMessage($registerResult[1], 'success');
            $this->redirect('Sign:');
        } else {
            $this->flashMessage($registerResult[1], 'error');
        }
    }

    private function setFormRenderer($renderer) {
        $renderer->wrappers['controls']['container'] = 'div class=form-horizontal';
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-4 control-label text-right"';
        $renderer->wrappers['control']['container'] = 'div class=col-sm-8';
        $renderer->wrappers['control']['.text'] = 'form-control';
        $renderer->wrappers['control']['.password'] = 'form-control';
        $renderer->wrappers['control']['.submit'] = 'btn btn-primary';
    }

    public function actionSendEmailToResetPassword() {
        ;
    }

    public function emailExistsValidator($item, $arg) {
        return $this->userRepository->emailExists($item->value);
    }

    protected function createComponentSendEmailToResetPasswordForm() {
        $form = new Form;
        $form->addText('email', 'Email:')
                ->addRule(Form::EMAIL, 'Musíte zadať validný email')
                ->addRule(callback($this, 'emailExistsValidator'), 'Email sa nenachádza v databáze')
                ->setAttribute('placeholder', 'Zadajte email');
        $form->addSubmit('sendEmail', 'Poslať')
                ->setAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = $this->sendEmailToResetPasswordFormSubmitted;

        return $form;
    }

    public function sendEmailToResetPasswordFormSubmitted($form, $values) {
        $uniq_id = uniqid('', TRUE);

        $this->userRepository->addResetPasswordHash($values->email, $uniq_id);

        $mail = new Message;
        $mail->setFrom('Jozko Mrkvicka <jozko-mrkvicka@gmail.com>')
                ->addTo($values->email)
                ->setSubject('Zmena hesla')
                ->setHTMLBody('Dobry den,<br/><br/>Poziadali ste o zmenu hesla. Zmenu hesla prevediete na nasledujúcom formulári: <a href="' + $uniq_id + '">' + $uniq_id + '</a><br/>Ak ste o zmenu neziadali, tento email ignorujte.');
        $mailer = new Nette\Mail\SmtpMailer(array(
            'host' => 'smtp.gmail.com',
            'username' => 'prevodyjednotiek@gmail.com',
            'password' => 'Heslo123',
            'secure' => 'ssl',
        ));
        $mailer->send($mail);
    }

}