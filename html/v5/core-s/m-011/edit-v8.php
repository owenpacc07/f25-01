<?php

require_once './../../config.php';
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: ./../../login.php');
    exit();
}

$user = $_SESSION['userid'];

// 1. Get submission ID and folder, mechanism CODE and ID from SESSION variables
//-----------------------------------------------------------------
$submissionID = $_SESSION['submissionID'];
$mechanismID = $_SESSION['mechanismID'];
$mechanismCode = $_SESSION['mechanismCode'];
//$mechanismTitle = $_SESSION['mechanismTitle'];
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

/*

// 2. Verify and load submission from DATABASE
//---------------------------------------------------
$submission = null;
if ($_SESSION[$session_key]) {
    $submission_query = "SELECT input_path, output_path FROM submissions WHERE submission_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($link, $submission_query);
    mysqli_stmt_bind_param($stmt, "ii", $submission_id, $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $submission = mysqli_fetch_assoc($result);
    
    $_SESSION['log_messages'][] = "DEBUG edit.php - Submission query result: " . json_encode($submission);
    
    if (!$submission) {
        // Submission doesn't exist, clear session
        $_SESSION[$session_key] = null;
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

*/

// 3. LOAD data from files to textboxes

// Get INITIAL contents of TEXTBOXES from STANDARD I/O in /files/core-s/m-...
//---------------------------------------------------------

//get Standard INPUT data as initial (to textbox)
//$filename1 = "../../../files/core-s/m-".$mid_padded."/in-".$mid_padded.".dat";
$input = "";

//get Standard OUTPUT data as initial (to textbox)
//$filename2 = "../../../files/core-s/m-".$mid_padded."/out-".$mid_padded.".dat";
$output = "";

//get Standard FORMAT data as initial (to textbox)
$filename3 = "../../../files/core-s/m-".$mid_padded."/format-".$mid_padded.".txt";
$fileContent3 = "";

if (file_exists($filename3)) {
    $fileContent3 = file_get_contents($filename3);
}

$_SESSION['log_messages'][] = "DEBUG edit.php - INPUT=,OUTPUT=,FORMAT=";

$saveMessage = '';

// When click on "Compile & Run" Java codes
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
        
        $inPadPath  = $subDir . "in-$mid_padded.dat";
        $outPadPath = $subDir . "out-$mid_padded.dat";

$_SESSION['log_messages'][] = "DEBUG edit.php - subDIR: $subDir, inPadPath: $inPadPath ";


        // Write current textarea input to PADDED only
        $inputContent = $_POST['input_content'] ?? '';
        if (empty(trim($inputContent))) {
            $inputContent = "1 0 7 1\n2 2 4 2\n3 4 1 3";
        }
        @file_put_contents($inPadPath, $inputContent);


        // Compile in submission dir
        $javaCode = $_POST['java_code'] ?? '';
        if (!$javaCode) throw new Exception("No Java code provided");
        $fileName = 'Main.java';
        file_put_contents($subDir . $fileName, $javaCode);
        $compileCmd = "cd " . escapeshellarg($subDir) . " && javac " . escapeshellarg($fileName) . " 2>&1";
        $compileOutput = shell_exec($compileCmd);

        $response = [];
        if (empty($compileOutput) || strpos($compileOutput, 'error') === false) {
            // Run with PADDED input
            $inputArg = escapeshellarg("in-$mid_padded.dat");
            $runCmd = "cd " . escapeshellarg($subDir) . " && timeout 5 java Main " . $inputArg . " 2>&1";
            $runOutput = shell_exec($runCmd);

            // Append input preview
            $inputFileBasename = basename($inPadPath);
            $inputFileContentsEcho = file_exists($inPadPath) ? file_get_contents($inPadPath) : '(input file not found)';
            $runOutput .= "\n\n--- Input File Used: $inputFileBasename ---\n" . $inputFileContentsEcho;

            // Read output back from PADDED only
            $fileOutput = '';
            if (file_exists($outPadPath) && filesize($outPadPath) > 0) {
                $fileOutput = file_get_contents($outPadPath);
                $runOutput .= "\n\n--- Output File Contents ---\n" . $fileOutput;
            }

            $_SESSION['log_messages'][] = "DEBUG edit.php - Appended input file ($inputFileBasename) contents to run output (length " . strlen($inputFileContentsEcho) . ")";
            $response = [
                'success' => true,
                'output' => $runOutput ?: 'Program executed successfully with no output.',
                'path' => $subDir,
                'submission_id' => $_SESSION[$session_key]
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

    // ?? 1) If user clicked "Proceed to View", DO NOT SAVE ANYTHING
    if ($_POST['submit'] === 'proceed') {
        // Just go back to the mechanism viewer page
        header("Location: ./");
        exit();
    }

    // ?? 2) If user clicked "Load System Data", DO NOT SAVE ANYTHING
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

        @file_put_contents($subDir . "in-$mid_padded.dat",  $inputText);
        @file_put_contents($subDir . "out-$mid_padded.dat", $outputText);

        // Only "save" comes here, so show the success message and stay on page
        $saveMessage = '<div class="alert alert-success text-center" role="alert">Data saved to submission #' . $_SESSION[$session_key] . '!</div>';
        // Reload from DATA FILES in submission folder to TEXTBOX
        $input  = file_get_contents($subDir . "in-$mid_padded.dat");
        $output = file_get_contents($subDir . "out-$mid_padded.dat");
      } catch (Exception $e) {
        $saveMessage = '<div class="alert alert-danger text-center" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
   }
}

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
                    <h4 id="description">Mechanism <?php echo $mechanismCode; ?> - Edit Data<?php if ($submissionID) echo " (Submission #$submissionID)"; ?></h4>
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
                    <button class="btn btn-success" type="submit" name="submit" value="load">Load System Data</button>
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
        const templateCode = `// PROCESS ALLOCATION, FIRST FIT (with splitting / reusable leftover space)
// Update: Allow reusing a memory slot's leftover space after an allocation.

import java.io.*;
import java.util.ArrayList;
import java.util.Scanner;

public class Main {
    static String mid = "011";
    // NOTE: SUBMISSION example for m011 path
    static String inputFile = "in-011.dat";
    static String outputFile = "out-011.dat";

    public static void main(String args[]) throws IOException {
        ArrayList<String> input = inFile();

        // Build free-list (memory slots)
        int numOfMemorySlots = Integer.parseInt(input.get(0));
        ArrayList<MemorySlot> memSlots = new ArrayList<>();
        for (int i = 1; i < (numOfMemorySlots * 2); i += 2) {
            memSlots.add(new MemorySlot(
                Integer.parseInt(input.get(i)),
                Integer.parseInt(input.get(i + 1))
            ));
        }

        // Build processes
        int newBaseIndex = numOfMemorySlots * 2 + 2;
        int numOfProcesses = Integer.parseInt(input.get(newBaseIndex - 1));
        ArrayList<Process> processes = new ArrayList<>();
        for (int i = newBaseIndex; i < (newBaseIndex + numOfProcesses * 2); i += 2) {
            processes.add(new Process(
                Integer.parseInt(input.get(i)),
                Integer.parseInt(input.get(i + 1))
            ));
        }

        // FIRST-FIT with splitting:
        // scan slots in order; place into the first slot that fits.
        // exact fit -> remove hole; partial fit -> shrink hole from the front.
        for (int p = 0; p < processes.size(); p++) {
            Process proc = processes.get(p);
            if (proc.allocated) continue;

            for (int s = 0; s < memSlots.size(); s++) {
                MemorySlot slot = memSlots.get(s);
                int leftover = slot.size - proc.size;
                if (leftover >= 0) {
                    // allocate at slot.start
                    proc.setStart(slot.start);

                    if (leftover == 0) {
                        // hole fully consumed
                        memSlots.remove(s);
                    } else {
                        // consume from the front; remainder stays a free hole
                        slot.start += proc.size;
                        slot.size = slot.end - slot.start;
                        if (slot.size < 0) { // defensive
                            memSlots.remove(s);
                        }
                    }
                    break; // first-fit: stop scanning for this process
                }
            }
        }

        outFile(processes);
        updateFlagFile();
    }

    // Read workingDirectory/in-011.dat into tokens
    public static ArrayList<String> inFile() throws FileNotFoundException {
        ArrayList<String> arr = new ArrayList<>();
        File inFile = new File(inputFile);
        Scanner in = new Scanner(inFile);
        while (in.hasNextLine()) arr.add(in.nextLine());
        in.close();

        ArrayList<String> input = new ArrayList<>();
        for (int i = 0; i < arr.size(); i++) {
            String[] temp = arr.get(i).split(" ");
            for (int x = 0; x < temp.length; x++) {
                if (!temp[x].isEmpty()) input.add(temp[x]);
            }
        }
        return input;
    }

    // Write allocations to workingDirectory/out-011.dat
    public static void outFile(ArrayList<Process> processes) throws IOException {
        File outFile = new File(outputFile);
        PrintWriter out = new PrintWriter(outFile);
        for (int i = 0; i < processes.size(); i++) {
            if (processes.get(i).allocated) {
                out.println(processes.get(i).start + " " + processes.get(i).end + " " + processes.get(i).id);
            }
        }
        out.close();
    }

    // Flag file so the UI knows Java completed
    static void updateFlagFile() {
        try {
            File flagFile = new File("flag-file.txt");
            FileWriter writer = new FileWriter(flagFile, false);
            writer.write("1");
            writer.close();
        } catch (IOException e) {
            System.out.println("Error in updateFlagFile()" + e.getMessage());
        }
    }
}

class Process {
    public int id;
    public int size;
    public int start;
    public int end;
    public boolean allocated = false;

    public Process(int id, int size) {
        this.id = id;
        this.size = size;
    }
    public void setStart(int start) {
        this.start = start;
        this.end = start + this.size;
        this.allocated = true;
    }
}

class MemorySlot {
    public int start;
    public int end;
    public int size;

    public MemorySlot(int start, int end) {
        this.start = start;
        this.end = end;
        this.size = end - start;
    }
}
`;

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
