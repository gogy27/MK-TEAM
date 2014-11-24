<?php

namespace App\Model;

use Nette,
		Nette\Utils\Strings,
                Nette\Security\Passwords
        ;

class ClassRepository extends Repository {
    
    const
        TABLE_NAME = 'class',
	COLUMN_ID = 'id_group',
	COLUMN_NAME = 'str_group_name',
	COLUMN_DESCRIPTION = 'str_group_description',
	COLUMN_PASSWORD = 'str_group_password',
        COLUMN_CREATE_TIME = 'dt_created',
	COLUMN_USER_ID = 'id_user';
    
    public function getTeacherGroups($teacher_id){
        return $this->findBy(array(self::COLUMN_USER_ID => $teacher_id));
    } 
    
    public function addGroup($teacher_id, $group_name, $group_key, $description){
	    $this->getTable()->insert(array(self::COLUMN_NAME => $group_name,
		    self::COLUMN_PASSWORD => $group_key,
		    self::COLUMN_DESCRIPTION => $description,
		    self::COLUMN_USER_ID => $teacher_id,
		    self::COLUMN_CREATE_TIME => date("Y-m-d H:i:s")));
    }
}
