import { fetchIO, fetchPHP } from "../api.js";

// Memory Allocation Worst Fit Advanced

let flagFileUpdated = false;

// load in data from input and output files
export async function loadData() {

    
    flagFileUpdated = await fetchPHP(0, mid);
    if (!flagFileUpdated) {
        console.log("Flag file not updated");
        return false;
    }
    resetFlagFile();
    
    const IOResponse = await fetchIO(mid);
    if(IOResponse.error){
        console.log(IOResponse.error);
        return false;
    }

    let data = {};
    data.input = parseInputDataFile(IOResponse.input);
    data.output = parseOutputDataFile(IOResponse.output);
    return data;
}

async function resetFlagFile() {
    flagFileUpdated = await fetchPHP(1, mid);
}

function parseInputDataFile(text) {
    // break data into lines
    let allText = text;

    let lines = allText.split('\n');
    // create array of slot objects
    let numMemSlots = parseInt(lines[0]);
    let memSlots = [];
    for (let i = 1; i <= numMemSlots; i++) {
        let memSlot = lines[i].split(' ').map(Number);
        if (memSlot.length == 2)
            memSlots.push(
                {
                    start: memSlot[0],
                    end: memSlot[1],
                }
            );
    }
    // create array of process objects
    let pIndex = numMemSlots + 1; // index of process count in input file
    let numProcesses = parseInt(lines[pIndex]);
    let processes = [];
    for (let i = pIndex+1; i <= (pIndex+numProcesses); i++) {
        let process = lines[i].split(' ').map(Number);
        if (process.length == 2)
            processes.push(
                {
                    id: process[0],
                    size: process[1],
                }
            );
    }
    // return object
    return {
        memSlots: memSlots,
        processes: processes,
    };
}

function parseOutputDataFile(text) {
    let allText = text;
    // create array of process objects
    let processSlots = []
    allText.split('\n').forEach((line) => {
        let processSlot = line.split(' ').map(Number);
        if (processSlot.length == 3) {
            processSlots.push({
                start: processSlot[0],
                end: processSlot[1],
                id: processSlot[2],
            });
        }
    });
    return processSlots;
}
