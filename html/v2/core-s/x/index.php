<?php
session_start();

// If no user is logged in they can't access this page
if (!isset($_SESSION['email'])) {
    header('Location: ../../login.php');
    exit();
}

// For tracking execution results
$executionLog = "";
$executionResult = null;

// Process Java code execution if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['runJavaCode'])) {
    // Configuration
    $SERVER_URL = 'https://hydra.newpaltz.edu/java'; // Server URL
    $API_KEY = 'jexec_7e7007e8b9cd35a05a0ccc27b21e855e503e4aadea1e39c7375443c28afe9517'; // API key for Hydra

    // Helper function for cURL requests
    function send_request($url, $apiKey, $method = 'GET', $data = null) {
        $ch = curl_init();
        $headers = [
            'X-API-Key: ' . $apiKey,
            'Content-Type: application/json'
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['code' => $http_code, 'body' => json_decode($response, true)];
    }

    // Prepare the payload
    $javaCode = $_POST['javaCode'] ?? '';
    $args = !empty($_POST['args']) ? preg_split('/\s+/', trim($_POST['args'])) : [];
    
    $inputFiles = [];
    if (!empty($_FILES['inputFiles']['name'][0])) {
        $file_count = count($_FILES['inputFiles']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            $inputFiles[] = [
                'name' => $_FILES['inputFiles']['name'][$i],
                'content' => base64_encode(file_get_contents($_FILES['inputFiles']['tmp_name'][$i]))
            ];
        }
    }

    $payload = [
        'javaCode' => $javaCode,
        'args' => $args,
        'inputFiles' => $inputFiles
    ];

    // Submit the job
    $executionLog = "Submitting job...\n";
    $submit_result = send_request($SERVER_URL . '/api/submit', $API_KEY, 'POST', $payload);

    if ($submit_result['code'] !== 200 || !isset($submit_result['body']['jobId'])) {
        $executionLog .= "Error submitting job:\n";
        $executionLog .= print_r($submit_result['body'], true);
        $executionResult = null;
    } else {
        $jobId = $submit_result['body']['jobId'];
        $executionLog .= "Job submitted successfully. Job ID: $jobId\n";

        // Poll for results
        $attempts = 0;
        $max_attempts = 20; // Poll for 10 seconds (20 * 500ms)
        $job_result = null;

        while ($attempts < $max_attempts) {
            $status_result = send_request($SERVER_URL . '/api/job/' . $jobId, $API_KEY, 'GET');
            $status = $status_result['body']['status'] ?? 'unknown';
            $executionLog .= "Polling... Status: $status\n";

            if ($status === 'done') {
                $job_result = $status_result['body']['result'];
                break;
            }

            usleep(500000); // Wait 500ms
            $attempts++;
        }

        // Display the final results
        if ($job_result) {
            $executionResult = $job_result;
        } else {
            $executionLog .= "Job did not complete within the polling time.";
        }
    }
}

// Default Java code example
$defaultJavaCode = <<<JAVA
public class Main {
    public static void main(String[] args) {
        System.out.println("Hello from PHP client!");
    }
}
JAVA;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Java Code Execution</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            max-width: 1200px; 
            margin: 20px auto; 
            padding: 0 15px; 
            background-color: #f4f7fa;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            margin-top: 20px;
            background: linear-gradient(135deg, #f3f4f6, #ffffff);
        }
        h1, h2 { color: #111; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        .CodeMirror { 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            height: 400px; 
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        #results {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        #results h2 {
            margin-top: 0;
            color: #007bff;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="back-btn">
            <a href="../index.php" class="btn btn-secondary">← Back to Submissions</a>
        </div>
        
        <h1>Java Code Execution</h1>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="code">Java Code</label>
                <textarea id="code" name="javaCode"><?= htmlspecialchars($_POST['javaCode'] ?? $defaultJavaCode); ?></textarea>
            </div>

            <div class="form-group">
                <label for="args">Command-line Arguments (space-separated)</label>
                <input type="text" id="args" name="args" class="form-control" value="<?= htmlspecialchars($_POST['args'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="inputFiles">Additional Files</label>
                <input type="file" id="inputFiles" name="inputFiles[]" class="form-control" multiple>
            </div>

            <div class="form-group">
                <label>Demos</label>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" onclick="loadDemo('args')">Command-Line Args</button>
                    <button type="button" class="btn btn-outline-primary" onclick="loadDemo('error')">Compilation Error</button>
                    <button type="button" class="btn btn-outline-primary" onclick="loadDemo('timeout')">Timeout</button>
                    <button type="button" class="btn btn-outline-primary" onclick="loadDemo('inputFile')">Input File</button>
                </div>
            </div>

            <button type="submit" name="runJavaCode" class="btn btn-primary btn-lg">Execute Code</button>
        </form>

        <?php if (!empty($executionLog) || $executionResult): ?>
            <div id="results">
                <h2>Execution Log</h2>
                <pre><?= htmlspecialchars($executionLog) ?></pre>
                
                <?php if ($executionResult): ?>
                    <h2>Final Results</h2>
                    <h3>Compilation</h3>
                    <pre><?= htmlspecialchars($executionResult['compilation']['status']) ?></pre>
                    <?php if (!empty($executionResult['compilation']['errors'])): ?>
                        <pre><?= htmlspecialchars($executionResult['compilation']['errors']) ?></pre>
                    <?php endif; ?>
                    
                    <h3>Execution</h3>
                    <pre><?= htmlspecialchars($executionResult['execution']['status']) ?></pre>
                    <?php if (!empty($executionResult['execution']['stdout'])): ?>
                        <h4>Standard Output</h4>
                        <pre><?= htmlspecialchars($executionResult['execution']['stdout']) ?></pre>
                    <?php endif; ?>
                    
                    <?php if (!empty($executionResult['execution']['stderr'])): ?>
                        <h4>Standard Error</h4>
                        <pre><?= htmlspecialchars($executionResult['execution']['stderr']) ?></pre>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/clike/clike.min.js"></script>
    <script>
        var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
            lineNumbers: true,
            mode: "text/x-java",
            matchBrackets: true,
            theme: "default",
            indentUnit: 4,
            indentWithTabs: true
        });

        function loadDemo(demo) {
            let code = '';
            let args = '';
            let fileNote = '';

            switch (demo) {
                case 'args':
                    code = `public class Main {\n    public static void main(String[] args) {\n        System.out.println("Received " + args.length + " arguments:");\n        for (int i = 0; i < args.length; i++) {\n            System.out.println("Arg " + (i+1) + ": " + args[i]);\n        }\n    }\n}`;
                    args = 'first "second arg with spaces" third';
                    break;
                case 'error':
                    code = `public class Main {\n    public static void main(String[] args) {\n        System.out.println("This code has a syntax error on purpose");\n        int x = "hello"; // This will cause a compilation error\n    }\n}`;
                    args = '';
                    break;
                case 'timeout':
                    code = `public class Main {\n    public static void main(String[] args) throws InterruptedException {\n        System.out.println("Starting a long task that will time out...");\n        Thread.sleep(15000); // 15 seconds, will exceed 10s limit\n        System.out.println("This line will never be reached.");\n    }\n}`;
                    args = '';
                    break;
                case 'inputFile':
                    code = `import java.io.File;\nimport java.util.Scanner;\n\npublic class Main {\n    public static void main(String[] args) {\n        try {\n            File f = new File("my_file.txt");\n            Scanner sc = new Scanner(f);\n            System.out.println("Reading from my_file.txt:");\n            while (sc.hasNextLine()) {\n                System.out.println(sc.nextLine());\n            }\n            sc.close();\n        } catch (Exception e) {\n            System.err.println("Error reading file: " + e.getMessage());\n        }\n    }\n}`;
                    args = '';
                    fileNote = 'NOTE: For this demo to work, please upload a file named "my_file.txt".';
                    break;
            }

            editor.setValue(code);
            document.getElementById('args').value = args;
            
            // Handle file note
            let existingNote = document.getElementById('file-note');
            if (existingNote) {
                existingNote.remove();
            }
            if (fileNote) {
                let noteElement = document.createElement('p');
                noteElement.id = 'file-note';
                noteElement.style.color = 'red';
                noteElement.style.fontWeight = 'bold';
                noteElement.textContent = fileNote;
                document.getElementById('inputFiles').parentElement.appendChild(noteElement);
            }
        }
    </script>
</body>
</html>
