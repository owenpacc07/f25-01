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
    
    $input = file_exists($submission['input_path']) ? file_get_contents($submission['input_path']) : '';
    $output = file_exists($submission['output_path']) ? file_get_contents($submission['output_path']) : '';
    
    $_SESSION['log_messages'][] = "DEBUG edit.php - Input file exists: " . (file_exists($submission['input_path']) ? 'YES' : 'NO');
    $_SESSION['log_messages'][] = "DEBUG edit.php - Output file exists: " . (file_exists($submission['output_path']) ? 'YES' : 'NO');
    $_SESSION['log_messages'][] = "DEBUG edit.php - Input content length: " . strlen($input);
    $_SESSION['log_messages'][] = "DEBUG edit.php - Output content length: " . strlen($output);
} else {
    $_SESSION['log_messages'][] = "DEBUG edit.php - NO SUBMISSION FOUND - Please create a submission first";
    // Don't load any default data - force user to create submission
}

// Load format from v2c format files (these are read-only reference files)
$format_path = realpath("../../../files/core-s/m-$mid_padded");
if ($format_path) {
    $format_file = $format_path . "/format-$mid.txt";
    $format = file_exists($format_file) ? file_get_contents($format_file) : 'Format file not found';
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
        
        $fileName = 'Main.java';
        file_put_contents($subDir . $fileName, $javaCode);
        
        $className = 'Main';
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
        
        file_put_contents($subDir . "in-$mid.dat", $_POST['input']);
        file_put_contents($subDir . "out-$mid.dat", $_POST['output']);
        
        if ($_POST['submit'] === 'proceed') {
            header("Location: ./");
            exit();
        } else {
            $saveMessage = '<div class="alert alert-success text-center" role="alert">Data saved to submission #' . $submission_id . '!</div>';
            $output = file_get_contents($subDir . "out-$mid.dat");
            $input = file_get_contents($subDir . "in-$mid.dat");
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
        const templateCode = `import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.LinkedList;
import java.util.List;
import java.util.Queue;
import java.util.Scanner;
import java.util.stream.Collectors;
import java.io.FileWriter;

/*
PiD, Arrival, Burst, Priority 

-Non-preemptive
1 SJF: Shortest Job First 	-Finished
2 PH: Priority High (higher number means higher priority)
3 PL: Priority Low (lower number means higher priority)

-Preemptive-
4 RR: Round Robin
5 pSJF: Preemptive Shortest Job First
6 pPH: Preemptive Priority High (higher number means higher priority)
7 pPL: Preemptive Priority Low (lower number means higher priority)
*/
public class Main {

	private static ArrayList<Process> process = new ArrayList<Process>(); // new created
	private static ArrayList<EndProcess> sProcess = new ArrayList<EndProcess>(); // writing to file

	// First Come First Serve
	private static Queue<Process> processFCFSsort = new LinkedList<Process>();

	public static int typeScheduler,
			cpuTime, quantum, waiting, size = 0;
	public static int endFlag = 0;

	public static void addToQueue() // adds a process to a queue if the cpuTime meets arrival time
	{
		for (int i = 0; i < process.size(); i++) {
			if (process.get(i).getArrive() == cpuTime) {
				processFCFSsort.add(process.get(i));
			}
		}
	}

	public static Process swap(Process p) // swaps process with one waiting in queue (Non-Preemptive)
	{
		return processFCFSsort.remove();
	}

	public static void CPU() {
		cpuTime = 0;

		// sort the processes in ascending order by arrival time
		ArrayList<Process> clonedProcess = new ArrayList<Process>();
		for (Process proc: process){
			clonedProcess.add(proc);
		}
		while (processFCFSsort.size() < process.size()){
			int minArrival = process.get(0).getArrive();
			int minIndex = 0;
			for (int i = 0; i < clonedProcess.size(); i++){
				if(clonedProcess.get(i).getArrive() < minArrival){
					minArrival = clonedProcess.get(i).getArrive();
					minIndex = i;
				}
			}
			processFCFSsort.add(clonedProcess.get(minIndex));
			clonedProcess.remove(minIndex);
		}
		clonedProcess = null;

		// Process the CPU FCFS
		int time = 0;
		int fcfsSize = processFCFSsort.size();
		for(int pID = 0; pID < fcfsSize; pID++){
			Process proc = processFCFSsort.poll();
			EndProcess endProc = new EndProcess(proc.getID(), time, time+proc.getBurst());
			time += proc.getBurst();
			sProcess.add(endProc);
		}
	}

	// updates flag file to have a value of 1
	static void updateFlagFile() {
		try {

			File flagFile = new File("../../../files/core/m-001/flag-file.txt");
			FileWriter writer = new FileWriter(flagFile, false);
			writer.write("1");
			writer.close();

		} catch (IOException e) {
			System.out.println("Error in updateFlagFile()");
		}
	}

	public static void main(String args[]) throws IOException {
		String inputFile;
		if (args.length == 0) {
			System.out.println("No arguments given, using default values");
			inputFile = "../../../files/core/m-001/in-001.dat";
		} else {
			System.out.println("Using input file: " + args[0]);
			inputFile = args[0];
		}
		inFile(inputFile); // reads from file
		size = process.size();
		CPU(); // CPU scheduler
		outFile(); // outputs to file
		updateFlagFile(); // updates flag file
	}

	public static void inFile(String inputFile) throws FileNotFoundException // Good
	{
		File inFile = new File(inputFile);
		Scanner scan = new Scanner(inFile);

		// Iterate through each line of the input file
		while (scan.hasNextLine()) {
			// Split the current line into parts using blankspace as the delimiter.
			/*
			 * Format is in this order: process_id, arrival_time, burst_time, priority.
			 * Input: 1 0 7 1
			 * currentProcessInfo = ["1", "0", "7", "1"]
			 */
			String currentProcessInfo[] = scan.nextLine().split(" ");

			// Copy the string array into an int array and parse the string as ints.
			// Could honestly just use keep string array and just parse it individually when creating the new process at line 180~
			int currentProcess[] = new int[currentProcessInfo.length];
			for (int i = 0; i < currentProcess.length; i++) {
				currentProcess[i] = Integer.parseInt(currentProcessInfo[i]);
			}

			// Clean up
			currentProcessInfo = null;

			// Creates a new Process and puts it into process array
			Process newProcess = new Process(currentProcess[0], currentProcess[1], currentProcess[2],
					currentProcess[3]);
			process.add(newProcess);

			//Clean up
			currentProcess = null;
		}
		scan.close();
	}

	public static void outFile() throws IOException {
		File outFile = new File("../../../files/core/m-001/out-001.dat");
		PrintWriter out = new PrintWriter(outFile);
		String scheduler = null;

		scheduler = "First Come First Serve(Non-Preemptive)";

		out.println("Type of Scheduler: " + scheduler);
		out.println("Number of Processes: " + size);
		out.println("");

		for (int i = 0; i < sProcess.size(); i++) // loop to write processes to file
			out.println((sProcess.get(i).getID()) + "," + (sProcess.get(i).getStart()) + "," + (sProcess.get(i).getEnd()));

		out.println("");

		int nop = process.size();
		Integer[] pwait = new Integer[nop];
		Integer[] plastend = new Integer[nop];
		for (int i = 0; i < nop; i++) {
			pwait[i] = 0;
			plastend[i] = 0;
		}

		//Print output for detailedTable
		Collections.sort(sProcess);		// sort in ascending order by process id cuz detailed table reads it in the order of p1,p2,p3,p4
		int duration = process.stream().mapToInt(p -> p.getOriginalBurstTime()).sum();
		for (int timeStep = 0; timeStep <= duration; timeStep++){
			StringBuffer line = new StringBuffer();
			line.append(timeStep + ",");

			for(EndProcess proc: sProcess){
				
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null).getArrive();

				// guard clause cuz it hasnt arrived yet in the current time step
				if (timeStep < arrivalTime) {
					line.append("-,");
					continue;
				}

				//print the remaining burst time if the timestep is within a process' start and end time
				if (timeStep >= proc.getStart() && timeStep <= proc.getEnd()){
					line.append(proc.getEnd() - timeStep);
				}
				else if(timeStep > proc.getEnd()){
					// print 0 because the timestep has already exceeded the processes end time meaning it has finished
					line.append(0);
				}
				else if(timeStep < proc.getStart()){
					//prints the total burst time of the process since it has arrived yet is not being worked on since timestep is not yet there
					line.append(proc.getEnd() - proc.getStart());
				}
				line.append(",");
			}

			//Print what process the CPU is currently handling
			for(EndProcess proc: sProcess){
				if(timeStep >= proc.getStart() && timeStep <= proc.getEnd()){
					line.append("P" + proc.getID() + ",");
					break;
				}
			}

			//Print the waiting times for each process
			for(EndProcess proc: sProcess){
				int burstTime = proc.getEnd() - proc.getStart();
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null).getArrive();

				int waitingTime = proc.getEnd() - arrivalTime - burstTime; // totalWaitingTime
				int dtWaitTime = timeStep - arrivalTime; // waiting time at the current timeStep

				// guard clause for printing a finished process' total waiting time
				if (timeStep > proc.getStart() && timeStep <= proc.getEnd() || timeStep > proc.getEnd()){
					line.append(waitingTime + ",");
					continue;
				}

				// if it has arrived print is instantenous waiting time, if not print nothing
				if (timeStep >= arrivalTime){
					line.append(dtWaitTime + ",");
				}else{
					line.append("-" + ",");
				}
			}

			//Print the processes in queue for the current timestep
			for(EndProcess proc: sProcess){
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null).getArrive();

				if (timeStep > proc.getStart() && timeStep <= proc.getEnd() || timeStep > proc.getEnd()){
					continue;
				}

				if (timeStep >= arrivalTime){
					line.append("P"+proc.getID()+" ");
				}
			}

			out.println(line.toString());
		}
		
		out.close();
	}

}

class EndProcess implements Comparable<EndProcess> {

	private int id, start, end;

	public EndProcess(int id, int start, int end) {
		this.id = id;
		this.start = start; // equivalent to wait
		this.end = end; // equivalent to wait
	}

	public int getID() {
		return id;
	}

	public int getStart() {
		return start;
	}

	public int getEnd() {
		return end;
	}

	public int compareTo(EndProcess proc){
		if (this.id > proc.id){
			return 1;
		}
		else if(this.id < proc.id){
			return -1;
		}
		else 
			return 0;
	}

	public String toString(){
		StringBuffer info = new StringBuffer();
		info.append("\\nProcess ID : "+id);
		info.append("\\nStart Time : "+start);
		info.append("\\nEnd Time : "+end);
		return info.toString();
	}

}

class Process {

	private int id, arrive, burst, priority, originalBurstTime;

	public Process(int id, int arrive, int burst, int priority) {
		this.id = id;
		this.arrive = arrive;
		this.burst = burst;
		this.originalBurstTime = burst;
		this.priority = priority;
	}

	public int getID() {
		return this.id;
	}

	public int getArrive() {
		return this.arrive;
	}

	public int getBurst() {
		return this.burst;
	}

	public int getOriginalBurstTime(){
		return this.originalBurstTime;
	}

	public int decBurst() {
		return burst = burst - 1;
	}

	public int getPri() {
		return this.priority;
	}

	public String toString(){
		StringBuffer info = new StringBuffer();
		info.append("Process ID: " + id);
		info.append("\\n");
		info.append("Arrival Time: " + arrive);
		info.append("\\n");
		info.append("Burst Time: " + burst);
		info.append("\\n");
		info.append("Priority: " + priority);
		info.append("\\n");
		return info.toString();
	}
}
`;

        function loadTemplate() {
            document.getElementById('javaCode').value = templateCode;
        }

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
            
            fetch('edit.php', {
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
// Log all messages to browser console
if (!empty($_SESSION['log_messages'])) {
    foreach ($_SESSION['log_messages'] as $logMessage) {
        $escapedMsg = addslashes($logMessage);
        echo "<script>console.log('" . $escapedMsg . "');</script>";
    }
    unset($_SESSION['log_messages']);
}
?>