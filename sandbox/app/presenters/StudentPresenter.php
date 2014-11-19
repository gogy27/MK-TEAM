<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StudentPresenter
 *
 * @author Gogy
 */
namespace App\Presenters;

use Nette;

class StudentPresenter extends BasePresenter {
	//put your code here
    
    public function actionDefault(){
	$user = $this->getUser();
	if($user->isLoggedIn()){
	    if($user->isInRole(\App\Model\UserManager::TEACHER)){
		$this->redirect('Teacher:default');
	    }
	}else{
	    $this->redirect('Auth:default');
	}
    }
}
