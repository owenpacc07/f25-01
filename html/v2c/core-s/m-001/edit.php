<?php

$mid = basename(dirname(__FILE__));
$mid = str_replace('m-', '', $mid);
$mid = (int)$mid;
$mid_padded = str_pad($mid, 3, '0', STR_PAD_LEFT);

require_once './../../config.php';
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: ./../../login.php');
    exit();
}

$user = $_SESSION['userid'];

// Session key for persistent submission tracking
$session_key = "submission_m{$mid_padded}_uid{$user}";

// Debug logging
if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

$_SESSION['log_messages'][] = "DEBUG edit.php - MID: $mid, MID_PADDED: $mid_padded, USER: $user, SESSION_KEY: $session_key";

// Get submission_id from session (should already exist from homepage)
$submission_id = $_SESSION[$session_key] ?? null;

$_SESSION['log_messages'][] = "DEBUG edit.php - Retrieved submission_id: " . ($submission_id ?? 'NULL');

// Verify and load submission
$submission = null;
if ($submission_id) {
    $submission_query = "SELECT input_path, output_path FROM submissions WHERE submission_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($link, $submission_query);
    mysqli_stmt_bind_param($stmt, "ii", $submission_id, $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $submission = mysqli_fetch_assoc($result);
    
    $_SESSION['log_messages'][] = "DEBUG edit.php - Submission query result: " . json_encode($submission);
    
    if (!$submission) {
        // Submission doesn't exist, clear session
        $submission_id = null;
        unset($_SESSION[$session_key]);
        $_SESSION['log_messages'][] = "DEBUG edit.php - Submission not found in database, cleared session";
    } else {
        // Fix incomplete paths if they don't start with /
        if ($submission['input_path'] && strpos($submission['input_path'], '/') !== 0) {
            $submissions_base = realpath("../../../files/submissions/");
            $submission['input_path'] = $submissions_base . $submission['input_path'];
            $submission['output_path'] = $submissions_base . $submission['output_path'];
            $_SESSION['log_messages'][] = "DEBUG edit.php - Fixed incomplete paths";
        }
    }
}

// Load data ONLY from submission files (don't fall back to v2 defaults)
$input = '';
$output = '';
$format = '';

if ($submission) {
    $_SESSION['log_messages'][] = "DEBUG edit.php - Loading from submission paths:";
    $_SESSION['log_messages'][] = "DEBUG edit.php - Input path: " . $submission['input_path'];
    $_SESSION['log_messages'][] = "DEBUG edit.php - Output path: " . $submission['output_path'];

    $subDir = dirname($submission['input_path']) . '/';
    $in1Path   = $subDir . "in-$mid.dat";
    $inPadPath = $subDir . "in-$mid_padded.dat";
    $out1Path   = $subDir . "out-$mid.dat";
    $outPadPath = $subDir . "out-$mid_padded.dat";

    // Prefer padded input; fallback to unpadded
    if (file_exists($inPadPath) && filesize($inPadPath) > 0) {
        $input = file_get_contents($inPadPath);
    } elseif (file_exists($in1Path)) {
        $input = file_get_contents($in1Path);
    } else {
        $input = '';
    }

    // Prefer padded output; fallback to unpadded
    if (file_exists($outPadPath) && filesize($outPadPath) > 0) {
        $output = file_get_contents($outPadPath);
    } elseif (file_exists($out1Path)) {
        $output = file_get_contents($out1Path);
    } else {
        $output = '';
    }

    // Ensure input has data; write to BOTH for compatibility
    if (empty(trim($input))) {
        $input = "1 0 7 1\n2 2 4 2\n3 4 1 3";
        @file_put_contents($inPadPath, $input);
        @file_put_contents($in1Path, $input);
        $_SESSION['log_messages'][] = "DEBUG edit.php - Seeded default input to both in-$mid_padded.dat and in-$mid.dat";
    }

    $_SESSION['log_messages'][] = "DEBUG edit.php - Input(001) exists: " . (file_exists($inPadPath) ? 'YES' : 'NO');
    $_SESSION['log_messages'][] = "DEBUG edit.php - Input(1) exists: " . (file_exists($in1Path) ? 'YES' : 'NO');
    $_SESSION['log_messages'][] = "DEBUG edit.php - Output(1) exists: " . (file_exists($out1Path) ? 'YES' : 'NO');
    $_SESSION['log_messages'][] = "DEBUG edit.php - Output(001) exists: " . (file_exists($outPadPath) ? 'YES' : 'NO');
    $_SESSION['log_messages'][] = "DEBUG edit.php - Input content length: " . strlen($input);
    $_SESSION['log_messages'][] = "DEBUG edit.php - Output content length: " . strlen($output);
} else {
    $_SESSION['log_messages'][] = "DEBUG edit.php - NO SUBMISSION FOUND - Please create a submission first";
    // Don't load any default data - force user to create submission
}

// Load format from v2c format files (these are read-only reference files)
$format_path = realpath("../../../files/core-s/m-$mid_padded");
if (!$format_path) {
    $format_path = realpath("../../files/core-s/m-$mid_padded");
}
if ($format_path) {
    $format_file = $format_path . "/format-$mid.txt";
    if (file_exists($format_file)) {
        $format = file_get_contents($format_file);
    } else {
        // If format file doesn't exist, create a default one
        $format = "Process Scheduling Algorithm Format\n\nInput Format:\nprocess_id arrival_time burst_time priority\n\nExample:\n1 0 7 1\n2 2 4 2\n3 4 1 3\n\nOutput Format:\nType of Scheduler: [Algorithm Name]\nNumber of Processes: [count]\n\n[process_id],[start_time],[end_time]";
    }
    $_SESSION['log_messages'][] = "DEBUG edit.php - Format loaded from: $format_file";
} else {
    $format = 'Format directory not found';
    $_SESSION['log_messages'][] = "DEBUG edit.php - Format directory not found: ../../../files/core-s/m-$mid_padded";
}

$saveMessage = '';

if (isset($_POST['action']) && $_POST['action'] === 'execute_java') {
    ob_start();
    try {
        if (!isset($_SESSION['userid'])) {
            ob_end_clean();
            $response = ['success' => false, 'output' => 'Error: User not logged in'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        $javaCode = $_POST['java_code'] ?? '';
        if (!$javaCode) throw new Exception("No Java code provided");
        
        if (!$submission_id) throw new Exception("No active submission. Please use 'Make a Submission' button.");
        
        // Get submission folder
        $submission_query = "SELECT input_path FROM submissions WHERE submission_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($link, $submission_query);
        mysqli_stmt_bind_param($stmt, "ii", $submission_id, $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sub_data = mysqli_fetch_assoc($result);
        
        if (!$sub_data) throw new Exception("Submission not found");
        
        $subDir = dirname($sub_data['input_path']) . '/';
        $in1Path   = $subDir . "in-$mid.dat";
        $inPadPath = $subDir . "in-$mid_padded.dat";
        $out1Path   = $subDir . "out-$mid.dat";
        $outPadPath = $subDir . "out-$mid_padded.dat";

        // Always write current textarea input to BOTH
        $inputContent = $_POST['input_content'] ?? '';
        if (empty(trim($inputContent))) {
            $inputContent = "1 0 7 1\n2 2 4 2\n3 4 1 3";
        }
        @file_put_contents($inPadPath, $inputContent);
        @file_put_contents($in1Path, $inputContent);

        // Determine which input file will be used (padded preferred)
        $inputFileUsed = (file_exists($inPadPath) ? $inPadPath : $in1Path);
        $inputFileBasename = basename($inputFileUsed);

        $fileName = 'Main.java';
        file_put_contents($subDir . $fileName, $javaCode);

        $className = 'Main';
        $compileCmd = "cd " . escapeshellarg($subDir) . " && javac " . escapeshellarg($fileName) . " 2>&1";
        $compileOutput = shell_exec($compileCmd);

        $response = [];
        if (empty($compileOutput) || strpos($compileOutput, 'error') === false) {
            // Pass the PADDED input filename to match v2 behaviour
            $inputArg = escapeshellarg("in-$mid_padded.dat");
            $runCmd = "cd " . escapeshellarg($subDir) . " && timeout 5 java Main " . $inputArg . " 2>&1";
            $runOutput = shell_exec($runCmd);

            // Append input file contents to output
            $inputFileContentsEcho = file_exists($inputFileUsed) ? file_get_contents($inputFileUsed) : '(input file not found)';
            $runOutput .= "\n\n--- Input File Used: $inputFileBasename ---\n" . $inputFileContentsEcho;

            // After run, prefer padded output; sync to unpadded for compatibility
            if (file_exists($outPadPath)) {
                @copy($outPadPath, $out1Path);
            }

            // Read output back: prefer padded when non-empty
            $outFileToRead = null;
            if (file_exists($outPadPath) && filesize($outPadPath) > 0) {
                $outFileToRead = $outPadPath;
            } elseif (file_exists($out1Path)) {
                $outFileToRead = $out1Path;
            }

            if ($outFileToRead) {
                $fileOutput = file_get_contents($outFileToRead);
                $runOutput .= "\n\n--- Output File Contents ---\n" . $fileOutput;
            }

            $_SESSION['log_messages'][] = "DEBUG edit.php - Appended input file ($inputFileBasename) contents to run output (length " . strlen($inputFileContentsEcho) . ")";
            $response = [
                'success' => true,
                'output' => $runOutput ?: 'Program executed successfully with no output.',
                'path' => $subDir,
                'submission_id' => $submission_id
            ];
        } else {
            $response = ['success' => false, 'output' => $compileOutput];
        }
        
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        ob_end_clean();
        $response = ['success' => false, 'output' => 'Exception: ' . $e->getMessage()];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

if (isset($_POST['submit'])) {
    try {
        if (!$submission_id) {
            throw new Exception("No active submission. Please use 'Make a Submission' button on the homepage.");
        }
        
        // Get submission folder
        $submission_query = "SELECT input_path FROM submissions WHERE submission_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($link, $submission_query);
        mysqli_stmt_bind_param($stmt, "ii", $submission_id, $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sub_data = mysqli_fetch_assoc($result);
        
        if (!$sub_data) throw new Exception("Submission not found");
        
        $subDir = dirname($sub_data['input_path']) . '/';
        // Save to BOTH file name styles to keep v2/v2c in sync
        @file_put_contents($subDir . "in-$mid_padded.dat", $_POST['input']);
        @file_put_contents($subDir . "in-$mid.dat", $_POST['input']);
        @file_put_contents($subDir . "out-$mid_padded.dat", $_POST['output']);
        @file_put_contents($subDir . "out-$mid.dat", $_POST['output']);

        if ($_POST['submit'] === 'proceed') {
            header("Location: ./");
            exit();
        } else {
            $saveMessage = '<div class="alert alert-success text-center" role="alert">Data saved to submission #' . $submission_id . '!</div>';
            // Prefer padded when reloading, fallback to unpadded
            $reloadIn  = file_exists($subDir . "in-$mid_padded.dat") ? "in-$mid_padded.dat" : "in-$mid.dat";
            $reloadOut = file_exists($subDir . "out-$mid_padded.dat") && filesize($subDir . "out-$mid_padded.dat") > 0
                ? "out-$mid_padded.dat" : "out-$mid.dat";
            $input  = file_get_contents($subDir . $reloadIn);
            $output = file_get_contents($subDir . $reloadOut);
        }
    } catch (Exception $e) {
        $saveMessage = '<div class="alert alert-danger text-center" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

?>

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>M-<?php echo $mid; ?> Edit Data</title>
    <link rel="icon" href="/p/s23-01/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        /* Main header styling */
        #description {
            font-weight: bold;
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
        }
        
        /* Box headers styling */
        label {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 10px;
            display: block;
            text-align: center;
        }
        
        /* Custom Code Submission Box Styling */
        .form-control {
            border: 2px solid #007bff;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        /* Input textarea - dark theme with green text */
        #input {
            background-color: #1e1e1e;
            color: #4ec9b0;
        }
        
        #input:focus {
            background-color: #1e1e1e;
            color: #4ec9b0;
            border-color: #007acc;
            box-shadow: 0 0 8px rgba(0,122,204,0.5);
            outline: none;
        }
        
        /* Output textarea - dark navy theme with cyan text */
        #output {
            background-color: #0e1621;
            color: #9cdcfe;
        }
        
        #output:focus {
            background-color: #0e1621;
            color: #9cdcfe;
            border-color: #007acc;
            box-shadow: 0 0 8px rgba(0,122,204,0.5);
            outline: none;
        }
        
        /* Format textarea - light theme */
        #format {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        /* Button group styling */
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .container {
            max-width: 1200px;
        }
        
        /* Center the textareas within their columns */
        .row {
            display: flex;
            justify-content: center;
        }
        
        .col-md-4 {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        /* Custom Java Code Submission Box */
        #codeSubmissionSection {
            margin-top: 40px;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        #codeSubmissionSection h5 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.3rem;
            color: #2c3e50;
        }
        
        #javaCode {
            width: 100%;
            min-height: 300px;
            background-color: #282c34;
            color: #abb2bf;
            border: 2px solid #61afef;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            padding: 18px;
            resize: vertical;
            transition: all 0.3s ease;
        }
        
        #javaCode:focus {
            background-color: #282c34;
            color: #abb2bf;
            border-color: #98c379;
            box-shadow: 0 0 10px rgba(152,195,121,0.4);
            outline: none;
        }
        
        #javaCode::placeholder {
            color: #5c6370;
            opacity: 0.8;
        }
        
        /* Java Output Section */
        #javaOutputSection {
            margin-top: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            border: 2px solid #61afef;
        }
        
        #javaOutputSection h6 {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        #javaOutput {
            background-color: #000000;
            color: #00ff00;
            border: 2px solid #333;
            border-radius: 6px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            min-height: 150px;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        #javaOutput.error {
            color: #ff6b6b;
        }
        
        #javaOutput.success {
            color: #51cf66;
        }
        
        /* Alert message styling */
        .alert {
            margin: 20px auto;
            max-width: 600px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .alert-success {
            background-color: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>

<body>
    <?php include '../../navbar.php'; ?>
    
    <br><br><br>
    
    <?php echo $saveMessage; ?>
    
    <form action="edit.php" method="POST">
        <div class="container">
            <div class="field is-grouped">
                <div class="control" style="width: 100%;">
                    <h4 id="description">Mechanism <?php echo $mid; ?> - Edit Data<?php if ($submission_id) echo " (Submission #$submission_id)"; ?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="input">Input:</label>
                    <textarea class="form-control" name="input" id="input" rows="10"><?php echo htmlentities($input); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label for="output">Output:</label>
                    <textarea class="form-control" name="output" id="output" rows="10"><?php echo htmlentities($output); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label for="format">Format:</label>
                    <textarea readonly class="form-control" name="format" id="format" rows="10"><?php echo htmlentities($format); ?></textarea>
                </div>
            </div>
            <div class="field is-grouped">
                <div class="control button-group">
                    <button class="btn btn-success" type="submit" name="submit" value="save">Save Data</button>
                    <button class="btn btn-primary" type="submit" name="submit" value="proceed">Proceed to View</button>
                    <button type="button" class="btn btn-info" onclick="executeJavaCode()">Compile & Run Java</button>
                    <button type="button" class="btn btn-danger" onclick="clearJavaOutput()">Clear Output</button>
                    <a class="btn btn-secondary" href="../../core-s">Go Back</a>
                </div>
            </div>
            
            <div id="codeSubmissionSection">
                <h5>Custom Java Code Submission</h5>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <label for="javaCode" style="margin: 0;">Enter your custom Java code here:</label>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="loadTemplate()">Use Template</button>
                </div>
                <textarea class="form-control" name="javaCode" id="javaCode" rows="15"></textarea>
                
                <div id="javaOutputSection">
                    <h6>Output:</h6>
                    <div id="javaOutput">Ready to execute Java code...</div>
                </div>
            </div>
        </div>
    </form>

    <script>
        const templateCode = `import java.io.*;
import java.util.*;

public class Main {
    public static void main(String[] args) throws IOException {
        String inputFile = args.length > 0 ? args[0] : "in-001.dat"; // prefer padded like v2
        
        // Read processes
        ArrayList<Process> processes = new ArrayList<>();
        Scanner scan = new Scanner(new File(inputFile));
        
        while (scan.hasNextLine()) {
            String[] parts = scan.nextLine().trim().split("\\\\s+");
            if (parts.length >= 4) {
                int id = Integer.parseInt(parts[0]);
                int arrival = Integer.parseInt(parts[1]); 
                int burst = Integer.parseInt(parts[2]);
                int priority = Integer.parseInt(parts[3]);
                processes.add(new Process(id, arrival, burst, priority));
            }
        }
        scan.close();
        
        // Sort by arrival time (FCFS)
        processes.sort((a, b) -> Integer.compare(a.arrival, b.arrival));
        
        // Simulate FCFS scheduling
        int currentTime = 0;
        ArrayList<Result> results = new ArrayList<>();
        
        for (Process p : processes) {
            if (currentTime < p.arrival) {
                currentTime = p.arrival;
            }
            int startTime = currentTime;
            int endTime = currentTime + p.burst;
            results.add(new Result(p.id, startTime, endTime));
            currentTime = endTime;
        }
        
        // Write output
        PrintWriter out = new PrintWriter("out-001.dat");
        out.println("Type of Scheduler: First Come First Serve(Non-Preemptive)");
        out.println("Number of Processes: " + processes.size());
        out.println();
        
        for (Result r : results) {
            out.println(r.id + "," + r.start + "," + r.end);
        }
        out.close();
    }
}

class Process {
    int id, arrival, burst, priority;
    
    Process(int id, int arrival, int burst, int priority) {
        this.id = id;
        this.arrival = arrival; 
        this.burst = burst;
        this.priority = priority;
    }
}

class Result {
    int id, start, end;
    
    Result(int id, int start, int end) {
        this.id = id;
        this.start = start;
        this.end = end;
    }
}`;

        function loadTemplate() {
            document.getElementById('javaCode').value = templateCode;
        }

        function executeJavaCode() {
            const javaCode = document.getElementById('javaCode').value;
            // Remove the input content sending - just like v2
            const outputDiv = document.getElementById('javaOutput');
            
            if (!javaCode.trim()) {
                outputDiv.className = 'error';
                outputDiv.textContent = 'Error: No Java code provided.';
                return;
            }
            
            outputDiv.className = '';
            outputDiv.textContent = 'Compiling and executing...';
            
            fetch('edit.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                // Only send Java code, not input content - just like v2
                body: 'action=execute_java&java_code=' + encodeURIComponent(javaCode)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    outputDiv.className = data.success ? 'success' : 'error';
                    outputDiv.textContent = data.output;
                    if (data.path) {
                        outputDiv.textContent += '\n\n[Executed in: ' + data.path + ']';
                    }
                    if (data.submission_id) {
                        outputDiv.textContent += '\n[Submission ID: ' + data.submission_id + ']';
                    }
                } catch (e) {
                    outputDiv.className = 'error';
                    outputDiv.textContent = 'Error parsing response:\n' + text;
                    console.error('Parse error:', e);
                }
            })
            .catch(error => {
                outputDiv.className = 'error';
                outputDiv.textContent = 'Error: ' + error.message;
                console.error('Fetch error:', error);
            });
        }
        
        function clearJavaOutput() {
            document.getElementById('javaOutput').textContent = 'Output cleared. Ready for next execution...';
            document.getElementById('javaOutput').className = '';
        }
    </script>
</body>

</html>

<?php
if (!empty($_SESSION['log_messages'])) {
    foreach ($_SESSION['log_messages'] as $logMessage) {
        $escapedMsg = addslashes($logMessage);
        echo "<script>console.log('" . $escapedMsg . "');</script>";
    }
}
unset($_SESSION['log_messages']);
?>
