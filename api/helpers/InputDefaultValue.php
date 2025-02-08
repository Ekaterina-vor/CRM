<?php

function InputDefaultValue($field, $defaultValue) {
    if (isset($_GET[$field])) {
        $input = !empty($_GET[$field]) ? $_GET[$field] : $defaultValue;

        echo "value='$input'";
    } else {
        echo '';
    }
}

?>