import { fetchIO, fetchPHP } from "../api.js";

// This file handles the loading of data from the server 

let flagFileUpdated = false;
// Input and output file paths
const input_file_path = `${httpcore_IO}/m-${mid}/in-${mid}.dat`;
const output_file_path = `${httpcore_IO}/m-${mid}/out-${mid}.dat`;

let data = {}

// load in data from input and output files
// returns an object with parsed input and output data
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

    data.input = parseInputDataFile(IOResponse.input);
    data.output = parseOutputDataFile(IOResponse.output);
    return data;
}

async function resetFlagFile() {
    flagFileUpdated = await fetchPHP(1, mid);
}

function parseInputDataFile(text) {
    let allText = text;
    let input = []; // array of input data
    // input file vals are comma seperated
    allText.split(',').forEach((val) => {
        let page = parseInt(val);
        if (page >= 0) { // make sure page is valid
            input.push(page);
        }
    });
    return input;
}

function parseOutputDataFile(text) {
    let allText = text;
    let output = [];
    //let allval = HardCodedOutput; 
    // decosntruciton of the outputfile array into an array of arrays by Extract the first character and split the rest of the input on newline
    const [firstChar, ...lines] = allText.trim().split('\n'); 
    // Split each result on comma and remove any empty values
    const result = lines.map(lines => lines.split(',').filter(Boolean));
    // frame count is first value in output file/arrays
    let frameCount = parseInt(firstChar); // # of frames allocated
    for(let i = 0; i <= data.input.length; i++) { //start at i = 0 because First value is no longer at # of frames
        //let result = lines[i].split(',');
        //result becomes 2d array of output file
        console.log("result elements " + result);
        if (result.length-1 < (frameCount*2+2)) { // needed to make sure there is an appropiate amount of elements result contains all elements of frame + used-in vals for all frames + cur page + faulted boolean, 
            //result.lnegth -1 because result contains the # of faults, which is the last element which isnt needed for the
            console.log(`the result "${result}" does not contain ${frameCount*2+2} necessary values`);
        } else {
            let slot = {}
            // console.log("result slot " + parseInt(result[i][0]));
            slot.page =(parseInt(result[i][0]));//getting appropiate slots // [3, 1, 2, 1, 6, 5, 1, 3]
            // console.log("pages " + result[i][0]);
            //this was changed from lines[0] 
            // frameCount+1 because faulted comes after list of frames, like [page, frame1, frame2, ..., faulted]
            slot.faulted = (parseInt(result[i][frameCount+1]) == 1) ? true : false; //0 = no fault, 1 = fault,
            // console.log(slot.faulted);
            slot.frames = []
            for (let k = 1; k <= frameCount; k++) {
                slot.frames.push(result[i][k]);
                // console.log("result slots ", result[i][k]);
                // console.log("slots",slot.frames);
            }
            slot.timesUsed = []
            for (let k = frameCount+2; k < result.length; k++) {
                slot.timesUsed.push(result[i][k]);
            }
            output.push(slot);
        }
    }
    return output;
}
