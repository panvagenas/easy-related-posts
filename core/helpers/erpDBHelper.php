<?php

/**
 *
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpDBHelper {

	/**
	 */
	function __construct( ) { }

	/**
	 */
	function __destruct( ) { }

	/**
	 * Increases displayed values for given pids
	 *
	 * @param int $pid
	 * @param array $pids
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public static function addDisplayed( $pid, Array $pids ) {
		erpPaths::requireOnce(erpPaths::$erpDBActions);
		$db = erpDBActions::getInstance();
		$db->addDisplayed( $pid, $pids );
	}
}

?>