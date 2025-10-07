<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Java Executor Client</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 900px; margin: 20px auto; padding: 0 15px; }
        h1, h2 { color: #111; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="file"] { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .CodeMirror { border: 1px solid #ccc; border-radius: 4px; height: 300px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        #results { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 15px; margin-top: 20px; white-space: pre-wrap; word-wrap: break-word; }
        #results h2 { margin-top: 0; }
    </style>
</head>
<body>

    <h1>Java Executor Client (PHP)</h1>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="code">Java Code</label>
            <?php
            $defaultJavaCode = <<<JAVA
public class Main {
    public static void main(String[] args) {
        System.out.println("Hello from PHP client!");
    }
}
JAVA;
            ?>
            <textarea id="code" name="javaCode"><?= htmlspecialchars($_POST['javaCode'] ?? $defaultJavaCode); ?></textarea>
        </div>

        <div class="form-group">
            <label for="args">Command-line Arguments (space-separated)</label>
            <input type="text" id="args" name="args" value="<?= htmlspecialchars($_POST['args'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="inputFiles">Additional Files</label>
            <input type="file" id="inputFiles" name="inputFiles[]" multiple>
        </div>

        <div class="form-group">
            <label>Demos</label>
            <button type="button" onclick="loadDemo('args')">Command-Line Args</button>
            <button type="button" onclick="loadDemo('error')">Compilation Error</button>
            <button type="button" onclick="loadDemo('timeout')">Timeout</button>
            <button type="button" onclick="loadDemo('inputFile')">Input File</button>
            <button type="button" onclick="loadDemo('fcfs')">FCFS Example</button>
        </div>

        <button type="submit">Execute</button>
    </form>

    <?php
    // --- PHP Backend Logic ---

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // --- Configuration ---
        $SERVER_URL = 'https://hydra.newpaltz.edu/java'; // Server URL
        $API_KEY = 'jexec_7e7007e8b9cd35a05a0ccc27b21e855e503e4aadea1e39c7375443c28afe9517'; // <-- IMPORTANT: Replace with your actual API key

        // --- Helper function for cURL requests ---
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

        // --- 1. Prepare the payload ---
        $javaCode = $_POST['javaCode'] ?? '';
        $args = !empty($_POST['args']) ? preg_split('/\s+/', trim($_POST['args'])) : [];
        
        $inputFiles = [];
        if (!empty($_FILES['inputFiles']['name'][0])) {
            $file_count = count($_FILES['inputFiles']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                $inputFiles[] = [
                    'name' => $_FILES['inputFiles']['name'][$i],
                    'content' => file_get_contents($_FILES['inputFiles']['tmp_name'][$i])
                ];
            }
        }

        // --- Auto-create input files based on arguments ---
        if (!empty($args) && preg_match('/^\d{3}$/', $args[0])) {
            $file_number = $args[0];
            $input_filename = "in-{$file_number}.dat";
            
            // Check if this input file wasn't already uploaded
            $already_uploaded = false;
            foreach ($inputFiles as $file) {
                if ($file['name'] === $input_filename) {
                    $already_uploaded = true;
                    break;
                }
            }
            
            if (!$already_uploaded) {
                // Read from x/files/in-XXX.dat if it exists
                $sample_input_path = __DIR__ . "/x/files/{$input_filename}";
                if (file_exists($sample_input_path)) {
                    $input_data = file_get_contents($sample_input_path);
                    $inputFiles[] = [
                        'name' => $input_filename,
                        'content' => $input_data
                    ];
                }
            }
        }

        $payload = [
            'javaCode' => $javaCode,
            'args' => $args,
            'inputFiles' => $inputFiles
        ];

        echo '<div id="results">';
        echo '<h2>Execution Log</h2>';

        // --- 2. Submit the job ---
        echo "Submitting job...<br>";
        $submit_result = send_request($SERVER_URL . '/api/submit', $API_KEY, 'POST', $payload);

        if ($submit_result['code'] !== 200 || !isset($submit_result['body']['jobId'])) {
            echo "Error submitting job: <br>";
            echo '<pre>' . htmlspecialchars(print_r($submit_result['body'], true)) . '</pre>';
            echo '</div>';
            exit;
        }

        $jobId = $submit_result['body']['jobId'];
        echo "Job submitted successfully. Job ID: $jobId<br>";

        // --- 3. Poll for results ---
        $attempts = 0;
        $max_attempts = 20; // Poll for 10 seconds (20 * 500ms)
        $job_result = null;

        while ($attempts < $max_attempts) {
            $status_result = send_request($SERVER_URL . '/api/job/' . $jobId, $API_KEY, 'GET');
            $status = $status_result['body']['status'] ?? 'unknown';
            echo "Polling... Status: $status<br>";

            if ($status === 'done') {
                $job_result = $status_result['body']['result'];
                break;
            }

            usleep(500000); // Wait 500ms
            $attempts++;
        }

        // --- 4. Display the final results ---
        /*
        $job_result structure:
        [
            'stdout' => '...',        // Standard output from Java program
            'stderr' => '...',        // Standard error output
            'crashed' => bool,        // Whether the program crashed
            'timedOut' => bool,       // Whether the program timed out
            'memoryUsageMB' => float, // Peak memory usage in MB
            'cpuPercentMax' => float, // Peak CPU usage percentage
            'executionTimeMs' => int  // Total execution time in milliseconds
        ]
        */
        echo "<h2>Final Results</h2>";
        if ($job_result) {
            echo "<pre>" . htmlspecialchars(print_r($job_result, true)) . "</pre>";
        } else {
            echo "Job did not complete within the polling time.";
        }
        echo '</div>';

        // --- 5. Write out stdout
        $tmp_dir = sys_get_temp_dir();
        // file name is out-{args[0]}.txt or out.txt if no args
        $arg_part = !empty($args) ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $args[0]) : 'out';
        //$output_file = $tmp_dir . '/out-' . $arg_part . '.txt';
        $output_file = '/var/www/projects/f25-01/html/files/x/out-' . $arg_part . '.txt';
        if ($job_result && isset($job_result['stdout'])) {
            file_put_contents($output_file, $job_result['stdout']);
            echo "<p>Standard output written to: <strong>$output_file</strong></p>";
            $content = file_get_contents($output_file);
            echo "<h3>Content of output file:</h3><pre>" . htmlspecialchars($content) . "</pre>";
        } else {
            echo "<p>No standard output to write.</p>"; 
        }
    }
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/clike/clike.min.js"></script>
    <script>
        var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
            lineNumbers: true,
            mode: "text/x-java",
            matchBrackets: true
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
                case 'fcfs':
                    <?php
                        $file_path = __DIR__ . '/Main.java';
                        $code = file_get_contents($file_path);
                        echo "code = " . json_encode($code) . ";";
                    ?>
                    args = '041';
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

