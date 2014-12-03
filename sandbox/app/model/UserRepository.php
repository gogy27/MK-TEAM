<?php

namespace App\Model;

use Nette,
		Nette\Utils\Strings,
		Nette\Security\Passwords;

class UserRepository extends Repository {

	const
					TABLE_NAME = 'user',
					COLUMN_ID = 'id',
					COLUMN_ID_GROUP = 'id_group',
					COLUMN_NAME = 'str_name',
					COLUMN_EMAIL = 'str_mail',
					COLUMN_PASSWORD = 'str_user_password',
					COLUMN_PASSWORD_HASH = 'str_pass_hash',
					COLUMN_ROLE = 'fl_user_type',
					COLUMN_REG_TIME = 'dt_registration',
					COLUMN_LOG_TIME = 'dt_login',
					STUDENT = 'S', TEACHER = 'T', ADMIN = 'A',
					PASSWORD_MIN_LENGTH = 6;

	public function getInfo($id) {
		return $this->find($id);
	}

	public function getInfoByEmail($email) {
		return $this->findBy([self::COLUMN_EMAIL => $email]);
	}

	public function setInfo($id, $values) {
		$this->find($id)->update($values);
	}

	public function register($data) {
		$insertData = array(
				self::COLUMN_NAME => $data->name,
				self::COLUMN_EMAIL => $data->email,
				self::COLUMN_PASSWORD => Passwords::hash($data->password),
				self::COLUMN_ROLE => ($data->type == self::TEACHER) ? self::TEACHER : self::STUDENT,
				self::COLUMN_REG_TIME => date("Y-m-d H:i:s")
		);
		if ($data->type == self::STUDENT) {
			$insertData[self::COLUMN_ID_GROUP] = $data->group;
		}
		$this->getTable()->insert($insertData);
	}

	public function checkEmailAvailability($email) {
		return !($this->findBy(array(self::COLUMN_EMAIL => $email))->count() > 0);
	}

	public function addResetPasswordHash($email, $hash) {
		return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EMAIL, $email)->update([self::COLUMN_PASSWORD_HASH => $hash]);
	}

	public function resetPassword($user_id, $hash, $password) {
		$toUpdate = [
				self::COLUMN_PASSWORD_HASH => NULL,
				self::COLUMN_PASSWORD => Passwords::hash($password),
		];
		$user = $this->find($user_id);
		if ($user[self::COLUMN_PASSWORD_HASH] != $hash) {
			throw new Exception('Zly hash');
		}
		return $user->update($toUpdate);
	}

	public function getStudentsByGroup($group_id) {
		return $this->findBy(array(self::COLUMN_ID_GROUP => $group_id));
	}

	public function removeUser($user_id) {
		$this->database->table(self::TABLE_NAME)->where(array(self::COLUMN_ID => $user_id))->delete();
	}

	public function getPassword($user_id) {
		return $this->database->table(self::TABLE_NAME)->where(array(self::COLUMN_ID => $user_id))->fetch();
	}

	public function changePassword($user_id, $new_password) {
		$this->database->table(self::TABLE_NAME)->where(array(self::COLUMN_ID => $user_id))->update(array(self::COLUMN_PASSWORD => $new_password));
	}

	public function getStatisticsOfTasks($group_id) {
		return $this->database->query("SELECT 
					sum(CASE WHEN fl_correct = 'A' THEN 1 ELSE 0 END) as correct,
					sum(CASE WHEN fl_correct = 'N' THEN 1 ELSE 0 END) as uncorrect,
					sum(CASE WHEN fl_correct IS NULL AND dt_updated IS NOT NULL THEN 1 ELSE 0 END) as unfilled
					FROM task t
					LEFT JOIN user u
						ON u.id = t.id_user
					WHERE u.id_group = " . $group_id . ";")->fetch();
	}

	public function getStatisticsOfGroupUser($group_id) {
		return $this->database->query("SELECT
				    u.str_name,
				    CASE WHEN src.points < 0 THEN 0 ELSE src.points END as points
				    FROM(
					SELECT
					    u.id,
					    sum(CASE WHEN t.fl_correct = 'A' THEN 2 
						    WHEN t.dt_updated IS NOT NULL THEN -1
						    ELSE 0 END) as points
					FROM task t
					LEFT JOIN user u
					    ON u.id = t.id_user
					WHERE u.id_group = " . $group_id .
										" GROUP BY u.id
					)src
				    LEFT JOIn user u
					    ON u.id = src.id
				    ORDER BY src.points;")->fetchAll();
	}

	public function getStatisticsOfUnits($group_id) {
		return $this->database->query("SELECT 
					    un.nb_category,
					    un.str_unit_description,
					    COALESCE(sum(CASE WHEN t.fl_correct = 'A' THEN 1 ELSE 0 END), 0) as correct,
					    COALESCE(sum(CASE WHEN t.fl_correct = 'N' THEN 1 ELSE 0 END), 0) as uncorrect
					FROM task t
					LEFT JOIN user u
					    ON u.id = t.id_user
					LEFT JOIN unit un
					    ON un.id = t.id_unit
					WHERE u.id_group = " . $group_id . 
					" GROUP BY un.nb_category, un.str_unit_description ORDER BY un.str_unit_description DESC")
		->fetchAll();
    }
    
    public function  getTeachers() {
        return $this->database->table(self::TABLE_NAME)->where([self::COLUMN_ROLE => self::TEACHER])->fetchAll();
    }
}
