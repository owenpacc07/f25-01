<?php

    $defaultIn = fopen("../../files/p1-cpu-input.txt", "w") or die("Unable to open file!");

    fwrite($defaultIn, 1);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 0);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 5);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 3);
    fwrite($defaultIn, "\n");

    fwrite($defaultIn, 2);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 1);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 7);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 5);
    fwrite($defaultIn, "\n");

    fwrite($defaultIn, 3);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 2);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 3);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 4);
    fwrite($defaultIn, "\n");

    fwrite($defaultIn, 4);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 3);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 10);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 2);
    fwrite($defaultIn, "\n");

    fwrite($defaultIn, 5);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 4);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 9);
    fwrite($defaultIn, ",");
    fwrite($defaultIn, 1);

    fclose($defaultIn);



    //FCFS
    $defaultFCFS = fopen("../../files/p1-cpu-output-fcfs.txt","w") or die("Unable to open file!");

    fwrite($defaultFCFS, 1);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 0);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 5);
    fwrite($defaultFCFS, "\n");

    fwrite($defaultFCFS, 2);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 5);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 12);
    fwrite($defaultFCFS, "\n");

    fwrite($defaultFCFS, 3);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 12);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 15);
    fwrite($defaultFCFS, "\n");

    fwrite($defaultFCFS, 4);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 15);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 25);
    fwrite($defaultFCFS, "\n");

    fwrite($defaultFCFS, 5);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 25);
    fwrite($defaultFCFS, ",");
    fwrite($defaultFCFS, 34);

    fclose($defaultFCFS);



    //SJF:NON
    $defaultSJFnon = fopen("../../files/p1-cpu-output-sjf.txt","w") or die("Unable to open file!");

    fwrite($defaultSJFnon, 1);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 0);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 5);
    fwrite($defaultSJFnon, "\n");

    fwrite($defaultSJFnon, 3);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 5);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 8);
    fwrite($defaultSJFnon, "\n");

    fwrite($defaultSJFnon, 2);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 8);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 15);
    fwrite($defaultSJFnon, "\n");

    fwrite($defaultSJFnon, 5);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 15);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 24);
    fwrite($defaultSJFnon, "\n");

    fwrite($defaultSJFnon, 4);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 24);
    fwrite($defaultSJFnon, ",");
    fwrite($defaultSJFnon, 34);

    fclose($defaultSJFnon);



    //SJF:PRE
    
    $defaultSJFpre = fopen("../../files/p1-cpu-output-sjf-p.txt", "w") or die("Unable to open file!");

    fwrite($defaultSJFpre, 1);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 0);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 2);
    fwrite($defaultSJFpre, "\n");

    fwrite($defaultSJFpre, 3);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 2);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 5);
    fwrite($defaultSJFpre, "\n");

    fwrite($defaultSJFpre, 1);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 5);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 8);
    fwrite($defaultSJFpre, "\n");

    fwrite($defaultSJFpre, 2);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 8);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 15);
    fwrite($defaultSJFpre, "\n");

    fwrite($defaultSJFpre, 5);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 15);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 24);
    fwrite($defaultSJFpre, "\n");

    fwrite($defaultSJFpre, 4);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 24);
    fwrite($defaultSJFpre, ",");
    fwrite($defaultSJFpre, 34);
    

    fclose($defaultSJFpre);





    //PRIO:NON
    $defaultPRIOnon = fopen("../../files/p1-cpu-output-priority.txt","w") or die("Unable to open file!");

    fwrite($defaultPRIOnon, 1);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 0);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 5);
    fwrite($defaultPRIOnon, "\n");

    fwrite($defaultPRIOnon, 5);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 5);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 14);
    fwrite($defaultPRIOnon, "\n");

    fwrite($defaultPRIOnon, 4);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 14);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 24);
    fwrite($defaultPRIOnon, "\n");

    fwrite($defaultPRIOnon, 2);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 24);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 27);
    fwrite($defaultPRIOnon, "\n");

    fwrite($defaultPRIOnon, 3);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 27);
    fwrite($defaultPRIOnon, ",");
    fwrite($defaultPRIOnon, 34);
    

    fclose($defaultPRIOnon);


    //PRIO:PRE
    $defaultPRIOpre = fopen("../../files/p1-cpu-output-priority-p.txt","w") or die("Unable to open file!");

    fwrite($defaultPRIOpre, 1);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 0);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 3);
    fwrite($defaultPRIOpre, "\n");

    fwrite($defaultPRIOpre, 4);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 3);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 4);
    fwrite($defaultPRIOpre, "\n");

    fwrite($defaultPRIOpre, 5);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 4);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 13);
    fwrite($defaultPRIOpre, "\n");

    fwrite($defaultPRIOpre, 4);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 13);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 22);
    fwrite($defaultPRIOpre, "\n");

    fwrite($defaultPRIOpre, 1);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 22);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 24);
    fwrite($defaultPRIOpre, "\n");

    fwrite($defaultPRIOpre, 3);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 24);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 27);
    fwrite($defaultPRIOpre, "\n");

    fwrite($defaultPRIOpre, 2);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 27);
    fwrite($defaultPRIOpre, ",");
    fwrite($defaultPRIOpre, 34);

    fclose($defaultPRIOpre);



    //RR
    $defaultRR = fopen("../../files/p1-cpu-output-roundrobin.txt", "w") or die("Unable to open file!");

    fwrite($defaultRR, 1);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 0);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 4);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 2);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 4);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 8);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 3);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 8);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 11);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 4);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 11);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 15);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 5);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 15);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 19);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 1);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 19);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 20);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 2);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 20);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 23);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 4);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 23);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 27);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 5);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 27);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 31);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 4);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 31);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 33);
    fwrite($defaultRR, "\n");

    fwrite($defaultRR, 5);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 33);
    fwrite($defaultRR, ",");
    fwrite($defaultRR, 34);
    
    fclose($defaultRR);

    header("Location: index.html")

?>
