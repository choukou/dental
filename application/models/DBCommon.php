<?php
class Model_DBCommon extends Zend_Db_Table {
	public $_db;
	private $_config;
	public $sql;

	public function __construct() {
		$this->_db = Zend_Db_Table::getDefaultAdapter();
	}
	public function getDb() {
		return $this->_db;
	}
	/**
	 * select (col1, col2,...) from table where condition1
	 * @param string $tbl_nm : table name
	 * @param array $cols
	 * @param string $where
	 * @param array  $order
	 * @return array
	 */
	public function selectDB($tbl_nm, $cols, $where = NULL, $order = array(), $distinct = FALSE,$page=FALSE,$limit=FALSE,$group =NULL) {
		try {
			$select = $this->_db->select();
			if( $distinct )
				$select = $select->distinct();
			if( !isset($where) )
				$select = $select->from($tbl_nm, $cols);
			else
				$select = $select->from($tbl_nm, $cols)->where($where);

			if(is_array($group))
			{
				foreach ($group as $val)
				{
					$select->group( $group );
				}
			}
			else if($group) $select->group( $group );

			$result = $select->order($order);
			if($limit && $page)  $result = $select->limitPage($page,$limit);

			return $this->_db->fetchAll($result);
		} catch (Exception $e) {
			var_dump($e);
		}
	}
	public function getCount($tbl_nm, $col, $where = NULL) {
		try {
			$select = $this->_db->select()->from($tbl_nm, array(new Zend_Db_Expr("count(LPAD($col, 10, '0')) as countPrk")));
			if($where != null) {
				$select = $select->where($where);
			}
			$result = $this->_db->fetchRow($select);
			return $result['countPrk'];
		} catch (Exception $e) {
			var_dump($e);
		}
	}
	/**
	 * get max primakey
	 * @param string $tbl_nm
	 * @param string $col
	 * @param string $where
	 * @return max primakey
	 */
	public function getMaxPrk($tbl_nm, $col, $where = NULL) {
		try {
			$select = $this->_db->select()->from($tbl_nm, array(new Zend_Db_Expr("max(LPAD($col, 10, '0')) as maxPrk")));
			if($where != null) {
				$select = $select->where($where);
			}
			$result = $this->_db->fetchRow($select);
			return $result['maxPrk'];
		} catch (Exception $e) {
			var_dump($e);
		}
	}

	/**
	 * join table
	 * @param string $prk_tbl_nm : first table of join
	 * @param array $prk_cols : columns of first table
	 * @param array(array()) $join_tbl : array(table join, array(join condition), array(columns of join table))
	 * @param string $where: where condition string
	 * @param array  $order
	 * @return array
	 * @example $join_tbl = array(
						array(  'join_typ' => 'left'
							,   'join_tbl' => 'tbl'
							,   'join_cond' => 'tbla.a = tblb.b'
							,   'join_cols' => array('col')
						)
					);
		joinDB('tbl', array('a', 'b', 'c'), $join_tbl, 'del_flg = 1');
	 */
	public function joinDB($prk_tbl_nm, $prk_cols, $join_tbl, $where=NULL, $order = array(), $distinct = FALSE, $group = NULL,$page=FALSE,$limit=FALSE) {
		try {
			$select = $this->_db->select();
			if( $distinct )
				$select = $select->distinct();
			$select = $select->from($prk_tbl_nm, $prk_cols);
			foreach ( $join_tbl as $join ) {
				$join_typ = $join['join_typ'];
				switch ($join_typ) {
					case 'left':
						$select->joinLeft($join['join_tbl'], $join['join_cond'], $join['join_cols']);
						break;
					case 'right':
						$select->joinRight($join['join_tbl'], $join['join_cond'], $join['join_cols']);
						break;
					case 'inner':
						$select->joinInner($join['join_tbl'], $join['join_cond'], $join['join_cols']);
						break;
					case 'cross':
						$select->joinCross($join['join_tbl'], $join['join_cols']);
						break;
					default:
						;
					break;
				}
			}
			if($where) $select->where( $where );
			if($order) $select->order( $order );
			if(is_array($group))
			{
				foreach ($group as $val)
				{
					$select->group( $group );
				}
			}
			else if($group) $select->group( $group );
			if($limit && $page)  $result = $select->limitPage($page,$limit);
			$this->sql=$select;

			return $this->_db->fetchAll($select);
		} catch (Exception $e) {
			var_dump($e);
		}
	}
	/**
	 * start transaction
	 */
	public function beginTransactionDB()
	{
		$this->_db->beginTransaction();
	}
	/**
	 * comit transaction
	 */
	public function comitDB()
	{
		$this->_db->commit();
	}

	public function rollBackDB()
	{
		$this->_db->rollBack();
	}
	/**
	 * insert data
	 * @param string $tbl_nm
	 * @param array $data
	 * @return string : result is error or ok
	 */

	public function insertDB($tbl_nm, $data) {
		try {
			$this->_db->insert($tbl_nm, $data);
			return '1';
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	/**
	 * update data
	 * @param string $tbl_nm
	 * @param array $data
	 * @param string $where
	 * @return string : result is error or ok
	 */
	public function updateDB($tbl_nm, $data, $where) {
		try {
			$this->_db->update($tbl_nm, $data, $where);
			return '1';
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	/**
	 * delete data
	 * @param string $tbl_nm
	 * @param string $where
	 * @return string : result is error or ok
	 */
	public function deleteDB($tbl_nm, $where) {
		try {
			$this->_db->delete($tbl_nm, $where);
			return '1';
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	/**
	 * paging
	 * @param array  $data
	 * @param string $currentPage
	 * @param string $itemPerPage
	 * @param string $pageRange
	 * @return Ambigous <Zend_Paginator, Zend_Paginator>
	 */
	public function paginatorDB($data, $currentPage = '1', $itemPerPage = '20', $pageRange = '4') {
		try {
			$paginator = Zend_Paginator::factory($data);
			$paginator->setItemCountPerPage($itemPerPage);
			$paginator->setPageRange($pageRange);
			$paginator->setCurrentPageNumber($currentPage);
			return $paginator;
		} catch (Exception $e) {
		}
	}

	/**
	 * Manual-SQL
	 * @param string $sql
	 * @return array
	 */
	public function executeSql($sql) {
		try {
			$result = $this->_db->fetchAll($sql);
			return $result;
		} catch (Exception $e) {
			var_dump($e);
		}
	}
}
