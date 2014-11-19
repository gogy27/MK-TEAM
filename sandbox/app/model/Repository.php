<?php
namespace App\Model;

use Nette;

abstract class Repository extends Nette\Object
{
  /** @var Nette\Database\Context */
	protected $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

    /**
     * Returns object representing a database table.
     * @return Nette\Database\Table\Selection
     */
    protected function getTable()
    {
        // název tabulky odvodíme z názvu tøídy
        preg_match('#(\w+)Repository$#', get_class($this), $m);
        return $this->database->table(lcfirst($m[1]));
    }

    /**
     * Returns row specified by primary key.
     * @param mixed $id primary key
     * @return Nette\Database\Table\IRow | FALSE if there is no such row.          
     */
    public function find($key)
    {
      return $this->getTable()->get($key);
    }

    /**
     * Returns all rows from table.
     * @return Nette\Database\Table\Selection
     */
    public function findAll()
    {
        return $this->getTable();
    }

    /**
     * Returns rows specified by the filter, e.g. array('name' => 'John').
     * @param array $by Filter values (key is column name) & (value is value of the row in that column).     
     * @return Nette\Database\Table\Selection
     */
    public function findBy(array $by)
    {
        return $this->getTable()->where($by);
    }

}