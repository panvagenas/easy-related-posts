<?php

/**
 * Easy Related Posts .
 *
 * @package   Easy_Related_Posts_Helpers
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */

/**
 * HTML helper object
 *
 * @package Easy_related_posts_helpers
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpHTMLHelper {

	/**
	 */
	function __construct( ) { }

	/**
	 */
	function __destruct( ) { }

	/**
	 * Renders given options as table elements.
	 * Html table opening and closing tags must be initialized outside function.
	 *
	 * @package WordPress
	 * @since 1.0
	 * @param string $optType
	 *        	Text, checkbox, etc
	 * @param string $optName
	 *        	Unique name
	 * @param string $optID
	 *        	Unique ID
	 * @param string $optValue
	 *        	Current value
	 * @param string $optMultiple
	 *        	Array with choises in case of multiple choises ('dropdown' or 'radio') or with html element atributes in other cases
	 * @return string
	 */
	public static function optArrayRenderer($erpOptions,  $optType, $optName, $optID, $optMultiple = FALSE ) {
		if ( $optType == 'checkbox' ) {
			if ( is_array( $optMultiple ) ) {
				$atrs = '';
				foreach ( $optMultiple as $atr => $value ) {
					$atrs .= ' ' . $atr . '="' . $value . '" ';
				}
			} else {
				$atrs = '';
			}
			$output = '<tr><td><label for="' . $optID . '">' . $optName . ' :' . '</label></td>';
			$output .= '<td><input class="erp-optchbx" id="' . $optID . '" name="' . $optID . '" type="' . $optType . '"' . 'value="' . (isset($erpOptions [ $optID ]) && !empty($erpOptions [ $optID ]) ? $erpOptions [ $optID ] : "true") . '" ' . $atrs . checked( $erpOptions [ $optID ], '1', FALSE ) . '/></td></tr>';
		} elseif ( $optType == 'text' || $optType == 'number' ) {
			if ( is_array( $optMultiple ) ) {
				$atrs = '';
				foreach ( $optMultiple as $atr => $value ) {
					$atrs .= ' ' . $atr . '="' . $value . '" ';
				}
			} else {
				$atrs = '';
			}
			$output = '<tr><td><label for="' . $optID . '">' . $optName . ' :' . '</label></td>';
			$output .= '<td><input class="erp-opttxt" id="' . $optID . '" name="' . $optID . '" type="' . $optType . '"' . $atrs . 'value="' . $erpOptions [ $optID ] . '"/></td></tr>';
		} elseif ( $optType == 'select' && is_array( $optMultiple ) ) {
			$id = $erpOptions [ $optID ];
			$output = '<tr><td><label for="' . $optID . '">' . $optName . ' :' . '</td>';
			$output .= '<td><select class="erp-optsel" id="' . $optID . '" name="' . $optID . '">';
			foreach ( $optMultiple as $key => $val ) {
				$valLow = strtolower( str_replace(',','',str_replace( ' ', '_', $val )) );
				$output .= '<option value="' . $valLow . '"' . selected( $id, $valLow, FALSE ) . '>' . $val . '</option>';
			}
			$output .= '</select></label></td></tr>';
		} elseif ( $optType == 'radio' && is_array( $optMultiple ) ) {
			$output = '<tr><td><label for="' . $optID . '">' . $optName . ' :' . '</label></td><td></td></tr>';
			foreach ( $optMultiple as $val ) {
				$valLow = strtolower( str_replace( ' ', '_', $val ) );
				$output .= '<tr><td></td><td><input type="' . $optType . '" name="' . $val . '" value="' . checked( $erpOptions [ $optID ], $val, FALSE ) . '"/></td></tr>';
			}
		}
		return $output;
	}
}

?>