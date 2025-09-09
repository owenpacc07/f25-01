<?php
if (isset($_POST["reset-request-submit"])) {

    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);


    //NOTE THAT THIS IS NOT RELATIVE PATH-----> CHANGE v() WHEN UPDATING VERSIONS
    $url = "https://cs.newpaltz.edu/p/s23-01/v2/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);

    $expires = date("U") + 1800;

    require "config-legacy.php";

    $userEmail = $_POST["email"];

    $sql = "DELETE FROM tokens WHERE Email=?;";
    $stmt = mysqli_stmt_init($link);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "Error";
    } else {
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        mysqli_stmt_execute($stmt);
    }

    $sql = "INSERT INTO tokens (Email, Selector, Token, Expires) VALUES (?, ?, ?, ?);";

    $stmt = mysqli_stmt_init($link);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo mysqli_stmt_error($stmt);
        exit();
    } else {
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);

    $to = $userEmail;
    $subject = 'Reset Password';
    $message = '<p>Password reset request. Click on the link below to reset your password. Ignore this email if you did not make this request</p>';
    $message .= '<a href="' . $url . '">' . $url . '</a></p>';

    $headers = "From: cs@newpaltz.edu\r\n";
    $headers .= "Reply-to: bradyc4@newpaltz.edu\r\n";
    $headers .= "Content-type: text/html\r\n";

    mail($to, $subject, $message, $headers);

    header("Location: forgot-password.php?reset=success");
} else {
    header("Location: index.php");
}
