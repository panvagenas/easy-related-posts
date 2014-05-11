<?php

function validateOptionSaveWidCarousel( $options ) {
	$newOptions = array (
			'carouselPauseHover' => isset( $options [ 'carouselPauseHover' ] ) ? true : false
	);
	if (isset($options['carouselAutoTime']) && $options['carouselAutoTime'] >= 0) {
		$newOptions['carouselAutoTime'] = $options['carouselAutoTime'] > 0 ? $options['carouselAutoTime'] : false;
	}
	if (isset($options['carouselHeight']) && $options['carouselHeight'] >= 0) {
		$newOptions['carouselHeight'] = $options['carouselHeight'];
	}
	return $newOptions;
}