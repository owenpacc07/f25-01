<?php
    /*
        Contributor Spring 2023 - Dakota Marino
    */
    include '../../system.php'; 
    $post = json_decode(array_keys($_POST)[0]);
    $mid = '045';
    $type = $post->type;

    // Read flag-file
    if ($type == 0) {  
        $myfile = fopen($io_files_a . "/m-${mid}/flag-file.txt", "r") or die("Unable to open file!");
        $filecontent = fgets($myfile);
        echo $filecontent;
    }
    // Reset flag-file
    if ($type == 1) {
        $myfile = fopen($io_files_a . "/m-${mid}/flag-file.txt", "w") or die("Unable to open file!");
        fwrite($myfile, 0)."\n";
        echo "flag-file reset to 0";
    }

    fclose($myfile);
?>