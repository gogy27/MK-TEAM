<?php

use Nette\Security\Passwords;

class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator {
	
	/** @var Nette\Database\Context */
	private $database;

  /**
   * Constructor for setting a database
   *    
   * @param Nette\Database\Context $database    
   */     
	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}
	
	public function authenticate(array $credentials) {
		list($email, $password) = $credentials;

		$row = $this->database->table(App\Model\UserRepository::TABLE_NAME)->where(App\Model\UserRepository::COLUMN_EMAIL, $email)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $row[App\Model\UserRepository::COLUMN_PASSWORD])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		} elseif (Passwords::needsRehash($row[App\Model\UserRepository::COLUMN_PASSWORD])) {
			$row->update(array(
					App\Model\UserRepository::COLUMN_PASSWORD => Passwords::hash($password),
			));
		}

		$arr = $row->toArray();
		unset($arr[App\Model\UserRepository::COLUMN_PASSWORD]);
		return new Nette\Security\Identity($row[App\Model\UserRepository::COLUMN_ID], $row[App\Model\UserRepository::COLUMN_ROLE], $arr);
	}
}
