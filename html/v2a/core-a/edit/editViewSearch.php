<?php


session_start();
//if no no user is logged in they cant access advanced mode
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['mechanismid'] = $_POST['mechanismid'];
    header("Location: ./editView.php");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Run a Mechanism</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" type="text/css" href="styles/index_styles.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

<?php include '../../navbar.php'; ?>

    <main>
        <section>
            <br>
            <div class="d-flex align-items-center justify-content-center">
                <!-- redirect to visualization page w/ mechanismid -->
                <form id="midForm" method="post" enctype="multipart/form-data" action="">
                    Mechanism ID: <input type="text" name="mechanismid" id="mechanismid" required>
                    <input id="formBtn" class="btn btn-primary" type="submit" value="Submit">
                </form>
            </div>
        </section>
    </main>

</body>
</html>