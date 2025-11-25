<?php
//datacopy.php
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
            <th style="max-width: 270px;"><a href="edit/editLRU.php">LRU Page Replacement</a>
            <br><br>
            <a class="px-4 button mt-2 has-background-primary-light" href="./m-023/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile(get_temp_file_path("../../files/core-a/m-023/in-023.dat")); ?> </th>
            <th>
            <br><span style="color: green;">lru output:    </span> <pre><?php readfile(get_temp_file_path("../../files/core-a/m-023/out-023.dat")); ?></pre>
            
           </th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editLFU.php">LFU Page Replacement</a>
            <br><br>
            <!-- Displayinh contents of temp and user edited files -->
            <a class="px-4 button mt-2 has-background-primary-light" href="./m-024/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php if (!array_key_exists('https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/in-024.dat', $_SESSION['temp_files'])) {
            $_SESSION['temp_files']['https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/in-024.dat'] = 'https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/in-024.dat';}
            echo htmlentities(file_get_contents(get_temp_file_path($_SESSION['temp_files']["https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/in-024.dat"]))); ?> </th>
            <th>
            <br><span style="color: green;">lfu output:    </span> <pre><?php echo htmlentities(file_get_contents("https://cs.newpaltz.edu/p/s23-01/files/core-a/m-024/out-024.dat")); ?></pre>
            
           </th>
        </tr>
        <tr>
            <th style="max-width: 270px;"><a href="edit/editMFU.php">MFU Page Replacement</a>
            <br><br>
            <a class="px-4 button mt-2 has-background-primary-light" href="./m-025/index.php">Run</a></th>
            <th style="max-width: 550px;"><?php readfile(get_temp_file_path("../../files/core-a/m-025/in-025.dat")); ?> </th>
            <th>
            <br><span style="color: green;">lfu output:    </span> <pre><?php readfile(get_temp_file_path("../../files/core-a/m-025/out-025.dat")); ?></pre>
            
           </th>
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