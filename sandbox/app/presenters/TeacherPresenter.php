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
	$this->template->groups = $this->classRepository->getTeacherGroupsWithTestInfo($this->user->getId());
	$this->template->classRepository = $this->classRepository;
    }

    public function actionShowStudentsInGroup($group_id) {
	$this->template->students = $this->userRepository->getStudentsByGroup($group_id);
	$this->template->userRepository = $this->userRepository;
	$this->template->statistics = $this->userRepository->getStatisticsOfTasks($group_id);
	$this->template->statistics2 = $this->userRepository->getStatisticsOfGroupUser($group_id);
	$this->template->statistics3 = $this->userRepository->getStatisticsOfUnits($group_id);
    }

    public function actionRemoveUser($student_id) {
	$this->userRepository->removeUser($student_id);
	$this->flashMessage('ĂšspeĹˇne ste zmazali Ĺˇtudenta', self::FLASH_MESSAGE_INFO);
	$this->redirect('Teacher:');
    }

    public function actionRemoveGroup($group_id) {
	$this->classRepository->removeGroup($group_id);
	$this->flashMessage('ĂšspeĹˇne ste vymazali skupinu', self::FLASH_MESSAGE_INFO);
	$this->redirect('Teacher:');
    }

    public function actionSetTest($group_id) {
	$this->group_id = $group_id;
	$this->template->open = $this->testRepository->getUnclosedTestForGroup($group_id);
	$this->template->testRepository = $this->testRepository;
    }

    public function actionCloseTest($test_id) {
	if ($this->testRepository->getOwnerOfTest($test_id)->id == $this->user->getId()) {
	    $this->testRepository->closeTest($test_id);
	    $this->flashMessage('Test ĂşspeĹˇne ukonÄŤenĂ˝', self::FLASH_MESSAGE_SUCCESS);
	} else {
	    $this->flashMessage('Test nemĂ´Ĺľete ukonÄŤit. NepatrĂ­ VĂˇm!', self::FLASH_MESSAGE_DANGER);
	}
	$this->redirect('Teacher:');
    }

    public function createComponentNewGroup() {
	$form = new Form;
	$form->addText('name', 'NĂˇzov skupiny:')
		->setRequired('Prosim zadajte nĂˇzov novej skupiny')
		->addRule(Form::MIN_LENGTH, 'Meno skupiny je prĂ­liĹˇ krĂˇtke', 5)
		->setAttribute('placeholder', 'NĂˇzov skupiny');
	$form->addTextArea('description', 'Popis skupiny:')
		->setAttribute('ProsĂ­m zadajte popis skupiny')
		->setAttribute('placeholder', 'Popis')
		->setAttribute('class', 'form-control');
	$form->addPassword('password', 'Heslo skupiny:')
		->addRule(Form::MIN_LENGTH, 'Heslo musĂ­ obsahovaĹĄ aspoĹ� %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH)
		->setAttribute('placeholder', 'Heslo');
	$form->addPassword('passwordVerify', 'Heslo znova:')
		->addRule(Form::MIN_LENGTH, 'Heslo musĂ­ obsahovaĹĄ aspoĹ� %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH)
		->addRule(Form::EQUAL, 'Hesla sa nezhodujĂş', $form['password'])
		->setAttribute('placeholder', 'Heslo znovu');
	$form->addSubmit('createGroup', 'VytvoriĹĄ skupinu');
	$form->onSuccess[] = $this->newGroupSubmitted;

	$this->setFormRenderer($form->getRenderer());
	return $form;
    }

    public function newGroupSubmitted($form, $values) {
	if ($this->classRepository->getGroupByName($values->name)) {
	    $this->flashMessage('NĂˇzov skupiny uĹľ existuje. ZvoÄľte inĂ˝.', self::FLASH_MESSAGE_DANGER);
	} else {
	    $this->classRepository->addGroup($this->user->getId(), $values->name, $values->password, $values->description);
	    $this->flashMessage("ĂšspeĹˇne ste vytvorili ste novĂş skupinu", self::FLASH_MESSAGE_SUCCESS);
	    $this->redirect('Auth:');
	}
    }

    public function createComponentNewTest() {
	$form = new Form;
	$form->addText('count', 'PoÄŤet prĂ­kladov:')
		->setRequired('ProsĂ­m zadajte poÄŤet prĂ­kladov')
		->addRule(Form::INTEGER, 'MusĂ­ byĹĄ ÄŤĂ­slo')
		->addRule(Form::RANGE, 'PoÄŤet musĂ­ byĹĄ v rozsahu ' . Model\TestRepository::MIN_COUNT . ' - ' . Model\TestRepository::MAX_COUNT, array(Model\TestRepository::MIN_COUNT, Model\TestRepository::MAX_COUNT));
	$levels = $this->unitConversion->getDistinctLevels();
	foreach ($levels as $item) {
	    $levely[$item->level] = $item->level;
	}
	$form->addSelect('level', 'NĂˇroÄŤnosĹĄ ', $levely)
		->setRequired('Zadajte nĂˇroÄŤnosĹĄ')
		->setPrompt('Vyberte nĂˇroÄŤnosĹĄ')
		->setAttribute('class', 'form-control');
	$form->addHidden('id_group', $this->group_id);
	$form->addSubmit('createTest', 'SpustiĹĄ test');
	$form->onSuccess[] = $this->newTestSubmitted;

	$this->setFormRenderer($form->getRenderer());
	return $form;
    }

    public function newTestSubmitted($form, $values) {
	if ($this->classRepository->getGroup($values->id_group)->{Model\ClassRepository::COLUMN_USER_ID} == $this->user->getId()) {
	    if ($this->testRepository->getUnclosedTestForGroup($values->id_group)) {
		$this->flashMessage('Pre danĂş skupinu uĹľ je spustenĂ˝ test', self::FLASH_MESSAGE_WARNING);
	    } else {
		$this->testRepository->addTest($values->id_group, $values->level, $values->count);
		$this->flashMessage('Test bol spustenĂ˝!', self::FLASH_MESSAGE_INFO);
	    }
	} else {
	    $this->flashMessage('Pre tĂşto skupinu nemĂ´Ĺľete vytvĂˇraĹĄ test!', self::FLASH_MESSAGE_DANGER);
	}
	$this->redirect('Teacher:');
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
