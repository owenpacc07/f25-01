//  031 File Allocation Contiguous
// Advanced Mode Version
//  SUNY New Paltz 2023
//  Contributors: James O'Sullivan
//

import { loadData } from "./load_data.js";

//Set "loading data" overlay while data is loading
let overlay = document.getElementById("overlay");
overlay.style.display = "block";

let input, output;

// for the animation
var animList = [];

var paused = true;

var animSpeed = 1000;
var animLock = false;

var memtblpos;

// stores the current step
var currStep = 0;

// saves a copy of the visuals for reset function
var defaultTable;

// stores the values for the function
var slots = [];
var files = [];
var slotcount = 0;
var filecount = 0;
var filedata = [];

//Load call load_data.js and load input / output data
await loadData().then((data) => {
    if (!data) {
        document.getElementById("text").innerText = "Could not load output data";
        throw new Error("Could not load output data");
    } else {
        overlay.style.display = "none";
        input = data.input;
        output = data.output;
        // sets up the values
        slots = output.slots;

        files = input.files;
        slotcount = input.slotcount;
        filecount = input.filecount;
        filedata = input.filedata;
        //console.log(slots);
        updateFileSlots();
    }
});

$(document).ready(function () {

    // saves a copy of the default html
    defaultTable = $('#animarea').html();

    memtblpos = $('#disktable').position();

    // displays the whole directory section at once
    displayFullDirectory(files);

    // the button bindings
    $('#next').click(function () {
        if (animLock) return;
        animSpeed = 1000;
        paused = false;
        animate(0);
        paused = true;
    });
    // this doesn't work yet
    $('#back').click(function () {
        if (animLock) return;
        Undo();
    });
    //
    // play button will change into a pause button when clicked
    $('#play').click(function () {
        play();
    });
    // skips to the end of the animation
    $('#end').click(function () {
        if (animLock) return;
        paused = false;
        animSpeed = 100;
        animate(0);
    });
    $('#reset').click(function () {
        reset();
        //window.location.reload(true);
    });
    // does the same thing as end but faster
    $('#run').click(function () {
        if (animLock) return;
        paused = false;
        animSpeed = 10;
        animate(0);
    });
    // does the same thing as play but.. wait, no, it's exactly the same
    $('#visualize').click(function () {
        play();
    });
});

// resets the program
//
function reset() {
    // resets the animation area
    $('#animarea').empty().append(defaultTable);
    // resets the buttons
    document.getElementById('play').value = 'Play';
    document.getElementById('play').removeAttribute('disabled');
    document.getElementById('back').removeAttribute('disabled');
    document.getElementById('next').removeAttribute('disabled');
    document.getElementById('end').removeAttribute('disabled');
    // displays the whole directory section at once
    displayFullDirectory(files);
    // resets the variables
    paused = true;
    currStep = 0;
}

function play() {
    paused = !paused;
    if (paused == false) {
        document.getElementById('play').value = 'Pause';
        if (animLock) return;
        animSpeed = 1000;
        animate(0);
    } else {
        document.getElementById('play').value = 'Play';
    }
}

// runs when the program finishes successfully
//
function completed() {
    // updates play button text
    document.getElementById('play').value = 'Finished';
    // disables the buttons
    document.getElementById('play').setAttribute('disabled', 'disabled');
    document.getElementById('next').setAttribute('disabled', 'disabled');
    document.getElementById('back').setAttribute('disabled', 'disabled');
    document.getElementById('end').setAttribute('disabled', 'disabled');
}

function updateFileSlots() {
    var tablehtml = '';
    var odd = slotcount % 4;
    var loop = slotcount / 4;

    for (let i = 0; i <= loop; i++) {
        tablehtml += "<tr>\r\n";
        for (let i2 = 1; (i * 4 + i2) <= slotcount; i2++) {
            tablehtml += "<td id=\"" + (i * 4 + i2) + "\">" + (i * 4 + i2) + "<\/td>";
            if (i2 >= 4) break;
        }
        tablehtml += "\r\n<\/tr>";
    }
    $('#disktable').html(tablehtml);
}

// updates the text in the directory table
function updateDirectory(mfile) {
    var fileName = mfile[1];
    var fileStart = parseInt(slots.indexOf(mfile[0]));
    var fileLength = parseInt(mfile[2]);
    var fileEnd = fileStart + fileLength;
    $('#dir tr:last').after('<tr><td>' + fileName + '</td>' + '<td>' + (fileStart + 1) + '</td>' + '<td>' + fileEnd + '</tr>');
    //$('#step').text('Step: ' + currStep);
}

// displays the entire directory table at once
function displayFullDirectory(mfile) {
    var fileName;
    var fileStart;
    var fileLength;
    var fileEnd;
    for (var i = 0; i < mfile.length; i++) {
        fileName = mfile[i][1];
        fileStart = parseInt(slots.indexOf(mfile[i][0]));
        fileLength = parseInt(mfile[i][2]);
        fileEnd = fileStart + fileLength;
        $('#dir tr:last').after('<tr><td>' + fileName + '</td>' + '<td>' + (fileStart + 1) + '</td>' + '<td>' + fileEnd + '</tr>');
        //$('#step').text('Step: ' + currStep);
    }
}


// creates a random nice color
function getRandomColor() {
    var colorVal = currStep / files.length;
    var color = "hsl(" + 360 * colorVal + ',' + (100) + '%,' + (75) + '%)';
    return color;
}

function animate(type) {
    //console.log("Step: " + currStep);
    if (animLock) return;
    if (paused) return;
    if (currStep >= files.length) return;

    $('#step').html("Step: " + (currStep + 1));

    //updateDirectory(files[currStep]);

    var blocks = [];

    // makes the block display
    files[currStep].push(getRandomColor());

    // the number of blocks to display on a line
    var numberOfBlocks = parseInt(files[currStep][2]);

    // creates each block
    for (let i = 0; i < numberOfBlocks; i++) {
        blocks += "<td " + "id=" + (files[currStep][1] + (i + 1)) + " style=\"width: 30px; height: 30px; background-color:" + files[currStep][3] + ";border: 1px solid;\"><\/td>";
    }

    $('#filearea').append("<span id=\"step\" style=\"display: inline; float: left; margin:" + (190 + (70 * currStep)) + "px 0px 0px -280px;\">" + files[currStep][1] + "<\/span>\r\n<table id=\'fileblock\' style=\"border: 0px solid;display: inline; float: left; margin: " + (220 + (70 * currStep)) + "px 0px 0px -280px;\">\r\n<tr>\r\n" + blocks + "\r\n<\/tr>\r\n<\/table>");

    for (let i = 0; i <= (files[currStep][2] - 1); i++) {
        moveProcToMem(files[currStep][1] + (i + 1), parseInt(slots.indexOf(files[currStep][0])) + i + 1, i);
    }

    // increments the current step
    currStep++;

    // checks if the function is done
    if (files.length == currStep) {
        completed();
    }


}

function moveProcToMem(proc, mem, rem, chck) {
    //console.log('proc: ' + proc + ' mem: ' + mem);
    animLock = true;
    //$('#' + proc).css({ backgroundColor: getRandomColor() });
    var clonedProc = $('#' + proc).clone();
    var cloned = "<div id=\"" + (proc + 'cloned') + "\" style=\"display: inline-block;position: absolute;height: 30px; width:30px;border: 1px solid;\"></div>";
    //$('#animarea').append(cloned);
    $('#' + proc).append(cloned);
    animList.push({ org: $('#' + proc), proc: clonedProc, mem: mem, top: clonedProc.position().top });
    $('#' + (proc + 'cloned')).css('background-color', files[currStep][3]);
    //$('#' + (proc + 'cloned')).css('background-color', files[currStep][3]);
    $('#' + (proc + 'cloned')).animate({ left: memtblpos.left + $('#' + mem).position().left + 100, top: memtblpos.top + $('#' + mem).position().top, width: 50, height: 50 }, animSpeed, function () {
        animLock = false;
        if (rem > 0) return;
        //$('#' + mem + 'slot').text('' + rem);
        if (currStep > filecount) return;
        animate(0);
    });
    if (rem > 0) {
        //$('#' + mem).animate({ height: $('#' + mem).height() + 50 });
    }
}

// this works now, undoes the last step if possible
function Undo() {
    animLock = true;
    if (currStep > 0) {
        // counts the step back
        currStep--;
        // removes the block and its corresponding text
        $("#filearea").children().last().remove();
        $("#filearea").children().last().remove();

        // updates the "Step: " display
        $('#step').html("Step: " + (currStep));
    }
    animLock = false;
}