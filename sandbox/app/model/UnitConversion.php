<?php
namespace App\Model;

use Nette,
		Nette\Database;

class UnitConversion extends Nette\Object {
	
	/** @var Nette\Database\Context */
	private $database;
	
	public function __construct(Nette\Database\Connection $connection) {
		$this->database = new Database\Context($connection);
	}
}
