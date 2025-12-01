<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Run a Mechanism</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="https://cs.newpaltz.edu/p/f23-05/v1/files/favicon.ico" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="styles/index_styles.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <?php include '../navbar.php'; ?>
    

    <main>
        <div id="title-content" class="d-flex align-items-center justify-content-center">
            <!-- <a class="btn btn-primary" href="../core-a/data.php"> EDIT input/Output DATA </a> -->
        </div>
        <section>
            <br>
            <div class="d-flex align-items-center justify-content-center">
                <h1 id="title">RUN a Mechanism </h1>
            </div>
            <p> Please ENTER: </p>
            <p> [CPU Scheduling] 001 for nonpreemptive FCFS, 002 for nonpreemptive SJF, 003 for nonpreemptive Priority (High), 004 for nonpreemptive Priority (Low), 005 for Round Robin, 006 for preemptive SJF, 007 for preemptive Priority (High), 008 for preemptive Priority (Low)
            <p> [Page Replacement] 021 for FIFO, 022 for Optimal, 023 for LRU, 026 for MRU, 024 for LFU, 025 for MFU
            <p> [Disk Scheduling] 041 for FCFS, 042 for SSTF, 043 for CSCAN, 044 for LOOK, 045 for CLOOK
            <p> [Memory Allocation] 011 for First Fit, 012 for Best Fit, 013 for Worst Fit
            <p> [File Allocation] 031 for Contiguous, 032 for Linked, 033 for Indexed
            <div class="center">
                <!-- redirect to visualization page w/ mechanismid -->
                <form id="midForm" method="post" enctype="multipart/form-data" action="">
                    Mechanism ID: <input type="text" name="mechanismid" id="mechanismid" required>
                    <input id="formBtn" class="btn btn-primary" type="submit" value="Submit">
                </form>
            </div>
        </section>
    </main>

</body>

<script>
    function redirect() {
        let mechanismid = document.getElementById("mechanismid").value;
        let midForm = document.getElementById("midForm");
        midForm.action = "./m-" + mechanismid; // example ./m-001
        midForm.submit();
    }
    $("#formBtn").click(function() {
        redirect();
    });
</script>

</html>