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

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <link rel="icon" href="/https://cs.newpaltz.edu/p/<?= $semester ?><?= $version_path ?>/pic/logocircle02.png" type="image/x-icon" />
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cs.newpaltz.edu/p/<?= $semester ?><?= $version_path ?>/templates/navbarstyle.css">
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary navbar-fixed-top fixed-top">
    <!-- Navbar content -->
    <div class="container-fluid d-flex">
      <a href="<?=$SITE_ROOT . $version_path?>/index.php">
        <Button class="navbar-brand btn btn-primary logodimension" type="button">
          <img src="https://cs.newpaltz.edu/p/<?= $semester ?><?= $version_path ?>/pic/logocircle02.png" alt="" class="d-inline-block align-text-top">
          WebViz OS
        </Button>
      </a>


      <div class="collapse navbar-collapse margin " id="navbarNav">
        <div class="row align-items-center">

          <div class='col-auto d-flex justify-content-center dropdown'>
            <a role='button' class='btn btn-primary dimension fw-bold' href='<?=$SITE_ROOT . $version_path?>/<?= $coretype ?>'>
              Select Mechanism
            </a>
          </div>

          <div class='col-auto d-flex justify-content-center dropdown'>
            <a role='button' class='btn btn-primary dimension fw-bold' href='<?=$SITE_ROOT . $version_path?>/submission.php'>
              Submission
            </a>
          </div>

          <div class='col-auto d-flex justify-content-center dropdown'>
            <a role='button' class='btn btn-primary dimension fw-bold' href='<?=$SITE_ROOT . $version_path?>/guide.php'>
              Guide
            </a>
          </div>


          <div class="col-auto d-flex justify-content-center dropdown">
            <div class="vr"></div>
            <button class="btn btn-primary dimension fw-bold dropdown-toggle" type="button" id="modeDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Mode
            </button>
            <div class="dropdown-menu" aria-labelledby="modeDropdown">

              <?php
              $viewhref = '';
              $advancedhref = '';
              $viewtext = ' View';
              $advancedtext = '  Advanced';
              if ($coretype == 'core') {
                $viewtext = '✓ View';
                $advancedhref = $SITE_ROOT . $version_path . '/toggle-mode.php';
              } else {
                $advancedtext = '✓ Advanced';
                $viewhref = $SITE_ROOT . $version_path . '/toggle-mode.php';
              }

              echo "
                <a class='dropdown-item' href='$viewhref'> $viewtext </a>
                <div class='dropdown-divider'></div>
                <a class='dropdown-item' href='$advancedhref'> $advancedtext </a>
                ";
              ?>

            </div>
            <div class="vr"></div>
          </div>


          <?php if (!isset($_SESSION['logged_in'])) {
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
          } ?>


        </div>

      </div>
    </div>
    </div>
  </nav>

  <?php include __DIR__ . "/navbarvertical.php"; ?>

  <br><br><br>

</body>

<script>

</script>

</html>