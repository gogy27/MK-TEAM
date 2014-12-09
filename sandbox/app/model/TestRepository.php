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

    public function getUnclosedTestForGroup($group_id) {
	return $this->database->table(self::TABLE_NAME)->select(self::COLUMN_ID . ','. self::COLUMN_CREATED_TIME)->where(array(self::COLUMN_ID_GROUP => $group_id, self::COLUMN_CLOSED => self::FALSE_VALUE))->fetch();
    }

    public function addTest($group_id, $level, $count) {
	$this->database->table(self::TABLE_NAME)->insert(array(self::COLUMN_ID_GROUP => $group_id,
	    self::COLUMN_DIFFICULTY => $level,
	    self::COLUMN_COUNT => $count,
	    self::COLUMN_CLOSED => self::FALSE_VALUE,
	    self::COLUMN_CREATED_TIME => date("Y-m-d H:i:s")));
    }

    public function closeTest($test_id) {
	$this->database->table(self::TABLE_NAME)->where(array(self::COLUMN_ID => $test_id))->
		update(array(self::COLUMN_CLOSED => self::TRUE_VALUE,
		    self::COLUMN_CLOSED_TIME => date("Y-m-d H:i:s")));
    }
    
    public function getTestsOfGroup($group_id){
	return $this->findBy(array(self::COLUMN_ID_GROUP =>$group_id))->order(self::COLUMN_CREATED_TIME)->fetchAll();
    }
    
    public function getOwnerOfTest($test_id){
	return $this->database->query('SELECT
					c.id_user as id
					FROM test t
					LEFT JOIN class c
						ON c.id = t.id_group
					WHERE t.id =  ' . $test_id . ';')->fetch();
    }
    
    public function getTestForUser($user_id) {
	//return $this->database->table(UserRepository::TABLE_NAME)->select('test.id, test.nb_level, test.nb_count')->where('test.id_group = user.id_group AND test.fl_closed = "N" AND user.id = ' . $user_id)->fetch();

	return $this->database->query("SELECT test.id, test.nb_level, test.nb_count 
				FROM user 
				LEFT JOIN test 
				ON test.id_group = user.id_group
				AND test.fl_closed = 'N'
				WHERE user.id = " . $user_id . ";")->fetch();
    }
    
    public function getFilledTaskInTest($test_id, $student_id){
	return $this->database->query('SELECT id FROM task WHERE dt_updated IS NOT NULL AND id_test =' . $test_id . ' AND id_user =' . $student_id . ';')->fetch();
    }
    
    public function getUnfilledTaskInTest($test_id, $student_id){
	return $this->database->query('SELECT t.id as idcko, t.nb_value_from, t.nb_power_from, u.* FROM task t
					LEFT JOIN unit u
					ON u.id = t.id_unit
					WHERE dt_updated IS NULL AND id_test =' . $test_id . ' AND id_user =' . $student_id . ';')
		->fetchAll()
		;
    }

}
