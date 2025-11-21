// 033 File Allocation Linked
// Advanced Mode Version
// load_data.js

let flagFileUpdated = false;

// Input and output file paths
const input_file_path = `../../../files/core-a/m-${mid}/in-${mid}.dat`;
const output_file_path = `../../../files/core-a/m-${mid}/out-${mid}.dat`;


// load in data from input and output files
export async function loadData() {
    
    await fetchPHP(0);
    if (!flagFileUpdated) {
        console.log("Flag file not updated");
        return false;
    }
    

    let data = {};
    resetFlagFile();
    data.input = parseInputDataFile();
    data.output = parseOutputDataFile();
    return data;
}

// calls php file which manages flag file
// type: 0 = read value of flag file, 1 = reset flag file to 0
async function fetchPHP(type) {
    await fetch(`../../core-a/m-${mid}/manage-flag-file.php`, {
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
                console.log("Flag file updated");
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
    // break data into lines
    let allText = loadDataFile(input_file_path);
    let lines = allText.split('\n');

    let slots = [];
    let files = [];
    let slotcount = 0;
    let filecount = 0;
    let filedata = [];

    let i = 0;
    lines.forEach(function (line, index) {
        i++;
        //console.log(line + ' ' + line.length);
        if (index == 0) {
            slotcount = parseInt(line);
        }
        if (index == 1) {
            filecount = parseInt(line);
        }
        if (index >= 2) {
            filedata = line.split(',');
            files.push([filedata[0], filedata[1], filedata[2]]);
        }
    }, this);

    // return object
    return {
        slots: slots,
        files: files,
        slotcount: slotcount,
        filecount: filecount,
        filedata: filedata,
    };
}

function parseOutputDataFile() {
    // break data into lines
    let allText = loadDataFile(output_file_path);
    let lines = allText.split('\n');

    let slots = [];

    let i = 0;
    lines.forEach(function (line, index) {
        i++;
        //console.log(line + ' ' + line.length);
        if (index == 0)
            slots = line.split(',');
    }, this);

    // return object
    return {
        slots: slots,
    };
}
