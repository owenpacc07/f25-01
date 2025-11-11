<?php
error_reporting(error_reporting() & ~E_NOTICE);
ob_start();
session_start();
include(__DIR__ . '../../variables/var.php');
include(__DIR__ . '../../system.php');

$coretype = 'core';

if (isset($_SESSION['coremode'])) {
  $coretype = $_SESSION['coremode'];
} else {
  $_SESSION['coremode'] = "core";
  $coretype = "core";
}

//NOTES: Alogrithms And concepts hsould be moved
//Guide = About
// mode is auto set but should be under login
?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- bootstraps css, js, etc. ill eventually want to learn node and react or vue -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

    <!-- my own styles sheet -->
    <link rel="stylesheet" href="{{url_for('static', filename='styles.css')}}">
    <link rel="stylesheet" href="https://cs.newpaltz.edu/p/<?= $semester ?><?= $version_path ?>/templates/navbarstyle.css">
    <link rel="icon" href="/https://cs.newpaltz.edu/p/<?= $semester ?><?= $version_path ?>/pic/logocircle02.png" type="image/x-icon" />

    <!-- navbar from boostrap -->
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
        <a class="navbar-brand logodimension" href="#"><img src="https://cs.newpaltz.edu/p/<?= $semester ?><?= $version_path ?>/pic/logocircle02.png" alt="" class="d-inline-block align-text-top"> WebViz OS</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">WebViz OS</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
              <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">

                <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="./submission">Select Mechanism</a>
                </li>

                <li class="nav-item">
                <a role='button' class='btn btn-primary dimension fw-bold' href='<?=$SITE_ROOT . $version_path?>/create_submission.php'>
              Submission
            </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="./guide.php">About</a>
                </li>
                
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Account
                  </a>
                  <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="#">Mode: View</a></li>
                    <li><a class="dropdown-item" href="#">Mode: Advanced</a></li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href='<?$SITE_ROOT . $version_path?>/login.php'>Login</a></li>
                    <li><a class="dropdown-item" href='<?$SITE_ROOT . $version_path?>/register.php'>No registered? Sign up here.</a></li>
                  </ul>
                </li>

              </ul>
              <form class="d-flex mt-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-success" type="submit">Search</button>
              </form>
            </div>
          </div>
        </div> 
      </nav>
    <br>
    <br>
    <br>
</head>
    <body>
    </body>
</html>

