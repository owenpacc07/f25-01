import { fetchIO, fetchPHP } from "../api.js";

// 033 File Allocation Linked
// load_data.js

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
  if (IOResponse.error){
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
  flagFileUpdated = false;
}

function parseInputDataFile(text) {
  // break data into lines
  let allText = text;
  let lines = allText.split("\n");

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
    if (index >= 2 && line.length > 0) {
      filedata = line.split(",");
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

function parseOutputDataFile(text) {
  // break data into lines
  let allText = text;
  let lines = allText.split("\n");

  //This had to be added, because the last line of the file may contain a newline character, which would cause an extra line to be added to the array
  if (lines[lines.length - 1] == "") {
    lines.pop();
  }

  //extract max from file. This is the maximum number of slots
  const max = Number(lines.shift());

  //This will be the output data for displaying the linking portion on the right side of the page.
  let outputdata = [];

  //This creates a 2d array of the output data
  lines.forEach(function (line, index) {
    //create array for each line
    outputdata.push(line.split(",").map(Number));
  });

  //fill array of max size with 0s
  let slots = new Array(max).fill("0");

  //This loops through the output data and fills the slots array with the correct values, based on the output data
  outputdata.forEach(function (line, index) {
    const id = line[0];
    line.forEach(function (slot, index) {
      if (index > 0) {
        //ignores first element, which is the id
        slots[slot - 1] = String(id);
      }
    });
  });

  // return object
  return {
    slots: slots,
    outputdata: outputdata,
  };
}
