
    <?php
    $to = 'chapps30@gmail.com';
    $subject = 'testing';
    $message = 'testing';
    $from = 'admin@email.com';


    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Create email headers
    $headers .= 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    // Compose a simple HTML email message
    $message = '<html><body>';
    $message .= '<h1 style="color:#f40;">testing</h1>';
    $message .= '<p style="color:#080;font-size:18px;">testing</p>';
    $message .= '</body></html>';

    // Sending email
    if (mail($to, $subject, $message)) {
        echo 'Your mail has been sent successfully.';
    } else {
        echo 'Unable to send email. Please try again.';
    }
    ?>