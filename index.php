<?php
function prent ($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
ini_set('display_errors', '1');
try {
    require_once("core2/inc/classes/Error.php");
    require_once("core2/inc/classes/Init.php");

    $init = new Init();
    $init->checkAuth();
    echo $init->dispatch();
} catch (Exception $e) {
    \Core2\Error::catchException($e);
}


