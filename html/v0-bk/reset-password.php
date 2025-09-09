<?php
if (isset($_POST["reset-password-submit"])) {

    $selector = $_POST["selector"];
    $validator = $_POST["validator"];
    $password = $_POST["pwd"];
    $passwordRepeat = $_POST["pwd-repeat"];

    if (empty($password) || empty($validator)) {
        header("Location: login.php");
        exit();
    } else if ($password != $passwordRepeat) {
        header("Location: login.php");
        exit();
    }

    $currentDate = date("U");

    require 'config-legacy.php';

    $sql = "SELECT * FROM tokens WHERE Selector = ? AND Expires >= ?;";
    $stmt = mysqli_stmt_init($link);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "1There was an error!";
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $selector, $currentDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$row = mysqli_fetch_assoc($result)) {
            echo date("U");
            exit();
        } else {
            $tokenBin = hex2bin($validator);
            $tokenCheck = password_verify($tokenBin, $row["Token"]);

            if ($tokenCheck === false) {
                echo "You need to re-submit your reset request.";
                exit();
            } elseif ($tokenCheck === true) {
                $tokenEmail = $row['Email'];
                $sql = "SELECT * FROM users WHERE Email=?;";
                $stmt = mysqli_stmt_init($link);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "3There was an error!";
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if (!$row = mysqli_fetch_assoc($result)) {
                        echo "4Error";
                        exit();
                    } else {
                        $sql = "UPDATE users SET Password = ? WHERE Email = ?;";
                        $stmt = mysqli_stmt_init($link);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            echo "5There was an error!";
                            exit();
                        } else {
                            $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "ss", $password, $tokenEmail);
                            mysqli_stmt_execute($stmt);

                            $sql = "DELETE FROM tokens WHERE Email=?;";
                            $stmt = mysqli_stmt_init($link);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                echo "6Error";
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $userEmail);
                                mysqli_stmt_execute($stmt);
                                header("Location: login.php?newpwd=passwordupdated");
                            }
                        }
                    }
                }
            }
        }
    }
} else {
}
