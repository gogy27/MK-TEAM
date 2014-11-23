<?php

namespace App\Model;

use Nette,
		Nette\Utils\Strings,
                Nette\Security\Passwords
        ;

class GroupRepository extends Repository {
    
    const
        TABLE_NAME = 'class',
	COLUMN_ID = 'id_group',
	COLUMN_NAME = 'str_group_name',
	COLUMN_PASSWORD = 'str_group_password',
        COLUMN_CREATE_TIME = 'dt_created',
	COLUMN_USER_ID = 'id_user';
    
    public function getTeacherGroups($teacher_id){
        return $this->findBy(array(self::COLUMN_USER_ID => $teacher_id));
    } 
}
