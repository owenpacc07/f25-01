<?php
error_reporting(error_reporting() & ~E_NOTICE);
ob_start();
session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">



  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

  <style>
    /* Use Titan One font for brand button */
    @font-face {
      font-family: 'Titan One';
      src: url('../fonts/TitanOne-Regular.ttf') format('truetype');
    }
    .navbar-brand {
      font-family: 'Titan One';
      font-size: 1.25rem;
      min-height:auto;
      display: inline-block;
    }
    .navbar-brand img{
      width:30px;
      height:24px;
    }
  </style>

</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <!-- Navbar content -->
    <div class="container-fluid">
      <a class="navbar-brand" href="https://cs.newpaltz.edu/p/f22-02/v2">
        <img src="/p/f22-02/files/favicon.ico" alt=""  class="d-inline-block align-text-top">
        WEBVIZ OS
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link fw-bold text-uppercase " aria-current="page" href="https://cs.newpaltz.edu/p/f22-02/v2/1-cpu/index.php">CPU Scheduling</a>
          </li>
          <li class="nav-item ">
            <a class="nav-link fw-bold text-uppercase" href="https://cs.newpaltz.edu/p/f22-02/v2/6-address/index.php">Address Translation</a>
          </li>
          <li class="nav-item   ">
            <a class="nav-link fw-bold text-uppercase" href="https://cs.newpaltz.edu/p/f22-02/v2/4-memory-allocation/index.php">Memory Allocation</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold text-uppercase" href="https://cs.newpaltz.edu/p/f22-02/v2/2-replace/index.php">Page Replacement</a>
          </li>
          <li class="nav-item">
            <a href="https://cs.newpaltz.edu/p/f22-02/v2/5-files/index.php" class="nav-link fw-bold text-uppercase">File Allocation</a>
          </li>
          <li class="nav-item">
            <a href="https://cs.newpaltz.edu/p/f22-02/v2/3-disk/index.php" class="nav-link fw-bold text-uppercase">Disk Scheduling</a>
          </li>
          <li class="nav-item">
            <a href="https://cs.newpaltz.edu/p/f22-02/v2/submission.php" class="nav-link fw-bold text-uppercase">Submit</a>
          </li>
          <li class="nav-item">
            <b><a href="https://cs.newpaltz.edu/p/f22-02/v2/runStart.php" class="nav-link fw-bold text-uppercase">START</a></b>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto mb-2">

          <?php if (!isset($_SESSION['logged_in'])) {
            echo "<a role='button' class='btn btn-primary border border-info' href='https://cs.newpaltz.edu/p/f22-02/v2/register.php'>
                <strong>Sign up</strong>
              </a>";
            echo "<a role='button' class='btn btn-light' href='https://cs.newpaltz.edu/p/f22-02/v2/login.php'>
                <strong>Log in</strong>
              </a>";
          } else {
            //if current_session_mode == admin display admin panel button 


            echo "<li class=\"nav-item dropstart\">
          <a class=\"nav-link dropdown-toggle\"  id=\"navbarDropdownMenuLink\" role=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
            Options
          </a>
          <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuLink\">
            <li><a class=\"dropdown-item\" href=\"https://cs.newpaltz.edu/p/f22-02/v2/myPage.php\">My Page</a></li>
                <li><a class=\"dropdown-item\" href=\"https://cs.newpaltz.edu/p/f22-02/v2/groupPageUser.php?id=$_SESSION[groupid]\">View Group</a></li>
                <li><a class=\"dropdown-item\" href='https://cs.newpaltz.edu/p/f22-02/v2/logout.php'>Log Out</a></li>
          </ul>
        </li>";
            if ($_SESSION['mode'] == 4 || $_SESSION['mode'] == 5) {
              echo "<li class=\"nav-item\"><a role=\"button\" href='https://cs.newpaltz.edu/p/f22-02/v2/adminPanel/index.php' class=\"btn btn-light\">Admin Panel</a></li>";
            }
          } ?>
          </li>
        </ul>
      </div>
    </div>

  </nav>
  <!--
  <nav class="navbar has-background-info" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">

      <a class="navbar-item has-text-black" href="https://cs.newpaltz.edu/p/f22-02/v2">
        OS Visualizations
      </a>
      <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarOS">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>

    </div>
    <style>
      .navbar-item:visited {
        text-decoration: none;
      }

      .navbar-item {
        text-decoration: none;
      }
    </style>

    <div id="navbarOS" class="navbar-menu">
      <div class="navbar-start">
        <a class="navbar-item has-text-black" href="https://cs.newpaltz.edu/p/f22-02/v2/1-cpu/index.php">
          CPU Scheduling
        </a>

        <a class="navbar-item has-text-black" href="https://cs.newpaltz.edu/p/f22-02/v2/6-address/index.php">
          Address Translation
        </a>

        <a class="navbar-item has-text-black" href="https://cs.newpaltz.edu/p/f22-02/v2/4-memory-allocation/index.php">
          Memory Allocation
        </a>

        <a class="navbar-item has-text-black" href="https://cs.newpaltz.edu/p/f22-02/v2/2-replace/index.php">
          Page Replacement
        </a>

        <a class="navbar-item has-text-black" href="https://cs.newpaltz.edu/p/f22-02/v2/5-files/index.php">
          File Allocation
        </a>

        <a class="navbar-item has-text-black" href="https://cs.newpaltz.edu/p/f22-02/v2/3-disk/index.php">
          Disk Scheduling
        </a>
        <a class="navbar-item has-text-black" href="https://cs.newpaltz.edu/p/f22-02/v2/coding.php">
          Coding
        </a>


      </div>

      <div class="navbar-end">

        <div class="navbar-item">
          <div class="buttons">
             If statement that makes signup/log in or logout display on the navbar 
            <?php /* if (!isset($_SESSION)) {
              echo "<a class='button is-primary' href='https://cs.newpaltz.edu/p/f22-02/v2/register.php'>
                <strong>Sign up</strong>
              </a>";
              echo "<a class='button is-light' href='https://cs.newpaltz.edu/p/f22-02/v2/login.php'>
                Log in
              </a>";
            } else {
              //if current_session_mode == admin display admin panel button 
              if ($_SESSION['mode'] == 4 || $_SESSION['mode'] == 5) {
                echo "<a class='button is-light' href='https://cs.newpaltz.edu/p/f22-02/v2/adminPanel/index.php'>
                  Admin Panel
                </a>";
              }
              echo "<div class=\"nav-item dropdown\" id=\"dpOptions\" style=\"background-color:blue; border-radius:15px;\">
              <a class=\"nav-link dropdown-toggle\" style=\"color:white;\" id=\"navbarDarkDropdownMenuLink\" role=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                Options
              </a>
              <ul class=\"dropdown-menu dropdown-menu-dark\" aria-labelledby=\"navbarDarkDropdownMenuLink\">
                <li><a class=\"dropdown-item\" href=\"https://cs.newpaltz.edu/p/f22-02/v2/myPage.php\">My Page</a></li>
                <li><a class=\"dropdown-item\" href=\"https://cs.newpaltz.edu/p/f22-02/v2/groupPageUser.php?id=$_SESSION[groupid]\">View Group</a></li>
                <li><a class=\"dropdown-item\" href='https://cs.newpaltz.edu/p/f22-02/v2/logout.php'>Log Out</a></li>
              </ul>
            </div>";
            } */ ?>
          </div>
        </div>
      </div>
    </div>
  </nav>-->
</body>
<script type="text/javascript">
  /* toggles navbar burger
  document.addEventListener('DOMContentLoaded', () => {

    // Get all "navbar-burger" elements
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Check if there are any navbar burgers
    if ($navbarBurgers.length > 0) {

      // Add a click event on each of them
      $navbarBurgers.forEach(el => {
        el.addEventListener('click', () => {

          // Get the target from the "data-target" attribute
          const target = el.dataset.target;
          const $target = document.getElementById(target);

          // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
          el.classList.toggle('is-active');
          $target.classList.toggle('is-active');

        });
      });
    }

  });*/
</script>

</html>