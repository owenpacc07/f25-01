/*
Contributor Spring 2023 - Dakota Marino
*/

import { fetchIO, fetchPHP } from "../api.js";

// mid is a variable declared in index.php script tag and is accessible here

//instance variables
var head = 0;
let flagFileUpdated = false;
let inputData = [];
let outputData = [];

// implemented new api for reading input/output
async function readTextFile(type) {
  // ensure the flag file is updated before reading IO
  const readFlagFile = await fetchPHP(0, mid);
  if (!readFlagFile) {
    console.log("Flag file not updated");
    return [];
  }
  resetFlagFile();

  // fetch the IO blob
  const response = await fetchIO(mid);

  // choose the correct text blob
  let allText = null;
  if (type === "input") allText = response.input;
  else if (type === "output") allText = response.output;
  else {
    console.warn("readTextFile called with unknown type:", type);
    return [];
  }

  if (!allText || typeof allText !== "string") {
    console.warn("No text returned for", type, response);
    return [];
  }

  const lines = allText.split('\n').map(l => l.trim()).filter(l => l.length > 0);
  let values = [];

  lines.forEach((line, idx) => {
    const nums = line.match(/-?\d+/g);
    if (!nums) return;
    const parsed = nums.map(Number).filter(n => !Number.isNaN(n));
    if (idx === 1 && parsed.length > 0) head = parsed[0];
    if (idx >= 2) values.push(...parsed);
  });

  if (type === "input") inputData = [...values];
  if (type === "output") outputData = [...values];

  console.log(`readTextFile(${type}) -> head:`, head, "values:", values);
  return values; // ? Return parsed values directly
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