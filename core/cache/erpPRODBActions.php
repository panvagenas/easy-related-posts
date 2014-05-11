<?php


/**
 * Easy related posts .
 *
 * @package Easy_Related_Posts_DB
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link http://example.com
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */

/**
 * Database actions class.
 *
 * @package Easy_Related_Posts
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpDBActions {

	/**
	 * WP $wpdb global
	 *
	 * @since 1.0.0
	 * @var wpdb
	 */
	private $db;

	/**
	 * Array containing column names
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $columnNames = array ();

	/**
	 * ERP related table name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $tableName;

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var erpDBActions
	 */
	protected static $instance = null;

	/**
	 * Holds data to be inserted in rel table
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $insert = array ();

	/**
	 * Holds data to be removed from rel table
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $delete = array ();

	/**
	 * Posts to increase displayed value
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $displayed = array ();

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	protected function __construct( ) {
		global $wpdb;
		$this->db = $wpdb;
		$this->tableName = $this->db->prefix . ERP_RELATIVE_TABLE;
		$this->columnNames = array (
				'pid1',
				'post_date1',
				'score1_cats',
				'score1_tags',
				'displayed1',
				'clicks1',
				'pid2',
				'post_date2',
				'score2_cats',
				'score2_tags',
				'clicks2',
				'displayed2',
				'time'
		);
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 * @return object A single instance of this class.
	 */
	public static function getInstance( ) {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Finds all occurrences of given post id in related table
	 *
	 * @param int $pid
	 * @return array Accosiative array containing all occurrenses
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getAllOccurrences( $pid ) {
		$where = ' pid1="' . $pid . '" OR pid2="' . $pid . '" ';
		$result = $this->db->get_results( 'SELECT * FROM ' . $this->tableName . ' WHERE ' . $where, ARRAY_A );
		return $result;
	}
	/**
	 * Deletes all occurences of a post in DB
	 * @param int $pid
	 * @return int|false Number of rows affected/selected or false on error
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function deleteAllOccurrences( $pid ){
		if (!is_int($pid)) {
			return false;
		}
		$where = ' pid1="' . $pid . '" OR pid2="' . $pid . '" ';
		return $this->db->query('DELETE FROM '.$this->tableName.' WHERE '.$where);
	}

	/**
	 * Returns all records from rel table
	 *
	 * @return mixed Database query results
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getAll(){
		return $this->db->get_results( 'SELECT * FROM ' . $this->tableName);
	}

	/**
	 * Returns unique ids in pid1 field of rel table
	 *
	 * @return array If rel table is empty an amty array will be returned
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getUniqueIds(){
		$res =  $this->db->get_results( 'SELECT DISTINCT(pid1) AS pid FROM ' . $this->tableName, ARRAY_A);
		return is_array($res) ? $res : array();
	}

	/**
	 * Flushes all records from rel table in DB.
	 *
	 * @return int|false Number of rows affected/selected or false on error
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function emptyRelTable(){
		return $this->db->query('DELETE FROM '.$this->tableName);
	}

	/**
	 * Get a single row form related table
	 *
	 * @param int $pid1
	 * @param int $pid2
	 * @return array Result array
	 * @since 1.0.0
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 */
	public function getRowFromRel( $pid1, $pid2 ) {
		$where = ' (pid1="' . $pid1 . '" AND pid2="' . $pid2 . '") OR (pid1="' . $pid2 . '" AND pid2="' . $pid1 . '") ';
		return $this->db->get_row( 'SELECT * FROM ' . $this->tableName . ' WHERE ' . $where, ARRAY_A );
	}

	/**
	 * Inserts a single row in related table, if a record allready exists it just updates it with given data
	 *
	 * @param int $pid1
	 * @param int $pid2
	 * @param array $data
	 *        	Assosiative array with data to be insterted
	 * @return boolean or NULL
	 * @since 1.5.0
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 */
	public function insertRecToRel( $pid1, $pid2, Array $data ) {
		/**
		 * If record allready exists in DB
		 */
		$exists = $this->getRowFromRel( $pid1, $pid2 );
		if ( !empty( $exists ) ) {
			return $this->updateRelRec( $pid1, $pid2, $data );
		}
		unset( $data [ 'pid1' ] );
		unset( $data [ 'pid2' ] );
		/**
		 * If record allready cached in insert array
		 */
		foreach ( $this->insert as $key => $value ) {
			if ( ( $value [ 'pid1' ] == $pid1 && $value [ 'pid2' ] == $pid2 ) || ( $value [ 'pid2' ] == $pid1 && $value [ 'pid1' ] == $pid2 ) ) {
				return true;
			}
		}
		/**
		 * Cache record in insert array
		 */
		array_push( $this->insert, array_merge( $data, array (
				'pid1' => $pid1,
				'pid2' => $pid2
		) ) );
		return TRUE;
	}

	/**
	 * Updates a record in related table or creates a new one if old is not found.
	 * If array has to be reversed, reverses it.
	 *
	 * @param int $pid1
	 * @param int $pid2
	 * @param array $data
	 *        	Associative array with updated data
	 * @return boolean True on success false on failure
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function updateRelRec( $pid1, $pid2, Array $data ) {
		/**
		 * Check if record existst in db
		 */
		$record = $this->getRowFromRel( $pid1, $pid2 );
		if ( empty( $record ) ) {
			return $this->insertRecToRel( $pid1, $pid2, $data );
		} elseif ( isset( $data [ 'pid1' ] ) || isset( $data [ 'pid2' ] ) ) {
			unset( $data [ 'pid1' ] );
			unset( $data [ 'pid2' ] );
		}
		/**
		 * Update
		 */
		if ( $record [ 'pid1' ] == $pid1 ) {
			$where = ' WHERE pid1 = ' . $pid1 . ' AND pid2 = ' . $pid2 . ' ';
		} else {
			$data = $this->reverseDataArray( $data );
			$where = ' WHERE pid1 = ' . $pid2 . ' AND pid2 = ' . $pid1 . ' ';
		}

		$query = 'UPDATE ' . $this->tableName . ' SET ';

		foreach ( $data as $index => $value ) {
			$query .= ' ' . $index . ' = "' . $value . '", ';
		}
		$query = rtrim( $query, ", " );
		return $this->db->query( $query . ' ' . $where );
	}

	/**
	 * Deletes a record from related table in DB
	 *
	 * @param int $pid1
	 * @param int $pid2
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function deleteRecFromRelTable( $pid1, $pid2 ) {
		array_push( $this->delete, array (
				'pid1' => $pid1,
				'pid2' => $pid2
		) );
	}

	/**
	 * Increases click value in DB for a given pair of pids.
	 *
	 * @param int $pid1
	 *        	The host post id
	 * @param int $pid2
	 *        	The clicked post id
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function addClick( $pid1, $pid2 ) {
		/**
		 * Check if record existst in db
		 */
		$record = $this->getRowFromRel( $pid1, $pid2 );
		if ( empty( $record ) ) {
			return 0;
		}
		/**
		 * Update click number
		 */
		if ( $record [ 'pid1' ] == $pid1 ) {
			return $this->updateRelRec( $pid1, $pid2, array (
					'clicks2' => $record [ 'clicks2' ] + 1
			) );
		} else {
			return $this->updateRelRec( $pid2, $pid1, array (
					'clicks1' => $record [ 'clicks1' ] + 1
			) );
		}
	}

	/**
	 * Increases displayed value in DB for a given pid
	 *
	 * @param int $pid1
	 *        	Host post id
	 * @param int $pid2
	 *        	Displayed post id
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function addDisplayed( $pid1, $pid2 ) {
		if ( is_array( $pid2 ) ) {
			foreach ( $pid2 as $key => $value ) {
				$this->addDisplayed( $pid1, $value );
			}
		} else {
			if ( empty( $this->displayed [ $pid1 ] ) ) {
				$this->displayed [ $pid1 ] = array (
						$pid2
				);
			} else {
				if ( !in_array( $pid2, $this->displayed [ $pid1 ] ) ) {
					array_push( $this->displayed [ $pid1 ], $pid2 );
				}
			}
		}
	}

	/**
	 * Performs cached add displayed queries
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	private function performDisplayedQuery( ) {
		if ( !empty( $this->displayed ) ) {
			$query = '';
			foreach ( $this->displayed as $pid1 => $value ) {
				foreach ( $value as $key => $pid2 ) {
					$query = 'UPDATE ' . $this->tableName . ' ';
					$old = $this->getRowFromRel( $pid1, $pid2 );
					if ( $old [ 'pid1' ] == $pid1 ) {
						$query .= ' SET displayed2 = displayed2 + 1 WHERE pid1 = ' . $pid1 . ' AND pid2 = ' . $pid2 . '; ';
					} else {
						$query .= ' SET displayed1 = displayed1 + 1 WHERE pid1 = ' . $pid2 . ' AND pid2 = ' . $pid1 . '; ';
					}
					$this->db->query( $query );
				}
			}
		}
	}

	/**
	 * Inserts records cached in $this->insert array.
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	private function performInsertQuery( ) {
		$query = '';
		if ( !empty( $this->insert ) ) {
			$query = 'INSERT INTO ' . $this->tableName;
			$query .= ' (pid1, pid2, score1_cats, score1_tags, score2_cats,	score2_tags, clicks1, clicks2, time, displayed1, displayed2, post_date1, post_date2 ) VALUES ';
			$qLength = strlen( $query );
			foreach ( $this->insert as $k => $v ) {
				$chk = $this->getRowFromRel( $v [ 'pid1' ], $v [ 'pid2' ] );
				if ( !empty( $chk ) || !isset( $v [ 'post_date1' ] ) || !isset( $v [ 'post_date2' ] ) ) {
					continue;
				}
				$query .= '(' . $v [ 'pid1' ] . ',';
				$query .= $v [ 'pid2' ] . ',';
				$query .= (isset( $v [ 'score1_cats' ] ) ? $v [ 'score1_cats' ] : 0) . ',';
				$query .= (isset( $v [ 'score1_tags' ] ) ? $v [ 'score1_tags' ] : 0) . ',';
				$query .= (isset( $v [ 'score2_cats' ] ) ? $v [ 'score2_cats' ] : 0) . ',';
				$query .= (isset( $v [ 'score2_tags' ] ) ? $v [ 'score2_tags' ] : 0) . ',';
				$query .= (isset( $v [ 'clicks1' ] ) ? $v [ 'clicks1' ] : 0) . ',';
				$query .= (isset( $v [ 'clicks2' ] ) ? $v [ 'clicks2' ] : 0) . ',';
				$query .= '"' . (isset( $v [ 'time' ] ) ? $v [ 'time' ] : date( 'Y-m-d H:i:s' )) . '",';
				$query .= (isset( $v [ 'displayed1' ] ) ? $v [ 'displayed1' ] : 0) . ',';
				$query .= (isset( $v [ 'displayed2' ] ) ? $v [ 'displayed2' ] : 0) . ',';
				$query .= '"' . $v [ 'post_date1' ] . '",';
				$query .= '"' . $v [ 'post_date2' ] . '"';
				$query .= '),';
			}
		}
		if ( !empty( $query ) && isset( $qLength ) && strlen( $query ) > $qLength ) {
			$query = rtrim( $query, "," );
			$this->db->query( $query . ';' );
		}
	}

	/**
	 * Performs cached delete query
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	private function performDeleteQuery( ) {
		if ( !empty( $this->delete ) ) {
			$query = '';
			$query = 'DELETE FROM ' . $this->tableName . 'WHERE ';
			foreach ( $this->delete as $k => $v ) {
				$query .= ' (pid1 = ' . $v [ 'pid1' ] . ' AND pid2 = ' . $v [ 'pid2' ] . ') ';
				$query .= 'OR (pid1 = ' . $v [ 'pid2' ] . ' AND pid2 = ' . $v [ 'pid1' ] . ') OR';
			}
			if ( !empty( $query ) ) {
				$query = rtrim( $query, "OR" );
				$this->db->query( $query . ';' );
			}
		}
	}

	/**
	 * Reverses rel data array eg pid1<=>pid2 etc
	 *
	 * @param array $data
	 *        	The array to be reversed
	 * @return array The reversed array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	private function reverseDataArray( $data ) {
		$new = array ();
		if ( isset( $data [ 'score1_cats' ] ) ) {
			$new [ 'score2_cats' ] = $data [ 'score1_cats' ];
		}
		if ( isset( $data [ 'score2_cats' ] ) ) {
			$new [ 'score1_cats' ] = $data [ 'score2_cats' ];
		}
		if ( isset( $data [ 'score1_tags' ] ) ) {
			$new [ 'score2_tags' ] = $data [ 'score1_tags' ];
		}
		if ( isset( $data [ 'score2_tags' ] ) ) {
			$new [ 'score1_tags' ] = $data [ 'score2_tags' ];
		}
		if ( isset( $data [ 'clicks1' ] ) ) {
			$new [ 'clicks2' ] = $data [ 'clicks1' ];
		}
		if ( isset( $data [ 'clicks2' ] ) ) {
			$new [ 'clicks1' ] = $data [ 'clicks2' ];
		}
		if ( isset( $data [ 'displayed1' ] ) ) {
			$new [ 'displayed2' ] = $data [ 'displayed1' ];
		}
		if ( isset( $data [ 'displayed2' ] ) ) {
			$new [ 'displayed1' ] = $data [ 'displayed2' ];
		}
		if ( isset( $data [ 'post_date2' ] ) ) {
			$new [ 'post_date1' ] = $data [ 'post_date2' ];
		}
		if ( isset( $data [ 'post_date1' ] ) ) {
			$new [ 'post_date2' ] = $data [ 'post_date1' ];
		}
		return $new;
	}

	/**
	 * Performs cached queries if any
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	private function performCachedQueries( ) {
		$this->performInsertQuery();
		$this->performDeleteQuery();
		$this->performDisplayedQuery();
	}

	/**
	 * Before object destructed must perform chached queries
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function __destruct( ) {
		$this->performCachedQueries();
	}

	/**
	 * Prevent overiding
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	private function __clone( ) {}

	/**
	 * Prevent overiding
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	private function __wakeup( ) {}
}