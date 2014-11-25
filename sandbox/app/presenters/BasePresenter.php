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
	protected function startup() {
		parent::startup();
		$this->user = $this->getUser();
	}
}
