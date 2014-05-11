<?php

function validateMainListOptions($options) {
    $newOptions = array(
        'orderedList' => isset($options['orderedList']) ? true : false,
        'borderColor' => isset($options['borderColor']) ? wp_strip_all_tags($options['borderColor']) : '#ffffff',
        'borderRadius' => isset($options ['borderRadius']) && (int)$options['borderRadius'] >= 0 ? (int) $options ['borderRadius'] : 0,
        'borderWeight' => isset($options ['borderWeight']) && (int)$options['borderWeight'] >= 0 ? (int) $options ['borderWeight'] : 0,
    );
    return $newOptions;
}
