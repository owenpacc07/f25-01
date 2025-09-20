<?php
// Start a session to store the success message
/*
JUSTIN FEINMAN S25 - should be 100% working. will explain how this works to any future group if needed
*/
$_SESSION['coremode'] = 'core-c';
session_start();
require_once "../../system.php";
// Check if there is a success or error message
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Check if the logged-in user has ModeID >= 3 from sql tables
// this means research user,manage user or admin.

// Define the path for the page reference files
$mid = '031';
$path = realpath("../../../files/core-c/m-$mid");

// Define paths for writing user input and output
$coreCPath = realpath("../../../files/core-c/c-file/");
$inputFile = "$coreCPath/in-file.dat"; // Input file 
$outputFile = "$path/out-$mid.dat"; // Output file 
$flagFile = "$path/flag-file.txt";

// Load current input and output data (optional, if needed for display)
$output = file_exists($outputFile) ? file_get_contents($outputFile) : '';
$input = file_exists($inputFile) ? file_get_contents($inputFile) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Allocation Comparison</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['bar'] });
        google.charts.setOnLoadCallback(initializeChart);

        let chart;
        let simulationResults;

        const color = [];
        const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
        color.push(randomColor);

        function initializeChart() {
            const data = google.visualization.arrayToDataTable([
                ['Mechanism', 'Number of Unallocated Files'],
                ['Contiguous', 0], ['Linked', 0], ['Indexed', 0]
            ]);
            const options = {
                chart: { title: 'File Allocation Comparison' },
                legend: { position: 'bottom' },
                colors: color
            };
            chart = new google.charts.Bar(document.getElementById('draw-chart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        function showChart(event) {
            event.preventDefault();
            if (typeof simulationResults === 'undefined' || Object.keys(simulationResults).length === 0) {
                alert('Please Run The Simulation First!');
                return;
            }

            const dataArray = [['Mechanism', 'Number of Unallocated Files', { role: 'style' }]];
            for (const [algorithm, faults] of Object.entries(simulationResults)) {
                dataArray.push([algorithm, faults, randomColor]);
            }
            const data = google.visualization.arrayToDataTable(dataArray);
            const options = {
                chart: { title: 'File Allocation Comparison' },
                legend: { position: 'bottom' },
                colors: color
            };
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
        function generateRandomInput() {
            const fileNames = ['FileA', 'FileB', 'FileC', 'FileD', 'FileE', 'FileF'];
            const diskSize = 32; // Total disk capacity
            let randomFiles = [];
            let usedNames = new Set(); // Track used file names
            let totalSize = 0; // Track cumulative size (length + 1 per file)

            // Determine max possible files based on minimum size per file (1 overhead + 2 length = 3)
            const maxPossibleFiles = Math.min(fileNames.length, Math.floor(diskSize / 3)); // Max files if all were min size
            const numFiles = Math.floor(Math.random() * (maxPossibleFiles - 1)) + 2; // 2 to maxPossibleFiles

            while (randomFiles.length < numFiles && usedNames.size < fileNames.length) {
                const name = fileNames[Math.floor(Math.random() * fileNames.length)];
                if (!usedNames.has(name)) {
                    // Calculate remaining capacity
                    const remainingSize = diskSize - totalSize;
                    // Minimum size for a file is 3 (1 overhead + 2 length)
                    if (remainingSize < 3) break; // Stop if not enough space for min file

                    // Max length is constrained by remaining size or 9, minus 1 for overhead
                    const maxLength = Math.min(9, remainingSize - 1); // Leave room for overhead
                    const minLength = 2;
                    const length = Math.floor(Math.random() * (maxLength - minLength + 1)) + minLength;
                    const fileSize = length + 1; // Total size including overhead

                    // Double-check we don’t exceed diskSize
                    if (totalSize + fileSize <= diskSize) {
                        const head = Math.floor(Math.random() * (diskSize - length)); // Head from 0 to 31-length
                        randomFiles.push(`${name}-${head}-${length}`);
                        usedNames.add(name);
                        totalSize += fileSize; // Add to cumulative total
                    } else {
                        break; // Stop if this file would exceed 32
                    }
                }
            }
            document.getElementById('fileSizesInput').value = randomFiles.join(', ');
        }
    </script>
</head>
<body>
    <?php include realpath('../../navbar.php'); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">File Allocation Comparison</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-center">Input Files (name-head-length):<br>Example: "FileA-0-8" </p>
                        <form method="post" class="text-center" id="simulationForm">
                            <div class="form-group">
                                <label for="fileSizesInput">Files (separated by a comma):<br>Example: "FileA-0-8, FileB-10-6"</label>
                                <input type="text" name="file_sizes" class="form-control" id="fileSizesInput"
                                    value="<?php echo isset($_POST['file_sizes']) ? htmlspecialchars($_POST['file_sizes']) : (isset($_GET['input']) ? htmlspecialchars($_GET['input']) : 'countC-29-3, tr-31-5, yum-5-4, hyd-27-7'); ?>"
                                    required placeholder="e.g., countC-29-3, tr-31-5, yum-5-4, hyd-27-7">
                            </div>
                            <button type="submit" name="compare" class="btn btn-purple d-inline mr-2">Simulate for Results</button>
                            <button type="button" class="btn btn-purple d-inline mr-2" onclick="showChart(event)">Show Chart</button>
                            <button type="button" class="btn btn-purple d-inline" onclick="generateRandomInput()">Random Input</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['compare'])) {
        $fileSizesInput = trim(isset($_POST['file_sizes']) ? $_POST['file_sizes'] : '');
        $mechanisms =['031', '032', '033'];
        $mechanismID = isset($_POST['mechanism']) ? $_POST['mechanism'] : $mid;
        // Parse input and prepare formatted content
        $fileEntries = array_filter(explode(',', $fileSizesInput), 'trim');
        $files = [];
        $fileId = 1;
        $formattedContent = "32\n" . count($fileEntries) . "\n";
        
        foreach ($fileEntries as $entry) {
            $parts = array_map('trim', explode('-', $entry));
            if (count($parts) === 3) {
                $files[] = ['name' => $parts[0], 'head' => intval($parts[1]), 'length' => intval($parts[2])];
                $formattedContent .= "$fileId,{$parts[0]},{$parts[2]}\n"; // Java ignores head
                $fileId++;
            }
        }
        $formattedContent = rtrim($formattedContent, "\n");
    
        // Write input file to $coreCPath/in-file.dat
        if (!file_exists($coreCPath)) {
            mkdir($coreCPath, 0770, true);
        }
        if (!file_put_contents($inputFile, $formattedContent)) {
            error_log("Failed to write to $inputFile");
        } else {
            error_log("Wrote to $inputFile: $formattedContent");
        }
    
        // Copy in-file.dat to $path/in-031.dat for Java
        $javaInputFile = "$path/in-$mid.dat";
        if (!copy($inputFile, $javaInputFile)) {
            error_log("Failed to copy $inputFile to $javaInputFile");
        } else {
            error_log("Copied $inputFile to $javaInputFile");
        }
    
        foreach ($mechanisms as $mid) {
            $path = realpath("../../../files/core-c/m-$mid");
            $outputFile = "$path/out-$mid.dat"; // Output file specific to mechanism
            
            // Copy input file to mechanism-specific directory
            $javaInputFile = "$path/in-$mid.dat";
            if (!copy($inputFile, $javaInputFile)) {
                error_log("Failed to copy $inputFile to $javaInputFile");
            } else {
                error_log("Copied $inputFile to $javaInputFile");
            }
    
            // Run Java with the mechanism-specific path
            $javaCommand = "java -classpath " . escapeshellarg(realpath("../../../cgi-bin/core-c/m-$mid")) . " m$mid " . escapeshellarg($path);
            $javaOutput = shell_exec("$javaCommand 2>&1");
            echo "<script>console.log('Java output for m$mid:', " . json_encode($javaOutput) . ");</script>";
            if ($javaOutput) {
                error_log("Java execution output/error for m$mid: $javaOutput");
            } else {
                error_log("Java executed successfully for m$mid (no output)");
            }
    
            // Check if Java wrote the output
            if (!file_exists($outputFile)) {
                error_log("Java failed to create $outputFile for m$mid");
                $defaultOutput = array_fill(0, 32, 0);
                file_put_contents($outputFile, implode(',', $defaultOutput));
                error_log("Wrote fallback output to $outputFile: " . implode(',', $defaultOutput));
            } else {
                error_log("Java successfully wrote to $outputFile for m$mid");
            }
    
            // Optionally store the output for later use
            $results["m$mid"] = file_get_contents($outputFile);
        }

        // Run PHP simulation for UI
        if (!empty($files)) {
            $algorithms = [
                'Contiguous' => ['func' => 'contAlgorithm', 'mid' => '031'],
                'Linked' => ['func' => 'linkedAlgorithm', 'mid' => '032'],
                'Indexed' => ['func' => 'indexAlgorithm', 'mid' => '033']
            ];

            $results = [];
            foreach ($algorithms as $name => $info) {
                $results[$name] = $info['func']($files);
            }
            echo "<script>simulationResults = " . json_encode($results) . ";</script>";
        } else {
            echo "<script>alert('Please provide valid file entries in the format name-head-length.');</script>";
        }

        // Load updated output
        $output = file_get_contents($outputFile);
    }

    function contAlgorithm($files) {
        $diskSize = 32;
        $allocationMap = array_fill(0, $diskSize, 0); // Temporary map for simulation
        $unallocatedFiles = 0;
        $fileId = 1;
    
        foreach ($files as $file) {
            $start = $file['head'];
            $overheadBlocks = 1;
            $totalBlocks = $file['length'] + $overheadBlocks;
            $end = $start + $totalBlocks;
    
            if ($end <= $diskSize) {
                $isFree = true;
                for ($j = $start; $j < $end; $j++) {
                    if ($allocationMap[$j] !== 0) {
                        $isFree = false;
                        break;
                    }
                }
                if ($isFree) {
                    for ($j = $start; $j < $end; $j++) {
                        $allocationMap[$j] = $fileId; // Simulate allocation
                    }
                } else {
                    $unallocatedFiles++;
                }
            } else {
                $unallocatedFiles++;
            }
            $fileId++;
        }
    
        return $unallocatedFiles;
    }

    function linkedAlgorithm($files) {
        $diskSize = 32;
        $unallocatedFiles = 0;
        $allocationMap = array_fill(0, $diskSize, 0);
        $usedBlocks = 0;
        
        foreach ($files as $file) {
            $start = $file['head'];
            $overheadBlocks = 1;
            $totalNeeded = $file['length'] + $overheadBlocks;
            
            if ($usedBlocks + $totalNeeded > $diskSize) {
                $unallocatedFiles++;
                continue;
            }
            
            $blocksAllocated = 0;
            $allocatedFromStart = true;
            if ($start + $totalNeeded <= $diskSize) {
                for ($i = $start; $i < $start + $totalNeeded; $i++) {
                    if ($allocationMap[$i] == 0) {
                        $blocksAllocated++;
                    } else {
                        $allocatedFromStart = false;
                        break;
                    }
                }
            } else {
                $allocatedFromStart = false;
            }
            
            if ($allocatedFromStart) {
                for ($i = $start; $i < $start + $totalNeeded; $i++) {
                    $allocationMap[$i] = 1;
                    $usedBlocks++;
                }
            } else {
                $blocksAllocated = 0;
                for ($i = 0; $i < $diskSize && $blocksAllocated < $totalNeeded; $i++) {
                    if ($allocationMap[$i] == 0) {
                        $allocationMap[$i] = 1;
                        $blocksAllocated++;
                        $usedBlocks++;
                    }
                }
            }
        }
        return $unallocatedFiles;
    }

    function indexAlgorithm($files) {
        $diskSize = 32;
        $unallocatedFiles = 0;
        $allocationMap = array_fill(0, $diskSize, 0);
        $usedBlocks = 0;
        
        foreach ($files as $file) {
            $start = $file['head'];
            $indexBlocks = 1;
            $totalNeeded = $file['length'] + $indexBlocks;
            
            if ($usedBlocks + $totalNeeded > $diskSize) {
                $unallocatedFiles++;
                continue;
            }
            
            $indexAllocated = false;
            if ($start < $diskSize && $allocationMap[$start] == 0) {
                $allocationMap[$start] = 1;
                $usedBlocks++;
                $indexAllocated = true;
            } else {
                for ($i = 0; $i < $diskSize; $i++) {
                    if ($allocationMap[$i] == 0) {
                        $allocationMap[$i] = 1;
                        $usedBlocks++;
                        $indexAllocated = true;
                        break;
                    }
                }
            }
            
            if ($indexAllocated) {
                $dataBlocksNeeded = $file['length'];
                for ($i = 0; $i < $diskSize && $dataBlocksNeeded > 0; $i++) {
                    if ($allocationMap[$i] == 0) {
                        $allocationMap[$i] = 1;
                        $usedBlocks++;
                        $dataBlocksNeeded--;
                    }
                }
            } else {
                $unallocatedFiles++;
            }
        }
        return $unallocatedFiles;
    }
    ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">File Allocation Results</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Mechanism</th>
                                    <th>Number of Unallocated Files</th>
                                    <th>Unallocated File Names</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($results) && !empty($files)): ?>
                                    <?php 
                                    $algorithms = [
                                        'Contiguous' => ['func' => 'contAlgorithm', 'mid' => '031'],
                                        'Linked' => ['func' => 'linkedAlgorithm', 'mid' => '032'],
                                        'Indexed' => ['func' => 'indexAlgorithm', 'mid' => '033']
                                    ];
                                    foreach ($results as $algorithm => $faults): ?>
                                        <?php
                                        $unallocatedNames = [];
                                        $tempAllocated = array_fill(0, 32, 0);
                                        foreach ($files as $file) {
                                            $start = $file['head'];
                                            $totalNeeded = $file['length'] + 1;
                                            if ($algorithm === 'Contiguous') {
                                                $isFree = true;
                                                if ($start + $totalNeeded <= 32) {
                                                    for ($j = $start; $j < $start + $totalNeeded; $j++) {
                                                        if ($tempAllocated[$j] == 1) {
                                                            $isFree = false;
                                                            break;
                                                        }
                                                    }
                                                    if ($isFree) {
                                                        for ($j = $start; $j < $start + $totalNeeded; $j++) {
                                                            $tempAllocated[$j] = 1;
                                                        }
                                                    } else {
                                                        $unallocatedNames[] = $file['name'];
                                                    }
                                                } else {
                                                    $unallocatedNames[] = $file['name'];
                                                }
                                            } elseif ($algorithm === 'Linked') {
                                                $usedBlocks = array_sum($tempAllocated);
                                                if ($usedBlocks + $totalNeeded <= 32) {
                                                    $blocksAllocated = 0;
                                                    $allocatedFromStart = true;
                                                    if ($start + $totalNeeded <= 32) {
                                                        for ($i = $start; $i < $start + $totalNeeded; $i++) {
                                                            if ($tempAllocated[$i] == 0) {
                                                                $blocksAllocated++;
                                                            } else {
                                                                $allocatedFromStart = false;
                                                                break;
                                                            }
                                                        }
                                                    } else {
                                                        $allocatedFromStart = false;
                                                    }
                                                    if ($allocatedFromStart) {
                                                        for ($i = $start; $i < $start + $totalNeeded; $i++) {
                                                            $tempAllocated[$i] = 1;
                                                        }
                                                    } else {
                                                        $blocksAllocated = 0;
                                                        for ($i = 0; $i < 32 && $blocksAllocated < $totalNeeded; $i++) {
                                                            if ($tempAllocated[$i] == 0) {
                                                                $tempAllocated[$i] = 1;
                                                                $blocksAllocated++;
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $unallocatedNames[] = $file['name'];
                                                }
                                            } else { // Indexed
                                                $usedBlocks = array_sum($tempAllocated);
                                                if ($usedBlocks + $totalNeeded <= 32) {
                                                    $indexAllocated = false;
                                                    if ($start < 32 && $tempAllocated[$start] == 0) {
                                                        $tempAllocated[$start] = 1;
                                                        $indexAllocated = true;
                                                    } else {
                                                        for ($i = 0; $i < 32; $i++) {
                                                            if ($tempAllocated[$i] == 0) {
                                                                $tempAllocated[$i] = 1;
                                                                $indexAllocated = true;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    if ($indexAllocated) {
                                                        $dataBlocksNeeded = $file['length'];
                                                        for ($i = 0; $i < 32 && $dataBlocksNeeded > 0; $i++) {
                                                            if ($tempAllocated[$i] == 0) {
                                                                $tempAllocated[$i] = 1;
                                                                $dataBlocksNeeded--;
                                                            }
                                                        }
                                                    } else {
                                                        $unallocatedNames[] = $file['name'];
                                                    }
                                                } else {
                                                    $unallocatedNames[] = $file['name'];
                                                }
                                            }
                                        }
                                        $mid = $algorithms[$algorithm]['mid'];
                                        $link = "https://cs.newpaltz.edu/p/s25-01/v2/core-c/m-$mid/?input=" . urlencode($fileSizesInput);
                                        ?>
                                        <tr>
                                            <td><a href="<?php echo $link; ?>" target="_blank"><?php echo htmlspecialchars($algorithm); ?></a></td>
                                            <td><?php echo htmlspecialchars($faults); ?></td>
                                            <td><?php echo htmlspecialchars(implode(', ', $unallocatedNames)); ?></td>
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
</body>
<style>
    .btn-purple {
        background-color: #DB2F2F;
        color: white;
        border-radius: 8px;
    }
    .btn-purple:hover {
        background-color: #DE5252;
        color: white;
    }
    .card-header {
        background-color: #DB2F2F;
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
        background-color: #E97D7D;
        color: white;
        font-weight: bold;
    }
    .table th:nth-child(3) {
        background-color: #E97D7D;
    }
    input[type="text"] {
        border-radius: 8px;
        padding: 8px;
        border: 2px solid #ddd;
    }
    form {
        padding: 8px;
    }
    td a {
        text-decoration: none;
        color: rgb(103, 2, 2);
    }
</style>
</html>