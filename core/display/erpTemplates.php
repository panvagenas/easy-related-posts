<?php

/**
 * Easy related posts .
 *
 * @package   Easy_Related_Posts_Core_display
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */

/**
 * Abstract class of plugin templates
 *
 * @package Easy_Related_Posts_Core_display
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
abstract class erpTemplates {
	/**
	 * Absolute path to templates folder
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $templatesBasePath;
	/**
	 * Name readen from xml
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $name;
	/**
	 * Description as readen from xml
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $description;
	/**
	 * Absolute path of settings options file
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $settingsFilePath;
	/**
	 * Absolute path to the file that represents the public face of the template
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $viewFilePath;
	/**
	 * Template options array as readen from xml file
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $options = array();
	/**
	 * Array name of the template options that will be used to store them in DB
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $optionsArrayName;
	/**
	 * Absolute path to the css file as readen from xml
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $cssFilePath;
	/**
	 * Absolute path to the js file as readen from xml
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $jsFilePath;
	/**
	 * Assoc array containing the validation function and path as readen from xml
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $optionSaveValidation;
	/**
	 * Absolute path to the tepmplate root folder
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $basePath;
	/**
	 * A unique number defined per instance
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $uniqueInstanceID = null;

	/**
	 */
	function __construct( ) {
		erpPaths::requireOnce(erpPaths::$erpView);
		$this->templatesBasePath = EPR_BASE_PATH . 'front/views';
	}

	/**
	 */
	function __destruct( ) {}

	public function isLoaded() {
		return !empty($this->uniqueInstanceID);
	}
	/**
	 * Get the absolute path to the template folder
	 *
	 * @param string $templateName
	 * @return string absolute path to the template folder
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getTemplatePath($templateName){
		// Get dirs in templates root folder
		erpPaths::requireOnce(erpPaths::$erpFileHelper);
		$templates = erpFileHelper::dirToArray($this->templatesBasePath);
		// Search for the given template name
		foreach ($templates as $k => $v){
			if (strnatcasecmp($v, $templateName) == 0) {
				return $this->templatesBasePath . '/'.$v;
			}
		}
	}
	/**
	 * Searches in the template folder to find an xml file
	 * @param string $templateName
	 * @return string Empty string if not found, path to the file otherwise. If folder contains more than one xml files, firtst one will be returned.
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since
	 */
	public function getTemplateXMLPath($templateName){
		// Check if required strings are set
		if (empty($templateName) || empty($this->templatesBasePath)) {
			return '';
		}

		$xmlFilePath = '';
		// Get contents of folder
		erpPaths::requireOnce(erpPaths::$erpFileHelper);
		$dirContents = erpFileHelper::filesToArray($this->basePath);
		// Search for an xml file
		foreach ($dirContents as $k => $v){
			// If we found one break the loop
			if (strpos($v, '.xml')) {
				$xmlFilePath = $this->basePath.'/'.$v;
				break;
			}
		}
		// Return result
		return $xmlFilePath;
	}
	/**
	 * Returns an array containg the name of folders in templates base path
	 * @return array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getTemplateNames() {
		// Load file helper
		erpPaths::requireOnce(erpPaths::$erpFileHelper);
		// Use it to return result
		return erpFileHelper::dirToArray($this->templatesBasePath);
	}
	/**
	 * Loads the template with the given name. This populates all insance required fields in order to function prooperly
	 * @param string $templateName
	 * @return erpTemplates|null
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function load($templateName){
		// define template base path
		$this->basePath = $this->getTemplatePath($templateName, $this->templatesBasePath);
		// Get xml path
		$templateXMLPath = $this->getTemplateXMLPath($templateName);
		if (empty($templateXMLPath)) {
			return null;
		}
		// initialize template components

		$xml = $this->getSimpleXMLInstc($templateXMLPath);

		if ($xml === null) {
			return null;
		}
		// read name
		if (isset($xml->name)) {
			$this->name = (string)$xml->name;
		} else {
			return null;
		}
		// read description
		if (isset($xml->description)) {
			$this->description = (string)$xml->description;
		}
		// read options
		if (isset($xml->options)) {
			$this->options = $this->xmlToArray($xml->options);
		}
		// set options array name
		if (isset($xml->optionsArrayName)) {
			$this->optionsArrayName = (string)$xml->optionsArrayName;
			$optionsInDB = get_option($this->optionsArrayName);
                        if($optionsInDB && is_array($optionsInDB)){
                            $this->setOptions($optionsInDB);
                        }
		} elseif (isset($xml->options)){
			$this->optionsArrayName = $this->name . 'TemplateOptions';
			$optionsInDB = get_option($this->optionsArrayName);
			if($optionsInDB && is_array($optionsInDB)){
                            $this->setOptions($optionsInDB);
                        }
		}

		if (isset($xml->preregisteredScripts)) {
			$this->enqueRegisteredScripts($xml->preregisteredScripts);
		}

		if ($this->setFilesPaths($xml, $templateXMLPath) === null) {
			return null;
		}

		// hook validation function
		if (isset($xml->optionSaveValidation) && !empty($this->options) && !empty($this->optionsArrayName) && is_admin()) {
			$this->optionSaveValidation = $this->xmlToArray($xml->optionSaveValidation);
			if (isset($this->optionSaveValidation['file']) && isset($this->optionSaveValidation['function'])) {
				require_once dirname($templateXMLPath).DIRECTORY_SEPARATOR.$this->optionSaveValidation['file'];
				add_filter('erpTemplateOptionsSaveValidation', $this->optionSaveValidation['function']);
			}
		}
		// Generate a unique id
		$this->uniqueInstanceID = uniqid(str_replace(' ', '_', $this->name));
		return $this;
	}

	protected function enqueRegisteredScripts(SimpleXMLElement $xml){
		if (isset($xml->css)) {
			$styles = $this->xmlToArray($xml->css);
			foreach ($styles as $key => $value) {
				wp_enqueue_style($value);
			}
		}
		if (isset($xml->js)) {
			$js = $this->xmlToArray($xml->js);
			foreach ($js as $key => $value) {
				wp_enqueue_script($value);
			}
		}
	}

	protected function setFilesPaths(SimpleXMLElement $xml, $templateXMLPath){
		// read view file path
		if (isset($xml->viewFilePath)) {
			$this->viewFilePath = dirname($templateXMLPath).DIRECTORY_SEPARATOR.(string)$xml->viewFilePath;
		} else {
			return null;
		}
		// read settings view file path
		if (isset($xml->settingsPageFilePath)) {
			$this->settingsFilePath = dirname($templateXMLPath).DIRECTORY_SEPARATOR.(string)$xml->settingsPageFilePath;
		}
		// get css file paths
		if (isset($xml->cssFilePath)) {
			$this->cssFilePath = $this->xmlToArray($xml->cssFilePath);
			$this->enqueCSS();
		}
		// read js file paths
		if (isset($xml->jsFilePath)) {
			$this->jsFilePath = $this->xmlToArray($xml->jsFilePath);
			$this->enqueJS();
		}
		return $this;
	}

	/**
	 * Creates a simplexml instance from the given file
	 *
	 * @param string $templateXMLPath
	 * @return SimpleXMLElement|NULL
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	protected function getSimpleXMLInstc($templateXMLPath) {
		// Read xml file
		try {
			$contents = file_get_contents($templateXMLPath);
			$xml = new SimpleXMLElement($contents);
			return $xml;
		} catch (Exception $e) {
			$er = new WP_Error();
			$er->add($e->getCode(), $e->getMessage());
			return null;
		}
	}
	/**
	 * Converts an xml ellement to an assoc array
	 * @param SimpleXMLElement $xml
	 * @return array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	protected function xmlToArray($xml){
            // JSON_NUMERIC_CHECK not avail before php V 5.3.3
            if(version_compare(phpversion(), '5.3.3' ) > -1){
                $json = json_encode($xml,JSON_NUMERIC_CHECK);
            } else {
                $json = json_encode($xml);
            }
		
		return (array)json_decode($json,TRUE);
	}
	/**
	 * Enques all css files as specified in xml file
	 * @return \display\erpTemplates
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	protected function enqueCSS(){
		if (isset($this->cssFilePath) && is_array($this->cssFilePath) && is_admin_bar_showing() && !is_admin() || !is_admin()) {
			$plugin = easyRelatedPosts::get_instance();
			foreach ($this->cssFilePath as $key => $value) {
				wp_enqueue_style(
				$key,
				$this->getUrl($value),
				array (), easyRelatedPosts::VERSION );
			}

		}
		return $this;
	}
	/**
	 * Get the url for a given file
	 * @param string $templateFileRelativePath Relative path from the template base path
	 * @return string Url
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	protected function getUrl($templateFileRelativePath){
		// Convert to absolute path
		$fullPath = $this->basePath.DIRECTORY_SEPARATOR.$templateFileRelativePath;
		// Split to parts
		$templateParts = explode(DIRECTORY_SEPARATOR, $fullPath);
		// Get base parts
		$baseParts = explode(DIRECTORY_SEPARATOR, rtrim(EPR_BASE_PATH, '/ '));
		// Remove ..
		array_pop($baseParts);
		// Get the matching elements
		$relativeToPluginBase = array_diff($templateParts, $baseParts);
		// Since we found the path relative to blog base path we ready to return
		return plugins_url(implode(DIRECTORY_SEPARATOR, $relativeToPluginBase));
	}
	/**
	 * Enques all js files as specified in xml file
	 * @return \display\erpTemplates
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	protected function enqueJS(){
		if (isset($this->jsFilePath) && is_array($this->jsFilePath) && is_admin_bar_showing() && !is_admin() || !is_admin()) {
			$plugin = easyRelatedPosts::get_instance();
			foreach ($this->jsFilePath as $key => $value) {
				if (is_array($value)) {
					wp_enqueue_script(
					$key,
					$this->getUrl($value['path']),
					$value['deps'],
					easyRelatedPosts::VERSION, false );
				} else {
					wp_enqueue_script(
					$key,
					$this->getUrl($value),
					array ( ),
					easyRelatedPosts::VERSION, false );
				}
			}
		}
		return $this;
	}
	/**
	 * Renders the settings. Return HTML string
	 * @param bool $echo Whether to echo result or not
	 * @return string|NULL HTML string or null
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function renderSettings($echo = false) {
		if (isset($this->settingsFilePath)) {
			return erpView::render($this->settingsFilePath, $this->options, $echo);
		}
		return null;
	}
	/**
	 *
	 * @param unknown $postData
	 * @param string $echo
	 * @return string|NULL
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since
	 */
	public function render($postData, $echo = FALSE){
		if (isset($this->viewFilePath) && !empty($this->options) && !empty($postData)) {
			return erpView::render($this->viewFilePath, $this->options+$postData, $echo);
		} elseif (!empty($postData)){
			return erpView::render($this->viewFilePath, $postData, $echo);
		}
		return null;
	}
	/**
	 * Sets vars to be passed to view and returns result
	 * @param WP_Query $wpq
	 * @param array $additionalOptions
	 * @param array $ratings
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function display(WP_Query $wpq, erpOptions $optionsObj, $ratings = array()){
		if (!$this->isLoaded()) {
			return '';
		}

		erpPaths::requireOnce(erpPaths::$erpPostData);

		$from = get_the_ID();

		$this->setOptions($optionsObj->getOptions());

		$data = array(
			'title' => $optionsObj->getValue('title'),
			'options' => $this->options,
			'uniqueID' => $this->uniqueInstanceID,
			'optionsObj' => $optionsObj,
			'posts' => array()
		);


		while ($wpq->have_posts()) {
			$wpq->the_post();

			$rating = isset($ratings[get_the_ID()]) ? $ratings[get_the_ID()] : null;

			$postData = new erpPostData($wpq->post, $optionsObj, $rating, $from);
                        
			if ($optionsObj->haveToShowExcerpt()) {
				$postData->setExcerpt($optionsObj->getValue('excLength'), $optionsObj->getValue('moreTxt'));
			}

			if ($optionsObj->haveToShowThumbnail()) {
				$postData->setThumbnail($optionsObj->getDefaultThumbnail());
			}

			array_push($data['posts'], $postData);
		}

		wp_reset_postdata();
		return $this->render($data);
	}
	/**
	 * Validates new options and saves them to DB
	 * @param array $newOptions Assoc array
	 * @return boolean
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function saveTemplateOptions($newOptions) {
		if (empty($newOptions)) {
			return false;
		}
		foreach ($newOptions as $k => $v){
			if (!array_key_exists($k, $this->options)) {
				unset($newOptions[$k]);
			}
		}
		if (!isset($this->optionsArrayName)) {
			$this->optionsArrayName = $this->name . 'TemplateOptions';
		}
		$this->setOptions(apply_filters('erpTemplateOptionsSaveValidation', $newOptions));
		update_option($this->optionsArrayName, $this->options);
	}
	
	/**
	 * Sets options in instance field
	 * @param array $options Assoc array of new opotions
	 * @return \display\erpTemplates
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function setOptions($options){
		$this->options = array_merge((array)$this->options, (array)$options);
		return $this;
	}
	/**
	 * Returns template name
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * Returns template description
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getDescription() {
		return $this->description;
	}
	/**
	 * Returns template settigs file path
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getSettingsFilePath() {
		return $this->settingsFilePath;
	}
	/**
	 * Returns template public view file path
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getViewFilePath() {
		return $this->viewFilePath;
	}
	/**
	 * Returns template options
	 * @return array Assoc array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since 1.0.0
	 */
	public function getOptions() {
		return $this->options;
	}

	public function getTemplatesBasePath() {
		return $this->templatesBasePath;
	}

}