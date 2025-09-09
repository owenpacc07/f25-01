<?php

    $code = $_POST['code'];
    $file = fopen("code.html","w");
    fwrite($file,$code);
    fclose($file);
?>
