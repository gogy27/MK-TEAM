<?php

namespace App\Presenters;

use Nette,
    App\Model,
    Nette\Application\UI\Form;

class StudentPresenter extends BasePresenter {

    private $unitConversion;

    protected function startup() {
        parent::startup();
        $this->unitConversion = $this->context->unitConversion;

        if ($this->user->isLoggedIn()) {
            if ($this->user->isInRole(Model\UserRepository::TEACHER)) {
                $this->redirect('Teacher:');
            }
        } else {
            $this->redirect('Auth:');
        }
    }

    public function actionDefault() {
        
    }

    public function actionNewTask() {
        $this->template->tasks = [];
        $this->template->form = $this['newTaskForm'];
    }

    protected function createComponentNewTaskForm() {
        $form = new Form;
        
        $form->getElementPrototype()->class('form-horizontal task-list');
        $sub = $form->addContainer('task');
        for ($i = 0; $i < 10; $i++) {
            $singleTask = $this->unitConversion->generateConversion($this->user->getId());
            $this->template->tasks[] = $singleTask;
            $sub->addText($singleTask->getId(), $singleTask->toReal() . '&times;10<sub>'. $singleTask->getExp() .'</sub>');
        }
        return $form;
    }

}
