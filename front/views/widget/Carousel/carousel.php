<?php
/**
 * @package   Easy_Related_Posts_Templates_Widget
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
$containerClass = $uniqueID . 'Container';
$thumbClass = $uniqueID . 'Thumbnail';
$titleClass = $uniqueID . 'PostTitle';
$excClass = $uniqueID . 'Exc';
$carouWraperClass = $uniqueID . 'CarouWraper';
$navUp = $uniqueID . 'NavUp';
$navDown = $uniqueID . 'NavDown';
?>
<div class="<?php echo $containerClass; ?>">
    <div class="erpNavArrow erpWidCarouPrev-<?php echo $widIDNumber; ?> <?php echo $navUp; ?>"
         style="width:100%;height:25px;background-image:url(<?php echo plugin_dir_url(__FILE__) . '/assets/arrow-up.png'; ?> ); border-radius:5px 5px 0 0;"></div>
    <ul class="erpProWidCarousel erpProWidUl-<?php echo $widIDNumber . ' ' . $carouWraperClass; ?>" style="width:100%;">
        <?php
        if (isset($posts)) {
            foreach ($posts as $k => $v) {
                ?>
                <li class="erpProWidRelContainer" style=""
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
        } // if (isset($posts))
        ?>
    </ul>
    <div class="erpNavArrow erpWidCarouNext-<?php echo $widIDNumber; ?> <?php echo $navDown; ?>"
         style="width:100%;height:25px;background-image:url(<?php echo plugin_dir_url(__FILE__) . '/assets/arrow-down.png'; ?> ); border-radius:0 0 5px 5px;"></div>
</div>
<script type="text/javascript" >
    (function($) {
        $(function() {
            $('.<?php echo $carouWraperClass; ?>').carouFredSel({
                prev: $('.<?php echo $navUp; ?>'),
                next: $('.<?php echo $navDown; ?>'),
                height: <?php echo $options['carouselHeight']; ?>,
                auto: <?php echo $options['carouselAutoTime'] > 0 ? $options['carouselAutoTime'] * 1000 : 'false'; ?>,
                items: {
                    visible: "variable",
                    height: "variable"
                },
                scroll: {
                    pauseOnHover: <?php echo (bool) $options['carouselPauseHover'] ? 'true' : 'false'; ?>
                },
                direction: "up"
            });
        });
    }(jQuery));
</script>