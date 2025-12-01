<?php

require_once './../../config.php';
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: ./../../login.php');
    exit();
}

if (!isset($_SESSION['submissionID'])) {
    header('Location: ./../index.php');
    exit();
}
else{

$fileContent = "";
$codeText = "";

// 1. Get submission ID and folder, mechanism CODE and ID from SESSION variables
//-----------------------------------------------------------------
$user = $_SESSION['userid'];
$submissionID = $_SESSION['submissionID'];
$mechanismID = $_SESSION['mechanismID'];
$mechanismCode = $_SESSION['mechanismCode'];
$mechanismTitle = $_SESSION['mechanismTitle'];
$submissionFolder= $_SESSION['submissionFolder'];
//-----------------------------------------------------------------
$submission_id = $submissionID;
$mid_padded = str_pad($mechanismCode, 3, '0', STR_PAD_LEFT);

// Session key for persistent submission tracking
$session_key = "submission_m{$mid_padded}_uid{$user}";

//SUB FOLDER
$sub_base = "../../../files/submissions/";
$subDir = $sub_base . $submissionID . "_" . $mid_padded . "_" .$user ."/";

// Debug logging (can see at Browser's Consol)
if (!isset($_SESSION['log_messages'])) {
    $_SESSION['log_messages'] = [];
}

$_SESSION['log_messages'][] = "DEBUG edit.php - mySubID: $submissionID, Mcode: $mechanismCode, MID: $mechanismID, MID_PADDED: $mid_padded, USER: $user, SESSION_KEY: $session_key";
$_SESSION['log_messages'][] = "DEBUG edit.php - Retrieved submission_id: " . ($submission_id ?? 'NULL');


// 3. LOAD data from files to textboxes

// Get INITIAL contents of TEXTBOXES from System I/O 
//---------------------------------------------------------

//get Standard INPUT data as initial (to textbox)
$filename1 = $subDir."/in-".$mid_padded.".dat";
$input = "";

//get Standard OUTPUT data as initial (to textbox)
$filename2 = $subDir."/out-".$mid_padded.".dat";
$output = "";

//get Standard FORMAT data as initial (to textbox)
$filename3 = "../../../files/core-s/m-".$mid_padded."/format-".$mid_padded.".txt";
$fileContent3 = "";

if (file_exists($filename3)) {
    $fileContent3 = file_get_contents($filename3);
}

$_SESSION['log_messages'][] = "DEBUG edit.php - INPUT=,OUTPUT=,FORMAT=";

$saveMessage = '';


// When click on "Run" Java codes
//--------------------------------------------
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
        
        if (!$_SESSION[$session_key]) throw new Exception("No active submission. Please use 'Make a Submission' button.");
        
        // Resolve the actual submission folder from DB (ensure we use the real/current path)
        $submissionIdForRun = $_SESSION[$session_key];
        $submission_query = "SELECT input_path FROM submissions WHERE submission_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($link, $submission_query);
        mysqli_stmt_bind_param($stmt, "ii", $submissionIdForRun, $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sub_data = mysqli_fetch_assoc($result);
        if (!$sub_data) throw new Exception("Submission not found in database for execution.");
        
        $subDir = rtrim(dirname($sub_data['input_path']), '/\\') . DIRECTORY_SEPARATOR;
        // Ensure directory exists
        if (!is_dir($subDir)) {
            if (!mkdir($subDir, 0755, true)) {
                throw new Exception("Failed to create submission directory: $subDir");
            }
        }

        $inPadPath  = $subDir . "in-$mid_padded.dat";
        $outPadPath = $subDir . "out-$mid_padded.dat";

        $_SESSION['log_messages'][] = "DEBUG edit.php - execute_java: using subDir: $subDir, inPadPath: $inPadPath, outPadPath: $outPadPath";

        // Write current textarea input to PADDED
        $inputContent = $_POST['input_content'] ?? '';
        if (empty(trim($inputContent))) {
            // If client didn't submit input, try to reuse existing submission input file.
            if (file_exists($inPadPath)) {
                $inputContent = file_get_contents($inPadPath);
                $_SESSION['log_messages'][] = "DEBUG edit.php - execute_java: loaded existing input from $inPadPath (len=" . strlen($inputContent) . ")";
            } else {
                // No input provided and no existing file — write an empty input
                $inputContent = '';
                $_SESSION['log_messages'][] = "DEBUG edit.php - execute_java: no input provided and no existing input file found; using empty input.";
            }
        }
        if (false === @file_put_contents($inPadPath, $inputContent)) {
            throw new Exception("Failed to write input file: $inPadPath");
        }

        // Write Java source to submission folder
        $fileName = 'Main.java';
        if (false === @file_put_contents($subDir . $fileName, $javaCode)) {
            throw new Exception("Failed to write Java source file in: $subDir");
        }

        // Compile in submission dir
        $compileCmd = "cd " . escapeshellarg($subDir) . " && javac " . escapeshellarg($fileName) . " 2>&1";
        $_SESSION['log_messages'][] = "DEBUG edit.php - compileCmd: $compileCmd";
        $compileOutput = shell_exec($compileCmd);

        $response = [];
        if (empty($compileOutput) || stripos($compileOutput, 'error') === false) {
            // Run with PADDED input inside submission folder; use -cp . so classpath is current dir
            $inputArg = escapeshellarg("in-$mid_padded.dat");
            $runCmd = "cd " . escapeshellarg($subDir) . " && timeout 5 java -cp . Main " . $inputArg . " 2>&1";
            $_SESSION['log_messages'][] = "DEBUG edit.php - runCmd: $runCmd";
            $runOutput = shell_exec($runCmd);

            // Append input preview
            $inputFileBasename = basename($inPadPath);
            $inputFileContentsEcho = file_exists($inPadPath) ? file_get_contents($inPadPath) : '(input file not found)';
            $runOutput .= "\n\n--- Input File Used: $inputFileBasename ---\n" . $inputFileContentsEcho;

            // Read output back from PADDED only (same folder)
            $fileOutput = '';
            if (file_exists($outPadPath) && filesize($outPadPath) > 0) {
                $fileOutput = file_get_contents($outPadPath);
                $runOutput .= "\n\n--- Output File Contents ---\n" . $fileOutput;
            } else {
                $_SESSION['log_messages'][] = "DEBUG edit.php - output file not found or empty: $outPadPath";
            }

            $_SESSION['log_messages'][] = "DEBUG edit.php - Appended input file ($inputFileBasename) contents to run output (length " . strlen($inputFileContentsEcho) . ")";
            $response = [
                'success' => true,
                'output' => $runOutput ?: 'Program executed successfully with no output.',
                'path' => $subDir,
                'submission_id' => $submissionIdForRun
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

$saveMessage = '';

// When other BUTTONS are clicked
//-------------------------------------------------------------------
if (isset($_POST['submit'])) {

    // 1) If user clicked "View I/O", DO NOT SAVE ANYTHING
    if ($_POST['submit'] === 'view') {
        // Just go back to the mechanism viewer page
        header("Location: ./index-v.php");
        exit();
    }

    // 1) If user clicked "Full View", DO NOT SAVE ANYTHING
    if ($_POST['submit'] === 'full') {
        // Just go back to the mechanism viewer page
        header("Location: ./index-r.php");
        exit();
    }


    // 2) If user clicked "Load System Data", DO NOT SAVE ANYTHING
    if ($_POST['submit'] === 'load') {

      // Get INITIAL contents of TEXTBOXES from STANDARD I/O in /files/core-s/m-...
      //---------------------------------------------------------

      //get Standard INPUT data as initial (to textbox)
      $filename1 = "../../../files/core-s/m-".$mid_padded."/in-".$mid_padded.".dat";
      $input = "";
      if (file_exists($filename1)) {
         $input = file_get_contents($filename1);
      }
      //get Standard OUTPUT data as initial (to textbox)
      $filename2 = "../../../files/core-s/m-".$mid_padded."/out-".$mid_padded.".dat";
      $output = "";
      if (file_exists($filename2)) {
         $output = file_get_contents($filename2);
      }
    }
    else
    // 3) If user clicked "SHOW Data", DO NOT SAVE ANYTHING
    if ($_POST['submit'] === 'show') {

      // GET contents of DATA from SUB FOLDER to TEXTBOXES 
      //---------------------------------------------------------
      //get INPUT data from SUB (to textbox)
      $filename1 = $subDir."/in-".$mid_padded.".dat";
      $input = "";
      if (file_exists($filename1)) {
         $input = file_get_contents($filename1);
      }
      //get OUTPUT data from SUB (to textbox)
      $filename2 = $subDir."/out-".$mid_padded.".dat";
      $output = "";
      if (file_exists($filename2)) {
         $output = file_get_contents($filename2);
      }
      //read Codes from SUB file (to textbox)
      $filename4 = $subDir."/Main.java";
      $codeText = "";
      if (file_exists($filename4)) {
         $codeText = file_get_contents($filename4);
      }

    }
    else

    {
      // ?? 2) Otherwise (e.g. "save"), SAVE what's in TEXTBOX to FILES in submission folder
      try {
            if (!$_SESSION[$session_key]) {
                throw new Exception("No active submission. Please use 'Make a Submission' button on the homepage.");
        }
        
        // Get submission folder
        $submission_query = "SELECT input_path FROM submissions WHERE submission_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($link, $submission_query);
        mysqli_stmt_bind_param($stmt, "ii", $_SESSION[$session_key], $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sub_data = mysqli_fetch_assoc($result);
        
        if (!$sub_data) throw new Exception("Submission not found");
        
        $subDir = dirname($sub_data['input_path']) . '/';

        // 1) WRITE TO SUBMISSION FOLDER
        $inputText  = $_POST['input']  ?? '';
        $outputText = $_POST['output'] ?? '';
        $codeText = $_POST['javaCode'] ?? '';

        @file_put_contents($subDir . "in-$mid_padded.dat",  $inputText);
        @file_put_contents($subDir . "out-$mid_padded.dat", $outputText);
        @file_put_contents($subDir . "Main.java", $codeText);

        // Only "save" comes here, so show the success message and stay on page
        $saveMessage = '<div class="alert alert-success text-center" role="alert">Data saved to submission #' . $_SESSION[$session_key] . '!</div>';
        // Reload from DATA FILES in submission folder to TEXTBOX
        $input  = file_get_contents($subDir . "in-$mid_padded.dat");
        $output = file_get_contents($subDir . "out-$mid_padded.dat");
        $codes = file_get_contents($subDir . "Main.java");
      } catch (Exception $e) {
        $saveMessage = '<div class="alert alert-danger text-center" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
   }
}

}//end of ELSE

?>

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>M-<?php echo $mechanismCode; ?> Edit Data</title>
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
                    <h4 id="description">Mechanism <?php echo $mechanismCode; echo "("; echo $mechanismTitle; ?>)-<?php if ($submissionID) echo "Submission #$submissionID"; ?></h4>
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
                    <textarea readonly class="form-control" name="format" id="format" rows="10"><?php echo htmlentities($fileContent3); ?></textarea>
                </div>
            </div>
            <div class="field is-grouped">
                <div class="control button-group">
                    <button class="btn btn-danger" type="submit" name="submit" value="load">Load System Data</button>
                    <button class="btn btn-success" type="submit" name="submit" value="save">Save All</button>
                    <button class="btn btn-success" type="submit" name="submit" value="show">Show All</button>
                    <button class="btn btn-primary" type="submit" name="submit" value="view">View I/O</button>
                    <button class="btn btn-primary" type="submit" name="submit" value="full">View I/System</button>
                    <button type="button" class="btn btn-info" onclick="executeJavaCode()">Run Codes</button>
                    <button type="button" class="btn btn-danger" onclick="clearJavaOutput()">Clear Output</button>
                    <a class="btn btn-secondary" href="../../core-s">Make NEW</a>
                </div>
            </div>
            
            <div id="codeSubmissionSection">
                <h5>Custom Java Code Submission</h5>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <label for="javaCode" style="margin: 0;">Enter your custom Java code here:</label>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="loadTemplate()">Load Sample</button>
                </div>
                <textarea class="form-control" name="javaCode" id="javaCode" rows="15"><?php echo htmlentities($codeText); ?></textarea>
                
                <div id="javaOutputSection">
                    <h6>Output:</h6>
                    <div id="javaOutput">Ready to execute Java code...</div>
                </div>
            </div>
        </div>
    </form>

<?php
// Define the path to the file relative to the current script.
$filePath = $sampleDir . "/m-". $mid_padded. "/sample.txt";

// Read the file contents into a PHP variable (X) ---
$fileContent = "";
if (file_exists($filePath) && is_readable($filePath)) {
    // Read the entire file into a string
    $fileContent = file_get_contents($filePath);
    
    // Check for errors during reading
    if ($fileContent === false) {
        $fileContent = "Error: Could not read the file content.";
        // Log the error on the server side
        error_log("Failed to read file at: " . $filePath);
    }
} else {
    $fileContent = "Error: File not found or not readable at server path: " . $filePath;
}

?>


    <script>
        const templateCode = `<?php echo $fileContent?>`;

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
