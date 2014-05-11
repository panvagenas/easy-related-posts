<?php
?>
<img
	src="<?php echo $v->getThumbnail($options['thumbnailHeight'],$options['thumbnailWidth'],$options['cropThumbnail']); ?>"
	class="erpProThumb <?php echo $thumbClass; ?>"
	data-caption="<?php echo str_replace('"', "'", $v->getTitle());?>">

