import { fetchIO, fetchPHP } from "../api.js";

let flagFileUpdated = false;
//First In First Out

// load in data from input and output files
// returns an object with parsed input and output data
let data = {}

export async function loadData() {
    flagFileUpdated = await fetchPHP(0, mid);

    if (!flagFileUpdated) {
        console.log("Flag file not updated");
        return false;
    }

    resetFlagFile();

    const IOResponse = await fetchIO(mid);
    if (IOResponse.error) {
        console.log(IOResponse.error)
        return false;
    }

    data.input = parseInputDataFile(IOResponse.input);
    console.log("Parsed input");

    data.output = parseOutputDataFile(IOResponse.output);
    console.log("Parsed output");

    return data;
}

async function resetFlagFile() {
    flagFileUpdated = await fetchPHP(1, mid);
}

function parseInputDataFile(text) {
    let allText = text;

    let input = []; // array of input data
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
    // Log the results
    console.log("First character:", firstChar);
    console.log("Array of arrays:", result, result.length);
    let frameCount = parseInt(firstChar);

    for (let i = 0; i <= data.input.length; i++) { //start at i = 0 because First value is no longer at # of frames
        // split on pound, essentially copying outpput array, splitting on empty string and creating seperate output arrays for each line so for exmaple [6,3,1,6,1,3,2,+] on line 6 in out.022.dat becomes its onr array
        console.log("line elements " + result);
        if (result.result - 1 < (frameCount * 2 + 2)) { // lines contains all elements of frame + used-in vals for all frames + cur page + faulted boolean
            console.log(`the line "${result}" does not contain ${frameCount * 2 + 2} necessary values`);
        } else {
            let slot = {}
            slot.page = (parseInt(result[i][0]));//getting appropiate slots // [7,0,1,2,0,3,0,4,2,3,0,3,2,1,2,0,1,7,0,1
            // console.log("first " +line[i-1][0]);
            //this  was changed from lines[0] 
            // frameCount+1 because faulted comes after list of frames, like [page, frame1, frame2, ..., faulted]
            slot.faulted = (parseInt(result[i][frameCount + 1]) == 1) ? true : false; //0 = no fault, 1 = fault,
            console.log(slot.faulted);
            slot.frames = []
            for (let k = 1; k <= frameCount; k++) {
                slot.frames.push(result[i][k]);
                //console.log("slots " + slot.frames);
            }
            //removed because the meedin values are not needed
            slot.neededIn = []
            for (let k = frameCount + 2; k < result.length; k++) {
                slot.neededIn.push(result[i][k]);
                console.log(slot.neededIn);

            }
            output.push(slot);
            console.log("output " + slot);
        }
    }

    return output;
}

