<?php

namespace App\Model;

use Nette,
    Nette\Utils\Strings;

class TestRepository extends Repository {

    const
	    TABLE_NAME = 'test',
	    COLUMN_ID = 'id',
	    COLUMN_ID_GROUP = 'id_group',
	    COLUMN_DIFFICULTY = 'nb_level',
	    COLUMN_COUNT = 'nb_count',
	    COLUMN_CREATED_TIME = 'dt_created',
	    COLUMN_CLOSED_TIME = 'dt_closed',
	    COLUMN_CLOSED = 'fl_closed',
	    TRUE_VALUE = 'A',
	    FALSE_VALUE = 'N',
	    MIN_COUNT = 3,
	    MAX_COUNT = 50;

    public function getUnclosedTestForGroup($group_id){
	return $this->database->table(self::TABLE_NAME)->select(self::COLUMN_ID)->where(array(self::COLUMN_ID_GROUP => $group_id, self::COLUMN_CLOSED => self::FALSE_VALUE))->fetch();
    }
    
    public function addTest($group_id, $level, $count){
	$this->database->table(self::TABLE_NAME)->insert(array(self::COLUMN_ID_GROUP => $group_id,
		    self::COLUMN_DIFFICULTY => $level,
		    self::COLUMN_COUNT => $count,
		    self::COLUMN_CLOSED => self::FALSE_VALUE,
		    self::COLUMN_CREATED_TIME => date("Y-m-d H:i:s")));
    }
    
    public function closeTest($test_id){
	$this->database->table(self::TABLE_NAME)->where(array(self::COLUMN_ID => $test_id))->
		update(array(self::COLUMN_CLOSED => self::TRUE_VALUE,
		    self::COLUMN_CLOSED_TIME => date("Y-m-d H:i:s")));
    }
}
