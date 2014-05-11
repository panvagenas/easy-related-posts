<?php

function validateMainGridOptions($options) {
    $newOptions = array(
        'numOfPostsPerRow' => isset($options ['numOfPostsPerRow']) ? (int) $options ['numOfPostsPerRow'] : 3,
        'thumbCaption' => isset($options ['thumbCaption']) ? true : false,
        'backgroundColor' => isset($options['backgroundColor']) ? wp_strip_all_tags($options['backgroundColor']) : '#ffffff',
        'borderColor' => isset($options['borderColor']) ? wp_strip_all_tags($options['borderColor']) : '#ffffff',
        'borderRadius' => isset($options ['borderRadius']) && (int)$options['borderRadius'] >= 0 ? (int) $options ['borderRadius'] : 0,
        'borderWeight' => isset($options ['borderWeight']) && (int)$options['borderWeight'] >= 0 ? (int) $options ['borderWeight'] : 0,
    );
    return $newOptions;
}
