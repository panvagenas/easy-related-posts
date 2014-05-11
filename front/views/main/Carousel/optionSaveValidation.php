<?php

function validateMainCarouselOption( $options ) {
	$newOptions = array ();

	if (isset($options [ 'carouselAutoTime' ]) &&  $options [ 'carouselAutoTime' ] > -1) {
		$newOptions['carouselAutoTime'] = (float)$options [ 'carouselAutoTime' ];
	}
	if (isset($options [ 'carouselMinVisible' ]) && $options [ 'carouselMinVisible' ] > 0) {
		$newOptions['carouselMinVisible'] = (int)$options [ 'carouselMinVisible' ];
	}
	if (isset($options [ 'carouselMaxVisible' ]) && isset($options [ 'carouselMinVisible' ]) &&  (int)$options [ 'carouselMaxVisible' ] > (int)$options [ 'carouselMinVisible' ]) {
		$newOptions['carouselMaxVisible'] = (int)$options [ 'carouselMaxVisible' ];
	}
	if (isset( $options [ 'carouselPauseHover' ] )) {
		$newOptions['carouselPauseHover'] = true;
	} else {
		$newOptions['carouselPauseHover'] = false;
	}

	$newOptions['thumbCaption'] = isset( $options [ 'thumbCaption' ] ) ? true : false;

	return $newOptions;
}