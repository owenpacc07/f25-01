/*
Contributor Spring 2023 - Dakota Marino
*/
//instance variables
var head = 0;
var inputFileLocation = httpcore_a_IO + `/m-${mid}/in-${mid}.dat`;
var outputFileLocation = httpcore_a_IO + `/m-${mid}/out-${mid}.dat`;
let flagFileUpdated = false;
let inputData = [];
let outputData = [];

// load in data from input and output files
// returns true if successful, false otherwise
async function checkFlag() {
    await fetchPHP(0);
    if (!flagFileUpdated) {
        console.log("Flag file not updated");
        return false;
    }
    else {
        return true;
    }
}

// calls php file which manages flag file
// type: 0 = read value of flag file, 1 = reset flag file to 0
async function fetchPHP(type) {
    await fetch(`setFlag.php`, {
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



async function readTextFile(type) {
    var file;
    if (type === "input") {
        file = inputFileLocation;
    }
    else if (type === "output") {
        file = outputFileLocation;
    }
    var values = [];          
    console.log(file);
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", file, false);
    rawFile.onreadystatechange = function() {
        if (rawFile.readyState === 4) {
            if (rawFile.status === 0 || (rawFile.status >= 200 && rawFile.status < 400)) {
                var allText = rawFile.responseText;
                var counter = 0;
                console.log(allText);
                //split string by newline
                allText.split('\n').forEach(function(line) {
                    //split by space
                    line.split(' ').forEach(function(number) {
                        if (counter === 1) {
                            head = number;
                        }
                        if (counter > 1) {
                            console.log(number);
                            values.push(number);
                        }
                    });//end ' '
                    counter++;
                });// end /n
            }
        }
    }
    rawFile.send(null);
    console.log(values);
    if (type === "input") {
        inputData = values;
    }
    if (type === "output") {
        //values.pop();
        //values.pop();
        outputData = values;
    }
}

export {readTextFile, head, inputData, outputData, checkFlag, resetFlagFile};