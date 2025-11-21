
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="./styles.css">
    <title></title>
</head>

<body>

    <?php require './navbar.php'; ?>

    <main>
        <div class="card">
            <section class="card-content">
                <?php
                    $selector = $_GET["selector"];
                    $validator = $_GET["validator"];

                    if(empty($selector) || empty($validator)) {
                        echo "Could not validate your request!";
                    } else {
                        if(ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) {
                            ?>
                                <form action="reset-password.php" method="post">
                                    <input type="hidden" name="selector" value="<?php echo $selector ?>">
                                    <input type="hidden" name="validator" value="<?php echo $validator ?>">
                                    <div class="field">
                                        <label class="label">Enter a new password</label>
                                        <div class="control">
                                            <input class="input" type="password" name="pwd" placeholder="Enter a new password">
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Repeat new password</label>
                                        <div class="control">
                                            <input class="input" type="password" name="pwd-repeat" placeholder="Repeat new password">
                                        </div>
                                    </div>
                                    <button class="button is-primary" type="submit" name="reset-password-submit">Reset Password</button>
                                </form>
                            <?php
                        }
                    }
                ?>
            </section>
        </div>
    </main>

</body>

</html>