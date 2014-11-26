<?php

namespace App\Model;

use Nette,
		Nette\Utils\Strings,
		Nette\Security\Passwords

;

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
					STUDENT = 'S', TEACHER = 'T',
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
		$this->getTable()->insert(array(self::COLUMN_NAME => $data->name,
				self::COLUMN_EMAIL => $data->email,
				self::COLUMN_PASSWORD => Passwords::hash($data->password),
				self::COLUMN_ROLE => ($data->type == self::STUDENT) ? self::TEACHER : self::STUDENT,
				self::COLUMN_REG_TIME => date("Y-m-d H:i:s")));
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

}
