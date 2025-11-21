<?php
session_start();

$_SESSION['mechanismid'] = -1;

// header("Location: ../view.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mechanismid = trim($_POST['mechanismid']);
    $target = 'm-' . sprintf("%03d", $mechanismid).'.php';

    //$target = 'm-' . sprintf("%03d", $mechanismid);

    // file_put_contents($target, " ewrt");

    //header("Location: https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/index.php?page=" . $target);
    //header("Location: editInputOutput.php");
    header("Location: https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/" . $target);

}

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>CPU Scheduling</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	     

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" 
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        
        <!--
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        -->

        <link rel="preconnect" href="https://fonts.gstatic.com">

        <!--
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        -->

        <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <link rel="stylesheet" type="text/css" href="styles/styles.css">
        <script type="module" src="./javascript/index/main.js" defer></script>
        <script type="module" src="./javascript/index/loading_data_backend.js" defer></script>

    </head>

    <body>

        <?php include '../../navbar.php'; ?> 

        <br>

            <div class="d-flex align-items-center justify-content-center">
                <h1 id="title">RUN a Mechanism </h1>
            </div>

        <p> Please ENTER: </p>
        <p> 001 for nonpreemptive FCFS, 002 for nonpreemptive SJF, 003 for nonpreemptive SJF, 004 for nonpreemptive Priority,  
        <p> 005 for Round Robin, 006 for preemptive SJF, 007 for preemptive Priority, 
 
    <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
  
         Mechanism ID: <input type="text" name="mechanismid" id="mechanismid" required>

        <br>
        <br>

        <input type="submit" value="Submit">

    </form>



</body>

</html>