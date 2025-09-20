<?php

$corename = 'View';
$coremode;

include(__DIR__ . '/system.php');

session_start();

//for search bar submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['mechanismid'] = $_POST['mechanismid'];
    $mechanismid = $_POST['mechanismid'];
    header("Location: ./core/m-" . $mechanismid);
}


if (isset($_SESSION['coremode'])) {
  $coremode = $_SESSION['coremode'];
} else {
  $_SESSION['coremode'] = "core";
  $coremode = "core";
}

// displays a different name depending on the core type
if ($coremode == 'core-a') {
  $corename = 'Advanced';
} else if ($coremode == 'core') {
  $corename = 'View';
}


?>

<!DOCTYPE html>
<html lang="en">


<head>
  <title>OS Visuals Homepage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="<?= $SITE_ROOT . $version_path ?>/pic/OSVisualsIcon.ico"  />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <style>
    @font-face {
      font-family: 'Titan One';
      src: url('./fonts/TitanOne-Regular.ttf') format('truetype');
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

    .info {
      /* make a blue cirlce like a info button*/
      /* Create a circular shape by setting the border radius to 50% */
      border-radius: 50%;

      /* Set the width and height of the element to 30 pixels */
      width: 30px;
      height: 30px;

      /* Set the background color to a shade of blue (#2196F3) */
      background-color: #2196F3;

      /* Display the element as an inline block */
      display: inline-block;

      /* Center the text within the element */
      text-align: center;

      /* Set the text color to white */
      color: white;

      /* Set the font size to 14 pixels */
      font-size: 14px;

      /* Center the text vertically within the element using the line-height */
      line-height: 30px;

      /* Add 5 pixels of margin to the left and right sides of the element */
      margin-left: 5px;
      margin-right: 5px;


    }

    .hide {
      visibility: hidden;
    }

    .info:hover+.hide {
      visibility: visible;
      color: red;
    }




  </style>

</head>

<body>
  <?php include './navbar.php'; ?>

  <!-- <a href="https://www.os-book.com/OS10/index.html" class="info">?</a>
  <div class="hide">Here is a link to the textbook used in this course.</div>  Just commented out this question mark button for now, it is on guide page-->

  <div class="container">

    <div class="container">
      <br>
      <br>
      <br>
      <div class="row justify-content-center">
        <div class="col-lg-7">
        <img src="./pic/OSVisuals.png" alt="Image description" class="img-fluid rounded" style="width: 105%; max-width: 800px; height: auto;">
        </div>
      </div>
      <br>
      <br>
      <h2 class="description text-center"><?= $corename ?> Mode</h2>
      <br>


      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <form id="midForm" method="post" enctype="multipart/form-data" action="">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Input Mechanism ID" name="mechanismid" id="mechanismid" required>
                <div class="input-group-append">
                  <input id="formBtn" class="btn btn-secondary" type="submit" style="background-color:#4B0082"value="Search">
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <br>
      <br>
      <br>

      <hr class="new">
      <h4 class="text-center">CPU Scheduling</h4>
      <br>
      <div class="row align-items-center">
        <a href="./<?= $coremode ?>/m-001" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #6DA335;">
          <div class=" padding">
            <h5 class="text-center">001 FCFS</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-002" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #99C653;">
          <div class=" padding">
            <h5 class="text-center">002 Nonpreemptive SJF</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-003" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #B8DB8A;">
          <div class=" padding">
            <h5 class="text-center">003 Nonpreemptive Priority Hi</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-004" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #6DA335;">
          <div class=" padding">
            <h5 class="text-center">004 Nonpreemptive Priority Lo</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-005" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #99C653;">
          <div class=" padding">
            <h5 class="text-center">005 Round Robin</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-006" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #B8DB8A;">
          <div class=" padding">
            <h5 class="text-center">006 Preemptive SJF</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-007" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #6DA335;">
          <div class=" padding">
            <h5 class="text-center">007 Preemptive Priority (High)</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-008" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #99C653;">
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
        <a href="./<?= $coremode ?>/m-021" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #9769D9;">
          <div class=" padding">
            <h5 class="text-center">021 FIFO</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-022" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #B594E4;">
          <div class=" padding">
            <h5 class="text-center">022 Optimal</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-023" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #D2BFEF;">
          <div class=" padding">
            <h5 class="text-center">023 LRU</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-024" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #9769D9;">
          <div class=" padding">
            <h5 class="text-center">024 LFU</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-025" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #B594E4;">
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
        <a href="./<?= $coremode ?>/m-041" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #FFD100;">
          <div class=" padding">
            <h5 class="text-center">041 FCFS</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-042" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #FFDD40;">
          <div class=" padding">
            <h5 class="text-center">042 SSTF</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-043" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #FFE880;">
          <div class=" padding">
            <h5 class="text-center">043 CSCAN</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-044" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #FFD100;">
          <div class=" padding">
            <h5 class="text-center">044 LOOK</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-045" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #FFDD40;">
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
        <a href="./<?= $coremode ?>/m-011" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #FF8F00;">
          <div class=" padding">
            <h5 class="text-center">011 First Fit</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-012" class="col-md-4 btn btn-outline-secondary" style="color:black;background-color: #FFAB40;">
          <div class=" padding">
            <h5 class="text-center">012 Best Fit</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-013" class="col-md-4 btn btn-outline-secondary" style="color:black; background-color: #FFC780;">
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
        <a href="./<?= $coremode ?>/m-031" class="col-md-4 btn btn-outline-secondary" style="color: black;background-color: #DB2F2F;">
          <div class=" padding">
            <h5 class="text-center">031 Contiguous</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-032" class="col-md-4 btn btn-outline-secondary" style="color: black;background-color: #DE5252;">
          <div class=" padding">
            <h5 class="text-center">032 Linked</h5>
          </div>
        </a>
        <a href="./<?= $coremode ?>/m-033" class="col-md-4 btn btn-outline-secondary" style="color: black;background-color: #E97D7D;">
          <div class=" padding">
            <h5 class="text-center">033 Indexed</h5>
          </div>
        </a>
      </div>

      <hr class="new">
      <h4 class="text-center">Concepts</h4>
      <br />
      <div class="row">
        <a href="./p1-process-states" class="col-md-4 btn btn-outline-secondary" style="color: black;background-color: #5c6dc9;">
          <div class=" padding">
            <h5 class="text-center">P1 Process States</h5>
          </div>
        </a>

        <a href="./p2-deadlock" class="col-md-4 btn btn-outline-secondary" style="color: black; background-color: #8A98E5;">
          <div class=" padding">
            <h5 class="text-center">P2 Deadlock</h5>
          </div>
        </a>

        <a href="./6-address" class="col-md-4 btn btn-outline-secondary" style="color: black; background-color: #a0aadf;">
          <div class=" padding">
            <h5 class="text-center">P3 Address Translation</h5>
          </div>
        </a>

        <a href="./p4-memory-access" class="col-md-4 btn btn-outline-secondary" style="color: black;background-color: #5c6dc9;">
          <div class=" padding">
            <h5 class="text-center">P4 Memory Access</h5>
          </div>

          <a href="./p5-io-cycle" class="col-md-4 btn btn-outline-secondary" style="color: black; background-color: #8A98E5;">
            <div class=" padding">
              <h5 class="text-center">P5 IO Cycle</h5>
            </div>

            <a href="./p6-access-control" class="col-md-4 btn btn-outline-secondary" style="color: black; background-color: #a0aadf;">
              <div class=" padding">
                <h5 class="text-center">P6 Access Control</h5>
              </div>

            </a>
            <a href="./p7-memory-layout" class="col-md-4 btn btn-outline-second" style="color: black; background-color: #5c6dc9;">
              <div class="padding">
                <h5 class="text-center">P7 Memory Layout</h5>
                </div>
            </a>

      </div>

      <br>
      <br>

      <footer class="bg-light text-center text-lg-start">

        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
	  <p>Fall 2025 Contributors: Gavin Bell | Jack Lin | Michael Scotto | Owen Pacchiana </p>
        <p>Spring 2025 Contributors: Justin Feinman | Henry Becker </p>
	  <p>Fall 2024 Contributors: William Rubin | Marco Lemus </p>
	  <p>Spring 2024 Contributors: Shelby Hinton | Gianella Robles | Mary Seelmann </p>
          <p>Fall 2023 Contributors: Kyle Wendholt | Amir Marji | Manuel Reyes | Jalen Fenton | Emmanuel Johnson </p>
          <p>Spring 2023 Contributors: Aleks Pilmanis | Christian Collado | Dakota Marino | James O'Sullivan | Jaedan Smith | Tyler Wendover </p>
          <p>Fall 2022 Contributors: Mark Venuto | Tim Haines </p>
          <p>Spring 2022 Contributors: Ryan Arnold | Christopher Brady | Maria Hernandez | Jordon Roberts | Jenna Rodriguez | Huaqi Zhang | Alec Lehmphul | Mitchell Chappell</p>
          <p>Fall 2021 Contributors: Matthew Morfea | Henry Murillo | Charles Agudelo | Joshua Morris | Tevin Skeete | Aaron Traver </p>
          <p>Advisor (Manager): Hanh Pham</p>
        </div>

      </footer>
    </div>



</body>

<script>
$(document).ready(function() {
  function redirect() {
    let mechanismid = $("#mechanismid").val();
    let coremode = "<?= $coremode ?>";
    let url = './${coremode}/m-${mechanismid}';
    $("#midForm").attr("action", url);
    $("#midForm").submit();
  }
  $("#formBtn").click(function(event) {
    event.preventDefault();
    redirect();
  });
});

  $(document).ready(function() {
    // Collapse all sub-menus except for the active one
    $('#sidebar a[data-toggle="collapse"]').on('click', function() {
      $('#sidebar a[data-toggle="collapse"]').not($(this)).each(function() {
        $(this).removeClass('active');
        $($(this).attr('href')).collapse('hide');
      });
      $(this).addClass('active');
    });

    // Prevent sub-menu from closing when clicking on sub-option
    $('.sub-menu .sub-option').on('click', function(e) {
      e.stopPropagation();
    });
  });
</script>


</html>
