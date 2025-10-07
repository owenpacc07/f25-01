<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Java Executor</title>
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

    <h1>Local Java Executor</h1>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="code">Java Code</label>
            <?php
            $defaultJavaCode = <<<JAVA
public class Main {
    public static void main(String[] args) {
        System.out.println("Hello from local executor!");
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
        $start_time = microtime(true);
        //$tmp_dir = sys_get_temp_dir(); // go to /tmp/
        $tmp_dir = "/var/www/projects/f25-01/html/files/submissions/400_4_1/";

        
        echo '<div id="results">';
        echo '<h2>Execution Log</h2>';

        // --- 1. Prepare Java code and arguments ---
        $javaCode = $_POST['javaCode'] ?? '';
        $args = !empty($_POST['args']) ? preg_split('/\s+/', trim($_POST['args'])) : [];
        
        echo "Preparing execution in temporary directory: $tmp_dir<br>";

        // Create unique directory for this execution
        $exec_dir = $tmp_dir . '/java_exec_' . uniqid();
        if (!mkdir($exec_dir, 0755, true)) {
            echo "Error: Could not create execution directory.<br>";
            echo '</div>';
            exit;
        }
        echo "Created execution directory: $exec_dir<br>";

        // --- 2. Write Java file ---
        $java_file = $exec_dir . '/Main.java';
        if (file_put_contents($java_file, $javaCode) === false) {
            echo "Error: Could not write Java file.<br>";
            echo '</div>';
            exit;
        }
        echo "Java file written successfully.<br>";

        // --- 3. Handle uploaded files ---
        $uploaded_files = [];
        if (!empty($_FILES['inputFiles']['name'][0])) {
            $file_count = count($_FILES['inputFiles']['name']);
            echo "Processing $file_count uploaded file(s)...<br>";
            
            for ($i = 0; $i < $file_count; $i++) {
                $filename = $_FILES['inputFiles']['name'][$i];
                $tmp_file = $_FILES['inputFiles']['tmp_name'][$i];
                $dest_file = $exec_dir . '/' . $filename;
                
                if (move_uploaded_file($tmp_file, $dest_file)) {
                    echo "Uploaded file saved: $filename<br>";
                    $uploaded_files[] = $filename;
                } else {
                    echo "Warning: Could not save uploaded file: $filename<br>";
                }
            }
        }

        // --- 3.5. Auto-create input files based on arguments ---
        if (!empty($args) && preg_match('/^\d{3}$/', $args[0])) {
            $file_number = $args[0];
            $input_filename = "in-{$file_number}.dat";
            
            // Check if this input file wasn't already uploaded
            if (!in_array($input_filename, $uploaded_files)) {
                echo "Auto-creating input file: $input_filename<br>";
                
                // Create sample input data based on the file number
                $input_data = "";
                // Read in from x/files/in-XXX.dat if it exists
                // $sample_input_path = __DIR__ . "/x/files/{$input_filename}";
                $sample_input_path = "/var/www/projects/f25-01/html/files/submissions/400_4_1/{$input_filename}";

                if (file_exists($sample_input_path)) {
                    $input_data = file_get_contents($sample_input_path);
                    $input_file_path = $exec_dir . '/' . $input_filename;
                    if (file_put_contents($input_file_path, $input_data) !== false) {
                        echo "Created $input_filename with sample data<br>";
                        $uploaded_files[] = $input_filename;
                    } else {
                        echo "Warning: Could not create $input_filename<br>";
                    }
                } else {
                    echo "Warning: Sample input file $sample_input_path not found<br>";
                }
            } else {
                echo "Input file $input_filename already uploaded, skipping auto-creation<br>";
            }
        }

        // --- 4. Compile Java code ---
        echo "Compiling Java code...<br>";
        $compile_output = [];
        $compile_return_var = 0;
        $compile_cmd = "cd " . escapeshellarg($exec_dir) . " && javac Main.java 2>&1";
        exec($compile_cmd, $compile_output, $compile_return_var);
        
        $job_result = [
            'stdout' => '',
            'stderr' => '',
            'crashed' => false,
            'timedOut' => false,
            'executionTimeMs' => 0
        ];

        if ($compile_return_var !== 0) {
            echo "Compilation failed.<br>";
            $job_result['stderr'] = implode("\n", $compile_output);
            $job_result['crashed'] = true;
        } else {
            echo "Compilation successful.<br>";

            // --- 5. Execute Java program ---
            echo "Executing Java program...<br>";
            $run_output = [];
            $run_error = [];
            $run_return_var = 0;
            
            // Build command with arguments
            $escaped_args = array_map('escapeshellarg', $args);
            $args_string = implode(' ', $escaped_args);
            $run_cmd = "cd " . escapeshellarg($exec_dir) . " && timeout 10s java Main $args_string 2>&1";
            
            $exec_start = microtime(true);
            exec($run_cmd, $run_output, $run_return_var);
            $exec_end = microtime(true);
            
            $execution_time_ms = round(($exec_end - $exec_start) * 1000);
            $job_result['executionTimeMs'] = $execution_time_ms;
            
            if ($run_return_var === 124) {
                // Timeout occurred
                echo "Program execution timed out (10 seconds limit).<br>";
                $job_result['timedOut'] = true;
                $job_result['stderr'] = "Program execution timed out after 10 seconds.";
            } elseif ($run_return_var !== 0) {
                echo "Program execution failed with return code: $run_return_var<br>";
                $job_result['crashed'] = true;
                $job_result['stderr'] = implode("\n", $run_output);
            } else {
                echo "Program executed successfully.<br>";
                $job_result['stdout'] = implode("\n", $run_output);
            }
        }

        $total_time = microtime(true) - $start_time;
        echo "Total execution time: " . round($total_time * 1000) . "ms<br>";

        // --- 6. Display the final results ---
        echo "<h2>Final Results</h2>";
        echo "<pre>" . htmlspecialchars(print_r($job_result, true)) . "</pre>";

        // --- 7. Write out stdout to file ---
        if (!empty($job_result['stdout'])) {
            // Remove spaces and special characters from first argument for filename
            $arg_part = !empty($args) ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $args[0]) : 'out';
            //$output_file = $tmp_dir . '/out-' . $arg_part . '.txt';
            //$output_file = '/var/www/projects/f25-01/html/files/out-' . $arg_part . '.txt';
            $output_file = '/var/www/projects/f25-01/html/files/submissions/400_4_1/out-' . $arg_part . '.dat';

            file_put_contents($output_file, $job_result['stdout']);
            echo "<p>Standard output written to: <strong>$output_file</strong></p>";
            echo "<h3>Content of output file:</h3><pre>" . htmlspecialchars($job_result['stdout']) . "</pre>";
        } else {
            echo "<p>No standard output to write.</p>";
        }

        // --- 8. Cleanup ---
        // Remove the temporary execution directory and its contents
        //$files = glob($exec_dir . '/*');
        //foreach ($files as $file) {
        //    if (is_file($file)) {
        //        unlink($file);
        //    }
        //}
        //rmdir($exec_dir);
        //echo "<p>Temporary files cleaned up.</p>";

        echo '</div>';
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