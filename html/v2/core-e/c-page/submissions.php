<?php
/*  ----------  PAGE‑REPLACEMENT  submissions.php  ----------  */

session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../../login.php');  exit();
}

require '../../config.php';
require '../../system.php';

if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

if (!isset($_GET['experiment_id'])) {
    $_SESSION['error_message'] = "No experiment ID provided.";
    header("Location: ../index.php"); exit();
}
$experiment_id_param = $_GET['experiment_id'];
list($user_id, $experiment_id, $family_id) = explode('_', $experiment_id_param);
if (!$user_id || !$experiment_id || !$family_id) {
    $_SESSION['error_message'] = "Invalid experiment ID format.";
    header("Location: ../index.php"); exit();
}

$user_id = mysqli_real_escape_string($link, $user_id);
$experiment_id = mysqli_real_escape_string($link, $experiment_id);
$family_id = mysqli_real_escape_string($link, $family_id);

$base_path = realpath("../../../files/experiments/");
if ($base_path === false) {
    $_SESSION['log_messages'][] = "Base path resolution failed for ../../../files/experiments/";
    $_SESSION['error_message'] = "Invalid base experiments path.";
    header("Location: ../index.php"); exit();
}
$experiment_path = "$base_path/$experiment_id_param";

if (!file_exists($experiment_path)) {
    $_SESSION['log_messages'][] = "Experiment folder does not exist: $experiment_path";
    if (!mkdir($experiment_path, 0777, true)) {
        $_SESSION['error_message'] = "Failed to create experiment folder.";
        header("Location: ../index.php"); exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitInput'])) {
    $page_sequence = trim($_POST['page_sequence'] ?? '');
    $frames = intval($_POST['frames'] ?? 3);
    
    if (empty($page_sequence)) {
        $_SESSION['error_message'] = "No input data provided.";
    } else {
        $pages = array_map('trim', explode(',', $page_sequence));
        $formatted_content = "$frames\n" . implode(' ', $pages);

        $input_file = "$experiment_path/in-page.dat";
        if (file_put_contents($input_file, $formatted_content) === false) {
            $_SESSION['log_messages'][] = "Failed to write to $input_file - " . error_get_last()['message'];
            $_SESSION['error_message'] = "Failed to save input file.";
        } else {
            $_SESSION['log_messages'][] = "Wrote to $input_file: $formatted_content";

            $mechanisms = ['021', '022', '023', '024', '025'];

            foreach ($mechanisms as $mid) {
                $java_path = realpath("../../../cgi-bin/core-e/m-$mid");
                if ($java_path === false) {
                    $_SESSION['log_messages'][] = "Java path resolution failed for ../../../cgi-bin/core-e/m-$mid";
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
                    file_put_contents($output_file, "0"); // Create an empty output file as fallback
                }
            }
            
            $_SESSION['last_page_sequence'] = $page_sequence;
            $_SESSION['last_frames'] = $frames;
            $_SESSION['success_message'] = "Input processed successfully for experiment ID: $experiment_id.";
            header("Location: submissions.php?experiment_id=$experiment_id_param");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Replacement Submission</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function generateRandomInput() {
            const frameOptions = [3, 4, 5];
            const frames = frameOptions[Math.floor(Math.random() * frameOptions.length)];
            
            const pageCount = Math.floor(Math.random() * 6) + 10;
            let pages = [];
            for (let i = 0; i < pageCount; i++) {
                pages.push(Math.floor(Math.random() * 10));
            }
            
            document.getElementById('framesInput').value = frames;
            document.getElementById('pageSequenceInput').value = pages.join(', ');
        }
    </script>
</head>
<body>
    <?php include realpath('../../navbar.php'); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center">Page Replacement Experiment</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label for="pageSequenceInput">Page Reference Sequence (comma-separated):</label>
                                <input type="text" name="page_sequence" class="form-control" id="pageSequenceInput" 
                                    placeholder="e.g., 7, 0, 1, 2, 0, 3, 0, 4, 2, 3, 0, 3, 2"
                                    value="<?php echo isset($_POST['page_sequence']) ? htmlspecialchars($_POST['page_sequence']) : '7, 0, 1, 2, 0, 3, 0, 4, 2, 3, 0, 3, 2'; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="framesInput">Number of Frames:</label>
                                <select name="frames" class="form-control" id="framesInput">
                                    <option value="3" <?php echo (isset($_POST['frames']) && $_POST['frames'] == 3) ? 'selected' : ''; ?>>3</option>
                                    <option value="4" <?php echo (isset($_POST['frames']) && $_POST['frames'] == 4) ? 'selected' : ''; ?>>4</option>
                                    <option value="5" <?php echo (isset($_POST['frames']) && $_POST['frames'] == 5) ? 'selected' : ''; ?>>5</option>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="submitInput" class="btn btn-primary mr-2">Submit Input</button>
                                <button type="button" class="btn btn-secondary" onclick="generateRandomInput()">Generate Random Input</button>
                            </div>
                        </form>

                        <?php if (isset($_SESSION['last_page_sequence'])): ?>
                            <div class="mt-4 text-center">
                                <h4>View Mechanisms</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <a href="/p/s25-01/v2/core-e/m-021?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=021" target="_blank" class="btn btn-info btn-block">FIFO</a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="/p/s25-01/v2/core-e/m-022?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=022" target="_blank" class="btn btn-info btn-block">LRU</a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="/p/s25-01/v2/core-e/m-023?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=023" target="_blank" class="btn btn-info btn-block">OPT</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <a href="/p/s25-01/v2/core-e/m-024?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=024" target="_blank" class="btn btn-info btn-block">LFU</a>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <a href="/p/s25-01/v2/core-e/m-025?user_id=<?php echo $user_id; ?>&experiment_id=<?php echo $experiment_id_param; ?>&family_id=<?php echo $family_id; ?>&mechanism_id=025" target="_blank" class="btn btn-info btn-block">MFU</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (!empty($_SESSION['log_messages'])) {
        foreach ($_SESSION['log_messages'] as $logMessage) {
            echo "<script>console.log('" . addslashes($logMessage) . "');</script>";
        }
        unset($_SESSION['log_messages']);
    }
    ?>
</body>
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        border-radius: 10px 10px 0 0;
    }
    .btn-primary {
        background-color: #007bff;
    }
    .btn-secondary {
        background-color: #6c757d;
    }
    .btn-info {
        background-color: #17a2b8;
    }
    .btn {
        border-radius: 5px;
    }
</style>
</html>
