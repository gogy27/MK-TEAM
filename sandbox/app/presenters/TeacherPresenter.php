<?php

namespace App\Presenters;

use Nette,
    App\Model,
    Nette\Application\UI\Form;

class TeacherPresenter extends BasePresenter {

	private $classRepository;
	protected function startup() {
		parent::startup();
		$this->classRepository = $this->context->classRepository;
		if ($this->user->isLoggedIn()) {
			if ($this->user->isInRole(Model\UserRepository::STUDENT)) {
				$this->redirect('Student:');
			}
		} else {
			$this->redirect('Auth:');
		}
	}
        
        public function actionDefault (){
            
        }
        
        public function actionShowStudentsInGroup($group_id){
            
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
		->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH);
	$form->addPassword('passwordVerify', 'Heslo znova:')
		->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovať aspoň %d znaky', Model\UserRepository::PASSWORD_MIN_LENGTH)
		->addRule(Form::EQUAL, 'Hesla sa nezhodujú', $form['password']);
	$form->addSubmit('createGroup', 'Vytvoriť skupinu');
	$form->onSuccess[] = $this->newGroupSubmitted;

	$this->setFormRenderer($form->getRenderer());
	return $form;
    }	
    
    public function newGroupSubmitted($form, $values){
	$this->classRepository->addGroup($this->user->getId(), $values->name, $values->password, $values->description);
	$this->flashMessage("Úspešne ste vytvorili ste novú skupinu", 'success');
	$this->redirect('Auth:');
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
}
