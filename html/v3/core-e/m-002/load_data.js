// load_data.js for m-002
import { fetchIO, fetchPHP } from "../api.js";

const mid = '002';

let flagFileUpdated = false;

export async function loadData(user_id, experiment_id, family_id, mechanism_id) {
    const response2 = await fetchIO(user_id, experiment_id, family_id, mechanism_id);
    console.log("Raw data from get-io-experiment.php:", response2);

    if (response2.error) {
        console.error("Server error:", response2.error);
        return false;
    }

    // Check for invalid output
    if (response2.output === "No output generated for m002") {
        console.error("No output generated for mechanism m002. Check server-side logic or output file (out-001.dat).");
        return false;
    }

    const response = await fetchPHP(0, mid);
    flagFileUpdated = response;
    if (!flagFileUpdated) {
        console.warn("Flag file not updated, aborting");
        return false;
    }

    let data = {};
    await resetFlagFile();

    data.input = parseInputDataFile(response2.input);
    data.output = parseOutputDataFile(response2.output, 1);
    data.extraOutput = parseOutputDataFile(response2.output, 2);

    console.log("Parsed data:", data);

    // Check if output is empty and log a warning
    if (data.output.length === 0 && data.extraOutput.length === 0) {
        console.warn("Output data is empty. Verify the output format from out-001.dat.");
    }

    return data;
}

async function resetFlagFile() {
    try {
        flagFileUpdated = await fetchPHP(1, mid);
        console.log("Flag file reset:", flagFileUpdated);
    } catch (error) {
        console.error("Error resetting flag file:", error);
    }
}

function displayError(message) {
    const container = document.querySelector('.container');
    if (container) {
        const errorDiv = document.createElement('div');
        errorDiv.style.color = 'red';
        errorDiv.style.textAlign = 'center';
        errorDiv.textContent = `Error: ${message}`;
        container.prepend(errorDiv);
    }
}

function parseInputDataFile(input) {
    let inputData = [];
    const lines = input.split('\n').map(line => line.trim()).filter(line => line);
    for (let i = 0; i < lines.length; i++) {
        const process = lines[i].split(',').map(num => parseInt(num.trim(), 10));
        if (process.length === 4 && process.every(num => !isNaN(num))) {
            inputData.push({
                pid: process[0],
                arrival: process[1],
                burst: process[2],
                priority: process[3],
            });
        } else {
            console.warn(`Invalid input line ${i + 1}: ${lines[i]}`);
        }
    }
    if (inputData.length === 0) {
        console.error("No valid input data parsed");
        throw new Error("Failed to parse input data");
    }
    return inputData;
}

function parseOutputDataFile(output, section, numProcesses) {
    const lines = output.split('\n').map(line => line.trim()).filter(line => line.length > 0 || line === '');
    let output1 = [];
    let output2 = [];
    let inStartEndSection = false;
    let seenFirstBlank = false;

    console.log("Parsing output lines:", lines);

    lines.forEach((line, index) => {
        if (index < 2) {
            console.log(`Skipping header line ${index}: ${line}`);
            return;
        }
        if (line === '' && !seenFirstBlank) {
            seenFirstBlank = true;
            inStartEndSection = true;
            console.log("Entering start/end section at index", index);
            return;
        }
        if (line === '' && inStartEndSection && seenFirstBlank) {
            inStartEndSection = false;
            console.log("Entering detailed table section at index", index);
            return;
        }
        if (inStartEndSection) {
            const burst = line.split(',').map(num => parseInt(num.trim(), 10));
            console.log(`Attempting to parse start/end: ${line} ->`, burst);
            if (burst.length === 3 && !isNaN(burst[0]) && !isNaN(burst[1]) && !isNaN(burst[2])) {
                output1.push({
                    pid: burst[0],
                    start: burst[1],
                    end: burst[2],
                    burst: burst[2] - burst[1],
                });
                console.log(`Parsed start/end: ${line} ->`, output1[output1.length - 1]);
            } else {
                console.log(`Failed to parse start/end line: ${line}`);
            }
        } else if (index > 2 && !line.startsWith('Type') && !line.startsWith('Number')) {
            const parts = line.split(',').map(str => str.trim());
            const expectedColumns = 2 * numProcesses + 3; // time, burst times, cpu, waiting times, queue
            if (parts.length >= expectedColumns) {
                const row = {
                    time: parseInt(parts[0], 10),
                    cpu: parts[numProcesses + 1] || '',
                    queue: parts[expectedColumns - 1] || '',
                };
                for (let i = 0; i < numProcesses; i++) {
                    row[`p${i + 1}RemainingBurst`] = parts[i + 1];
                    row[`p${i + 1}WaitingTime`] = parts[numProcesses + 2 + i];
                }
                output2.push(row);
            }
        }
    });

    console.log("Parsed output1:", output1);
    console.log("Parsed output2:", output2);
    return section === 1 ? output1 : output2;
}