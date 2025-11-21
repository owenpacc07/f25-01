<?php
error_reporting(error_reporting() & ~E_NOTICE);
ob_start();
session_start();
include(__DIR__ . '../../variables/var.php');
include(__DIR__ . '../../system.php');

$coretype = 'core';
$subdirectory = '';

if (isset($_SESSION['coremode'])) {
  $coretype = $_SESSION['coremode'];
} else {
  $_SESSION['coremode'] = "core";
  $coretype = "core";
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

    <!-- my own styles sheet -->
    <link rel="icon" href="pic/logocircle02.png" type="image/x-icon" />
    <link rel="stylesheet" href="../templates/navbarstyle.css">

         <!-- navbar 2, for subdirectory folders in /core -->
         <nav class="hide navbar navbar-dark fixed-top" style="background-color:#003e7e;">
          <div class="container-fluid">
          <div>
            <a style="font-size: 20px; font-weight: bold; color: white; text-decoration: none; display: grid; grid-template-columns: auto auto auto auto;" href="../index.php">
            <img src="../pic/logocircle02.png" width="30" height="30"  class="d-inline-block align-text-top" style="margin-right: 7px; display: flex">  
              WebViz OS
            </a>
          </div>
            <button style="margin-bottom: 3px; margin-top:3px;" class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
              <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel"> <button href="#" style="margin-bottom:0; border: none; outline: none; font: bold; background: none; color: white" href="../index.php">WebViz OS</button></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close" ></button>
              </div>
              <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">

                  <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../index.php">Home</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link active" href="../core">Select Mechanism  </a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link active" href="../submission.php">Submission</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link active" href="../guide.php">About</a>
                  </li>
                  
                  <li class="nav-item">
                  </li>

                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                      <li><a class="dropdown-item" href="../core">Mode: View</a></li>
                      <li><a class="dropdown-item" href="../core-a">Mode: Advanced</a></li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li><a class="dropdown-item" href='/login.php'>Login</a></li>
                      <li><a class="dropdown-item" href='../register.php'>Sign up here.</a></li>
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

