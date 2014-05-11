<?php
/**
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
?>
<label for="<?php echo $widgetInstance->get_field_id('carouselPauseHover'); ?>">Pause on hover: </label>
<input class="erp-optchbx" id="<?php echo $widgetInstance->get_field_id('carouselPauseHover'); ?>" name="<?php echo $widgetInstance->get_field_name('carouselPauseHover'); ?>" type="checkbox" <?php checked((bool) $carouselPauseHover); ?> />
<br>
<label for="<?php echo $widgetInstance->get_field_id('carouselAutoTime'); ?>">Auto rotation time: </label>
<input class="" id="<?php echo $widgetInstance->get_field_id('carouselAutoTime'); ?>" name="<?php echo $widgetInstance->get_field_name('carouselAutoTime'); ?>" type="number" value="<?php echo $carouselAutoTime; ?>" />
<br>
<label for="<?php echo $widgetInstance->get_field_id('carouselHeight'); ?>">Overall height: </label>
<input class="" id="<?php echo $widgetInstance->get_field_id('carouselHeight'); ?>" name="<?php echo $widgetInstance->get_field_name('carouselHeight'); ?>" type="number" value="<?php echo $carouselHeight; ?>" />
