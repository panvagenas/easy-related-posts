<?php

/**
 * Easy related posts .
 *
 * @package   Easy_Related_Posts
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */

/**
 * Activator class.
 *
 * @package Easy_Related_Posts
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpActivator {

    /**
     * Checks the options names from array1 if they are pressent in array2
     *
     * @param array $array1 Associative options array (optionName => optionValue)
     * @param array $array2 Associative options array (optionName => optionValue)
     * @return array An array containing the options names that are present only in array1
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since 2.0.0
     */
    public static function optionArraysDiff(Array $array1, Array $array2) {
        $keys1 = array_keys($array1);
        $keys2 = array_keys($array2);
        return array_diff($keys1, $keys2);
    }

    /**
     * Inserts to main options array in DB values that are present in $newOpts and not in $oldOpts
     *
     * @param array $newOpts New options array
     * @param array $oldOpts Old options array, default to main options present in DB
     * @param string $optsName Options name, default to erp main options array
     * @return boolean True if operation was succefull, false otherwise
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since 2.0.0
     */
    public static function addNonExistingMainOptions(Array $newOpts, $optsName, Array $oldOpts = NULL) {
        if (!is_string($optsName)) {
            return FALSE;
        }
        if (empty($oldOpts)) {
            $oldOpts = get_option($optsName);
        }
        $merged = is_array($oldOpts) ? $oldOpts + $newOpts : $newOpts;
        return update_option($optsName, $merged);
    }

    /**
     * Inserts non existing widget options in DB that are present in $newOpts and not in $oldOpts
     * @param array $newOpts New options array
     * @param array $oldOpts Old options array, default to widget options present in DB
     * @param string $optsName Options name, default to erp widget options array
     * @return boolean False if operation was successfull, false otherwise
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since 2.0.0
     */
    public static function addNonExistingWidgetOptions(Array $newOpts, $optsName, Array $oldOpts = NULL) {
        if (!is_string($optsName)) {
            return FALSE;
        }
        if (empty($oldOpts)) {
            $oldOpts = get_option($optsName);
        }
        if (empty($oldOpts)) {
            return add_option($optsName, array(1 => $newOpts));
        }
        foreach ($oldOpts as $k => $v) {
            if (is_array($v) && isset($v['title'])) {
                $oldOpts[$k] = $oldOpts[$k] + $newOpts;
            }
        }
        return update_option($optsName, $oldOpts);
    }

}
