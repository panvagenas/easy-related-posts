<?php

/**
 * Easy related posts .
 *
 * @package   Easy_Related_Posts_Core_display
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
erpPaths::requireOnce(erpPaths::$erpTemplates);

/**
 * Main plugin templates class
 *
 * @package Easy_Related_Posts_Core_display
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpMainTemplates extends erpTemplates {

    /**
     */
    function __construct() {
        parent::__construct();
        $this->templatesBasePath = parent::getTemplatesBasePath() . '/main';
    }

    /**
     */
    function __destruct() {
        parent::__destruct();
    }

    public function deleteTemplateOptions() {
        if (!$this->isLoaded() || !isset($this->optionsArrayName)) {
            return FALSE;
        }
        return delete_option($this->optionsArrayName);
    }

}

?>