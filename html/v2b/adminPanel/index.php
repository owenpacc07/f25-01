<?php
session_start();
$preset = "";
// check session variable
 if (isset($_SESSION['mode'])) {
    if ($_SESSION["mode"] == 4 || $_SESSION["mode"] == 5) {
        if ($_SESSION["mode"] == 4) {
            $preset = "4";
        } else {
            $preset = "5";
        }
    } else {
        //header("location: ../index.php");
    }
} else {
    //header("location: ../login.php");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Admin Panel</title>

    <style>
        .menu-link {
            display: block;

            padding: 10px 20px;

            margin: 5px 0;

            background-color: #003E7E;

            color: white;

            text-align: center;

            border: 1px solid #ddd;

            border-radius: 5px;

            text-decoration: none;

            transition: background-color 0.3s, color 0.3s;

        }

        .menu-link:hover {
            background-color: #7192B4;

            color: white;

        }
    </style>
</head>

<body onload="toggle_panel()">

    <?php include '../navbar.php'; ?>



    <div class="columns" id="admin-panel" style="margin-top:3rem;">
        <div id="menuCol" class="column is-one-fifth">
            <div class="container  rounded-end" id="menuContainer">

                <ul class="nav flex-column">
                    <li class="nav-item"><a class="menu-link" id="manageUser" onclick="toggle_panel('manage-users')">
                            <h6 class="fs-5 text-decoration-none text-center lh-lg">Dashboard</h6>
                        </a></li>
                   <!-- <li class="nav-item"><a class="menu-link" id="manageUser" onclick="toggle_panel('manage-users')">
                            <h6 class="fs-5 text-decoration-none text-center lh-lg">Manage Users</h6>
                        </a></li> -->
                    <li class="nav-item"><a class="menu-link" id="ManageGroup" onclick="toggle_panel('manage-groups')">
                            <h6 class="fs-5 text-decoration-none text-center lh-lg">Manage Groups</h6>
                        </a></li>
                    <li class="nav-item"><a class="menu-link" id="ManageModes" onclick="toggle_panel('manage-modes')">
                            <h6 class="fs-5 text-decoration-none text-center lh-lg">Manage Modes</h6>
                        </a></li>
                    <li class="nav-item"><a class="menu-link" id="ManageSubmissions"
                            onclick="toggle_panel('manage-submissions')">
                            <h6 class="fs-5 text-decoration-none text-center lh-lg">Manage Submissions</h6>
                        </a></li>


		    <li class="nav-item"><a class="menu-link" id="ManageExperiments"
                            onclick="toggle_panel('manage-experiments')">
                            <h6 class="fs-5 text-decoration-none text-center lh-lg">Manage Experiments</h6>
                        </a></li>




                    <li class="nav-item"><a class="menu-link" style="color: white; text-decoration: none;"
                            id="EditViewMode" href="./editViewEnter">
                            <h6 class="fs-5 text-decoration-none text-center lh-lg">Edit (View) DATA</h6>
                        </a></li>
                </ul>

            </div>
        </div>

        <div class="column" id="panelHolder">
           <!-- <div id="dashboardPanel">
                <?php include './dashboardPanel.php'; ?>
            </div> -->
            <div id="manageUsersPanel">
                <?php include './manageUsers.php'; ?>
            </div>
            <div id="manageGroupsPanel">
                <?php include './manageGroups.php'; ?>
            </div>
            <div id="manageModesPanel">
                <?php include './manageModes.php'; ?>
            </div>
            <div id="manageSubmissionsPanel">
                <?php include './manageSubmissions.php'; ?>
            </div>
	    <div id="manageExperimentsPanel">
                <?php include './manageExperiments.php'; ?>
            </div>
        </div>
    </div>
    </div>

</body>


<script>
    function toggle_panel(showPanel) {
        var url_string = window.location.href;
        var url = new URL(url_string);
        var c = url.searchParams.get("from");

        if (c) {
            showPanel = c;
            window.history.pushState(null, null, window.location.pathname);
        }

        switch (showPanel) {
            case 'fileViewer':
                window.location.href = "https://cs.newpaltz.edu/p/s24-02/v2/adminPanel/fileViewer.php";
                break;
            case 'manage-users':
                document.getElementById('manageUsersPanel').style.display = "block";
                document.getElementById('manageGroupsPanel').style.display = "none";
                document.getElementById('manageModesPanel').style.display = "none";
                document.getElementById('manageSubmissionsPanel').style.display = "none";
		document.getElementById('manageExperimentsPanel').style.display = "none";
                document.getElementById('dashboardPanel').style.display = "none";
                break;
            case 'manage-groups':
                document.getElementById('manageUsersPanel').style.display = "none";
                document.getElementById('manageGroupsPanel').style.display = "block";
                document.getElementById('manageModesPanel').style.display = "none";
                document.getElementById('manageSubmissionsPanel').style.display = "none";
		document.getElementById('manageExperimentsPanel').style.display = "none";
                document.getElementById('dashboardPanel').style.display = "none";
                break;
            case 'manage-modes':
                document.getElementById('manageUsersPanel').style.display = "none";
                document.getElementById('manageGroupsPanel').style.display = "none";
                document.getElementById('manageModesPanel').style.display = "block";
                document.getElementById('manageSubmissionsPanel').style.display = "none";
		document.getElementById('manageExperimentsPanel').style.display = "none";
                document.getElementById('dashboardPanel').style.display = "none";
                break;
            case 'manage-submissions':
                document.getElementById('manageUsersPanel').style.display = "none";
                document.getElementById('manageGroupsPanel').style.display = "none";
                document.getElementById('manageModesPanel').style.display = "none";
                document.getElementById('manageSubmissionsPanel').style.display = "block";
		document.getElementById('manageExperimentsPanel').style.display = "none";
                document.getElementById('dashboardPanel').style.display = "none";
                break;

	     case 'manage-experiments':
                document.getElementById('manageUsersPanel').style.display = "none";
                document.getElementById('manageGroupsPanel').style.display = "none";
                document.getElementById('manageModesPanel').style.display = "none";
                document.getElementById('manageSubmissionsPanel').style.display = "none";
		document.getElementById('manageExperimentsPanel').style.display = "block";
                document.getElementById('dashboardPanel').style.display = "none";
                break;

            default:
                document.getElementById('manageUsersPanel').style.display = "block";
                document.getElementById('manageGroupsPanel').style.display = "none";
                document.getElementById('manageModesPanel').style.display = "none";
                document.getElementById('manageSubmissionsPanel').style.display = "none";
		document.getElementById('manageExperimentsPanel').style.display = "none";
                document.getElementById('dashboardPanel').style.display = "none";
                break;
        }

    }
</script>


</html>