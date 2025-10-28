<?php 

    $response = $_POST['text'];
    $schedulerType = $response . "";
    // Updates the in.dat dile's first character, setting a new scheduler type
    // shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/cpu-v3 Write " . $schedulerType . " /var/www/projects/f22-02/html/files/p3/in.dat");

    // Recalculates the output for the new scheduler type and writes the output to out.dat
    switch ($schedulerType){
        case 0:
            shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-001 m001");
            break;
        case 1:
            shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-002 m002");
            break;
        case 2:
            shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-003 m003");
            break;
        case 3:
            shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-004 m004");
            break;
        case 4:
            shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-005 m005");
            break;
        case 5:
            shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-006 m006");
            break;
        case 6:
            shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-007 m007");
            break;
        case 7:
            shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/core/m-008 m008");
            break;
        default:
            echo "Error with Scheduler Type";
    }
    
    //shell_exec("java -classpath /var/www/projects/f22-02/html/cgi-bin/cpu-v3 Scheduler");
    echo $schedulerType;
   
?>