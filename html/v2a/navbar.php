<?php
error_reporting(error_reporting() & ~E_NOTICE);
ob_start();
session_start();

include_once(__DIR__ . '/system.php');

// Define mechanism families
$mechanism_families = [
    'CPU Scheduling' => [
        ['id' => 'm-001', 'name' => 'FCFS'],
        ['id' => 'm-002', 'name' => 'Nonpreemptive SJF'],
        ['id' => 'm-003', 'name' => 'Nonpreemptive Priority Hi'],
        ['id' => 'm-004', 'name' => 'Nonpreemptive Priority Lo'],
        ['id' => 'm-005', 'name' => 'Round Robin'],
        ['id' => 'm-006', 'name' => 'Preemptive SJF'],
        ['id' => 'm-007', 'name' => 'Preemptive Priority (High)'],
        ['id' => 'm-008', 'name' => 'Preemptive Priority (Low)'],
    ],
    'Page Replacement' => [
        ['id' => 'm-021', 'name' => 'FIFO'],
        ['id' => 'm-022', 'name' => 'Optimal'],
        ['id' => 'm-023', 'name' => 'LRU'],
        ['id' => 'm-024', 'name' => 'LFU'],
        ['id' => 'm-025', 'name' => 'MFU'],
    ],
    'Disk Scheduling' => [
        ['id' => 'm-041', 'name' => 'FCFS'],
        ['id' => 'm-042', 'name' => 'SSTF'],
        ['id' => 'm-043', 'name' => 'CSCAN'],
        ['id' => 'm-044', 'name' => 'LOOK'],
        ['id' => 'm-045', 'name' => 'CLOOK'],
    ],
    'Memory Allocation' => [
        ['id' => 'm-011', 'name' => 'First Fit'],
        ['id' => 'm-012', 'name' => 'Best Fit'],
        ['id' => 'm-013', 'name' => 'Worst Fit'],
    ],
    'File Allocation' => [
        ['id' => 'm-031', 'name' => 'Contiguous'],
        ['id' => 'm-032', 'name' => 'Linked'],
        ['id' => 'm-033', 'name' => 'Indexed'],
    ],
    'Concepts' => [
        ['id' => 'p1-process-states', 'name' => 'Process States'],
        ['id' => 'p2-deadlock', 'name' => 'Deadlock'],
        ['id' => '6-address', 'name' => 'Address Translation'],
        ['id' => 'p4-memory-access', 'name' => 'Memory Access'],
        ['id' => 'p5-io-cycle', 'name' => 'IO Cycle'],
        ['id' => 'p6-access-control', 'name' => 'Access Control'],
        ['id' => 'p7-memory-layout', 'name' => 'Memory Layout'],
    ],
];

$coretype = 'core';
$subdirectory = '';

$uri = $_SERVER['REQUEST_URI'];
if ( strpos($uri, $version_path . '/core-a/') !== false
|| basename($_SERVER['PHP_SELF']) === 'index-a.php' ) {
$_SESSION['coremode'] = 'core-a';
}
elseif ( strpos($uri, $version_path . '/core-c/') !== false
|| basename($_SERVER['PHP_SELF']) === 'index-e.php' ) {
$_SESSION['coremode'] = 'core-c';
}
else {
$_SESSION['coremode'] = 'core';
}




// Set coretype based on session
if (isset($_SESSION['coremode']) && in_array($_SESSION['coremode'], ['core', 'core-a', 'core-c'])) {
    $coretype = $_SESSION['coremode'];
} else {
    $_SESSION['coremode'] = 'core';
    $coretype = 'core';
}

$adminbutton = "";
if (!isset($_SESSION['logged_in'])) {
    $login = '<li class="nav-item"><a class="nav-link active" href="' . $SITE_ROOT . $version_path . '/login.php">Login</a></li>';
    $settings = '<li class="nav-item"><a class="nav-link active" href="' . $SITE_ROOT . $version_path . '/register.php">Sign Up</a></li>';
    $logout = "";
} else {
    if ($_SESSION['mode'] == 4 || $_SESSION['mode'] == 5) {
        $adminbutton = '<li class="nav-item"><a class="nav-link active" href="' . $SITE_ROOT . $version_path . '/adminPanel/index.php">Admin</a></li>';
    }
    $login = "";
    $settings = '<li class="nav-item"><a class="nav-link active" href="' . $SITE_ROOT . $version_path . '/myPage.php">Settings</a></li>';
    $logout = '<li class="nav-item"><a class="nav-link active" href="' . $SITE_ROOT . $version_path . '/logout.php">Logout</a></li>';
    $email = $_SESSION['email'];
    $username = substr($email, 0, strpos($email, "@"));
    $welcomeMessage = '<span class="navbar-text" style="color: white; margin: 0 10px;">Welcome, ' . htmlspecialchars($username) . '</span>';
}

$infobutton = "";
$url = $_SERVER['REQUEST_URI'];
if (strpos($url, 'info')) {
    $infobutton = "";
} else if (strpos($url, 'core/m-')) {
    $infobutton = "<a href='./info'><img class='topcorner' style='height: 40px; width: 40px' src='" . $SITE_ROOT . $version_path . "/questionicon.png'></a>";
}

// Determine mode display text
// $modeDisplay = ($coretype == 'core') ? 'View' : 'Advanced';
switch ($coretype) {
case 'core':
$modeDisplay = 'View';
break;
case 'core-a':
$modeDisplay = 'Advanced';
break;
case 'core-c':
$modeDisplay = 'Research';
break;
default:
$modeDisplay = 'View';
break;
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

.nav-link {
  transition: color 0.3s ease;
}

.nav-link:hover {
    background-color: rgb(183, 59, 31);
}

.dropdown-menu .dropdown-item:hover {
    background-color: #EAECEE;
    color: white;
}

.navbar-nav .nav-link.active {
    font-weight: bold;
    color: white;
    background-color: #e94e19;
}

.navbar-nav .nav-link.active:hover {
    color:rgb(31, 82, 183);
}

.navbar-text {
    font-weight: bold;
    color: white;
}

.dimension {
    height: 60px;
    width: 130px;
}

.logodimension {
    height: 70px;
    width: 200px;
}

.topcorner {
    position: fixed;
    top: 60px;
    right: 10px;
}

/* Space out family dropdowns */
.dropdown {
    margin-right: 20px;
}

/* Ensure dropdown menu doesn’t expand navbar */
.dropdown-menu {
    position: absolute;
    top: 100%;
    z-index: 1000;
    background-image: url(<?= $SITE_ROOT . $version_path ?>/pic/newTabBackground.jpg);
}

/* Offcanvas menu styling */
.offcanvas.offcanvas-end {
    background-color: #0E3386 !important;
    width: 300px !important; /* Narrower flyout window */
}

.offcanvas-header {
    background-color: #0E3386 !important;
}

.offcanvas-body {
    background-color: #0E3386 !important;
    padding: 1rem;
}

.offcanvas-body .nav-link,
.offcanvas-body .dropdown-toggle,
.offcanvas-body .nav-link.active,
.offcanvas-body .dropdown-toggle.active {
    color: white !important;
    background-color: #1F5AB7; /* Default blue */
    border-radius: 8px; /* Rounded edges */
    margin: 5px auto; /* Center horizontally */
    padding: 8px 12px; /* Reduced padding */
    width: 200px; /* Consistent width */
    text-align: center;
    display: block; /* Consistent block behavior */
    box-sizing: border-box; /* Ensure padding doesn’t affect width */
}

.offcanvas-body .nav-item,
.offcanvas-body .nav-item.dropdown {
    display: flex;
    justify-content: center; /* Center all nav-items */
    width: 100%; /* Ensure full width for consistent alignment */
    margin: 0; /* Remove any default margins */
    padding: 0; /* Remove any default padding */
}

.offcanvas-body .dropdown {
    margin: 0; /* Remove Bootstrap dropdown offsets */
    padding: 0;
}

/* Specific styling for Mode: View and Mode: Advanced dropdown items */
.offcanvas-body .dropdown-menu {
    margin: 0 auto; /* Center dropdown menu */
    width: 180px; /* Match dropdown item width */
    background-color: transparent; /* Remove background interference */
}

.offcanvas-body .dropdown-menu .dropdown-item,
.offcanvas-body .dropdown-menu .dropdown-item.active {
    color: white !important;
    background-color: #1F5AB7; /* Default blue */
    border-radius: 4px;
    margin: 5px auto; /* Center horizontally */
    padding: 8px 12px; /* Reduced padding */
    width: 150px; /* Slightly smaller than main buttons */
    text-align: left;
    box-sizing: border-box; /* Consistent sizing */
}

.offcanvas-body .nav-link:hover,
.offcanvas-body .dropdown-toggle:hover,
.offcanvas-body .nav-link.active:hover,
.offcanvas-body .dropdown-toggle.active:hover {
    color: white !important;
    background-color: #ff7043 !important; /* Orange on hover */
}

.offcanvas-body .dropdown-menu .dropdown-item:hover,
.offcanvas-body .dropdown-menu .dropdown-item.active:hover {
    color: white !important;
    background-color: #ff7043 !important; /* Orange on hover */
}

/* Search form specific styling */
.offcanvas-body .form-control {
    background-color: white;
    color: black !important;
    border-radius: 8px;
    margin: 5px auto; /* Center horizontally */
    padding: 10px 15px;
    width: 200px; /* Match button width */
    box-sizing: border-box; /* Consistent sizing */
    text-align: center;
}

.offcanvas-body .btn {
    color: white !important;
    background-color: #4B0082;
    border-radius: 8px;
    margin: 5px auto; /* Center horizontally */
    padding: 8px 12px; /* Match button padding */
    width: 200px; /* Match button width */
    box-sizing: border-box; /* Consistent sizing */
}

/* Mode display styling */
.mode-display {
    color: white;
    font-weight: bold;
    margin-right: 10px;
}
</style>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script>
        const SITE_VERSION = `<?php echo $SITE_ROOT . $version_path ?>`;
    </script>
</head>

<body>
<nav class="navbar navbar-dark fixed-top" style="background-color:#e94e19;">
    <div class="container-fluid">
        <!-- Logo -->
        <div style="display: inline-grid">
            <a style="margin-bottom: 0px; font-size: 20px; font-weight: bold; color: white; text-decoration: none; display: grid; grid-template-columns: auto auto auto auto;" href="<?= $SITE_ROOT . $version_path ?>/index.php">
                <img src="<?= $SITE_ROOT . $version_path ?>/pic/OSVisualsIcon.ico" width="30" height="30" class="d-inline-block align-text-top" style="margin-right: 7px; display: flex">
                OS Visuals
            </a>
        </div>

        <!-- Family Dropdowns -->
        <div class="d-flex">
            <?php foreach ($mechanism_families as $family => $mechanisms): ?>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle active text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= htmlspecialchars($family) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <?php foreach ($mechanisms as $mechanism): ?>
                            <li>
                                <?php
				$validCoreTypes = ['core','core-a','core-c'];
				$effectiveCore = in_array($coretype, $validCoreTypes, true) ? $coretype : 'core';
                                // Use different URL structure for Concepts family
                                $linkPath = ($family == 'Concepts')
                                    ? $SITE_ROOT . $version_path . '/' . $mechanism['id']
                                    : $SITE_ROOT . $version_path . '/' . $effectiveCore . '/' . $mechanism['id'];
                                ?>
                                <a class="dropdown-item text-dark" href="<?= $linkPath ?>">
                                    <?= htmlspecialchars($mechanism['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Mode Display and Toggler -->
        <div class="d-flex align-items-center">
            <span class="navbar-text mode-display">Mode: <?= htmlspecialchars($modeDisplay) ?></span>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
</nav>

<!-- Offcanvas Menu -->
<div class="offcanvas offcanvas-end font-weight-bold" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel" style="text-decoration: none; color: #00BFFF">OS Visuals</h5>
        <div class="ml-auto" style="margin: 0 10px;">
            <?= isset($welcomeMessage) ? $welcomeMessage : '' ?>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <!-- Select Mode Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Select Mode
                </a>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="<?= $SITE_ROOT . $version_path ?>/index.php">Mode: View</a></li>
                    <li><a class="dropdown-item" href="<?= $SITE_ROOT . $version_path ?>/index-a.php">Mode: Advanced</a></li>
		    <li><a class="dropdown-item" href="<?= $SITE_ROOT . $version_path ?>/index-e.php">Mode: Research</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="<?= $SITE_ROOT . $version_path ?>/index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $SITE_ROOT . $version_path ?>/core">Select Mechanism to View</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $SITE_ROOT . $version_path ?>/core-e">Do an Experiment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $SITE_ROOT . $version_path ?>/core-s">Make a Submission</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $SITE_ROOT . $version_path ?>/guide.php">About</a>
            </li>
            <hr>
            <?= $adminbutton ?>
            <?= $login ?>
            <?= $settings ?>
            <?= $logout ?>
        </ul>
    </div>
</div>

<?= $infobutton ?>
<br><br><br>
</body>
</html>
