<?php
    // This file reads/updates the flag file of a given mechanism
    // TEMPLATE
    
    // $_POST is associative array - key is json string, value is null
    $post = json_decode(array_keys($_POST)[0]);
    $mid = '111'; // <-- change this depending on current mechanism
    $type = $post->type;

    // Read flag-file
    if ($type == 0) {  
        $myfile = fopen("/var/www/projects/f22-02/html/files/core/m-${mid}/flag-file.txt", "r") or die("Unable to open file!");
        $filecontent = fgets($myfile);
        echo $filecontent;
    }
    // Reset flag-file
    if ($type == 1) {
        $myfile = fopen("/var/www/projects/f22-02/html/files/core/m-${mid}/flag-file.txt", "w") or die("Unable to open file!");
        fwrite($myfile, 0)."\n";
        echo "flag-file reset to 0";
    }

    fclose($myfile);

?>
