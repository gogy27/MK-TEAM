<?php

namespace App\Model;

use Nette,
		Nette\Utils\Strings,
		Nette\Security\Passwords;

class UserRepository extends Repository implements Nette\Security\IAuthenticator {

	const
					TABLE_NAME = 'user',
					COLUMN_ID = 'id_user',
					COLUMN_NAME = 'str_name',
					COLUMN_PASSWORD_HASH = 'str_user_password',
					COLUMN_ROLE = 'fl_user_type',
					STUDENT = 'S', TEACHER = 'T';

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

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

	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function add($username, $password) {
		$this->database->table(self::TABLE_NAME)->insert(array(
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
		));
	}

	public function getInfo($id) {
		return $this->find($id);
	}

	public function setInfo($id, $values) {
		$this->find($id)->update($values);
	}

	public function register($data) {
		$data["registration_time"] = time();
		$data["password"] = $this->calculateHash($data["password"]);
		$this->getTable()->insert($data);
	}

	public function checkEmailAvailability($email) {
		return !($this->findBy(array('email' => $email))->count() > 0);
	}

	public function addResetPasswordHash($email, $hash) {
		return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $email)->update(['str_pass_hash' => $hash]);
	}

}