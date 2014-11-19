<?php

namespace App\Model;

use Nette,
		Nette\Utils\Strings;

class UserRepository extends Repository {

	const
					TABLE_NAME = 'user',
					COLUMN_ID = 'id_user',
					COLUMN_NAME = 'str_name',
					COLUMN_EMAIL = 'str_mail',
					COLUMN_PASSWORD = 'str_user_password',
					COLUMN_PASSWORD_HASH = 'str_pass_hash',
					COLUMN_ROLE = 'fl_user_type',
					COLUMN_REG_TIME = 'dt_registration',
					COLUMN_LOG_TIME = 'dt_login',
					STUDENT = 'S', TEACHER = 'T';

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($username, $password) = $credentials;

		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update(array(
					self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}

	public function getInfo($id) {
		return $this->find($id);
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
		return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $email)->update(['str_pass_hash' => $hash]);
	}

}
