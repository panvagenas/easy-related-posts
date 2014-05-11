<?php
erpPaths::requireOnce(erpPaths::$erpTemplates);
/**
 *
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpWidTemplates extends erpTemplates {
	/**
	 * Widget number
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $widIDNumber;

	/**
	 */
	function __construct( $widIDNumber = null ) {
		parent::__construct();
		$this->widIDNumber = $widIDNumber;
		$this->templatesBasePath = parent::getTemplatesBasePath() . '/widget';
	}

	/**
	 */
	function __destruct( ) { }
	/**
	 * Render setting for given instance
	 * @see erpTemplates::renderSettings()
	 * @since 1.0.0
	 */
	public function renderSettings($widInstance = false) {
		$widIns = array('widgetInstance'=>$widInstance);
		return erpView::render($this->settingsFilePath, array_merge($this->getOptions(), $widIns), false);
	}
	/**
	 * Render contend for wid insance
	 * @see \display\erpTemplates::render()
	 * @since 1.0.0
	 */
	public function render($postData, $echo = FALSE){
		$postData['widIDNumber'] = $this->widIDNumber;
		return parent::render($postData, $echo);
	}
	/**
	 * Save options for given instance
	 * @see \display\erpTemplates::saveTemplateOptions()
	 * @since 1.0.0
	 */
	public function saveTemplateOptions($newOptions) {
		if (empty($newOptions) ) {
			return array();
		}
		foreach ($newOptions as $k => $v){
			if (!array_key_exists($k, $this->options)) {
				unset($newOptions[$k]);
			}
		}
		$this->setOptions(apply_filters('erpTemplateOptionsSaveValidation', $newOptions));
		return $this->options;
	}
}

?>