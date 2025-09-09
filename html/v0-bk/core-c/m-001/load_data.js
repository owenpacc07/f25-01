// load_data.js
import { fetchIO, fetchPHP } from "../api.js";

const compare = "cpu";
const mid = '001';

let flagFileUpdated = false;

export async function loadData() {
    const response = await fetchPHP(0, compare);
    // console.log("fetchPHP Response:", response);

    flagFileUpdated = response;
    if (!flagFileUpdated) {
        // console.log("Flag file not updated, aborting");
        return false;
    }

    let data = {};
    await resetFlagFile();

    const response2 = await fetchIO(compare, mid);
    // console.log("fetchIO Response:", response2);
    // console.log("Raw output string:", response2.output);
    // console.log("Output split into lines:", response2.output.split('\n'));

    if (response2.error) {
        console.log("Error in loading data:", response2.error);
        return false;
    }

    data.input = parseInputDataFile(response2.input);
    data.output = parseOutputDataFile(response2.output, 1);
    data.extraOutput = parseOutputDataFile(response2.output, 2);

    // console.log("Data loaded:", data);
    return data;
}

async function resetFlagFile() {
    flagFileUpdated = await fetchPHP(1, mid);
    // console.log("Flag file reset:", flagFileUpdated);
}

function parseInputDataFile(input) {
    let inputData = [];
    const lines = input.split('\n').map(line => line.trim()).filter(line => line);
    for (let i = 0; i < Math.min(lines.length, 4); i++) {
        const process = lines[i].split(',').map(num => parseInt(num.trim(), 10));
        if (process.length === 4) {
            inputData.push({
                pid: process[0],
                arrival: process[1],
                burst: process[2],
                priority: process[3],
            });
        }
    }
    return inputData;
}

function parseOutputDataFile(output, section) {
    const lines = output.split('\n').map(line => line.trim()).filter(line => line.length > 0 || line === '');
    let output1 = [];
    let output2 = [];
    let inStartEndSection = false;
    let seenFirstBlank = false;

    // console.log("Parsing output lines:", lines);

    lines.forEach((line, index) => {
        if (index < 2) {
            // console.log(`Skipping header line ${index}: ${line}`);
            return;
        }
        if (line === '' && !seenFirstBlank) {
            seenFirstBlank = true;
            inStartEndSection = true;
            // console.log("Entering start/end section at index", index);
            return;
        }
        if (line === '' && inStartEndSection && seenFirstBlank) {
            inStartEndSection = false;
            // console.log("Entering detailed table section at index", index);
            return;
        }
        if (inStartEndSection) {
            const burst = line.split(',').map(num => parseInt(num.trim(), 10));
            if (burst.length === 3 && !isNaN(burst[0]) && !isNaN(burst[1]) && !isNaN(burst[2])) {
                output1.push({
                    pid: burst[0],
                    start: burst[1],
                    end: burst[2],
                    burst: burst[2] - burst[1],
                });
                // console.log(`Parsed start/end: ${line} ->`, output1[output1.length - 1]);
            } else {
                // console.log(`Failed to parse start/end line: ${line}`);
            }
        } else if (index > 2 && !line.startsWith('Type') && !line.startsWith('Number')) {
            const parts = line.split(',').map(str => str.trim());
            if (parts.length >= 11) {
                output2.push({
                    time: parseInt(parts[0], 10),
                    p1RemainingBurst: parts[1],
                    p2RemainingBurst: parts[2],
                    p3RemainingBurst: parts[3],
                    p4RemainingBurst: parts[4],
                    cpu: parts[5],
                    p1WaitingTime: parts[6],
                    p2WaitingTime: parts[7],
                    p3WaitingTime: parts[8],
                    p4WaitingTime: parts[9],
                    queue: parts[10] || '',
                });
                // console.log(`Parsed detailed table: ${line} ->`, output2[output2.length - 1]);
            } else {
                // console.log(`Skipped detailed table line (insufficient parts): ${line}`);
            }
        }
    });

    // console.log("Parsed output1:", output1);
    // console.log("Parsed output2:", output2);
    return section === 1 ? output1 : output2;
}