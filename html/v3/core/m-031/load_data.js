import { fetchIO, fetchPHP } from "../api.js";
// 031 File Allocation Contiguous
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
}

function parseInputDataFile(text) {
    // break data into lines
    let allText = text;
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

function parseOutputDataFile(text) {
    // break data into lines
    let allText = text;
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
