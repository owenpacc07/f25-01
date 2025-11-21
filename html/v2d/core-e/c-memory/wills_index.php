<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Replacement Comparison</title>
    <link rel="stylesheet" href="./page-rep.css">
</head>

<!-- This file will perform the following tasks and meet the following requirements:
    1: Users can type in their own input
    2: Users can generate random input
    3: These discrete input objects will be sent to an external java file in the same folder
    4: The external java file will return an array of arrays, which we will display as the results
    5: The results will be displayed in a table format, with the column as the identifying input and the results across the row 
-->
    <body>
        <!--include navbar-->
        <?php include realpath('../../navbar.php'); ?>
        <main>
            <div class="container">

                <section id="input-section">
                    <div class="card">
                        <div class="card-header">
                            <h1>Input your pages</h1>
                        </div>
                        <div class="card-body">
                            <form id="inputForm">
                                <div class="form-group">
                                    <label for="userInput">Enter your input:</label>
                                    <input type="text" id="userInput" name="userInput" class="form-control" value="<?php echo implode(',', array_map(function() { return rand(1, 10); }, range(1, 12))); ?>">
                                </div>
                                <button type="button" id="generateRandom" class="btn btn-primary">Generate Random Input</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h2>Inputs to be compared</h2>
                        </div>
                        <div class="card-body">
                            <table id="inputsTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Input #</th>
                                        <th>Input data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Inputs will be populated here -->
                                </tbody>
                            </table>

                            <form id="submitInputsForm">
                                <button type="submit" class="btn btn-warning">Submit All Inputs</button>
                            </form>
                        </div>
                    </div>
                </section>

                <section id="results-section">
                    <div class="card">
                        <div class="card-header">
                            <h2>Results</h2>
                        </div>
                        <div class="card-body">
                            <table id="resultsTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Input #</th>
                                        <th>Score</th>
                                        <th>FIFO</th>
                                        <th>LRU</th>
                                        <th>Optimal</th>
                                        <th>MFU</th>
                                        <th>LFU</th>
                                        <th>Chart</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Results will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>
        <script>
            //variable to count inputs
            let inputCount = 0;

            //generate random input and inject to input field
            document.getElementById('generateRandom').addEventListener('click', function() {
                const randomInput = Array.from({ length: 12 }, () => Math.floor(Math.random() * 10) + 1);
                document.getElementById('userInput').value = randomInput;
            });

            //add input to table when submit button is clicked
            document.getElementById('inputForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const userInput = document.getElementById('userInput').value;
                inputCount++;
                addInput(userInput);
            });

            //select the input table and add inputs there
            function addInput(input) {
                let inputs = [];
                inputs.push(input);
                const inputsTable = document.getElementById('inputsTable').getElementsByTagName('tbody')[0];
                const row = inputsTable.insertRow();
                const cell1 = row.insertCell(0);
                const cell2 = row.insertCell(1);
                cell1.textContent = inputCount;
                cell2.textContent = input;
            }

            //for each input object in inputs[], construct a results object after running each algorithm, when submit all inputs is clicked
            document.getElementById('submitInputsForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const inputs = Array.from(document.getElementById('inputsTable').getElementsByTagName('tbody')[0].children).map(row => row.children[1].textContent.split(','));
                const results = inputs.map(input => {
                    return {
                        input: input,
                        score: "# of page faults",
                        fifo: Math.floor(Math.random() * 100),
                        lru: Math.floor(Math.random() * 100),
                        optimal: Math.floor(Math.random() * 100),
                        mfu: Math.floor(Math.random() * 100),
                        lfu: Math.floor(Math.random() * 100),
                        chart: 'chart'
                    };
                });
                displayResults(results);
            });

            //function to display results in table
            function displayResults(results) {
                const resultsTable = document.getElementById('resultsTable').getElementsByTagName('tbody')[0];
                results.forEach(result => {
                    const row = resultsTable.insertRow();
                    const cell1 = row.insertCell(0);
                    const cell2 = row.insertCell(1);
                    const cell3 = row.insertCell(2);
                    const cell4 = row.insertCell(3);
                    const cell5 = row.insertCell(4);
                    const cell6 = row.insertCell(5);
                    const cell7 = row.insertCell(6);
                    const cell8 = row.insertCell(7);
                    cell1.textContent = result.input;
                    cell2.textContent = result.score;
                    cell3.textContent = result.fifo;
                    cell4.textContent = result.lru;
                    cell5.textContent = result.optimal;
                    cell6.textContent = result.mfu;
                    cell7.textContent = result.lfu;
                    cell8.textContent = result.chart;
                });
            }
        </script>
    </body>
</html>
