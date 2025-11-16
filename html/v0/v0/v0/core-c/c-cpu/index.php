<?php
// Start a session
if (!session_start()) {
    die("Session failed to start");
}
if (!isset($_SESSION['userid'])) {
    die("User not logged in.");
}

echo "Script started for user: " . htmlspecialchars($_SESSION['userid'] ?? 'unknown') . "<br>";
error_log("Script started for user: " . ($_SESSION['userid'] ?? 'unknown'));

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$compare = "cpu";
$user = $_SESSION['userid'];

// Check for success or error messages
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Define paths
//$coreCPath = "/var/www/projects/s25-01/html/files/core-c/c-cpu/";
$coreCPath = realpath(__DIR__ . '/../../../files/core-c/c-cpu/');
$inputFile = "$coreCPath/in-cpu.dat";
$outputFile = "$coreCPath/out-cpu.dat";
$flagFile = "$coreCPath/flag-file.txt";
$restrict_view = 0;

// Run Java simulations for each mechanism
$mechanisms = [
    '001' => 'FCFS',
    '002' => 'Nonpreemptive SJF',
    '003' => 'Nonpreemptive Priority (High)',
    '004' => 'Nonpreemptive Priority (Low)',
    '005' => 'Round Robin',
    '006' => 'Preemptive SJF',
    '007' => 'Preemptive Priority (High)',
    '008' => 'Preemptive Priority (Low)'
];

$results = [];
foreach ($mechanisms as $mid => $name) {
    //$mechanismPath = realpath("/var/www/projects/s25-01/html/files/core-c/m-$mid");
    $mechanismPath = realpath(__DIR__ . "/../../../files/core-c/m-$mid");
    $javaInputFile = "$mechanismPath/in-$mid.dat";
    $javaOutputFile = "$mechanismPath/out-$mid.dat";

    // Copy input file to mechanism-specific directory
    if (!copy($inputFile, $javaInputFile)) {
        error_log("Failed to copy $inputFile to $javaInputFile");
        $results[$name] = array_fill_keys(array_column($processes, 'id'), 0); // Fallback
        continue;
    }

    // Execute Java program
    //$javaCommand = "java -classpath /var/www/p/s25-01/html/cgi-bin/core-c/m-$mid m$mid " . escapeshellarg($mechanismPath);
    $javaCommand = "java -classpath " . realpath(__DIR__ . "/../../../cgi-bin/core-c/m-$mid") . " m$mid " . escapeshellarg($mechanismPath);
    $javaOutput = shell_exec("$javaCommand 2>&1");
    if ($javaOutput) {
        error_log("Java execution output/error for m$mid: $javaOutput");
    } else {
        error_log("Java executed successfully for m$mid (no output)");
    }

    // Read Java output
    if (file_exists($javaOutputFile)) {
        $outputContent = file_get_contents($javaOutputFile);
        // Assuming Java outputs "P1:5,P2:3,P3:8,P4:6" or similar
        $waitingTimes = [];
        foreach (explode(',', $outputContent) as $entry) {
            if (preg_match('/P(\d+):(\d+)/', trim($entry), $matches)) {
                $waitingTimes[$matches[1]] = (int)$matches[2];
            }
        }
        $results[$name] = $waitingTimes;
        error_log("Read from $javaOutputFile: " . $outputContent);
    } else {
        error_log("Java failed to create $javaOutputFile for m$mid");
        $results[$name] = array_fill_keys(array_column($processes, 'id'), 0); // Fallback
    }
}

// Load current input data (for display)
$input = file_exists($inputFile) ? file_get_contents($inputFile) : '';

// Handle simulation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['compare'])) {
    $newDataIN = trim($_POST['processes'] ?? '');
    
    // Parse input into processes array
    $processes = [];
    $inputLines = explode("\n", $newDataIN);
    foreach ($inputLines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            list($processId, $arrivalTime, $burstTime, $priority) = array_map('trim', explode(',', $line));
            $processes[] = [
                'id' => $processId,
                'arrival' => (int)$arrivalTime,
                'burst' => (int)$burstTime,
                'priority' => (int)$priority
            ];
        }
    }

    // Run the simulation algorithms
    $algorithms = [
        'FCFS' => 'fcfsAlgorithm',
        'Nonpreemptive SJF' => 'nonpreemptiveSJFAlgorithm',
        'Nonpreemptive Priority (High)' => 'nonpreemptivePriorityHighAlgorithm',
        'Nonpreemptive Priority (Low)' => 'nonpreemptivePriorityLowAlgorithm',
        'Round Robin' => 'roundRobinAlgorithm',
        'Preemptive SJF' => 'preemptiveSJFAlgorithm',
        'Preemptive Priority (High)' => 'preemptivePriorityHighAlgorithm',
        'Preemptive Priority (Low)' => 'preemptivePriorityLowAlgorithm'
    ];

    $results = [];
    foreach ($algorithms as $name => $function) {
        $results[$name] = $function($processes);
    }

    // Save input and output data
    $newDataOUT = json_encode($results);

    // Ensure the directory exists
    if (!file_exists($coreCPath)) {
        if (!mkdir($coreCPath, 0770, true)) {
            $_SESSION['error_message'] = "Failed to create directory: $coreCPath";
            error_log("Failed to create directory: $coreCPath");
            header("Location: index.php");
            exit();
        }
    }

    // Write to input file
    if (file_put_contents($inputFile, $newDataIN) === false) {
        $error = error_get_last();
        $_SESSION['error_message'] = "Failed to write to input file: $inputFile. Error: " . ($error['message'] ?? 'Unknown error');
        error_log("Failed to write to input file: $inputFile");
        header("Location: index.php");
        exit();
    } else {
        error_log("Successfully wrote to input file: $inputFile with data: $newDataIN");
    }

    // Write to output file
    if (file_put_contents($outputFile, $newDataOUT) === false) {
        $error = error_get_last();
        $_SESSION['error_message'] = "Failed to write to output file: $outputFile. Error: " . ($error['message'] ?? 'Unknown error');
        error_log("Failed to write to output file: $outputFile");
        header("Location: index.php");
        exit();
    } else {
        error_log("Successfully wrote to output file: $outputFile");
    }

    // Update flag file (for fetchPHP compatibility)
    file_put_contents($flagFile, "1");

    // Database connection
    require_once './../../config.php';
    if (!$link) {
        $_SESSION['error_message'] = "Database connection failed: " . mysqli_connect_error();
        header("Location: index.php");
        exit();
    }

    // Insert experiment data (using a generic mechanism ID since we're comparing all)
    $mechanism_query = "SELECT mechanism_id FROM comparisons WHERE client_code='cpu'";
    $mechanism_result = mysqli_query($link, $mechanism_query);
    $mechanism = ($mechanism_result && mysqli_num_rows($mechanism_result) > 0) 
        ? mysqli_fetch_all($mechanism_result, MYSQLI_NUM)[0][0] 
        : 'cpu'; // Fallback to 'cpu' if no specific mechanism

    $codeFilePath = realpath(__FILE__); // Use this PHP file as the code path
    $experiment_insert = "INSERT INTO experiments (family_id, user_id, input_path, output_path, code_path, restrict_view) 
                          VALUES ('$mechanism', '$user', '$inputFile', '$outputFile', '$codeFilePath', '$restrict_view')";
    if (mysqli_query($link, $experiment_insert)) {
        $experiment_id = mysqli_insert_id($link);
        $experiment_path = "../../../files/experiments/" . $experiment_id . "_" . $mechanism . "_" . $user;
        if (!mkdir($experiment_path, 0770, true) && !is_dir($experiment_path)) {
            error_log("Failed to create directory: $experiment_path");
        } else {
            copy($inputFile, "$experiment_path/in-cpu.dat");
            copy($outputFile, "$experiment_path/out-cpu.dat");
            $filenameIN = "$experiment_path/in-cpu.dat";
            $filenameOUT = "$experiment_path/out-cpu.dat";
            $experiment_update = "UPDATE experiments SET input_path='$filenameIN', output_path='$filenameOUT' WHERE experiment_id=$experiment_id";
            mysqli_query($link, $experiment_update);
        }
    } else {
        error_log("Error inserting experiment: " . mysqli_error($link));
    }

    // Store results in session
    $_SESSION['simulation_results'] = $results;
    $_SESSION['success_message'] = "All simulations completed!";
    header("Location: index.php?input=" . urlencode($newDataIN));
    exit();
}

// Algorithm URLs for display
$algorithmUrls = [
    'FCFS' => '../m-001/index.php',
    'Nonpreemptive SJF' => '../m-002/index.php',
    'Nonpreemptive Priority (High)' => '../m-003/index.php',
    'Nonpreemptive Priority (Low)' => '../m-004/index.php',
    'Round Robin' => '../m-005/index.php',
    'Preemptive SJF' => '../m-006/index.php',
    'Preemptive Priority (High)' => '../m-007/index.php',
    'Preemptive Priority (Low)' => '../m-008/index.php'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPU Scheduling Comparison</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['bar'] });
        google.charts.setOnLoadCallback(initializeChart);

        let chart;
        let simulationResults = <?php echo json_encode($_SESSION['simulation_results'] ?? []); ?>;

        const color = [];
        const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
        color.push(randomColor);

        function initializeChart() {
            const data = google.visualization.arrayToDataTable([
                ['Algorithm', 'Average Waiting Time'],
                ['FCFS', 0], ['Nonpreemptive SJF', 0], ['Nonpreemptive Priority (High)', 0],
                ['Round Robin', 0], ['Nonpreemptive Priority (Low)', 0], 
                ['Preemptive SJF', 0], ['Preemptive Priority (High)', 0],
                ['Preemptive Priority (Low)', 0]
            ]);
            const options = {
                chart: { title: 'CPU Scheduling Algorithms Comparison' },
                legend: { position: 'bottom' },
                colors: color
            };
            chart = new google.charts.Bar(document.getElementById('draw-chart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        function showChart(event) {
            event.preventDefault();
            if (!simulationResults || Object.keys(simulationResults).length === 0) {
                alert('Please Run The Simulation First!');
                return;
            }

            const dataArray = [['Algorithm', 'Average Waiting Time', { role: 'style' }]];
            for (const [algorithm, waitingTimes] of Object.entries(simulationResults)) {
                const avgWaitingTime = Object.values(waitingTimes).reduce((a, b) => a + b, 0) / Object.keys(waitingTimes).length;
                dataArray.push([algorithm, avgWaitingTime, randomColor]);
            }

            const data = google.visualization.arrayToDataTable(dataArray);
            const options = {
                chart: { title: 'CPU Scheduling Algorithms Comparison' },
                legend: { position: 'bottom' },
                colors: color
            };
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        function generateRandomInput() {
            const numProcesses = 4;
            let randomProcesses = [];
            for (let i = 1; i <= numProcesses; i++) {
                const processId = `${i}`;
                const arrivalTime = Math.floor(Math.random() * 10);
                const burstTime = Math.floor(Math.random() * 10) + 1;
                const priority = Math.floor(Math.random() * 5) + 1;
                randomProcesses.push(`${processId}, ${arrivalTime}, ${burstTime}, ${priority}`);
            }
            document.getElementById('processInput').value = randomProcesses.join('\n');
        }
    </script>
</head>

<body>
    <?php include realpath('../../navbar.php'); ?>

    <div class="container mt-3">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">CPU Scheduling Comparison</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-center">Input Data:</p>
                        <form method="post" class="text-center">
                            <div class="form-group">
                                <label for="processInput">Enter Process Details (Format: Process ID, Arrival Time, Burst Time, Priority):</label>
                                <textarea name="processes" class="form-control" id="processInput" rows="5" required><?php
                                    if (isset($_POST['processes'])) {
                                        echo htmlspecialchars($_POST['processes']);
                                    } elseif (isset($_GET['input'])) {
                                        echo htmlspecialchars($_GET['input']);
                                    } else {
                                        echo "1, 0, 5, 2\n2, 1, 3, 1\n3, 2, 8, 3\n4, 3, 6, 2";
                                    }
                                ?></textarea>
                            </div>
                            <button type="button" class="btn btn-purple d-inline mr-2" onclick="generateRandomInput()">Generate Random Input</button>
                            <button type="submit" name="compare" class="btn btn-purple d-inline mr-2">Simulate All</button>
                            <button type="button" class="btn btn-purple d-inline mr-2" onclick="showChart(event)">Show Chart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">CPU Scheduling Results</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Algorithm</th>
                                    <th>Waiting Time</th>
                                    <th>Average Waiting Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($_SESSION['simulation_results'])): ?>
                                    <?php foreach ($_SESSION['simulation_results'] as $algorithm => $waitingTimes): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo $algorithmUrls[$algorithm]; ?>?input=<?php echo urlencode($newDataIN ?? $input); ?>" target="_blank">
                                                    <?php echo htmlspecialchars($algorithm); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php
                                                foreach ($waitingTimes as $processId => $time) {
                                                    echo "P$processId: $time<br>";
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo number_format(array_sum($waitingTimes) / count($waitingTimes), 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Please Run a Simulation to See Results.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div id="draw-chart" style="width: 100%; height: 323px; display: block;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Algorithm functions (unchanged)
    function fcfsAlgorithm($processes) {
        usort($processes, function ($a, $b) {
            return $a['arrival'] - $b['arrival'];
        });
        $waitingTimes = [];
        $currentTime = 0;
        foreach ($processes as $process) {
            if ($currentTime < $process['arrival']) {
                $currentTime = $process['arrival'];
            }
            $waitingTimes[$process['id']] = $currentTime - $process['arrival'];
            $currentTime += $process['burst'];
        }
        return $waitingTimes;
    }

    function nonpreemptiveSJFAlgorithm($processes) {
        usort($processes, function ($a, $b) {
            return $a['arrival'] - $b['arrival'];
        });
        $waitingTimes = [];
        $currentTime = 0;
        $remainingProcesses = $processes;
        while (!empty($remainingProcesses)) {
            $arrivedProcesses = array_filter($remainingProcesses, function ($p) use ($currentTime) {
                return $p['arrival'] <= $currentTime;
            });
            if (empty($arrivedProcesses)) {
                $currentTime++;
                continue;
            }
            usort($arrivedProcesses, function ($a, $b) {
                return $a['burst'] - $b['burst'];
            });
            $nextProcess = array_shift($arrivedProcesses);
            $waitingTimes[$nextProcess['id']] = $currentTime - $nextProcess['arrival'];
            $currentTime += $nextProcess['burst'];
            $remainingProcesses = array_filter($remainingProcesses, function ($p) use ($nextProcess) {
                return $p['id'] !== $nextProcess['id'];
            });
        }
        return $waitingTimes;
    }

    function nonpreemptivePriorityHighAlgorithm($processes) {
        usort($processes, function ($a, $b) {
            return $a['arrival'] - $b['arrival'];
        });
        $waitingTimes = [];
        $currentTime = 0;
        $remainingProcesses = $processes;
        while (!empty($remainingProcesses)) {
            $arrivedProcesses = array_filter($remainingProcesses, function ($p) use ($currentTime) {
                return $p['arrival'] <= $currentTime;
            });
            if (empty($arrivedProcesses)) {
                $currentTime++;
                continue;
            }
            usort($arrivedProcesses, function ($a, $b) {
                return $b['priority'] - $a['priority'];
            });
            $nextProcess = array_shift($arrivedProcesses);
            $waitingTimes[$nextProcess['id']] = $currentTime - $nextProcess['arrival'];
            $currentTime += $nextProcess['burst'];
            $remainingProcesses = array_filter($remainingProcesses, function ($p) use ($nextProcess) {
                return $p['id'] !== $nextProcess['id'];
            });
        }
        return $waitingTimes;
    }

    function nonpreemptivePriorityLowAlgorithm($processes) {
        usort($processes, function ($a, $b) {
            return $a['arrival'] - $b['arrival'];
        });
        $waitingTimes = [];
        $currentTime = 0;
        $remainingProcesses = $processes;
        while (!empty($remainingProcesses)) {
            $arrivedProcesses = array_filter($remainingProcesses, function ($p) use ($currentTime) {
                return $p['arrival'] <= $currentTime;
            });
            if (empty($arrivedProcesses)) {
                $currentTime++;
                continue;
            }
            usort($arrivedProcesses, function ($a, $b) {
                return $a['priority'] - $b['priority'];
            });
            $nextProcess = array_shift($arrivedProcesses);
            $waitingTimes[$nextProcess['id']] = $currentTime - $nextProcess['arrival'];
            $currentTime += $nextProcess['burst'];
            $remainingProcesses = array_filter($remainingProcesses, function ($p) use ($nextProcess) {
                return $p['id'] !== $nextProcess['id'];
            });
        }
        return $waitingTimes;
    }

    function preemptiveSJFAlgorithm($processes) {
        $waitingTimes = array_fill_keys(array_column($processes, 'id'), 0);
        $remainingBurstTimes = array_column($processes, 'burst', 'id');
        $currentTime = 0;
        $completedProcesses = 0;
        while ($completedProcesses < count($processes)) {
            $arrivedProcesses = array_filter($processes, function ($p) use ($currentTime, $remainingBurstTimes) {
                return $p['arrival'] <= $currentTime && $remainingBurstTimes[$p['id']] > 0;
            });
            if (empty($arrivedProcesses)) {
                $currentTime++;
                continue;
            }
            usort($arrivedProcesses, function ($a, $b) use ($remainingBurstTimes) {
                return $remainingBurstTimes[$a['id']] - $remainingBurstTimes[$b['id']];
            });
            $nextProcess = $arrivedProcesses[0];
            $remainingBurstTimes[$nextProcess['id']]--;
            $currentTime++;
            foreach ($arrivedProcesses as $process) {
                if ($process['id'] !== $nextProcess['id']) {
                    $waitingTimes[$process['id']]++;
                }
            }
            if ($remainingBurstTimes[$nextProcess['id']] === 0) {
                $completedProcesses++;
            }
        }
        return $waitingTimes;
    }

    function roundRobinAlgorithm($processes, $timeQuantum = 2) {
        $waitingTimes = array_fill_keys(array_column($processes, 'id'), 0);
        $remainingBurstTimes = array_column($processes, 'burst', 'id');
        $queue = [];
        $currentTime = 0;
        usort($processes, function ($a, $b) {
            return $a['arrival'] - $b['arrival'];
        });
        foreach ($processes as $process) {
            if ($process['arrival'] <= $currentTime) {
                $queue[] = $process['id'];
            }
        }
        while (!empty($queue)) {
            $currentProcess = array_shift($queue);
            $executionTime = min($remainingBurstTimes[$currentProcess], $timeQuantum);
            foreach ($queue as $processId) {
                $waitingTimes[$processId] += $executionTime;
            }
            $remainingBurstTimes[$currentProcess] -= $executionTime;
            $currentTime += $executionTime;
            foreach ($processes as $process) {
                if ($process['arrival'] <= $currentTime && !in_array($process['id'], $queue) && $remainingBurstTimes[$process['id']] > 0) {
                    $queue[] = $process['id'];
                }
            }
            if ($remainingBurstTimes[$currentProcess] > 0) {
                $queue[] = $currentProcess;
            }
        }
        return $waitingTimes;
    }

    function preemptivePriorityHighAlgorithm($processes) {
        $waitingTimes = array_fill_keys(array_column($processes, 'id'), 0);
        $remainingBurstTimes = array_column($processes, 'burst', 'id');
        $currentTime = 0;
        $completedProcesses = 0;
        while ($completedProcesses < count($processes)) {
            $arrivedProcesses = array_filter($processes, function ($p) use ($currentTime, $remainingBurstTimes) {
                return $p['arrival'] <= $currentTime && $remainingBurstTimes[$p['id']] > 0;
            });
            if (empty($arrivedProcesses)) {
                $currentTime++;
                continue;
            }
            usort($arrivedProcesses, function ($a, $b) {
                return $b['priority'] - $a['priority'];
            });
            $nextProcess = $arrivedProcesses[0];
            $remainingBurstTimes[$nextProcess['id']]--;
            $currentTime++;
            foreach ($arrivedProcesses as $process) {
                if ($process['id'] !== $nextProcess['id']) {
                    $waitingTimes[$process['id']]++;
                }
            }
            if ($remainingBurstTimes[$nextProcess['id']] === 0) {
                $completedProcesses++;
            }
        }
        return $waitingTimes;
    }

    function preemptivePriorityLowAlgorithm($processes) {
        $waitingTimes = array_fill_keys(array_column($processes, 'id'), 0);
        $remainingBurstTimes = array_column($processes, 'burst', 'id');
        $currentTime = 0;
        $completedProcesses = 0;
        while ($completedProcesses < count($processes)) {
            $arrivedProcesses = array_filter($processes, function ($p) use ($currentTime, $remainingBurstTimes) {
                return $p['arrival'] <= $currentTime && $remainingBurstTimes[$p['id']] > 0;
            });
            if (empty($arrivedProcesses)) {
                $currentTime++;
                continue;
            }
            usort($arrivedProcesses, function ($a, $b) {
                return $a['priority'] - $b['priority'];
            });
            $nextProcess = $arrivedProcesses[0];
            $remainingBurstTimes[$nextProcess['id']]--;
            $currentTime++;
            foreach ($arrivedProcesses as $process) {
                if ($process['id'] !== $nextProcess['id']) {
                    $waitingTimes[$process['id']]++;
                }
            }
            if ($remainingBurstTimes[$nextProcess['id']] === 0) {
                $completedProcesses++;
            }
        }
        return $waitingTimes;
    }
    ?>

    <style>
        .btn-purple {
            background-color: #9769D9;
            color: white;
            border-radius: 8px;
        }
        .btn-purple:hover {
            background-color: #B594E4;
            color: white;
        }
        .card-header {
            background-color: #9769D9;
            color: white;
            font-weight: bold;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .table th, .table td {
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #B594E4;
            color: white;
            font-weight: bold;
        }
        .table th:nth-child(3) {
            background-color: #C6A4F1;
        }
        input[type="text"], textarea {
            border-radius: 8px;
            padding: 8px;
            border: 2px solid #ddd;
        }
        form {
            padding: 8px;
        }
        .table a {
            color: #9769D9;
            text-decoration: none;
        }
        .table a:hover {
            color: #B594E4;
            text-decoration: underline;
        }
    </style>
    </body>
</html>