<?php
echo 222;exit;
if (function_exists('eachSign')) {
    function eachSign($key, $value)
    {
        echo $key."--".$value;exit;
    }
}
?>