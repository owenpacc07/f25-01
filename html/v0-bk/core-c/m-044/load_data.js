/*
Contributor Spring 2023 - Dakota Marino
*/

import { fetchIO, fetchPHP } from "../api.js";

// mid is a variable declared in index.php script tag and is accessible here

//instance variables
const mid = '044';
const compare = "disk"

var head = 0;
let flagFileUpdated = false;
let inputData = [];
let outputData = [];

// implemented new api for reading input/output
async function readTextFile(type){
    const readFlagFile = await fetchPHP(0, mid);
    if (!readFlagFile) {
        console.log("Flag file not updated");
        return false;
      }
    resetFlagFile();

    const response = await fetchIO(compare, mid);
    let values = [];
    var counter = 0;
    let allText = null;
    if (type == "input"){
        allText = response.input;
    }
    else if(type == "output"){
        allText = response.output;
    }
    allText.split('\n').filter(string => string).forEach(function(line) {
        //split by space
        line.split(' ').filter(string => string).forEach(function(number) {
            if (counter === 1) {
                head = number;
            }
            if (counter > 1) {
                console.log(number);
                values.push(number);
            }
        });
        counter++;
    });
    console.log(allText)

    switch (type){
        case "input":
            inputData = values;
        case "output":
            outputData = values;
        default:
            console.log("Sorry, no type was specified.");
    }
}

async function resetFlagFile() {
    flagFileUpdated = await fetchPHP(1, mid);
    flagFileUpdated = false;
  }

/**
 * async function readTextFile(type) {
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
*/

export {readTextFile, head, inputData, outputData,};
