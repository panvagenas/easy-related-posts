<?php
/**
 * @package   Easy_Related_Posts_Templates_Main
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
$containerClass = $uniqueID . 'Container';
$titleClass = $uniqueID . 'PostTitle';
$excClass = $uniqueID . 'Exc';

$style = '';
if (isset($options['borderWeight']) && $options['borderWeight'] > 0) {
    $style .= ' border: ' . $options['borderWeight'] . 'px solid; ';
}
if (isset($options['borderRadius']) && $options['borderRadius'] > 0) {
    $style .= ' border-radius:  ' . $options['borderRadius'] . 'px; ';
}
if (isset($options['borderColor']) && $options['borderColor'] != '#ffffff') {
    $style .= ' border-color: ' . $options['borderColor'] . '; ';
}
?>
<div class="<?php echo $containerClass; ?>" style="<?php echo $style; ?>">
            <h2 class="erpProTitle col-md-12" style="line-height: 1.4;"><?php if (isset($title)) echo $title; ?></h2>
        <?php
        if (isset($posts)) {
            if($options['orderedList'] == true){
                echo '<ol>';
            } else {
                echo '<ul>';
            }
            foreach ($posts as $k => $v) {
                ?>
                <li class=""
                <?php
                if (current_user_can('activate_plugins')) {
                    echo 'title="Rating: ' . $v->getRating() . ' Post date: ' . $v->getTheTime() . '"';
                }
                ?>
                     >
                    <a href="<?php echo $v->getPermalink() ?>" class="erpProPostLink" rel="nofollow">
                        <?php
                        foreach ($options['content'] as $key => $value) {
                            include plugin_dir_path(__FILE__) . 'components/' . $value . '.php';
                        }
                        ?>
                    </a>
                </li>
                <?php
            } // foreach ($posts as $k => $v)
            if($options['orderedList'] == true){
                echo '</ol>';
            } else {
                echo '</ul>';
            }
        } // if (isset($posts))
        ?>
</div>