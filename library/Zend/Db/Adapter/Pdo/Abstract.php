<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Base/Result.php';
/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';


/**
 * @see Zend_Db_Statement_Pdo
 */
require_once 'Zend/Db/Statement/Pdo.php';


/**
 * Class for connecting to SQL databases and performing common operations using PDO.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Adapter_Pdo_Abstract extends Zend_Db_Adapter_Abstract
{

	/**
	 * Default class name for a DB statement.
	 *
	 * @var string
	 */
	protected $_defaultStmtClass = 'Zend_Db_Statement_Pdo';

	protected $_sql = '';

	/**
	 * Creates a PDO DSN for the adapter from $this->_config settings.
	 *
	 * @return string
	 */
	protected function _dsn()
	{
		// baseline of DSN parts
		$dsn = $this->_config;

		// don't pass the username, password, charset, persistent and driver_options in the DSN
		unset($dsn['username']);
		unset($dsn['password']);
		unset($dsn['options']);
		unset($dsn['charset']);
		unset($dsn['persistent']);
		unset($dsn['driver_options']);

		// use all remaining parts in the DSN
		foreach ($dsn as $key => $val) {
			$dsn[$key] = "$key=$val";
		}

		return $this->_pdoType . ':' . implode(';', $dsn);
	}

	/**
	 * Creates a PDO object and connects to the database.
	 *
	 * @return void
	 * @throws Zend_Db_Adapter_Exception
	 */
	protected function _connect()
	{
		// if we already have a PDO object, no need to re-connect.
		if ($this->_connection) {
			return;
		}

		// get the dsn first, because some adapters alter the $_pdoType
		$dsn = $this->_dsn();

		// check for PDO extension
		if (!extension_loaded('pdo')) {
			/**
			 * @see Zend_Db_Adapter_Exception
			 */
			require_once 'Zend/Db/Adapter/Exception.php';
			throw new Zend_Db_Adapter_Exception('The PDO extension is required for this adapter but the extension is not loaded');
		}

		// check the PDO driver is available
		if (!in_array($this->_pdoType, PDO::getAvailableDrivers())) {
			/**
			 * @see Zend_Db_Adapter_Exception
			 */
			require_once 'Zend/Db/Adapter/Exception.php';
			throw new Zend_Db_Adapter_Exception('The ' . $this->_pdoType . ' driver is not currently installed');
		}

		// create PDO connection
		$q = $this->_profiler->queryStart('connect', Zend_Db_Profiler::CONNECT);

		// add the persistence flag if we find it in our config array
		if (isset($this->_config['persistent']) && ($this->_config['persistent'] == true)) {
			$this->_config['driver_options'][PDO::ATTR_PERSISTENT] = true;
		}

		try {
			$this->_connection = new PDO(
				$dsn,
				$this->_config['username'],
				$this->_config['password'],
				$this->_config['driver_options']
			);

			$this->_profiler->queryEnd($q);

			// set the PDO connection to perform case-folding on array keys, or not
			$this->_connection->setAttribute(PDO::ATTR_CASE, $this->_caseFolding);

			// always use exceptions.
			$this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		} catch (PDOException $e) {
			/**
			 * @see Zend_Db_Adapter_Exception
			 */
			require_once 'Zend/Db/Adapter/Exception.php';
			throw new Zend_Db_Adapter_Exception($e->getMessage(), $e->getCode(), $e);
		}

	}

	/**
	 * Test if a connection is active
	 *
	 * @return boolean
	 */
	public function isConnected()
	{
		return ((bool) ($this->_connection instanceof PDO));
	}

	/**
	 * Force the connection to close.
	 *
	 * @return void
	 */
	public function closeConnection()
	{
		$this->_connection = null;
	}

	/**
	 * Prepares an SQL statement.
	 *
	 * @param string $sql The SQL statement with placeholders.
	 * @param array $bind An array of data to bind to the placeholders.
	 * @return PDOStatement
	 */
	public function prepare($sql)
	{
		$this->_connect();
		$stmtClass = $this->_defaultStmtClass;
		if (!class_exists($stmtClass)) {
			require_once 'Zend/Loader.php';
			Zend_Loader::loadClass($stmtClass);
		}
		$stmt = new $stmtClass($this, $sql);
		$stmt->setFetchMode($this->_fetchMode);
		return $stmt;
	}

	/**
	 * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
	 *
	 * As a convention, on RDBMS brands that support sequences
	 * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
	 * from the arguments and returns the last id generated by that sequence.
	 * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
	 * returns the last value generated for such a column, and the table name
	 * argument is disregarded.
	 *
	 * On RDBMS brands that don't support sequences, $tableName and $primaryKey
	 * are ignored.
	 *
	 * @param string $tableName   OPTIONAL Name of table.
	 * @param string $primaryKey  OPTIONAL Name of primary key column.
	 * @return string
	 */
	public function lastInsertId($tableName = null, $primaryKey = null)
	{
		$this->_connect();
		return $this->_connection->lastInsertId();
	}

	/**
	 * Special handling for PDO query().
	 * All bind parameter names must begin with ':'
	 *
	 * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
	 * @param array $bind An array of data to bind to the placeholders.
	 * @return Zend_Db_Statement_Pdo
	 * @throws Zend_Db_Adapter_Exception To re-throw PDOException.
	 */
	public function query($sql, $bind = array())
	{
		if (empty($bind) && $sql instanceof Zend_Db_Select) {
			$bind = $sql->getBind();
		}

		if (is_array($bind)) {
			foreach ($bind as $name => $value) {
				if (!is_int($name) && !preg_match('/^:/', $name)) {
					$newName = ":$name";
					unset($bind[$name]);
					$bind[$newName] = $value;
				}
			}
		}

		try {
			return parent::query($sql, $bind);
		} catch (PDOException $e) {
			/**
			 * @see Zend_Db_Statement_Exception
			 */
			require_once 'Zend/Db/Statement/Exception.php';
			throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Special handling for PDO call procedure().
	 * All bind parameter names must begin with ':'
	 *
	 * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
	 * @param array $bind An array of data to bind to the placeholders.
	 * @return Zend_Db_Statement_Pdo
	 * @throws Zend_Db_Adapter_Exception To re-throw PDOException.
	 */
	public function callProcedureGroup($sql, $bind = array(), $struct = array()) {

		$sql = $this->setTypeSqlProcedure($sql, $bind);
		$this->_sql = $this->interpolateQuery($sql, $bind);
		$statement = $this->query($sql, $bind);
		// $statement->setFetchMode(PDO::FETCH_ASSOC);
		$this->setFetchMode(PDO::FETCH_ASSOC);
		$result = array();
		$i = 0;
		do {
			if(isset($struct[$i])){
				while($respon = $statement->fetch(PDO::FETCH_ASSOC)) {
					foreach ($respon as &$res) {
						$res = htmlspecialchars($res);
					}
					$result[$i][$respon[$struct[$i]]][] = $respon;
				}
			} else {
				while($respon = $statement->fetch(PDO::FETCH_ASSOC)) {
					foreach ($respon as &$res) {
						$res = htmlspecialchars($res);
					}
					$result[$i][] = $respon;
				}
			}

			if(!isset($result[$i])) {
				$result[$i] = $this->getColumns($statement);
			}
			$i++;
			$statement->nextRowset();
		} while ($statement->columnCount());

		return new Result($result);
	}
	/**
	 * Special handling for PDO call procedure().
	 * All bind parameter names must begin with ':'
	 *
	 * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
	 * @param array $bind An array of data to bind to the placeholders.
	 * @return Result
	 */

	public function callProcedurePrepare($sql, $bind = array()) {
		if(!is_array($bind) && !is_null($bind)) {
			$bind = array($bind);
		}

		$bind = array_values($bind);
		$sql = $this->setTypeSqlProcedure($sql, $bind);
		$this->_sql = $this->interpolateQuery($sql, $bind);

		if(LOGSQL && Zend_Registry::isRegistered('log')) {
			Zend_Registry::get("log")->info($this->_sql);
		}

		$statement = $this->query($sql, $bind);
		// $statement->setFetchMode(PDO::FETCH_ASSOC);
		$this->setFetchMode(PDO::FETCH_ASSOC);
		$result = array();
		$i = 0;
		do {
			while($respon = $statement->fetch(PDO::FETCH_ASSOC)) {
				foreach ($respon as &$res) {
					$res = htmlspecialchars($res);
				}
				$result[$i][] = $respon;
			}

			if(!isset($result[$i])) {
				$result[$i] = $this->getColumns($statement);
			}
			$i++;
			$statement->nextRowset();
		} while ($statement->columnCount());
		$statement->closeCursor();

		return new Result($result);
	}

	protected function getColumns($statement) {
		$columns = array();
		for ($i = 0; $i < $statement->columnCount(); $i++) {
			$col = $statement->getColumnMeta($i);
			$columns[0][$col['name']] = '';
		}
		return $columns;
	}

	protected function setTypeSqlProcedure($sql, array $params=null) {
		switch ($this->_pdoType) {
			case 'mysql':
				$sql .= $this->addMysqlPrepare($params);
				$sql = 'CALL ' . $sql;
				break;
			case 'sqlsrv':
				$params = array_values($params);
				$sql = 'EXECUTE ' . $sql . ' ';
				$sql .= $this->addMssqlPrepare($params);
				break;
			default:
				$sql .= $this->addMysqlPrepare($params);
				$sql = 'CALL ' . $sql;
				break;
		}
		return $sql;
	}

	protected function addMysqlPrepare(array $params=null) {
		$str = "(";
		$tmp ="";
		foreach ($params as $value) {
			$tmp .= "?,";
		}
		$str .= rtrim($tmp, ",");
		$str .= ")";
		return $str;

	}

	protected function addMssqlPrepare($params= array()) {
		if(empty($params)){return ' ';}
		return implode(',', array_fill(0, count($params), '?'));
	}

	protected function interpolateQuery($query, $params= array()) {
		$keys = array();
		$values = $params;

		# build a regular expression for each parameter
		foreach ($params as $key => $value) {
			if (is_string($key)) {
				$keys[] = '/:'.$key.'/';
			} else {
				$keys[] = '/[?]/';
			}

			if (is_string($value))
				$values[$key] = "'" . $value . "'";

			if (is_array($value))
				$values[$key] = "'" . implode("','", $value) . "'";

			if (is_null($value))
				$values[$key] = 'NULL';
		}

		$query = preg_replace($keys, $values, $query, 1, $count);

		return $query;
	}

	public function getSql(){
		return $this->_sql;
	}

	/**
	 * Executes an SQL statement and return the number of affected rows
	 *
	 * @param  mixed  $sql  The SQL statement with placeholders.
	 *                      May be a string or Zend_Db_Select.
	 * @return integer      Number of rows that were modified
	 *                      or deleted by the SQL statement
	 */
	public function exec($sql)
	{
		if ($sql instanceof Zend_Db_Select) {
			$sql = $sql->assemble();
		}

		try {
			$affected = $this->getConnection()->exec($sql);

			if ($affected === false) {
				$errorInfo = $this->getConnection()->errorInfo();
				/**
				 * @see Zend_Db_Adapter_Exception
				 */
				require_once 'Zend/Db/Adapter/Exception.php';
				throw new Zend_Db_Adapter_Exception($errorInfo[2]);
			}

			return $affected;
		} catch (PDOException $e) {
			/**
			 * @see Zend_Db_Adapter_Exception
			 */
			require_once 'Zend/Db/Adapter/Exception.php';
			throw new Zend_Db_Adapter_Exception($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Quote a raw string.
	 *
	 * @param string $value     Raw string
	 * @return string           Quoted string
	 */
	protected function _quote($value)
	{
		if (is_int($value) || is_float($value)) {
			return $value;
		}
		$this->_connect();
		return $this->_connection->quote($value);
	}

	/**
	 * Begin a transaction.
	 */
	protected function _beginTransaction()
	{
		$this->_connect();
		$this->_connection->beginTransaction();
	}

	/**
	 * Commit a transaction.
	 */
	protected function _commit()
	{
		$this->_connect();
		$this->_connection->commit();
	}

	/**
	 * Roll-back a transaction.
	 */
	protected function _rollBack() {
		$this->_connect();
		$this->_connection->rollBack();
	}

	/**
	 * Set the PDO fetch mode.
	 *
	 * @todo Support FETCH_CLASS and FETCH_INTO.
	 *
	 * @param int $mode A PDO fetch mode.
	 * @return void
	 * @throws Zend_Db_Adapter_Exception
	 */
	public function setFetchMode($mode)
	{
		//check for PDO extension
		if (!extension_loaded('pdo')) {
			/**
			 * @see Zend_Db_Adapter_Exception
			 */
			require_once 'Zend/Db/Adapter/Exception.php';
			throw new Zend_Db_Adapter_Exception('The PDO extension is required for this adapter but the extension is not loaded');
		}
		switch ($mode) {
			case PDO::FETCH_LAZY:
			case PDO::FETCH_ASSOC:
			case PDO::FETCH_NUM:
			case PDO::FETCH_BOTH:
			case PDO::FETCH_NAMED:
			case PDO::FETCH_OBJ:
				$this->_fetchMode = $mode;
				break;
			default:
				/**
				 * @see Zend_Db_Adapter_Exception
				 */
				require_once 'Zend/Db/Adapter/Exception.php';
				throw new Zend_Db_Adapter_Exception("Invalid fetch mode '$mode' specified");
				break;
		}
	}

	/**
	 * Check if the adapter supports real SQL parameters.
	 *
	 * @param string $type 'positional' or 'named'
	 * @return bool
	 */
	public function supportsParameters($type)
	{
		switch ($type) {
			case 'positional':
			case 'named':
			default:
				return true;
		}
	}

	/**
	 * Retrieve server version in PHP style
	 *
	 * @return string
	 */
	public function getServerVersion()
	{
		$this->_connect();
		try {
			$version = $this->_connection->getAttribute(PDO::ATTR_SERVER_VERSION);
		} catch (PDOException $e) {
			// In case of the driver doesn't support getting attributes
			return null;
		}
		$matches = null;
		if (preg_match('/((?:[0-9]{1,2}\.){1,3}[0-9]{1,2})/', $version, $matches)) {
			return $matches[1];
		} else {
			return null;
		}
	}
}

