// This file handles the loading of data from the server 
// CPU Scheduling

let flagFileUpdated = false;

// Input and output file paths
const input_file_path = `${httpcore_a_IO}/m-${mid}/in-${mid}.dat`;
const output_file_path = `${httpcore_a_IO}/m-${mid}/out-${mid}.dat`;


// load in data from input and output files
// returns true if successful, false otherwise
export async function loadData() {
    await fetchPHP(0);
    if (!flagFileUpdated) {
        console.log("Flag file not updated");
        return false;
    }
    let data = {};
    resetFlagFile();
    data.input = parseInputDataFile();
    data.output = parseOutputDataFile(1);
    data.extraOutput = parseOutputDataFile(2);
    return data;
}

// calls php file which manages flag file
// type: 0 = read value of flag file, 1 = reset flag file to 0
async function fetchPHP(type) {
    await fetch(`${httpcore_a}/m-${mid}/manage-flag-file.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        // enter mid and flag file action 
        body: JSON.stringify({
            'type': type,
        })
    })
        .then(response => response.text())
        .then((data) => {
            // change flagFileUpdated based on value of flag file
            if (data == 0)
                flagFileUpdated = false;
            else if (data == 1)
                flagFileUpdated = true;
            else { // write message to console 
                console.log(data);
            }
        })
}

async function resetFlagFile() {
    await fetchPHP(1);
    flagFileUpdated = false;
}

// Makes HTTP request to get data from server file
function loadDataFile(file) {
    let rawFile = new XMLHttpRequest(); // <-- only way to read in a file in js
    rawFile.open("GET", file, false); // not async, so send() will wait for response
    rawFile.onreadystatechange = function () {
        // check status
        if (rawFile.readyState !== 4) {
            console.log("readyState: " + rawFile.readyState);
            return;
        }
        if (rawFile.status != 200 && rawFile.status != 0) {
            console.log("status code = " + rawFile.status);
            console.log(typeof rawFile.status);
            console.log(rawFile.status == 200);
            return;
        }
        console.log("File " + file.split('/').pop() + " loaded");
    }
    rawFile.send(null); // send request, (should) change readyState to 4
    return rawFile.responseText;
}

function parseInputDataFile() {
    let inputData = [];
    let allText = loadDataFile(input_file_path);
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

//NEW way of parsing out file
function parseOutputDataFile(x) {
    let output1 = [];
    let output2 = [];
    let allText = loadDataFile(output_file_path);
    let result = allText.split(/\r?\n/);
    
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
        if(line !== '' && result.indexOf(line) >= output1.length){
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
/*
function parseOutputDataFile() {
    let outputData = [];
    let allText = loadDataFile(output_file_path);
    let ganttVals = allText.split('\n').slice(3,7); // vals for gantt chart
    ganttVals.forEach((line) => {
        // parse line, convert to nums
        let burst = line.split(',').map(Number);
        if (burst.length == 3)
            // [pid, start time, end time]
            outputData.push({
                pid: burst[0],
                start: burst[1],
                end: burst[2],
                burst: burst[2] - burst[1],
            });
    });
    return outputData;
}
*/