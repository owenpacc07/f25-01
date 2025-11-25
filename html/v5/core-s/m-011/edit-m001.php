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

//Get mechanismID, submissionID, submissionFolder from SESSION variable
//---------------------------------------------------------------------
$mechanismID = $_SESSION['mechanismID'];
$submissionID = $_SESSION['submissionID'];
$submissionFolder = $_SESSION['submissionFolder'];
//---------------------------------------------------------------------


// Session key for persistent submission tracking
$session_key = "submission_m{$mid_padded}_uid{$user}";

// Debug logging
if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

$_SESSION['log_messages'][] = "DEBUG edit.php - MID: $mid, MID_PADDED: $mid_padded, USER: $user, SESSION_KEY: $session_key, SUBMISSION_FOLDER: $submissionFolder";

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
            $submissions_base = realpath("../../../files/submissions/") ?: "../../../files/submissions";
            $base = rtrim($submissions_base, '/\\');
            $inpRel = ltrim($submission['input_path'], '/\\');
            $outRel = ltrim($submission['output_path'] ?? '', '/\\');
            $submission['input_path'] = $base . DIRECTORY_SEPARATOR . $inpRel;
            if ($outRel !== '') {
                $submission['output_path'] = $base . DIRECTORY_SEPARATOR . $outRel;
            }
            $_SESSION['log_messages'][] = "DEBUG edit.php - Fixed incomplete paths with base: $base";
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
    $_SESSION['log_messages'][] = "DEBUG edit.php - Using submission folder: $subDir";

    $in1Path   = $subDir . "in-$mid.dat";
    $inPadPath = $subDir . "in-$mid_padded.dat";
    $out1Path  = $subDir . "out-$mid.dat";
    $outPadPath= $subDir . "out-$mid_padded.dat";

    // Track sources for debug
    $inputSource = 'UNKNOWN';
    $outputSource = 'UNKNOWN';

    // Prefer UNPADDED; fallback to PADDED (read-only legacy)
    if (file_exists($in1Path) && filesize($in1Path) > 0) {
        $input = file_get_contents($in1Path);
        $inputSource = $in1Path;
    } elseif (file_exists($inPadPath) && filesize($inPadPath) > 0) {
        $input = file_get_contents($inPadPath);
        $inputSource = $inPadPath;
    } else {
        $input = '';
        $inputSource = 'MISSING';
    }

    if (file_exists($out1Path) && filesize($out1Path) > 0) {
        $output = file_get_contents($out1Path);
        $outputSource = $out1Path;
    } elseif (file_exists($outPadPath) && filesize($outPadPath) > 0) {
        $output = file_get_contents($outPadPath);
        $outputSource = $outPadPath;
    } else {
        $output = '';
        $outputSource = 'MISSING';
    }

    // Ensure input has data; WRITE UNPADDED ONLY
    if (empty(trim($input))) {
        $input = "1 0 7 1\n2 2 4 2\n3 4 1 3";
        @file_put_contents($in1Path, $input);
        $inputSource = 'DEFAULT_SEEDED';
        $_SESSION['log_messages'][] = "DEBUG edit.php - Seeded default INPUT to $in1Path";
    }

    // Ensure output has data; seed from core default; WRITE UNPADDED ONLY
    if (empty(trim($output))) {
        $coreDefaultDir = realpath("../../../files/core-s/m-$mid_padded");
        $coreOut = $coreDefaultDir ? ($coreDefaultDir . "/out-$mid_padded.dat") : null;
        if ($coreOut && file_exists($coreOut) && filesize($coreOut) > 0) {
            $output = file_get_contents($coreOut);
            $outputSource = "CORE_DEFAULT:$coreOut";
        } else {
            $output = "Type of Scheduler: First Come First Serve(Non-Preemptive)\nNumber of Processes: 0";
            $outputSource = 'STUB_SEEDED';
        }
        @file_put_contents($out1Path, $output);
        $_SESSION['log_messages'][] = "DEBUG edit.php - Seeded OUTPUT from {$outputSource} to $out1Path";
    }

    // Final concise source summary + previews
    $_SESSION['log_messages'][] = "DEBUG edit.php - INPUT source: $inputSource";
    $_SESSION['log_messages'][] = "DEBUG edit.php - OUTPUT source: $outputSource";
    $inSize = strlen($input);
    $outSize = strlen($output);
    $inPreview  = addslashes(substr($input, 0, 120));
    $outPreview = addslashes(substr($output, 0, 120));
    $_SESSION['log_messages'][] = "DEBUG edit.php - INPUT size: {$inSize}, preview: {$inPreview}";
    $_SESSION['log_messages'][] = "DEBUG edit.php - OUTPUT size: {$outSize}, preview: {$outPreview}";
    $_SESSION['log_messages'][] = "DEBUG edit.php - Input(1) exists: " . (file_exists($in1Path) ? 'YES' : 'NO');
    $_SESSION['log_messages'][] = "DEBUG edit.php - Output(1) exists: " . (file_exists($out1Path) ? 'YES' : 'NO');
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
    // Use padded file name, e.g., format-001.txt
    $format_file = $format_path . "/format-$mid_padded.txt";
    if (file_exists($format_file)) {
        $format = file_get_contents($format_file);
    } else {
        // Detailed default FCFS format fallback
        $format = "Mechanism = FCFS (Non-Preemptive) CPU Scheduling

INPUT data in \"in.dat\"
------------------------------------------------------------------------
1 0 7 1//       ID: 1    Arrival: 0     Burst: 10  Priority: 1
2 1 2 1//       ID: 2    Arrival: 2     Burst: 6   Priority: 1
3 2 5 0//       ID: 3    Arrival: 1     Burst: 2   Priority: 0
4 3 4 3//       ID: 4    Arrival: 7     Burst: 1   Priority: 3
------------------------------------------------------------------------

OUTPUT data in \"out.dat\"
THE DETAILED TABLE IS LIMITED TO 4 PROCESSES ONLY
-----------------------
1,0,7       // ID: 1   Start: 0   End: 7
2,7,9       // ID: 2   Start: 7   End: 9
3,9,14      // ID: 3   Start: 9   End: 14
4,14,18     // ID: 4   Start: 14  End: 18


CURRENT_TIME, P1_BURST_TIME_REMAINING, P2_BURST_TIME_REMAINING, P3_BURST_TIME_REMAINING, P4_BURST_TIME_REMAINING, CURRENT_PROCESS_HELD_BY_CPU, P1_WAITING_TIME,P2_WAITING_TIME, P3_WAITING_TIME,P4_WAITING_TIME,QUEUE (space separated)
- : indicates the process has not yet arrived
0,10,-,-,-,1,0,-,-,-,1 
1,9,2,-,-,1,0,0,-,-,2 
2,8,2,5,-,1,0,1,0,-,2 3 
3,7,2,5,4,1,0,2,1,0,2 3 4 
4,6,2,5,4,1,0,3,2,1,2 3 4 
5,5,2,5,4,1,0,4,3,2,2 3 4 
6,4,2,5,4,1,0,5,4,3,2 3 4 
7,3,2,5,4,1,0,6,5,4,2 3 4 
8,2,2,5,4,1,0,7,6,5,2 3 4 
9,1,2,5,4,1,0,8,7,6,2 3 4 
10,0,2,5,4,1,0,9,8,7,2 3 4 
11,0,1,5,4,2,0,9,9,8,3 4 
12,0,0,5,4,2,0,9,10,9,3 4 
13,0,0,4,4,3,0,9,10,10,4 
14,0,0,3,4,3,0,9,10,11,4 
15,0,0,2,4,3,0,9,10,12,4 
16,0,0,1,4,3,0,9,10,13,4 
17,0,0,0,4,3,0,9,10,14,4 
18,0,0,0,3,4,0,9,10,14,
19,0,0,0,2,4,0,9,10,14,
20,0,0,0,1,4,0,9,10,14,
21,0,0,0,0,4,0,9,10,14,

------------------------";
    }
    $_SESSION['log_messages'][] = "DEBUG edit.php - Format loaded from: " . ($format_file ?? 'fallback');
} else {
    $format = 'Format directory not found';
    $_SESSION['log_messages'][] = "DEBUG edit.php - Format directory not found: ../../../files/core-s/m-' . $mid_padded";
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
        
        $subDir   = dirname($sub_data['input_path']) . '/';
        $in1Path  = $subDir . "in-$mid.dat";
        $out1Path = $subDir . "out-$mid.dat";

        // Write current textarea input to UNPADDED only
        $inputContent = $_POST['input_content'] ?? '';
        if (empty(trim($inputContent))) {
            $inputContent = "1 0 7 1\n2 2 4 2\n3 4 1 3";
        }
        @file_put_contents($in1Path, $inputContent);

        // Compile in submission dir
        $javaCode = $_POST['java_code'] ?? '';
        if (!$javaCode) throw new Exception("No Java code provided");
        $fileName = 'Main.java';
        file_put_contents($subDir . $fileName, $javaCode);
        $compileCmd = "cd " . escapeshellarg($subDir) . " && javac " . escapeshellarg($fileName) . " 2>&1";
        $compileOutput = shell_exec($compileCmd);

        $response = [];
        if (empty($compileOutput) || strpos($compileOutput, 'error') === false) {
            // Run with UNPADDED input
            $inputArg = escapeshellarg("in-$mid.dat");
            $runCmd = "cd " . escapeshellarg($subDir) . " && timeout 5 java Main " . $inputArg . " 2>&1";
            $runOutput = shell_exec($runCmd);

            // Append input preview
            $inputFileBasename = basename($in1Path);
            $inputFileContentsEcho = file_exists($in1Path) ? file_get_contents($in1Path) : '(input file not found)';
            $runOutput .= "\n\n--- Input File Used: $inputFileBasename ---\n" . $inputFileContentsEcho;

            // Read output back from UNPADDED only
            if (file_exists($out1Path) && filesize($out1Path) > 0) {
                $fileOutput = file_get_contents($out1Path);
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

        // Save UNPADDED only
        @file_put_contents($subDir . "in-$mid.dat", $_POST['input']);
        @file_put_contents($subDir . "out-$mid.dat", $_POST['output']);

        if ($_POST['submit'] === 'proceed') {
            header("Location: ./");
            exit();
        } else {
            $saveMessage = '<div class="alert alert-success text-center" role="alert">Data saved to submission #' . $submission_id . '!</div>';
            // Reload UNPADDED
            $input  = file_get_contents($subDir . "in-$mid.dat");
            $output = file_get_contents($subDir . "out-$mid.dat");
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
        String inputFile = args.length > 0 ? args[0] : "in-1.dat";
        
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
        PrintWriter out = new PrintWriter("out-1.dat");
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
    // Use JSON to safely escape newlines/quotes in console logs
    $msgsJson = json_encode($_SESSION['log_messages']);
    echo "<script>(function(){var msgs={$msgsJson};for(var i=0;i<msgs.length;i++){console.log(msgs[i]);}})();</script>";
}
unset($_SESSION['log_messages']);
?>
