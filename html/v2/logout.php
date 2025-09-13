<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    //Destroying the session clears the $_SESSION variable, thus "logging" the user 
    //out. This also happens automatically when the browser is closed.



    session_destroy();
    header("Location: login.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
     
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="./styles.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Logout</title>
</head>

<body>
    <?php include './navbar.php'; ?>
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-half">
                    <form method="post" action="logout.php">
                        <p>Are You sure you want to <strong class="is-danger is-size-6"> Log Out </strong>?</p>
                        <br>
                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button is-warning" type="submit" name="submit">Yes</button>
                                <a class="button is-warning is-light" href="index.php">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>

</html>