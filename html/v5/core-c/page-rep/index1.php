

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Replacement Comparison</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <?php
    // Start a session to store the success message
    session_start();

    // Check if there is a success or error message
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        unset($_SESSION['success_message']); // Clear the success message after it's displayed
    } elseif (isset($_SESSION['error_message'])) {
        $error_message = $_SESSION['error_message'];
        unset($_SESSION['error_message']); // Clear the error message after it's displayed
    }

    ?>
    <!-- Include navbar -->
    <?php include realpath('../../navbar.php'); ?>


    <!-- Main container for input and simulation -->
    <div class="container mt-5">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Page Replacement Comparison</h3>
                    </div>
                    <div class="card-body">

                        <p class="text-center">Input Data:</p>
                        <!-- Form to input the page reference string -->
                        <form id="input-form" method="post" class="text-center">
                            <div class="form-group">
                                <label for="pageInput">Edit Input Data-Page Reference String (comma-separated):</label>
                                <input type="text" name="pages" class="form-control" id="pageInput"
                                    value="<?php echo isset($_POST['pages']) ? htmlspecialchars($_POST['pages']) : (isset($_GET['input']) ? htmlspecialchars($_GET['input']) : '7, 0, 1, 2, 4, 1, 3, 4, 2, 3, 0, 3, 2, 1, 2, 0'); ?>"
                                    required>
                            </div>
                            <button type="button" class="btn btn-primary d-inline mr-2" onclick="document.getElementById('pageInput').value = Array.from({ length: 16 }, () => Math.floor(Math.random() * 10)).join(',');">Generate
                                Random Input
                            </button>
                            <button type="submit" name="compare" class="btn btn-purple d-inline mr-2">Simulate for
                                Results
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results and Chart Side by Side -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <!-- Results Table Column -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Page Replacement Results</h3>
                    </div> 
                    <div class="card-body">
                        <table id="input-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Input</th>
                                    <th>Score</th>
                                    <th>FIFO</th>
                                    <th>LRU</th>
                                    <th>OPT</th>
                                    <th>MFU</th>
                                    <th>LFU</th>
                                    <th>Chart</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Inputs and results will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('input-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const pageInput = document.getElementById('pageInput').value;
            addInputAndResultsToTable(input);
            
        });

        function addInputAndResultsToTable(input) {
            const tableBody = document.getElementById('inputs-table').getElementsByTagName('tbody')[0];
            const row = tableBody.insertRow();
            row.insertCell().textContent = input.input.join(', ');

            row.insertCell().textContent = FIFO(input);
            row.insertCell().textContent = LRU(input);
            row.insertCell().textContent = OPT(input);
            row.insertCell().textContent = MFU(input);
            row.insertCell().textContent = LFU(input);

            const chartCell = row.insertCell();
            const chartButton = document.createElement('button');
            chartButton.textContent = 'View Chart';
            chartButton.classList.add('btn', 'btn-primary');
            chartButton.addEventListener('click', function() {
                drawChart(input);
            });
            chartCell.appendChild(chartButton);
        };

        function FIFO(pages) {
            const frameCount = 3;
            let memory = [];
            let faults = 0;
            for (let page of pages) {
                if (!memory.includes(page)) {
                    if (memory.length < frameCount) {
                        memory.push(page);
                    } else {
                        memory.shift();
                        memory.push(page);
                    }
                    faults++;
                }
            }
            return faults;
        }

        function LRU(pages) {
            const frameCount = 3;
            let memory = [];
            let faults = 0;
            for (let page of pages) {
                if (!memory.includes(page)) {
                    if (memory.length >= frameCount) {
                        memory.shift();
                    }
                    memory.push(page);
                    faults++;
                } else {
                    memory = memory.filter(p => p !== page);
                    memory.push(page);
                }
            }
            return faults;
        }

        function OPT(pages) {
            const frameCount = 3;
            let memory = [];
            let faults = 0;
            for (let i = 0; i < pages.length; i++) {
                const page = pages[i];
                if (!memory.includes(page)) {
                    if (memory.length < frameCount) {
                        memory.push(page);
                    } else {
                        let farthestUseIndex = -1;
                        let pageToReplace = -1;
                        for (let memPage of memory) {
                            const nextUse = pages.slice(i + 1).indexOf(memPage);
                            if (nextUse === -1) {
                                pageToReplace = memPage;
                                break;
                            } else {
                                if (nextUse > farthestUseIndex) {
                                    farthestUseIndex = nextUse;
                                    pageToReplace = memPage;
                                }
                            }
                        }
                        memory = memory.filter(p => p !== pageToReplace);
                        memory.push(page);
                    }
                    faults++;
                }
            }
            return faults;
        }

        function MFU(pages) {
            const frameCount = 3;
            let memory = {};
            let faults = 0;
            for (let page of pages) {
                if (memory[page]) {
                    memory[page]++;
                } else {
                    if (Object.keys(memory).length < frameCount) {
                        memory[page] = 1;
                    } else {
                        const mostFrequentPage = Object.keys(memory).reduce((a, b) => memory[a] > memory[b] ? a : b);
                        delete memory[mostFrequentPage];
                        memory[page] = 1;
                    }
                    faults++;
                }
            }
            return faults;
        }

        function LFU(pages) {
            const frameCount = 3;
            let memory = {};
            let faults = 0;
            for (let page of pages) {
                if (memory[page]) {
                    memory[page]++;
                } else {
                    if (Object.keys(memory).length >= frameCount) {
                        const leastFrequentPage = Object.keys(memory).reduce((a, b) => memory[a] < memory[b] ? a : b);
                        delete memory[leastFrequentPage];
                    }
                    memory[page] = 1;
                    faults++;
                }
            }
            return faults;
        }

    </script>
</body>

<style>
    /* Button styles to match purple theme */
    .btn-purple {
        background-color: #9769D9;
        color: white;
        border-radius: 8px;
        /* Rounded corners for buttons */
    }

    .btn-purple:hover {
        background-color: #B594E4;
        /* Lighter purple for hover effect */
        color: white;
    }

    /* Customize the background color of the table card header */
    .card-header {
        background-color: #9769D9;
        color: white;
        font-weight: bold;
    }

    /* Add a subtle shadow to the card */
    .card {
        border-radius: 12px;
        /* Rounding corners of the card */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        /* Soft shadow */
        overflow: hidden;
        /* Ensures content doesn't overflow rounded corners */

    }

    .card:hover {
        transform: translateY(-5px);
        /* Slight upward movement */
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        /* Deeper shadow on hover */
    }

    .table th,
    .table td {
        text-align: center;
        border-bottom: 1px solid #ddd;
        /* Soft bottom borders */
    }

    /* Customize the table header background color */
    .table th {
        background-color: #B594E4;
        color: white;
        font-weight: bold;
    }

    .table th:nth-child(3) {
        background-color: #C6A4F1;
        /* Different color for 'Input' column */

    }

    /* Input field for page reference string */
    input[type="text"] {
        border-radius: 8px;
        /* Rounded corners for input fields */
        padding: 8px;
        /* Add some internal padding */
        border: 2px solid #ddd;
        /* Soft border */
    }

    /* Add a little padding inside the form */
    form {
        padding: 8px;
    }
</style>

</html>