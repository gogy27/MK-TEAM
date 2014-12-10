<?php

namespace App\Presenters;

use Nette,
		App\Model,
		Nette\Application\UI\Form;

/**
 * Homepage presenter.
 */
class AuthPresenter extends BasePresenter {

        const SMTP_HOST = 'smtp.gmail.com',
            SMTP_USERNAME = 'prevodyjednotiek@gmail.com',
            SMTP_PASSWORD = 'Heslo123',
            SMTP_SECURE = 'ssl';
        
	private $userManager, $userRepository, $classRepository, $args;

	protected function startup() {
		parent::startup();
		$this->userManager = $this->context->authorizator;
		$this->userRepository = $this->context->userRepository;
		$this->classRepository = $this->context->classRepository;
print_r($_SERVER);
die();
		if ($this->user->isLoggedIn()) {
			if ($this->getAction() != 'logout' && $this->getAction() != 'changepassword' && $this->getAction() != 'changePassword') {
				if ($this->user->isInRole(Model\UserRepository::STUDENT)) {
					$this->redirect('Student:default');
				} else if ($this->user->isInRole(Model\UserRepository::TEACHER)) {
					$this->redirect('Teacher:default');
				} else if ($this->user->isInRole(Model\UserRepository::ADMIN)) {
					$this->redirect('Admin:default');
				}
			}
		}
	}

	public function actionDefault() {
            //print_r($_SERVER);
	}

	public function actionRegister() {
		
	}

	public function actionLogout() {
		if ($this->user->isLoggedIn()) {
			$this->user->logout();
		}
		$this->redirect('Auth:');
	}

	public function actionSendEmailToResetPassword() {
		
	}

	public function actionChangePassword() {
		if (!$this->user->isLoggedIn()) {
			$this->redirect('Auth:');
		}
	}

	public function actionResetPassword($user_id, $hash) {
		$this->args = ['user_id' => $user_id, 'hash' => $hash];
	}

	protected function createComponentNewLoginForm() {
		$form = new Form;
		$form->addText('email', 'E-mail:')
						->addRule(Form::FILLED, 'Musíte zadať svoj prihlasovací e-mail')
						->setAttribute('placeholder', 'Zadajte email');
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
			$this->getUser()->login($values->email, $values->password);
			$this->redirect('Auth:default');
		} catch (Nette\Security\AuthenticationException $e) {
			$this->flashMessage($e->getMessage(), self::FLASH_MESSAGE_DANGER);
		}
	}

	public function checkGroupKeyValidator($key, $groupID) {
		if (!($group = $this->classRepository->getGroup($groupID))) {
			return false;
		}
		return $key->getValue() == $group->{Model\ClassRepository::COLUMN_PASSWORD};
	}

	public function createComponentNewRegisterUser() {
		$form = new Form;
		$form->addText('name', 'Meno:')
						->setRequired('Prosím zadajte Vaše celé meno')
						->addRule(Form::MIN_LENGTH, 'Vaše meno je príliš krátke', 5)
						->setAttribute('placeholder', 'Meno Priezvisko');
		$form->addText('email', 'Email:')
						->setRequired('Musíte zadať email')
						->setDefaultValue('@')
						->addRule(Form::EMAIL, 'Zle zadaný email');
		$type = array(Model\UserRepository::TEACHER => 'Učiteľ', Model\UserRepository::STUDENT => 'Žiak',);
		$form->addRadioList('type', 'Som:', $type)
						->setRequired('Musíte zadať, či ste študent alebo učiteľ');
		$form['type']->getItemLabelPrototype()->addAttributes(array('class' => 'radio'));
		$form['type']->getSeparatorPrototype()->setName(NULL);

		$classes = $this->classRepository->getAllGroups();
		$form->addSelect('group', 'Skupina:', $classes)
						->setPrompt('---')
						->setAttribute('class', 'form-control group-control')
						->addConditionOn($form['type'], Form::EQUAL, Model\UserRepository::STUDENT)
						->setRequired("Vyberte si svoju skupinu");
		$form->addText('groupKey', 'Kľúč skupiny:')
						->setAttribute('class', 'group-control')
						->addConditionOn($form['type'], Form::EQUAL, Model\UserRepository::STUDENT)
						->setRequired('Prosím zadajte kľúč skupiny')
						->addRule(callback($this, 'checkGroupKeyValidator'), 'Neplatný kľúč do skupiny', $form['group']);

		$form->addPassword('password', 'Heslo:')
						->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH);
		$form->addPassword('passwordVerify', 'Heslo znova:')
						->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH)
						->addRule(Form::EQUAL, 'Hesla sa nezhodujú', $form['password']);
		$form->addSubmit('register', 'Zaregistrovať');
		$form->onSuccess[] = $this->newRegisterUserSubmitted;

		$this->setFormRenderer($form->getRenderer());

		return $form;
	}

	public function newRegisterUserSubmitted($form, $values) {
		if (!$this->userRepository->checkEmailAvailability($values->email)) {
			$this->flashMessage("Ospravedlňujeme sa, ale Vami zadaná e-mailová adresa sa už v našej databáze nachádza. Prosím zvoľte inú.", self::FLASH_MESSAGE_DANGER);
		} else {
			$this->userRepository->register($values);
			$this->flashMessage("Ďakujeme Vám za Vašu registráciu. Teraz sa môžete prihlásiť!", self::FLASH_MESSAGE_SUCCESS);
			$this->redirect('Auth:');
		}
	}

	public function createComponentChangePassword() {
		$form = new Form;
		$form->addPassword('old', 'Staré heslo:')
						->setRequired('Musíte zadať heslo');
		$form->addPassword('new', 'Nové heslo:')
						->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znakov', Model\UserRepository::PASSWORD_MIN_LENGTH);
		$form->addPassword('newVerify', 'Nové heslo znova:')
						->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znakov', Model\UserRepository::PASSWORD_MIN_LENGTH)
						->addRule(Form::EQUAL, 'Heslá sa nezhodujú', $form['new']);
		$form->addSubmit('change', 'Zmeniť heslo');
		$form->onSuccess[] = $this->newChangePasswordSubmitted;

		$this->setFormRenderer($form->getRenderer());

		return $form;
	}

	public function newChangePasswordSubmitted($form, $values) {
		$password = $this->userRepository->getUser($this->user->getId());
		if (!Nette\Security\Passwords::verify($values->old, $password[Model\UserRepository::COLUMN_PASSWORD])) {
			$this->flashMessage('Zle zadané heslo', self::FLASH_MESSAGE_DANGER);
		} else {
			$this->userRepository->changePassword($this->user->getId(), Nette\Security\Passwords::hash($values->new));
			$this->flashMessage('Heslo úspešne zmenené', self::FLASH_MESSAGE_SUCCESS);
			$this->redirect('Auth:');
		}
	}

	public function emailExistsValidator($item, $arg) {
		return !($this->userRepository->checkEmailAvailability($item->value));
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

		$this->setFormRenderer($form->getRenderer());

		return $form;
	}

	public function sendEmailToResetPasswordFormSubmitted($form, $values) {
		$user_id = $this->userRepository->getInfoByEmail($values->email)->fetch()[Model\UserRepository::COLUMN_ID];
		if (is_null($user_id)) {
			$this->flashMessage('Zadaný email je neplatný', self::FLASH_MESSAGE_DANGER);
			//throw new Exception('Email ' . $values->email . ' does not exists in the database');
		}
		$uniq_id = uniqid('', TRUE);
		$this->userRepository->addResetPasswordHash($values->email, $uniq_id);
                
		$urlToResetPassword = $this->link('//Auth:resetPassword', ['user_id' => $user_id, 'hash' => $uniq_id]);
                
		$mail = new Nette\Mail\Message;
		$mail->setFrom('Jozko Mrkvicka <jozko-mrkvicka@gmail.com>')
						->addTo($values->email)
						->setSubject('Zmena hesla')
						->setHTMLBody('Dobry den,<br/><br/>Poziadali ste o zmenu hesla. Zmenu hesla prevediete na nasledujucom formulari: <a href="' . $urlToResetPassword . '">' . $urlToResetPassword . '</a><br/>Ak ste o zmenu neziadali, tento email ignorujte.');
		$mailer = new Nette\Mail\SmtpMailer(array(
				'host' => self::SMTP_HOST,
				'username' => self::SMTP_USERNAME,
				'password' => self::SMTP_PASSWORD,
				'secure' => self::SMTP_SECURE,
		));
		$mailer->send($mail);
	}

	protected function createComponentResetPasswordForm() {
		$form = new Form;
		$form->addPassword('password', 'Heslo:')
						->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH);
		$form->addPassword('passwordVerify', 'Heslo znova:')
						->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH)
						->addRule(Form::EQUAL, 'Hesla sa nezhodujú', $form['password']);
		$form->addHidden('user_id', $this->args['user_id']);
		$form->addHidden('hash', $this->args['hash']);
		$form->addSubmit('resetPassword', 'Zmeniť heslo');
		$form->onSuccess[] = $this->resetPasswordFormSubmitted;

		$this->setFormRenderer($form->getRenderer());

		return $form;
	}

	public function resetPasswordFormSubmitted($form, $values) {
		$userUpdated = $this->userRepository->resetPassword($values->user_id, $values->hash, $values->password);
		$this->flashMessage('Heslo bolo zmenené, môžete sa prihlásiť', self::FLASH_MESSAGE_SUCCESS);
		$this->redirect('Auth:');
	}

	private function setFormRenderer($renderer) {
		$renderer->wrappers['controls']['container'] = 'div class=form-horizontal';
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['label']['container'] = 'div class="col-sm-4 control-label"';
		$renderer->wrappers['control']['container'] = 'div class=col-sm-8';
		$renderer->wrappers['control']['.text'] = 'form-control';
		$renderer->wrappers['control']['.password'] = 'form-control';
		$renderer->wrappers['control']['.submit'] = 'btn btn-primary';
		$renderer->wrappers['control']['errorcontainer'] = 'span class="error"';
	}

}
