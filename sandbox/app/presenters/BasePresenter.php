<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    const FLASH_MESSAGE_INFO = 'info',
            FLASH_MESSAGE_SUCCESS = 'success',
            FLASH_MESSAGE_DANGER = 'danger',
            FLASH_MESSAGE_WARNING = 'warning';
	protected $user;
        private $titleSection;
        private $visibleHeadline;
        protected function startup() {
		parent::startup();
		$this->user = $this->getUser();
                $this->titleSection = null;
                $this->visibleHeadline = true;
	}
        
        protected function beforeRender() {
            parent::beforeRender();
            $this->template->titleSection = $this->titleSection;
            $this->template->visibleHeadline = $this->visibleHeadline;
        }

        protected function setTitleSection($section) {
            $this->titleSection = $section;
        }
        protected function setTitle($title) {
            $this->template->title = $title;
        }
        
        protected function setVisibleHeadline($visible) {
            $this->visibleHeadline = $visible;
        }
}
