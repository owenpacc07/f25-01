<?php 
session_start();
require_once './tempfunctions.php';
    if (!isset($_SESSION['email'])) {
        header('Location: ../login.php');
        exit();
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
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f21-13/v7/1-cpu/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f21-13/html/files/p1-cpu-input.txt"); ?></th>
            <th><span style="color: green;">fcfs:</span> <?php readfile("/var/www/projects/f21-13/html/files/p1-cpu-output-fcfs.txt"); ?>
            <br><span style="color: green;">sjf:</span> <?php readfile("/var/www/projects/f21-13/html/files/p1-cpu-output-sjf.txt"); ?>
            <br><span style="color: green;">sjf-p:</span> <?php readfile("/var/www/projects/f21-13/html/files/p1-cpu-output-sjf-p.txt"); ?>
            <br><span style="color: green;">priority:</span> <?php readfile("/var/www/projects/f21-13/html/files/p1-cpu-output-priority.txt"); ?>
            <br><span style="color: green;">priority-p:</span> <?php readfile("/var/www/projects/f21-13/html/files/p1-cpu-output-priority-p.txt"); ?>
            <br><span style="color: green;">round robin:</span><?php readfile("/var/www/projects/f21-13/html/files/p1-cpu-output-roundrobin.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editMemory.php">Memory Allocation</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f21-13/v7/4-memory/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f21-13/html/files/p3-memory-input.txt"); ?> </th>
            <th><span style="color: green;">firstfit:</span> <?php readfile("/var/www/projects/f21-13/html/files/p3-memory-output-firstfit.txt"); ?>
            <br><span style="color: green;">bestfit:</span> <?php readfile("/var/www/projects/f21-13/html/files/p3-memory-output-bestfit.txt"); ?>
            <br><span style="color: green;">worstfit:</span> <?php readfile("/var/www/projects/f21-13/html/files/p3-memory-output-worstfit.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editLRU.php">LRU Page Replacement</a>
            <br><br>
            <a class="px-4 button mt-2 has-background-primary-light" href="./m-023/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("../../files/core-a/m-023/in-023.dat"); ?> </th>
            <th>
            <br><span style="color: green;">lru output:    </span> <pre><?php readfile("../../files/core-a/m-023/out-023.dat"); ?></pre>
            
           </th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editLFU.php">LFU Page Replacement</a>
            <br><br><!-- Displaying contents of temp and user edited files -->
            <a class="px-4 button mt-2 has-background-primary-light" href="./m-024/index.php">Run</a></th>
            <!-- Check if session array key pair exists, if not, intialize it and display the contents -->
            <th style="max-width: 550px;"><?php  if (!array_key_exists('https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/in-024.dat', $_SESSION['temp_files'])) {
                $_SESSION['temp_files']['https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/in-024.dat'] = 'https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/in-024.dat';}
                echo htmlentities(file_get_contents(get_temp_file_path($_SESSION['temp_files']["https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/in-024.dat"]))) ?> </th>
            <th>
            <br><span style="color: green;">lfu output:    </span> <pre><?php echo htmlentities(file_get_contents("https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/out-024.dat")); ?></pre>
            
           </th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editMFU.php">MFU Page Replacement</a>
            <br><br>
            <a class="px-4 button mt-2 has-background-primary-light" href="./m-025/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("../../files/core-a/m-025/in-025.dat"); ?> </th>
            <th>
            <br><span style="color: green;">lfu output:    </span> <pre><?php readfile("../../files/core-a/m-025/out-025.dat"); ?></pre>
            
           </th>
        </tr>
       
        <tr>
            <th style="max-width: 270px;"><a href="edit/editContinuous.php">File Allocation (Continuous)</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f21-13/v7/5-files/index.php">Run</a></th>
        
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f21-13/html/files/p5-file-input-continuous.txt"); ?></th>
            <th><span style="color: green;">continuous:</span> <?php readfile("/var/www/projects/f21-13/html/files/p5-file-output.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editLinked.php">File Allocation (Linked)</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f21-13/v7/5-files/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f21-13/html/files/p5-file-input-linked.txt"); ?></th>
            <th><span style="color: green;">linked:</span> <?php readfile("/var/www/projects/f21-13/html/files/p5-file-output.txt"); ?></th>   
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editIndexed.php">File Allocation (Indexed)</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f21-13/v7/5-files/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f21-13/html/files/p5-file-input-indexed.txt"); ?></th>
            <th><span style="color: green;">indexed:</span> <?php readfile("/var/www/projects/f21-13/html/files/p5-file-output.txt"); ?></th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editCLOOK.php">Disk Scheduling</a>
            <br>
            <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f21-13/v7/3-disk/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile("/var/www/projects/f21-13/html/files/p6-disk-input.txt"); ?></th>
            <th><span style="color: green;">clook:</span> <?php readfile("/var/www/projects/f21-13/html/files/p6-disk-output-clook.txt"); ?>
            <br><span style="color: green;">look:</span> <?php readfile("/var/www/projects/f21-13/html/files/p6-disk-output-look.txt"); ?>
            <br><span style="color: green;">sstf:</span> <?php readfile("/var/www/projects/f21-13/html/files/p6-disk-output-sstf.txt"); ?>
            <br><span style="color: green;">cscan:</span> <?php readfile("/var/www/projects/f21-13/html/files/p6-disk-output-cscan.txt"); ?>
            <br><span style="color: green;">fcfs:</span> <?php readfile("/var/www/projects/f21-13/html/files/p6-disk-output-fcfs.txt"); ?></th>
           
        </tr>
        
    </tbody>
</table>
<?php } ?>

<html>
<head>
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
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