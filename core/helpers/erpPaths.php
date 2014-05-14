<?php

/**
 *
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpPaths {
	// Helpers
	public static $erpFileHelper = 'core/helpers/erpFileHelper.php';
	// Display
	public static $erpPostData = 'core/display/erpPostData.php';
	public static $erpView = 'core/display/erpView.php';
	public static $erpMainTemplates = 'core/display/erpMainTemplates.php';
	public static $erpTemplates = 'core/display/erpTemplates.php';
	public static $erpWidTemplates = 'core/display/erpWidTemplates.php';
	// Admin
	public static $easyRelatedPostsAdmin = 'admin/easyRelatedPostsAdmin.php';
	public static $erpActivator = 'admin/erpActivator.php';
	public static $erpWidget = 'admin/erpWidget.php';
	// Includes
	public static $bfiResizer = 'includes/bfi_thumb.php';
	// Options
	public static $erpWidOpts = 'core/options/erpWidOpts.php';
	public static $erpOptions = 'core/options/erpOptions.php';
	public static $erpMainOpts = 'core/options/erpMainOpts.php';
	public static $erpDefaults = 'core/options/erpDefaults.php';
	// Related
	public static $erpQueryFormater = 'core/related/erpQueryFormater.php';
	public static $erpProRelated = 'core/related/erpProRelated.php';
	public static $erpRelData = 'core/related/erpRelData.php';
	public static $erpRatingSystem = 'core/related/erpRatingSystem.php';
	// Front
	public static $easyRelatedPosts = 'front/easyRelatedPosts.php';



	public static function requireOnce($path) {
		require_once EPR_BASE_PATH . $path;
	}
}