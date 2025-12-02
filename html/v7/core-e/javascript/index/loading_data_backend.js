
import { readInputDataFile, readOutputDataFile } from "./main.js";

// JavaScript for how the front-end (webpage) responds when input/output files are being updated on the backend

var flagFileUpdated = false;
var interval;

// address to the other CPU Scheduling pages
let first_come_first_serve = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-001.php';
let shortest_job_first = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-002.php';
let priority_high_to_low = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-003.php';
let priority_low_to_high = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-004.php';
let round_robin = 'https://cs.newpaltz.edu/p/f22-02/v2/p3-os-main/core/m-005.php';

// Input and output file paths
let input_file_path = 'https://cs.newpaltz.edu/p/f22-02/files/p3/in.dat';
let output_file_path = 'https://cs.newpaltz.edu/p/f22-02/files/p3/out.dat';


function toggleOverlay() {
    document.getElementById("overlay").style.display = "block";
}

function loadingData(schedulerType, address) {
    interval = setInterval(checkFlagFile, 500, schedulerType, address);
}

function checkFlagFile(schedulerType, address) {
    // data = 0 indicates in the php to check flag
    fetchPHP(0);

    console.log(flagFileUpdated);
    console.log("\n\n\n");

    if (flagFileUpdated) {
        console.log("Clear interval");
        clearInterval(interval);
        resetFlagFile();
        readInputDataFile(input_file_path);
        readOutputDataFile(output_file_path);

        console.log("FILES UPDATED");

        // timer for last tasks
        setTimeout(() => {
            // disables overlay
            document.getElementById("overlay").style.display = "none";
            location.replace(address);

        }, 3000);
        
    }
}

function resetFlagFile() {
    let response = "";
    // data = 1 indicates in the php to reset flag
    response = fetchPHP(1);
    flagFileUpdated = false;

    // halts execution until response is complete
    setTimeout(() => console.log("Finished resetting flag file -- done"), 2000);

}

function fetchPHP(type) {
    // Calls a php file that checks the value inside the flag file or overwrites the value
    fetch('manage-flag-file.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: "text=" + type
      })
      .then(response => response.text())
      .then(data => updateVariable(data))
}

function updateVariable(value) {
    console.log("VALUE: " + value);
    if (value == 0)
        flagFileUpdated = false;
    else if (value == 1)
        flagFileUpdated = true;
}

export { toggleOverlay, loadingData };