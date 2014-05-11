<?php

function validateOptionSaveWidBasic($options) {
    $newOptions = array(
        'thumbCaption' => isset($options ['thumbCaption']) ? true : false
    );
    return $newOptions;
}
