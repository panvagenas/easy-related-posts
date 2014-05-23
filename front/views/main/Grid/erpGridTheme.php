<?php

/*
 * Copyright (C) 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * erpTheme.php
 *
 * @package   @todo
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      @todo
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
erpPaths::requireOnce(erpPaths::$erpTheme);

/**
 * Description of erpTheme
 * 
 * @package @todo
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpGridTheme extends erpTheme{

    /**
     * The name of the theme
     * @var string 
     */
    protected $name = 'Grid';

    /**
     * A description for theme
     * @var string
     */
    protected $description = 'Display related in a grid';

    /**
     * An array name if you are going  to save options to DB
     * If no array name is defined then options wont get stored in DB. 
     * Instead they are validated and returned as an assoc array.
     * @var string Default is null 
     */
    protected $optionsArrayName = 'erpGridOptions';

    /**
     * An assoc array containing default theme options if any
     * @var array
     */
    protected $defOptions = array(
        'thumbCaption' => false,
        'numOfPostsPerRow' => 3,
        'backgroundColor' => '#ffffff',
        'borderColor' => '#ffffff',
        'borderRadius' => 0,
        'borderWeight' => 0
    );
    // TODO admin scripts should not be hooked here
    protected $css = array();
    protected $js = array();
    // TODO Preregistered admin scripts should not be hooked here
    protected $preregScripts = array(
        'css' => array('erp-bootstrap', 'erp-bootstrap-text', 'erp-erpCaptionCSS', 'wp-color-picker'),
        'js' => array('erp-erpCaptionJS', 'wp-color-picker')
    );
    
    /**
     * Type of theme eg main, widget etc
     * @var string
     */
    protected $type = 'main';

    /**
     * Always call the parent constructor at child classes
     */
    public function __construct() {
        $this->basePath = plugin_dir_path(__FILE__);
        parent::__construct();
    }

    public function validateSettings($options){
        $newOptions = array(
            'numOfPostsPerRow' => isset($options ['numOfPostsPerRow']) ? (int) $options ['numOfPostsPerRow'] : 3,
            'thumbCaption' => isset($options ['thumbCaption']),
            'backgroundColor' => isset($options['backgroundColor']) ? wp_strip_all_tags($options['backgroundColor']) : '#ffffff',
            'borderColor' => isset($options['borderColor']) ? wp_strip_all_tags($options['borderColor']) : '#ffffff',
            'borderRadius' => isset($options ['borderRadius']) && (int)$options['borderRadius'] >= 0 ? (int) $options ['borderRadius'] : 0,
            'borderWeight' => isset($options ['borderWeight']) && (int)$options['borderWeight'] >= 0 ? (int) $options ['borderWeight'] : 0,
        );
        return $newOptions;
    }

    public function render($path = '', Array $data = array(), $echo = false) {
        return parent::render(plugin_dir_path(__FILE__).'grid.php', $data, $echo);
    }
    
    public function renderSettings($filePath = '', $echo = false) {
        return parent::renderSettings(plugin_dir_path(__FILE__).'settings.php', $echo);
    }
}
