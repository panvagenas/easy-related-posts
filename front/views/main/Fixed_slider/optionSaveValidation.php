<?php

function validateMainFixedSliderOptions( $options ) {
	$newOptions = array ();

	if (isset($options [ 'position' ])) {
		$newOptions['position'] = strip_tags($options [ 'position' ]);
	}
	if (isset($options [ 'backgroundColor' ])) {
		$newOptions['backgroundColor'] = strip_tags($options [ 'backgroundColor' ]);
	}
	if (isset($options [ 'backgroundTransparency' ]) && $options [ 'backgroundTransparency' ] > 0 &&  $options [ 'backgroundTransparency' ] <= 1) {
		$newOptions['backgroundTransparency'] = (float)$options [ 'backgroundTransparency' ];
	}
	if (isset( $options [ 'triggerAfter' ] ) && $options [ 'triggerAfter' ] > 0 &&  $options [ 'triggerAfter' ] <= 1) {
		$newOptions['triggerAfter'] = (float)$options['triggerAfter'];
	}
	if (isset( $options [ 'numOfPostsPerRow' ] ) && $options [ 'numOfPostsPerRow' ] > 0) {
		$newOptions['numOfPostsPerRow'] = (int)$options['numOfPostsPerRow'];
	}
	$newOptions['thumbCaption'] = isset( $options [ 'thumbCaption' ] ) ? true : false;
	return $newOptions;
}