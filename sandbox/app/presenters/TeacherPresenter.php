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
    /**
     * Shows groups of teacher and form for creating new groups
     */
    public function actionDefault() {
	$this->template->groups = $this->classRepository->getTeacherGroupsWithTestInfo($this->user->getId());
	$this->template->classRepository = $this->classRepository;
    }

    /**
     * Shows students in group
     * @param type $group_id Group unique id in DB
     */
    public function actionShowStudentsInGroup($group_id) {
	$this->template->students = $this->userRepository->getStudentsByGroup($group_id);
	$this->template->userRepository = $this->userRepository;
	$this->template->statistics = $this->userRepository->getStatisticsOfTasks($group_id);
	$this->template->statistics2 = $this->userRepository->getStatisticsOfGroupUser($group_id);
	$this->template->statistics3 = $this->userRepository->getStatisticsOfUnits($group_id);
    }

    /**
     * Deletes user and his tasks.
     * @param type $student_id User unique id in DB
     */
    public function actionRemoveUser($student_id) {
	if ($this->userRepository->isOwnedByTeacher($student_id, $this->user->getId())) {
	    $this->userRepository->removeUser($student_id);
	    $this->flashMessage('Úspešne ste zmazali študenta', self::FLASH_MESSAGE_INFO);
	    $this->redirect('Teacher:');
	} else {
	    $this->flashMessage('Zmazanie študenta sa nepodarilo', self::FLASH_MESSAGE_DANGER);
	    $this->redirect('Teacher:');
	}
    }

    /**
     * Deletes group, it's students, tests and tasks
     * @param type $group_id Unique group id in DB
     */
    public function actionRemoveGroup($group_id) {
	if ($this->classRepository->removeGroupByTeacher($group_id, $this->user->getId())) {
	    $this->flashMessage('Úspešne ste vymazali skupinu', self::FLASH_MESSAGE_INFO);
	    $this->redirect('Teacher:');
	} else {
	    $this->flashMessage('Zmazanie skupiny sa nepodarilo', self::FLASH_MESSAGE_DANGER);
	    $this->redirect('Teacher:');
	}
    }

    /**
     * Provides basic actions with test. List of tests and creating a new test
     * @param type $group_id Unique group id in DB for which is test generated
     */
    public function actionSetTest($group_id) {
	if ($this->classRepository->getGroup($group_id)->{Model\ClassRepository::COLUMN_USER_ID} == $this->user->getId()) {
	    $this->group_id = $group_id;
	    $this->template->open = $this->testRepository->getUnclosedTestForGroup($group_id);
	    $this->template->testRepository = $this->testRepository;
	    $this->template->tests = $this->testRepository->getTestsOfGroup($group_id);
	} else {
	    $this->flashMessage('K tejto groupe nemáte prístup!', self::FLASH_MESSAGE_WARNING);
	    redirect('Teacher:');
	}
    }

    /**
     * Closing test
     * @param type $test_id Unique test id in DB, which test is being closed
     */
    public function actionCloseTest($test_id) {
	if ($this->testRepository->getOwnerOfTest($test_id)->id == $this->user->getId()) {
	    $this->testRepository->closeTest($test_id);
	    $this->flashMessage('Test úspešne ukončený', self::FLASH_MESSAGE_SUCCESS);
	    $this->redirect('Teacher:Test', $test_id);
	} else {
	    $this->flashMessage('Test nemôžete ukončiť. Nepatrí Vám!', self::FLASH_MESSAGE_DANGER);
	    $this->redirect('Teacher:');
	}
    }

    /**
     * List of students and their answers on test
     * @param type $test_id Unique test id in DB
     */
    public function actionTest($test_id) {
	$id_test = intval($test_id);
	if ($this->testRepository->getTest($id_test)) {
	    if ($this->testRepository->getOwnerOfTest($id_test)->id == $this->user->getId()) {
		$this->template->students = $this->testRepository->getStudentsResults($id_test);
	    } else {
		$this->flashMessage('K tomuto testu nemáte prístup', self::FLASH_MESSAGE_DANGER);
		$this->redirect('Teacher:');
	    }
	} else {
	    $this->flashMessage('Takýto test neexistuje', self::FLASH_MESSAGE_DANGER);
	    $this->redirect('Teacher:');
	}
    }

    protected function createComponentNewGroup() {
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

    /**
     * Calls after submitting a form for creating new groups
     * @param type $form Form which called this method
     * @param type $values Values the form was called with
     */
    public function newGroupSubmitted($form, $values) {
	if ($this->classRepository->getGroupByName($values->name)) {
	    $this->flashMessage('Názov skupiny už existuje. Zvoľte iný˝.', self::FLASH_MESSAGE_DANGER);
	} else {
	    $this->classRepository->addGroup($this->user->getId(), $values->name, $values->password, $values->description);
	    $this->flashMessage("Úspešne ste vytvorili ste novú skupinu", self::FLASH_MESSAGE_SUCCESS);
	    $this->redirect('Auth:');
	}
    }

    protected function createComponentNewTest() {
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
		->setPrompt('Vyberte náročnosť')
		->setAttribute('class', 'form-control');
	$form->addHidden('id_group', $this->group_id);
	$form->addSubmit('createTest', 'Spustiť test');
	$form->onSuccess[] = $this->newTestSubmitted;

	$this->setFormRenderer($form->getRenderer());
	return $form;
    }

    /**
     * Calls after submitting a form for creating new tests
     * @param type $form Form which called this method
     * @param type $values Values the form was called with
     */
    public function newTestSubmitted($form, $values) {
	if ($this->classRepository->getGroup($values->id_group)->{Model\ClassRepository::COLUMN_USER_ID} == $this->user->getId()) {
	    if ($this->testRepository->getUnclosedTestForGroup($values->id_group)) {
		$this->flashMessage('Pre danú skupinu už je spustený test', self::FLASH_MESSAGE_WARNING);
	    } else {
		$this->testRepository->addTest($values->id_group, $values->level, $values->count);
		$this->flashMessage('Test bol spustený!', self::FLASH_MESSAGE_INFO);
	    }
	} else {
	    $this->flashMessage('Pre túto skupinu nemôžete vytvoriť test!', self::FLASH_MESSAGE_DANGER);
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
