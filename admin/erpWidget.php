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
 * Widget class.
 *
 * @package Easy_Related_Posts
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpWidget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(erpDefaults::erpWidgetOptionsArrayName, 'Easy Related Posts', array(
            'description' => __('Show related posts ')
                ), array(
            'width' => 500
        ));
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args
     *        	Widget arguments.
     * @param array $instance
     *        	Saved values from database.
     * @since 2.0.0
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     */
    public function widget($args, $instance) {
        global $post;
        // get instance of main plugin
        $plugin = easyRelatedPosts::get_instance();
        // check if it's time to take action
        if (is_single($post->ID)) {
            if ($plugin->isInExcludedPostTypes($post) || $plugin->isInExcludedTaxonomies($post)) {
                return;
            }
            // Fill missing options
            if (empty($instance)) {
                $instance = erpDefaults::$comOpts + erpDefaults::$widOpts;
            } else {
                $instance = $instance + erpDefaults::$comOpts + erpDefaults::$widOpts;
            }

            erpPaths::requireOnce(erpPaths::$erpRelated);
            erpPaths::requireOnce(erpPaths::$erpMainOpts);
            erpPaths::requireOnce(erpPaths::$erpWidOpts);

            $mainOpts = new erpMainOpts();

            $instance ['tags'] = $mainOpts->getTags();
            $instance ['categories'] = $mainOpts->getCategories();
            $instance ['postTypes'] = $mainOpts->getPostTypes();

            $widOpts = new erpWidOpts($instance);

            // Get related
            $relatedObj = erpRelated::get_instance($widOpts);
            $wpQ = $relatedObj->getRelated($post->ID);
            // If we have some posts to show
            if ($wpQ->have_posts()) {
                // Get template instance for the specific widget number
                erpPaths::requireOnce(erpPaths::$erpWidTemplates);
                $template = new erpWidTemplates($this->number);
                // load template
                $template->load($instance ['dsplLayout']);
                // If template isn't found display empty widget
                if (!$template->isLoaded()) {
                    return $this->displayEmptyWidget($args, $instance);
                }
                // else display rel content
                echo $args ['before_widget'];
                echo $args ['before_title'] . $instance ['title'] . $args ['after_title'];
                echo $template->display($wpQ, $widOpts, $relatedObj->getRatingsFromRelDataObj());
                echo $args ['after_widget'];
            } else {
                // else diplay empty widget
                $this->displayEmptyWidget($args, $instance);
            }
        }
    }

    /**
     * Back-end widget form.
     * Outputs the options form on admin
     *
     * @see WP_Widget::form()
     *
     * @param array $instance
     *        	Previously saved values from database.
     * @since 1.0
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     */
    public function form($instance) {
        // Fill missing options
        if (empty($instance)) {
            $instance = erpDefaults::$comOpts + erpDefaults::$widOpts;
        } else {
            $instance = $instance + erpDefaults::$comOpts + erpDefaults::$widOpts;
        }

        // Pass it to viewData
        erpPaths::requireOnce(erpPaths::$erpView);
        $widgetInstance = $this;
        $optionsTemplate = EPR_BASE_PATH . 'admin/views/widgetSettings.php';
        erpView::render($optionsTemplate, array(
            'options' => $instance,
            'widgetInstance' => $widgetInstance
                ), true);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance
     *        	Values just sent to be saved.
     * @param array $old_instance
     *        	Previously saved values from database.
     * @return array Updated safe values to be saved.
     * @since 2.0.0
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     */
    public function update($new_instance, $old_instance) {
        /* #? Verify nonce */
        if (!isset($_POST ['erp_meta_box_nonce']) || !wp_verify_nonce($_POST ['erp_meta_box_nonce'], 'erp_meta_box_nonce')) {
            return;
        }
        erpPaths::requireOnce(erpPaths::$erpWidOpts);
        erpPaths::requireOnce(erpPaths::$erpWidTemplates);

        // get an instance to validate options
        $widOpts = new erpWidOpts($old_instance);
        // validate wid options
        $widOptsValidated = $widOpts->saveOptions($new_instance, $old_instance);
        // validate template options
        $template = new erpWidTemplates();
        $template->load($new_instance ['dsplLayout']);

        if ($template->isLoaded()) {
            $tempalteOptionsValidated = $template->saveTemplateOptions($new_instance);
        } else {
            $tempalteOptionsValidated = array();
        }
        // save updated options
        return array_merge($widOptsValidated, $tempalteOptionsValidated);
    }

    /**
     * Just echoes an empty widget.
     *
     * @param array $args
     * @param array $instance
     *
     * @since 2.0.0
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     */
    private function displayEmptyWidget($args, $instance) {
        if (!$instance ['hideIfNoPosts']) {
            echo $args ['before_widget'];
            echo $args ['before_title'] . $instance ['title'] . $args ['after_title'];
            echo 'No related posts found';
            echo $args ['after_widget'];
        }
    }

}
