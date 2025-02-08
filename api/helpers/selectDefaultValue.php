<?php
function SelectDefaultValue($field, $options, $defaultValue) {
    

}

foreach ($options as $key => $option) {
    $key = $option['key'];
    $value = $option['value'];
    $Selected = '';

    if (isset($_GET[$field]) && $_GET[$field] === $key) {
        $selected = 'selected';
    }
    if (isset($_GET[$field]) && $_GET[$field] === $defaultValue) {
        $selected = 'selected';
    }


    echo "<option value='$selected' value='$key'>$value</option>";

    

}
?>