<?php

    $sentdata = $_POST['text'];

    // Read flag-file
    if ($sentdata == 0) {
        $myfile = fopen("/var/www/projects/f22-02/html/files/core/m-002/flag-file.txt", "r") or die("Unable to open file!");
        $filecontent = fgets($myfile);
        fclose($myfile);

        echo $filecontent;
    }
    // Reset flag-file
    if ($sentdata == 1) {
        $reset = 0;

        $myfile = fopen("/var/www/projects/f22-02/html/files/core/m-002/flag-file.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $reset);
        fclose($myfile);

        echo "done";
    }

?>