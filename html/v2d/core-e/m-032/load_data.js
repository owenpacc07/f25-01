// load_data.js for m032 (Linked Allocation)
import { fetchIO } from "../api.js";

// Load data using URL parameters
export async function loadData(user_id, experiment_id, family_id, mechanism_id) {
  const IOResponse = await fetchIO(user_id, experiment_id, family_id, mechanism_id);
  console.log("Raw data from get-io-experiment.php:", IOResponse);
  if (IOResponse.error) {
    console.log(IOResponse.error);
    return false;
  }

  let data = {};
  data.input = parseInputDataFile(IOResponse.input);
  data.output = parseOutputDataFile(IOResponse.output);
  console.log("Parsed input:", data.input);
  console.log("Parsed output:", data.output);
  return data;
}

function parseInputDataFile(text) {
  // Break data into lines
  let lines = text.trim().split("\n");
  let slots = [];
  let files = [];
  let slotcount = parseInt(lines[0]);
  let filecount = parseInt(lines[1]);
  let filedata = [];

  for (let i = 2; i < lines.length; i++) {
    if (lines[i].length > 0) {
      filedata = lines[i].split(",");
      files.push([filedata[0], filedata[1], filedata[2]]);
    }
  }

  // Return object
  return {
    slots: slots,
    files: files,
    slotcount: slotcount,
    filecount: filecount,
    filedata: filedata,
  };
}

function parseOutputDataFile(text) {
  // Break data into lines
  let lines = text.trim().split("\n");
  if (lines[lines.length - 1] === "") {
    lines.pop();
  }

  // Extract max from file (maximum number of slots)
  const max = Number(lines.shift());
  let outputdata = lines.map((line) => line.split(",").map(Number));

  // Fill slots array with "0" initially
  let slots = new Array(max).fill("0");

  // Populate slots based on output data
  outputdata.forEach((line) => {
    const id = line[0];
    line.slice(1).forEach((slot) => {
      slots[slot - 1] = String(id);
    });
  });

  // Return object
  return {
    slots: slots,
    outputdata: outputdata,
  };
}