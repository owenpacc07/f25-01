<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="./styles.css">
    <title>Password reset</title>
</head>

<body>
    <?php require './navbar.php'; ?>
        <div class="card" id="new-password">
            <div class="card-content">
                <form action="reset-request.php" method="POST">
                    <p>An e-mail will be sent to you with instructions on how to reset your password.</p>
                    <div class="field">
                        <label class="label">Enter your e-mail address</label>
                        <div class="control">
                            <input class="input" type="email" name="email">
                        </div>
                    </div>

                    <div class="control">
                        <button class="button is-primary" type="submit" name="reset-request-submit">Submit</button>
                    </div>
                </form>
                <?php
                    if (isset($_GET["reset"])) {
                        if ($_GET["reset"] == "success") {
                            echo '<p class="signupsuccess"><strong>Check your e-mail!</strong></p>';
                        }
                    }
                ?>
            </div>
        </div>

</body>

</html>