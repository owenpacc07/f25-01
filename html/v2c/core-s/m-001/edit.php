<?php

// Automatically detect mechanism ID from current directory path
$mid = basename(dirname(__FILE__)); // Gets 'm-###' from path
$mid = str_replace('m-', '', $mid);  // Extracts '###' from 'm-###'

require_once './../../config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: ./../../login.php');
    exit();
}

$user = $_SESSION['userid'];

// Get submission_id from URL or session
$submission_id = $_GET['submission_id'] ?? $_SESSION['current_submission_id'] ?? null;

if (!$submission_id) {
    $_SESSION['error_message'] = "No submission ID provided.";
    header('Location: ./../../create_submission.php');
    exit();
}

// Get submission details from database
$submission_query = "SELECT input_path, output_path FROM submissions WHERE submission_id = ? AND user_id = ?";
$stmt = mysqli_prepare($link, $submission_query);
mysqli_stmt_bind_param($stmt, "ii", $submission_id, $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$submission = mysqli_fetch_assoc($result);

if (!$submission) {
    $_SESSION['error_message'] = "Submission not found or access denied.";
    header('Location: ./../../create_submission.php');
    exit();
}

// Use submission paths instead of default paths
$input = file_exists($submission['input_path']) ? file_get_contents($submission['input_path']) : '';
$output = file_exists($submission['output_path']) ? file_get_contents($submission['output_path']) : '';

// Get format from default location (format doesn't change per submission)
$path = realpath("../../../files/core-s/m-$mid");
$format = file_exists("$path/format-$mid.txt") ? file_get_contents("$path/format-$mid.txt") : '';

$saveMessage = '';

// Handle Java code execution via AJAX
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
        $fileName = 'Main.java';
        
        // Use existing submission folder
        $subDir = dirname($submission['input_path']) . '/';
        
        // Save Java file to submission directory
        $javaFilePath = $subDir . $fileName;
        file_put_contents($javaFilePath, $javaCode);
        
        // Compile Java code
        $className = pathinfo($fileName, PATHINFO_FILENAME);
        $compileCmd = "cd " . escapeshellarg($subDir) . " && javac " . escapeshellarg($fileName) . " 2>&1";
        $compileOutput = shell_exec($compileCmd);
        
        $response = [];
        if (empty($compileOutput) || strpos($compileOutput, 'error') === false) {
            $inputArg = escapeshellarg("in-$mid.dat");
            $runCmd = "cd " . escapeshellarg($subDir) . " && timeout 5 java " . escapeshellarg($className) . " " . $inputArg . " 2>&1";
            $runOutput = shell_exec($runCmd);
            
            $outputFile = $subDir . "out-$mid.dat";
            if (file_exists($outputFile)) {
                $fileOutput = file_get_contents($outputFile);
                $runOutput .= "\n\n--- Output File Contents ---\n" . $fileOutput;
            }
            
            $response = [
                'success' => true,
                'output' => $runOutput ?: 'Program executed successfully with no output.',
                'path' => $subDir
            ];
        } else {
            $response = [
                'success' => false,
                'output' => $compileOutput
            ];
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
    // Write to submission folder paths
    $newDataIN = $_POST['input'];
    file_put_contents($submission['input_path'], $newDataIN);

    $newDataOUT = $_POST['output'];
    file_put_contents($submission['output_path'], $newDataOUT);

    if ($_POST['submit'] === 'proceed') {
        header("Location: ./");
        exit();
    } else {
        $saveMessage = '<div class="alert alert-success text-center" role="alert">Data saved successfully!</div>';
        $output = file_get_contents($submission['output_path']);
        $input = file_get_contents($submission['input_path']);
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
        
        .java-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            justify-content: center;
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
    
    <form action="edit.php?submission_id=<?php echo $submission_id; ?>" method="POST">
        <div class="container">
            <div class="field is-grouped">
                <div class="control" style="width: 100%;">
                    <h4 id="description">Mechanism <?php echo $mid; ?> - Edit Data (Submission #<?php echo $submission_id; ?>)</h4>
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
            
            <!-- Custom Java Code Submission Section -->
            <div id="codeSubmissionSection">
                <h5>Custom Java Code Submission</h5>
                <label for="javaCode">Enter your custom Java code here:</label>
                <textarea class="form-control" name="javaCode" id="javaCode" rows="15" placeholder="// Enter your Java code here...
public class Main {
    public static void main(String[] args) {
        System.out.println(&quot;Hello, World!&quot;);
        // Your code implementation
    }
}"></textarea>
                
                <div id="javaOutputSection">
                    <h6>Output:</h6>
                    <div id="javaOutput">Ready to execute Java code...</div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function executeJavaCode() {
            const javaCode = document.getElementById('javaCode').value;
            const outputDiv = document.getElementById('javaOutput');
            
            if (!javaCode.trim()) {
                outputDiv.className = 'error';
                outputDiv.textContent = 'Error: No Java code provided.';
                return;
            }
            
            outputDiv.className = '';
            outputDiv.textContent = 'Compiling and executing...';
            
            fetch('edit.php?submission_id=<?php echo $submission_id; ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
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