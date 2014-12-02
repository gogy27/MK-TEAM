<?php

namespace App\Presenters;

use Nette,
    App\Model,
    Nette\Application\UI\Form;

class TeacherPresenter extends BasePresenter {

    private $classRepository, $userRepository, $unitConversion, $testRepository;
    private $group_id;

    protected function startup() {
	parent::startup();
	$this->classRepository = $this->context->classRepository;
	$this->userRepository = $this->context->userRepository;
	$this->unitConversion = $this->context->unitConversion;
	$this->testRepository = $this->context->testRepository;

	if ($this->user->isLoggedIn()) {
	    if ($this->user->isInRole(Model\UserRepository::STUDENT)) {
		$this->redirect('Student:');
	    }
	} else {
	    $this->redirect('Auth:');
	}
    }

    public function actionDefault() {
	$this->template->groups = $this->classRepository->getTeacherGroups($this->user->getId());
	$this->template->classRepository = $this->classRepository;
    }

    public function actionShowStudentsInGroup($group_id) {
	$this->template->students = $this->userRepository->getStudentsByGroup($group_id);
	$this->template->userRepository = $this->userRepository;
	$this->template->statistics = $this->userRepository->getStatisticsOfTasks($group_id);
	$this->template->statistics2 = $this->userRepository->getStatisticsOfGroupUser($group_id);
    }

    public function actionRemoveUser($student_id) {
	$this->userRepository->removeUser($student_id);
	$this->flashMessage('Úspešne ste zmazali študenta', self::FLASH_MESSAGE_INFO);
	$this->redirect('Teacher:');
    }

    public function actionRemoveGroup($group_id) {
	$this->classRepository->removeGroup($group_id);
	$this->flashMessage('Úspešne ste vymazali skupinu', self::FLASH_MESSAGE_INFO);
	$this->redirect('Teacher:');
    }

    public function actionSetTest($group_id) {
	$this->group_id = $group_id;
	$this->template->open = $this->testRepository->getUnclosedTestForGroup($group_id);
	$this->template->testRepository = $this->testRepository;
    }
    
    public function actionCloseTest($test_id){
	$this->testRepository->closeTest($test_id);
	$this->flashMessage('Test úspešne ukončený', self::FLASH_MESSAGE_SUCCESS);
	$this->redirect('Teacher:');
    }

    public function createComponentNewGroup() {
	$form = new Form;
	$form->addText('name', 'Názov skupiny:')
		->setRequired('Prosim zadajte názov novej skupiny')
		->addRule(Form::MIN_LENGTH, 'Meno skupiny je príliš krátke', 5)
		->setAttribute('placeholder', 'Názov skupiny');
	$form->addTextArea('description', 'Popis skupiny:')
		->setAttribute('Prosím zadajte popis skupiny')
		->setAttribute('placeholder', 'Popis')
		->setAttribute('class', 'form-control');
	$form->addPassword('password', 'Heslo skupiny:')
		->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH)
		->setAttribute('placeholder', 'Heslo');
	$form->addPassword('passwordVerify', 'Heslo znova:')
		->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH)
		->addRule(Form::EQUAL, 'Hesla sa nezhodujú', $form['password'])
		->setAttribute('placeholder', 'Heslo znovu');
	$form->addSubmit('createGroup', 'Vytvoriť skupinu');
	$form->onSuccess[] = $this->newGroupSubmitted;

	$this->setFormRenderer($form->getRenderer());
	return $form;
    }

    public function newGroupSubmitted($form, $values) {
	if ($this->classRepository->getGroup($values->name)) {
	    $this->flashMessage('Názov skupiny už existuje. Zvoľte iný.', self::FLASH_MESSAGE_DANGER);
	} else {
	    $this->classRepository->addGroup($this->user->getId(), $values->name, $values->password, $values->description);
	    $this->flashMessage("Úspešne ste vytvorili ste novú skupinu", self::FLASH_MESSAGE_SUCCESS);
	    $this->redirect('Auth:');
	}
    }

    public function createComponentNewTest() {
	$form = new Form;
	$form->addText('count', 'Počet príkladov:')
		->setRequired('Prosím zadajte počet príkladov')
		->addRule(Form::INTEGER, 'Musí byť číslo')
		->addRule(Form::RANGE, 'Počet musí byť v rozsahu ' . Model\TestRepository::MIN_COUNT . ' - ' . Model\TestRepository::MAX_COUNT, array(Model\TestRepository::MIN_COUNT, Model\TestRepository::MAX_COUNT));
	$levels = $this->unitConversion->getDistinctLevels();
	foreach ($levels as $item) {
	    $levely[$item->level] = $item->level;
	}
	$form->addSelect('level', 'Náročnosť ', $levely)
		->setRequired('Zadajte náročnosť')
		->setPrompt('Vyberte náročnosť');
	$form->addHidden('id_group', $this->group_id);
	$form->addSubmit('createTest', 'Spustiť test');
	$form->onSuccess[] = $this->newTestSubmitted;

	$this->setFormRenderer($form->getRenderer());
	return $form;
    }

    public function newTestSubmitted($form, $values) {
	if ($this->testRepository->getUnclosedTestForGroup($values->id_group)) {
	    $this->flashMessage('Pre danú skupinu už je spustený test', self::FLASH_MESSAGE_WARNING);
	    $this->redirect('Teacher:');
	} else {
	    $this->testRepository->addTest($values->id_group, $values->level, $values->count);
	    $this->flashMessage('Test bol spustený!', self::FLASH_MESSAGE_INFO);
	    $this->redirect('Teacher:');
	}
    }

    private function setFormRenderer($renderer) {
	$renderer->wrappers['controls']['container'] = 'div class=form-horizontal';
	$renderer->wrappers['pair']['container'] = 'div class=form-group';
	$renderer->wrappers['label']['container'] = 'div class="col-sm-4 control-label"';
	$renderer->wrappers['control']['container'] = 'div class=col-sm-8';
	$renderer->wrappers['control']['.text'] = 'form-control';
	$renderer->wrappers['control']['.password'] = 'form-control';
	$renderer->wrappers['control']['.submit'] = 'btn btn-primary';
    }

}