<?php
/**
 * Carousel template settings.
 *
 * This file will be loaded in plugin settings page
 * when carousel template is sellected
 *
 * @package   Easy_Related_Posts_Templates_Main
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
?>
<table class="lay-opt-table">
    <tr>
	<td>
	    <label for="carouselAutoTime">Auto scroll time: </label>
	</td>
	<td>
            <input class="erp-opttxt" id="carouselAutoTime" name="carouselAutoTime" min="0" type="number"value="<?php echo $carouselAutoTime; ?>"/>
	</td>
    </tr>
    <tr>
	<td>
	    <label for="carouselMinVisible">Minimum visible items: </label>
	</td>
	<td>
            <input class="erp-opttxt" id="carouselMinVisible" name="carouselMinVisible" type="number" min="1" value="<?php echo $carouselMinVisible; ?>"/>
	</td>
    </tr>
    <tr>
	<td>
	    <label for="carouselMaxVisible">Max visible items: </label>
	</td>
	<td>
            <input class="erp-opttxt" id="carouselMaxVisible" name="carouselMaxVisible" type="number" min="2" value="<?php echo $carouselMaxVisible; ?>"/>
	</td>
    </tr>
    <tr>
	<td>
	    <label for="carouselPauseHover">Pause on hover: </label>
	</td>
	<td>
	    <input class="erp-optchbx" id="carouselPauseHover" name="carouselPauseHover" type="checkbox" <?php checked((bool) $carouselPauseHover); ?> />
	</td>
    </tr>
    <tr>
	<td>
	    <label for="thumbCaption">Use thumbnail captions: </label>
	</td>
	<td>
	    <input class="erp-optchbx" id="thumbCaption" name="thumbCaption" type="checkbox" <?php checked((bool) $thumbCaption); ?> />
	</td>
    </tr>
</table>