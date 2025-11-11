<?php
// This is a file for placing any functions that are used in multiple files. This file should only be directly imported by config.php along with any other global config files and variables.
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function verify_mode($expected_mode) {
  if ($_SESSION['mode'] == $expected_mode) {
    return true;
  }
  if (str_contains($_SERVER['SCRIPT_NAME'], '/'.$expected_mode.'/')) {
    $new_url = str_replace('/'.$expected_mode.'/', '/'.$_SESSION['mode'].'/', $_SERVER['SCRIPT_NAME']);
    header('Location: '.$new_url);
    exit();
  }

}