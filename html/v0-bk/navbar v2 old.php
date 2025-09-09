<?php
error_reporting(error_reporting() & ~E_NOTICE);
ob_start();
session_start();

include_once(__DIR__ . '/system.php');

$coretype = 'core';
$subdirectory = '';

if (isset($_SESSION['coremode'])) {
  $coretype = $_SESSION['coremode'];
} else {
  $_SESSION['coremode'] = "core";
  $coretype = "core";
}

if (!isset($_SESSION['logged_in'])) {
  echo "
    <div class='col-auto d-flex justify-content-center dropdown'>
      <a role='button' class='btn btn-primary dimension fw-bold' href='$SITE_ROOT$version_path/login.php'>
        Log in
      </a>
    </div>";
} else {

  // if the admin is logged in display the button to access the admin panel
  if ($_SESSION['mode'] == 4 || $_SESSION['mode'] == 5) {
    echo "
    <div class='col-auto d-flex justify-content-center dropdown'>
      <a role='button' class='btn btn-primary dimension fw-bold' href='$SITE_ROOT$version_path/adminPanel/index.php'>
        Admin
      </a>
    </div>
    ";
  }

  echo "<div class='col-auto d-flex justify-content-center dropdown'>
    <a role='button' class='btn btn-primary dimension fw-bold' href='$SITE_ROOT$version_path/myPage.php'>
      Profile
    </a>
  </div>
  ";

  echo "
  <div class='col-auto d-flex justify-content-center dropdown'>
    <a role='button' class='btn btn-primary dimension fw-bold' href='$SITE_ROOT$version_path/logout.php'>
      Log Out
    </a>
  </div>
  ";

  $email = $_SESSION['email'];
  $username = substr($email, 0, strpos($email, "@"));
  echo "
  <div class='col-auto d-flex justify-content-center dropdown'>
    <a> Welcome back, [ $username ]</a>
  </div>
  ";
}

?>

<style>

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
</style>

<!DOCTYPE HTML>
<html lang="en-US">
<head >
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- bootstraps css, js, etc. ill eventually want to learn node and react or vue -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

    <!-- navbar 1 from boostrap -->
    
      <nav class="navbar navbar-dark fixed-top" style="background-color:#003e7e;">
        <div class="container-fluid">
          <div style="display: inline-grid">
            <a style="margin-bottom: 0px; font-size: 20px; font-weight: bold; color: white; text-decoration: none; display: grid; grid-template-columns: auto auto auto auto;" href="<?= $SITE_ROOT . $version_path?>/index.php">
            <img src="<?= $SITE_ROOT . $version_path?>/pic/logocircle02.png" width="30" height="30"  class="d-inline-block align-text-top" style="margin-right: 7px; display: flex">  
              WebViz OS
            </a>
          </div>
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
                    <a class="nav-link active" aria-current="page" href="<?= $SITE_ROOT . $version_path?>/index.php">Home</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link active" href="<?= $SITE_ROOT . $version_path?>/core">Select Mechanism  </a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link active" href="<?= $SITE_ROOT . $version_path?>/submission.php">Submission</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link active" href="<?= $SITE_ROOT . $version_path?>/guide.php">About</a>
                  </li>

                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                      <li><a class="dropdown-item" href="<?= $SITE_ROOT . $version_path?>/core">Mode: View</a></li>
                      <li><a class="dropdown-item" href="<?= $SITE_ROOT . $version_path?>/core-a">Mode: Advanced</a></li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li><a class="dropdown-item" href='<?= $SITE_ROOT . $version_path?>/login.php'>Login</a></li>
                      <li><a class="dropdown-item" href='<?= $SITE_ROOT . $version_path?>/register.php'>Sign up here.</a></li>
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
                    <!-- fix inline web pic with txt -->




    <br>
    <br>
    <br>
</head>
    <body>
    </body>
</html>

