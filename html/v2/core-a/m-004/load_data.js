import { fetchIO, fetchPHP } from "../api.js";
// This file handles the loading of data from the server 
// CPU Scheduling

let flagFileUpdated = false;

// load in data from input and output files
// returns true if successful, false otherwise
export async function loadData() {
    flagFileUpdated = await fetchPHP(0, mid);
    if (!flagFileUpdated) {
        console.log("Flag file not updated");
        return false;
    }

    resetFlagFile();

    const IOResponse = await fetchIO(mid);
    if (IOResponse.error){
        console.log(IOResponse.error);
        return false;
    }

    let data = {};
    data.input = parseInputDataFile(IOResponse.input);
    data.output = parseOutputDataFile(IOResponse.output, 1);
    data.extraOutput = parseOutputDataFile(IOResponse.output, 2);
    return data;
}

async function resetFlagFile() {
    flagFileUpdated = await fetchPHP(1, mid);
}

function parseInputDataFile(text) {
    let inputData = [];
    let allText = text;

    // for loop, on each newline
    allText.split('\n').forEach((line, i) => {
        // parse line, convert to nums
        let process = line.split('//')[0].split(' ').map(Number);
        if (process.length == 4)
            inputData.push({
                pid: process[0],
                arrival: process[1],
                burst: process[2],
                priority: process[3],
            });
    });
    return inputData;
}

function parseOutputDataFile(text, x) {
    let output1 = [];
    let output2 = [];
    let allText = text;
    let result = allText.split(/\r?\n/).filter(string => string);
    
    result.forEach((line) => {
        //breaks from loop when initial output section is parsed / when there is an empty line
        if(line == ''){
            return false;
        }
        // parse line, convert to nums
        let burst = line.split(',').map(Number);
        if (burst.length == 3)
            // [pid, start time, end time, burst time]
            output1.push({
                pid: burst[0],
                start: burst[1],
                end: burst[2],
                burst: burst[2] - burst[1],
            });
    });

    result.forEach((line) => {
        //parses the remainder of the file after the first section is done 
        if(result.indexOf(line) >= output1.length + 2){
            // parse line, convert to nums
            let burst = line.split(',').map(String);
            if (burst.length > 0)
                output2.push({
                    time: burst[0],
                    p1RemaingBurst: burst[1],
                    p2RemaingBurst: burst[2],
                    p3RemaingBurst: burst[3],
                    p4RemaingBurst: burst[4],
                    cpu: burst[5],
                    p1WaitingTime: burst[6],
                    p2WaitingTime: burst[7],
                    p3WaitingTime: burst[8],
                    p4WaitingTime: burst[9],
                    queue: burst[10],
                });
        }
        
    });
    //control what output section you are getting 
    switch(x){
        case 1:
            return output1;
        case 2:
            return output2;
    }
}