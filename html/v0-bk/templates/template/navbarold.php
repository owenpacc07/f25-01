<?php
error_reporting(error_reporting() & ~E_NOTICE);
ob_start();
session_start();
include (__DIR__ . '../../variables/var.php');

$coretype = 'core';
?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
    <style>
      /* Use Titan One font for brand button */
      @font-face {
        font-family: 'Titan One';
        src: url('../fonts/TitanOne-Regular.ttf') format('truetype');
      }

      .navbar-brand {
        font-family: 'Titan One';
        font-size: 1.25rem;
      }

      .navbar-brand img {
        width: 30px;
        height: 24px;
      }
      .dimension {
        height:60px;
        width: 130px;;
      }
      .logodimension {
        height:70px;
        width:200px;
      }
      .vr {
        opacity: 8%;
      }
      .signlog {
        width:150px;
      }
      .opacity {
        opacity: 0%;
        padding:5px;
      }

      .margin {
        margin-right:15%;
      }
    </style>

  </head>

  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <!-- Navbar content -->
      <div class="container-fluid d-flex">
        <a href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/indexVIEW.php">
          <Button class="navbar-brand btn btn-primary logodimension" type="button">
            <img src="/p/<?=$semester?>/files/favicon.ico" alt="" class="d-inline-block align-text-top">
            WEBVIZ OS
          </Button>
        </a>
        <?php if (!isset($_SESSION['logged_in'])) {
            echo "
              <div class=\"d-flex\">
                <a role='button' class='btn btn-primary border border-info' href='https://cs.newpaltz.edu/p/$semester/$version/register.php'>
                  <strong>Sign up</strong>
                </a>
                <a role='button' class='btn btn-primary border border-info' href='https://cs.newpaltz.edu/p/$semester/$version/login.php'>
                  <strong>Log in</strong>
                </a>
              </div>";
          } else {
            //if current_session_mode == admin display admin panel button 
            echo "
              <div class=\"col-auto d-flex justify-content-center dropdown\">
              <a role='button' class='btn btn-primary border border-info'  id=\"navbarDropdownMenuLink\" type=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                  Options
                </a>
                <div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuLink\">
                  <div>
                    <a class=\"dropdown-item\" href=\"https://cs.newpaltz.edu/p/$semester/$version/myPage.php\">
                      My Page
                    </a>
                  </div>
                  <div>
                    <a class=\"dropdown-item\" href=\"https://cs.newpaltz.edu/p/$semester/$version/groupPageUser.php?id=$_SESSION[groupid]\">
                      View Group
                    </a>
                  </div>
                  <div>
                    <a class=\"dropdown-item\" href='https://cs.newpaltz.edu/p/$semester/$version/logout.php'>
                      Log Out
                    </a>
                  </div>
                </div>
              </div>";
            if ($_SESSION['mode'] == 4 || $_SESSION['mode'] == 5) {
              echo "<li class=\"nav-item\"><a role=\"button\" href='https://cs.newpaltz.edu/p/$semester/$version/adminPanel/index.php' class=\"btn btn-light\">Admin Panel</a></li>";
            }
          } ?>
          <div class="vr opacity"></div>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button> 
        <div class="collapse navbar-collapse justify-content-center margin" id="navbarNav">
          <div class="row align-items-center">
            <div class="col-auto d-flex justify-content-center dropdown">
              <div class="vr"></div>
              <Button class="btn btn-primary dimension fw-bold " id="processDropdown" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Process Management
              </Button>
              <div class="dropdown-menu" aria-labelledby="processDropdown">
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/1-cpu">
                  CPU Scheduling
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/p1-process-states">
                  p1 - Process States
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/p2-deadlock">
                  p2 - Deadlock
                </a>
              </div>
              <div class="vr"></div>
            </div>
            <div class="col-auto d-flex justify-content-center dropdown">
              <div class="vr"></div>
              <Button class="btn btn-primary dimension fw-bold " href="#" id="memoryDropdown" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Memory Management
              </Button>
              <div class="dropdown-menu" aria-labelledby="memoryDropdown">
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/6-address">
                  Address Translation
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/4-memory-allocation">
                  Memory Allocation
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/2-replace">
                  Page Replacement
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/p4-memory-access">
                  p4 - Memory Access
                </a>
              </div>
              <div class="vr"></div>
            </div>
            <div class="col-auto d-flex justify-content-center dropdown">
              <div class="vr"></div>
              <Button class="btn btn-primary dimension fw-bold" href="#" id="IODropdown" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                I/O Management
              </Button>
              <div class="dropdown-menu" aria-labelledby="IODropdown">
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/p5-io-cycle">
                  p5 - I/O Cycle
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/5-files">
                  File Allocation
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/3-disk">
                  Disk Scheduling
                </a>
              </div>
              <div class="vr"></div>
            </div>
            <div class="col-auto d-flex justify-content-center dropdown">
              <div class="vr"></div>
              <Button class="btn btn-primary dimension fw-bold" href="#" id="SecurityDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Security Management
              </Button>
              <div class="dropdown-menu" aria-labelledby="SecurityDropdown">
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/p6-access-control">
                  p6 - Access Control
                </a>
              </div>
              <div class="vr"></div>
            </div>
        
        <!-- Changed to a dropdown to pick mode. Will probably add a separate button later so leaving this here.

            <div class="col-auto d-flex justify-content-center">
              <div class="vr"></div>
              <a href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/core/">
                <Button class="btn btn-primary dimension fw-bold">
                  OS Main
                </Button>
              </a>
              <div class="vr"></div>
            </div>
            
        -->
            <div class="col-auto d-flex justify-content-center dropdown">
              <div class="vr"></div>
              <Button class="btn btn-primary dimension fw-bold" href="#" id="OSDropdown" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                OS Main
              </Button>
              <div class="dropdown-menu" aria-labelledby="OSDropdown">
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/core">
                  View Mode
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/core-a">
                  Advanced Mode
                </a>
              </div>
              <div class="vr"></div>
            </div>

            <div class="col-auto d-flex justify-content-center">
              <div class="vr"></div>
              <a href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/submission.php">
                <Button class="btn btn-primary dimension fw-bold">
                  Submit
                </Button>
              </a>
              <div class="vr"></div>
            </div>
            <div class="col-auto d-flex justify-content-center">
              <div class="vr"></div>
              <a href="https://cs.newpaltz.edu/p/<?=$semester?>/<?=$version?>/runStart.php">
                <Button class="btn btn-primary dimension fw-bold">
                  Start
                </Button>
              </a>
              <div class="vr"></div>
            </div>
          </div>      
        </div>          
      </div>       
    </nav>
  </body>
</html>


         