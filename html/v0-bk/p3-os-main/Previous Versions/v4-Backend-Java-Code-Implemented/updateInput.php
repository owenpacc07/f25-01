<?php 

    $response = $_POST['text'];
    $schedulerType = $response . "";
    // Updates the in.dat dile's first character, setting a new scheduler type
    shell_exec("java -classpath /var/www/projects/s22-02/html/cgi-bin/cpu-v3 Write " . $schedulerType . " /var/www/projects/s22-02/html/files/p3/in.dat");

    // Recalculates the output for the new scheduler type and writes the output to out.dat
    shell_exec("java -classpath /var/www/projects/s22-02/html/cgi-bin/cpu-v3 Scheduler");
    echo $schedulerType;
   
?>