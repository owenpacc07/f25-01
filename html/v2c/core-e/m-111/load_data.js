// This file handles the loading of data from the server 
// TEMPLATE

let flagFileUpdated = false;

// Input and output file paths
const input_file_path = `https://cs.newpaltz.edu/p/f22-02/files/core/m-${mid}/in-${mid}.dat`;
const output_file_path = `https://cs.newpaltz.edu/p/f22-02/files/core/m-${mid}/out-${mid}.dat`;


// load in data from input and output files
// returns an object with parsed input and output data
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
    await fetch(`https://cs.newpaltz.edu/p/f22-02/v2/core/m-${mid}/manage-flag-file.php`, {
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
    let allText = loadDataFile(input_file_path);

    // display input file on index.php 
    const ioData = document.getElementById('ioData');
    const fileContents = document.createElement('div');
    fileContents.append("INPUT: " + allText)
    ioData.append(fileContents);
    ioData.append(document.createElement('br'));
    
    // IMPLEMENT INPUT FILE PARSING

    return;
}

function parseOutputDataFile() {
    let allText = loadDataFile(output_file_path);

    // display output file on index.php 
    const ioData = document.getElementById('ioData');
    const fileContents = document.createElement('div');
    fileContents.append("OUTPUT: " + allText)
    ioData.append(fileContents);
    
    // IMPLEMENT OUTPUT FILE PARSING

    return;
}
