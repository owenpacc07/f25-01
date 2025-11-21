<?php 
    session_start();
    //if no user is logged in, go to login page
    if($_SESSION['logged_in'] != 1) {
        header('Location: login.php');
        
    }
        
?>
<!--this display function is called later on if an admin user is logged in. The html in this function is the contents of the view.php page 
and is a table containing the algorithm, inputs and outputs.  -->
<?php function display() { ?>
<br>
<p class="title has-text-link pl-5">INPUT and OUTPUT for Algorithms:</p>

<table class="table is-bordered is-striped is-hoverable">
    <tr>
        <th>ALGORITHM</th>
        <th>INPUT</th>
        <th>OUTPUT</th>
    </tr>
    <tbody>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editCPU.php">CPU Scheduling</a><br><br><br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f22-02/v2/1-cpu/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f22-02/html/files/p1-cpu-input.txt"); ?></th>
            <th><span style="color: green;">fcfs:</span> <?php readfile("/var/www/projects/f22-02/html/files/p1-cpu-output-fcfs.txt"); ?>
            <br><span style="color: green;">sjf:</span> <?php readfile("/var/www/projects/f22-02/html/files/p1-cpu-output-sjf.txt"); ?>
            <br><span style="color: green;">sjf-p:</span> <?php readfile("/var/www/projects/f22-02/html/files/p1-cpu-output-sjf-p.txt"); ?>
            <br><span style="color: green;">priority:</span> <?php readfile("/var/www/projects/f22-02/html/files/p1-cpu-output-priority.txt"); ?>
            <br><span style="color: green;">priority-p:</span> <?php readfile("/var/www/projects/f22-02/html/files/p1-cpu-output-priority-p.txt"); ?>
            <br><span style="color: green;">round robin:</span><?php readfile("/var/www/projects/f22-02/html/files/p1-cpu-output-roundrobin.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editMemory.php">Memory Allocation</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f22-02/v2/4-memory/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f22-02/html/files/p3-memory-input.txt"); ?> </th>
            <th><span style="color: green;">firstfit:</span> <?php readfile("/var/www/projects/f22-02/html/files/p3-memory-output-firstfit.txt"); ?>
            <br><span style="color: green;">bestfit:</span> <?php readfile("/var/www/projects/f22-02/html/files/p3-memory-output-bestfit.txt"); ?>
            <br><span style="color: green;">worstfit:</span> <?php readfile("/var/www/projects/f22-02/html/files/p3-memory-output-worstfit.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editPageReplacement.php">Page Replacement</a>
            <br><br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f22-02/v2/2-replace/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f22-02/html/files/p4-page-input.txt"); ?> </th>
            <th><span style="color: green;">fifo:   </span> <?php readfile("/var/www/projects/f22-02/html/files/p4-page-output-fifo.txt"); ?>
            <br><span style="color: green;">lru:    </span> <?php readfile("/var/www/projects/f22-02/html/files/p4-page-output-lru.txt"); ?>
            <br><span style="color: green;">optimal:</span> <?php readfile("/var/www/projects/f22-02/html/files/p4-page-output-optimal.txt"); ?>
            <br><span style="color: green;">lfu:    </span> <?php readfile("/var/www/projects/f22-02/html/files/p4-page-output-lfu.txt"); ?>
            <br><span style="color: green;">mfu:    </span> <?php readfile("/var/www/projects/f22-02/html/files/p4-page-output-mfu.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editContinuous.php">File Allocation (Continuous)</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f22-02/v2/5-files/index.php">Run</a></th>
        
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f22-02/html/files/p5-file-input-continuous.txt"); ?></th>
            <th><span style="color: green;">continuous:</span> <?php readfile("/var/www/projects/f22-02/html/files/p5-file-output.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editLinked.php">File Allocation (Linked)</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f22-02/v2/5-files/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f22-02/html/files/p5-file-input-linked.txt"); ?></th>
            <th><span style="color: green;">linked:</span> <?php readfile("/var/www/projects/f22-02/html/files/p5-file-output.txt"); ?></th>   
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editIndexed.php">File Allocation (Indexed)</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f22-02/v2/5-files/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f22-02/html/files/p5-file-input-indexed.txt"); ?></th>
            <th><span style="color: green;">indexed:</span> <?php readfile("/var/www/projects/f22-02/html/files/p5-file-output.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editCLOOK.php">Disk Scheduling</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f22-02/v2/3-disk/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f22-02/html/files/p6-disk-input.txt"); ?></th>
            <th><span style="color: green;">clook:</span> <?php readfile("/var/www/projects/f22-02/html/files/p6-disk-output-clook.txt"); ?>
            <br><span style="color: green;">look:</span> <?php readfile("/var/www/projects/f22-02/html/files/p6-disk-output-look.txt"); ?>
            <br><span style="color: green;">sstf:</span> <?php readfile("/var/www/projects/f22-02/html/files/p6-disk-output-sstf.txt"); ?>
            <br><span style="color: green;">cscan:</span> <?php readfile("/var/www/projects/f22-02/html/files/p6-disk-output-cscan.txt"); ?>
            <br><span style="color: green;">fcfs:</span> <?php readfile("/var/www/projects/f22-02/html/files/p6-disk-output-fcfs.txt"); ?></th>
           
        </tr>
        
    </tbody>
</table>
<?php } ?>

<html>
<head>
     
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>View Files</title>
    <style>
        body { background-color: #F0FFF0; }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php
        display();
    ?>
    
</body>
</html>