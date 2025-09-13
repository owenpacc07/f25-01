<?php

session_start();
include(__DIR__ . '../../variables/var.php');
include(__DIR__ . '../../system.php');

if (isset($_SESSION['coremode'])) {
  $coretype = $_SESSION['coremode'];
} else {
  $_SESSION['coremode'] = "core";
  $coretype = "core";
}

?>

<!DOCTYPE html>
<html lang="en">


<head>
  <title>WebVis OS Homepage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="https://cs.newpaltz.edu/p/<?= $semester ?><?= $version_path ?>/pic/logocircle02.png" type="image/x-icon" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cs.newpaltz.edu/p/<?= $semester ?><?= $version_path ?>/templates/navbarstyle.css">

  <style>
    body {
      overflow-x: hidden;
      font-family: 'Roboto', sans-serif;
    }

    /* Toggle Styles */

    #wrapper {
      padding-left: 0;
      -webkit-transition: all 0.5s ease;
      -moz-transition: all 0.5s ease;
      -o-transition: all 0.5s ease;
      transition: all 0.5s ease;
    }

    #wrapper.toggled {
      padding-left: 250px;
    }

    #sidebar-wrapper {
      z-index: 1000;
      position: fixed;
      left: 250px;
      width: 0;
      height: 100%;
      margin-left: -250px;
      overflow-y: auto;
      background: #2c3e50;
      /* Change the background color */
      -webkit-transition: all 0.5s ease;
      -moz-transition: all 0.5s ease;
      -o-transition: all 0.5s ease;
      transition: all 0.5s ease;
    }

    #wrapper.toggled #sidebar-wrapper {
      width: 250px;
    }

    #page-content-wrapper {
      width: 100%;
      position: absolute;
      padding: 15px;
    }

    #wrapper.toggled #page-content-wrapper {
      position: absolute;
      margin-right: -250px;
    }

    /* Sidebar Styles */
    .sidebar-nav {
      position: absolute;
      top: 0;
      width: 250px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .sidebar-nav {
      text-indent: 20px;
      line-height: 40px;
    }

    .sidebar-nav a:hover {
      display: block;
      text-decoration: none;

      /* Change the text color */
      background-color: #215338;
      border-color: #215338;
    }

    .sidebar-nav a {
      text-decoration: none;
      color: #ffffff;
      /* Change the hover text color */
      background: #34495e;
      /* Change the hover background color */
    }

    .sidebar-nav a:active,
    .sidebar-nav a:focus {
      text-decoration: none;
    }

    .sidebar-nav>.sidebar-brand {
      height: 65px;
      font-size: 18px;
      line-height: 60px;
    }

    .sidebar-nav>.sidebar-brand a {
      color: #bdc3c7;
      /* Change the text color */
    }

    .sidebar-nav>.sidebar-brand a:hover {
      color: #ffffff;
      /* Change the hover text color */
      background: none;
    }

    @media(min-width:768px) {
      #wrapper {
        padding-left: 250px;
      }

      #wrapper.toggled {
        padding-left: 0;
      }

      #sidebar-wrapper {
        width: 250px;
      }

      #wrapper.toggled #sidebar-wrapper {
        width: 0;
      }

      #page-content-wrapper {
        padding: 20px;
        position: relative;
      }

      #wrapper.toggled #page-content-wrapper {
        position: relative;
        margin-right: 0;
      }
    }

    .subitem {
      font-size: 12px;
      padding: 5px 10px;
      height: 80%;
      margin-left: 20px;
      /* adjust this value to make the box smaller or larger */
    }

    .bigbuttons {
      color: #fff;
      background-color: #28a745;
      border-color: #34495e;

    }

    #sidebar-toggle-show {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 1001;
      display: none;
      cursor: pointer;
      padding: 10px 15px;
      border-radius: 5px;
      background-color: #2c3e50;
      color: #fff;
      font-size: 18px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
    }

    #sidebar-toggle-show:hover {
      background-color: #34495e;
    }

    #wrapper.toggled #sidebar-toggle-show {
      display: block;
    }
  </style>
  <script>
    $(document).ready(function() {
      // Toggle the visibility of the sidebar when the button is clicked
      $('.toggle-sidebar').click(function() {
        $('#wrapper').toggleClass('toggled');
        if ($('#wrapper').hasClass('toggled')) {
          $('#sidebar-toggle-show').show();
          $('.toggle-sidebar').hide();
        } else {
          $('#sidebar-toggle-show').hide();
          $('.toggle-sidebar').show();
        }
      });
      // Show the sidebar when the show button is clicked
      $('#sidebar-toggle-show').click(function() {
        $('#wrapper').toggleClass('toggled');
        $(this).hide();
        $('.toggle-sidebar').show();
      });
    });
  </script>

</head>

<body>


  <div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">

      <ul class="sidebar-nav">
        <br><br>

        <button class="btn btn-primary toggle-sidebar" id="sidebar-toggle">Hide Sidebar</button>
        <div class="list-group">
          <a href="#tab1" class="list-group-item btn btn-primary fw-bold bigbuttons " data-toggle="collapse">Algorithms<span class="caret"></span></a>
          <div id="tab1" class="sub-menu collapse outer">

            <a href="#cpuscheduling" class="list-group-item" data-toggle="collapse">CPU Scheduling<span class="caret"></span></a>
            <div id="cpuscheduling" class="sub-menu collapse inner">
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-001">
                001 FCFS
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-002">
                002 Nonpreemptive SJF
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-003">
                003 Nonpreemptive Priority(High)
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-004">
                004 Nonpreemptive Priority (Low)
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-005">
                005 Round Robin
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-006">
                006 Preemptive SJF
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-007">
                007 Preemptive Priority (High)
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-008">
                008 Preemptive Priority (Low)
              </a>
            </div>

            <a href="#pagereplacement" class="list-group-item" data-toggle="collapse">Page Replacement<span class="caret"></span></a>
            <div id="pagereplacement" class="sub-menu collapse inner">
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-021">
                021 FIFO
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-022">
                022 Optimal
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-023">
                023 LRU
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-024">
                024 LFU
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-025">
                025 MFU
              </a>
            </div>

            <a href="#diskscheduling" class="list-group-item" data-toggle="collapse">Disk Scheduling<span class="caret"></span></a>
            <div id="diskscheduling" class="sub-menu collapse inner">
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-041">
                041 FCFS
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-042">
                042 SSTF
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-043">
                043 CSCAN
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-044">
                044 LOOK
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-045">
                045 CLOOK
              </a>
            </div>

            <a href="#memoryallocation" class="list-group-item" data-toggle="collapse">Memory Allocation<span class="caret"></span></a>
            <div id="memoryallocation" class="sub-menu collapse inner">
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-011">
                011 First Fit
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-012">
                012 Best Fit
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-013">
                013 Worst Fit
              </a>
            </div>

            <a href="#fileallocation" class="list-group-item" data-toggle="collapse">File Allocation<span class="caret"></span></a>
            <div id="fileallocation" class="sub-menu collapse inner">
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-031">
                031 Contiguous
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-032">
                032 Linked
              </a>
              <a class="list-group-item subitem" href="<?= $SITE_ROOT . $version_path ?>/<?= $coretype ?>/m-033">
                033 Indexed
              </a>
            </div>

          </div>
        </div>


        <div class="list-group">
          <a href="#tab2" class="list-group-item btn btn-primary dimension fw-bold bigbuttons" data-toggle="collapse">Concepts<span class="caret"></span></a>
          <div id="tab2" class="sub-menu collapse outer">

            <a href="<?= $SITE_ROOT . $version_path ?>/p1-process-states" class="list-group-item subitem">P1 Process States</a>
            <a href="<?= $SITE_ROOT . $version_path ?>/p2-deadlock" class="list-group-item subitem">P2 Deadlock</a>
            <a href="<?= $SITE_ROOT . $version_path ?>/6-address" class="list-group-item subitem">P3 Address Translation</a>
            <a href="<?= $SITE_ROOT . $version_path ?>/p4-memory-access" class="list-group-item subitem">P4 Memory Access</a>
            <a href="<?= $SITE_ROOT . $version_path ?>/p5-io-cycle" class="list-group-item subitem">P5 IO Cycle</a>
            <a href="<?= $SITE_ROOT . $version_path ?>/p6-access-control" class="list-group-item subitem">P6 Access Control</a>



          </div>

        </div>
      </ul>

    </div>
    <button type="button" class="btn btn-default" id="sidebar-toggle-show">
      <i class="fa fa-bars"> Show Sidebar </i>
    </button>
</body>

<script>
  // this makes it so you can only open one group of tabs at once
  $(document).ready(function() {
    $('.inner').on('show.bs.collapse', function() {
      $('.inner').not(this).collapse('hide');
    });

    $('.outer').on('show.bs.collapse', function() {
      $('.outer').not(this).collapse('hide');
    });
  });
</script>

</html>