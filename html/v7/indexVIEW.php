<?php

$corename = 'View';

$coretype = isset($_GET['coretype']) ? $_GET['coretype'] : 'core'; //check if the 'corevalue' parameter is set in the URL query string  and assigns that value to the $corevalue variable otherwise set to 'core'.

// displays a different name depending on the core type
if ($coretype == 'core-a') {
  //if "advanced" button pressed in index.html, show advanced mode links etc, otherwise, default to view also check if user is logged in to check for view mode
  session_start();
  if (!isset($_SESSION['email'])) { //also check if user is logged in before accessing 'advanced' mode of site
    header('Location: ./login.php');
    exit();
  }
  $coremode='core-a';
  $corename = 'Advanced';
  
}
else{$coremode='core';}

?>

<!DOCTYPE html>
<html lang="en">


<head>
  <title>WebVis OS Homepage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
   
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <meta http-equiv="refresh" content="0; URL=index.php" />

  <style>
    @font-face {
      font-family: 'Titan One';
      src: url('./fonts/TitanOne-Regular.ttf') format('truetype');
    }

    h1#title {
      height: auto;
      width: 100%;
      text-align: center;
      font-family: 'Titan One';
      font-size: 70px;
      color: var(--blue);
      text-shadow:
        0 6px 0 transparent,
        0 7px 0 transparent,
        0 8px 0 transparent,
        0 9px 0 transparent,
        0 10px 10px rgba(0, 0, 0, 0.2);
      margin-bottom: 40px;
      padding: 20px, 0;
      background-image: linear-gradient(to left, transparent, lightblue, lightblue, lightblue, transparent);
    }

    :root {
      --white: #f8f9fa;
      --lighter-gray: #F0FFF0;
      --light-gray: #c7c7c7;
      --lighter-blue: #d8e8ee;
      --light-blue: #1aa9cd;
      --sky-blue: #23ADFF;
      --blue: #0000cd;
    }

    .padding {
      padding-top: 10px;
      padding-bottom: 10px;
    }

    hr.new {
      display: block;
      border: none;
      height: 1px;
      background: #737373;
      background: linear-gradient(to right, white, #737373, #737373, #737373, white);
    }
  </style>

</head>

<body>

  <?php include './navbar.php'; ?>

  <div class="container">

    <div class="container">
    <br><br>
      <h2 class="description text-center"><?= $corename ?> Mode</h2>
      <h3 class="description text-center">Search or select from an OS process below to see it visualized!</h3>
      <br>


      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <form id="midForm" method="post" enctype="multipart/form-data" action="">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Input Mechanism ID" name="mechanismid" id="mechanismid" required>
                <div class="input-group-append">
                  <button id="formBtn" class="btn btn-primary" type="submit">Search</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <br>
      <br>
  
      <hr class="new">
      <h4 class="text-center">CPU Scheduling</h4>
      <br>
      <div class="row align-items-center">
        <a href="./<?= $coremode ?>/m-001" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">001 FCFS</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-002" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">002 Nonpreemptive SJF</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-003" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">003 Nonpreemptive Priority (High)</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-004" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">004 Nonpreemptive Priority (Low)</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-005" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">005 Round Robin</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-006" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">006 Preemptive SJF</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-007" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">007 Preemptive Priority (High)</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-008" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">008 Preemptive Priority (Low)</h5>
          </div>
        </a>
      </div>

      <br>

      <hr class="new">
      <h4 class="text-center">Page Replacement</h4>
      <br />
      <div class="row">
        <a href="./<?= $coremode ?>/m-021" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">021 FIFO</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-022" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">022 Optimal</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-023" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">023 LRU</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-024" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">024 LFU</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-025" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">025 MFU</h5>
          </div>
        </a>
      </div>

      <br>

      <hr class="new">
      <h4 class="text-center">Disk Scheduling</h4>
      <br />
      <div class="row">
        <a href="./<?= $coremode ?>/m-041" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">041 FCFS</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-042" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">042 SSTF</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-043" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">043 CSCAN</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-044" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">044 LOOK</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-045" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">045 CLOOK</h5>
          </div>
        </a>
      </div>

      <br>

      <hr class="new">
      <h4 class="text-center">Memory Allocation</h4>
      <br />
      <div class="row">
        <a href="./<?= $coremode ?>/m-011" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">011 First Fit</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-012" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">012 Best Fit</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-013" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">013 Worst Fit</h5>
          </div>
        </a>
      </div>

      <br>

      <hr class="new">
      <h4 class="text-center">File Allocation</h4>
      <br />
      <div class="row">
        <a href="./<?= $coremode ?>/m-031" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">031 Contiguous</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-032" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">032 Linked</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-033" class="col-md-4 btn btn-outline-secondary">
          <div class=" padding">
            <h5 class="text-center">033 Indexed</h5>
          </div>
        </a>
      </div>

      <br>
      <br>

      <footer class="bg-light text-center text-lg-start">

        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
          <p>Spring 2023 Contributors: Aleks Pilmanis | Christian Collado | Dakota Marino | James O'Sullivan | Jaedan Smith | Tyler Wendover </p>
          <p>Fall 2022 Contributors: Mark Venuto | Tim Haines </p>
          <p>Spring 2022 Contributors: Ryan Arnold | Christopher Brady | Maria Hernandez | Jordon Roberts | Jenna Rodriguez | Huaqi Zhang | Adrian Francis | Alec Lehmphul | Mitchell Chappell</p>
          <p>Fall 2021 Contributors: Matthew Morfea | Henry Murillo | Charles Agudelo | Joshua Morris | Tevin Skeete | Aaron Traver </p>
        </div>

      </footer>
    </div>



</body>

<script>
  function redirect() {
    let mechanismid = document.getElementById("mechanismid").value;
    let midForm = document.getElementById("midForm");
    midForm.action = "./<?= $coremode ?>/m-" + mechanismid; // example ./m-001
    midForm.submit();
  }
  $("#formBtn").click(function() {
    redirect();
  });
</script>

</html>