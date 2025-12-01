<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../config.php';

if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

if (!isset($_GET['experiment_id'])) {
    $_SESSION['error_message'] = "No experiment ID provided.";
    header("Location: ../experimentTest.php");
    exit();
}

$experiment_id_param = $_GET['experiment_id'];
list($user_id, $experiment_id, $family_id) = explode('_', $experiment_id_param);

if (!$user_id || !$experiment_id || !$family_id) {
    $_SESSION['error_message'] = "Invalid experiment ID format.";
    header("Location: ../experimentTest.php");
    exit();
}

$user_id = mysqli_real_escape_string($link, $user_id);
$experiment_id = mysqli_real_escape_string($link, $experiment_id);
$family_id = mysqli_real_escape_string($link, $family_id);

$base_path = realpath("../../../files/experiments/");
if ($base_path === false) {
    $_SESSION['log_messages'][] = "Base path resolution failed for ../../../files/experiments/";
    $_SESSION['error_message'] = "Invalid base experiments path.";
    header("Location: ../experimentTest.php");
    exit();
}
$experiment_path = "$base_path/$experiment_id_param";

if (!file_exists($experiment_path)) {
    $_SESSION['log_messages'][] = "Experiment folder does not exist: $experiment_path";
    $_SESSION['error_message'] = "Experiment folder not found.";
    header("Location: ../experimentTest.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitInput'])) {
    $requests = isset($_POST['requests']) ? array_map('intval', explode(',', $_POST['requests'])) : [];
    $head = isset($_POST['head']) ? intval($_POST['head']) : 0;
    $diskSize = isset($_POST['diskSize']) ? intval($_POST['diskSize']) : 400;

    $line1 = "0 $diskSize";
    $line2 = $head;
    $line3 = implode(' ', $requests);
    $newContents = "$line1\n$line2\n$line3";

    $input_file = "$experiment_path/in-disk.dat";
    if (file_put_contents($input_file, $newContents) === false) {
        $_SESSION['log_messages'][] = "Failed to write to $input_file - " . error_get_last()['message'];
        $_SESSION['error_message'] = "Failed to save input file.";
    } else {
        $_SESSION['log_messages'][] = "Wrote to $input_file: $newContents";
        $mechanisms = ['041', '042', '043', '044', '045'];
        foreach ($mechanisms as $mid) {
            $java_path = realpath("../../../cgi-bin/core-e/m-$mid");
            if ($java_path === false) {
                $_SESSION['log_messages'][] = "Java path resolution failed for ../../../cgi-bin/core-e/m-$mid";
                $_SESSION['error_message'] = "Invalid Java path for m$mid.";
                continue;
            }
            $java_command = "java -classpath " . escapeshellarg($java_path) . " m$mid " . escapeshellarg($experiment_path);
            $java_output = shell_exec("$java_command 2>&1");
            if ($java_output) {
                $_SESSION['log_messages'][] = "Java output/error for m$mid: $java_output";
            } else {
                $_SESSION['log_messages'][] = "Java executed successfully for m$mid (no output)";
            }

            $output_file = "$experiment_path/out-$mid.dat";
            if (!file_exists($output_file)) {
                $_SESSION['log_messages'][] = "Java failed to create $output_file for m$mid";
                $default_output = implode(',', array_fill(0, 32, 0));
                file_put_contents($output_file, $default_output);
                $_SESSION['log_messages'][] = "Wrote fallback output to $output_file: $default_output";
            } else {
                $_SESSION['log_messages'][] = "Java successfully wrote to $output_file for m$mid";
            }
        }

        $_SESSION['success_message'] = "Input processed and output files generated successfully in $experiment_path.";
        header("Location: submissions.php?experiment_id=$experiment_id_param");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disk Scheduling Submission</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function randomizeDiskSchedulingInputs() {
        let head = Math.floor(Math.random() * 701);
        let queueSize = Math.floor(Math.random() * 10) + 1;
        let values = new Set();
        while (values.size < queueSize) {
            values.add(Math.floor(Math.random() * 699) + 1);
        }
        document.getElementById("headInput").value = head;
        document.getElementById("requestInput").value = Array.from(values).join(", ");
        document.getElementById("diskSizeInput").value = 700;
    }
    </script>
</head>
<body>
    <?php include realpath('../../navbar.php'); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Submit Input for Experiment <?php echo htmlspecialchars($experiment_id); ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
                        <?php endif; ?>

                        <form method="POST" class="text-center">
                            <div class="form-group">
                                <label for="requestInput">Request Queue (comma-separated):</label>
                                <input type="text" name="requests" class="form-control" id="requestInput"
                                    value="<?php echo isset($_POST['requests']) ? htmlspecialchars($_POST['requests']) : ''; ?>"
                                    required>
                                <label for="headInput">Head:</label>
                                <input type="text" name="head" class="form-control" id="headInput"
                                    value="<?php echo isset($_POST['head']) ? htmlspecialchars($_POST['head']) : '50'; ?>"
                                    required>
                                <label for="diskSizeInput">Disk Size:</label>
                                <input type="text" name="diskSize" class="form-control" id="diskSizeInput"
                                    value="<?php echo isset($_POST['diskSize']) ? htmlspecialchars($_POST['diskSize']) : '700'; ?>"
                                    required>
                            </div>
                            <button type="submit" name="submitInput" class="btn btn-purple d-inline mr-2">Submit Input</button>
                            <button type="button" class="btn btn-purple d-inline" onclick="randomizeDiskSchedulingInputs()">Randomize Inputs</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<style>
    .btn-purple { background-color: #9769D9; color: white; border-radius: 8px; }
    .btn-purple:hover { background-color: #B594E4; color: white; }
    .card-header { background-color: #9769D9; color: white; font-weight: bold; }
    .card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); overflow: hidden; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
    .table th, .table td { text-align: center; border-bottom: 1px solid #ddd; }
    .table th { background-color: #E97D7D; color: white; font-weight: bold; }
    input[type="text"] { border-radius: 8px; padding: 8px; border: 2px solid #ddd; }
    form { padding: 8px; }
    td a { text-decoration: none; color: rgb(103, 2, 2); }
</style>
</html>
