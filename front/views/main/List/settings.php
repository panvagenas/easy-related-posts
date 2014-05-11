<?php
/**
 * Grid template settings.
 *
 * This file will be loaded in plugin settings page
 * when grid template is sellected
 *
 * @package   Easy_Related_Posts_Templates_Main
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
?>
<table class="lay-opt-table">
    <tr>
        <td>
            <label for="orderedList">Ordered list: </label>
        </td>
        <td>
            <input class="erp-optchbx" id="orderedList" name="orderedList" type="checkbox" <?php checked((bool) $orderedList); ?> />
        </td>
    </tr>
    <tr>
        <td>
            <label for="borderColor">Border color: </label>
        </td>
        <td>
            <input class="erp-opttxt wp-color-picker-field" data-default-color="#ffffff" id="borderColor" name="borderColor" type="text" value="<?php echo $borderColor; ?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="borderWeight">Border weight: </label>
        </td>
        <td>
            <input class="erp-opttxt" id="borderWeight" name="borderWeight" type="number" min="0" value="<?php echo $borderWeight; ?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="borderRadius">Border radius: </label>
        </td>
        <td>
            <input class="erp-opttxt" id="borderRadius" name="borderRadius" type="number" min="0" value="<?php echo $borderRadius; ?>" />
        </td>
    </tr>
</table>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#backgroundColor').wpColorPicker();
        $('#borderColor').wpColorPicker();
    });
</script>