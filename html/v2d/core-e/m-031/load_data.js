// load_data.js
import { fetchIO } from "../api.js";

let flagFileUpdated = false;

// Load in data from input and output files
export async function loadData(user_id, experiment_id, family_id, mechanism_id) {
    // Send POST request to get-io-experiment.php
    const IOResponse = await fetchIO(user_id, experiment_id, family_id, mechanism_id);
    console.log("Raw data from get-io-experiment.php:", IOResponse);
    if (IOResponse.error) {
        console.log(IOResponse.error);
        return false;
    }

    let data = {};
    data.input = parseInputDataFile(IOResponse.input);
    data.output = parseOutputDataFile(IOResponse.output);
    console.log("Parsed input:", data.input);
    console.log("Parsed output:", data.output);
    return data;
}

async function resetFlagFile() {
    flagFileUpdated = await fetchPHP(1, "file");
}

function parseInputDataFile(text) {
    let allText = text.trim();
    let lines = allText.split('\n');

    let slots = [];
    let files = [];
    let slotcount = 0;
    let filecount = 0;
    let filedata = [];

    lines.forEach((line, index) => {
        if (index === 0) {
            slotcount = parseInt(line);
        } else if (index === 1) {
            filecount = parseInt(line);
        } else if (line) { // Skip empty lines
            filedata = line.split(',');
            files.push([filedata[0], filedata[1], filedata[2]]);
        }
    });

    return {
        slots: slots,
        files: files,
        slotcount: slotcount,
        filecount: filecount,
        filedata: filedata,
    };
}

function parseOutputDataFile(text) {
    let allText = text.trim();
    let lines = allText.split('\n');

    let slots = [];

    lines.forEach((line, index) => {
        if (index === 0 && line) { // Only process first non-empty line
            slots = line.split(',');
        }
    });

    return {
        slots: slots,
    };
}