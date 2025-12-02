//  033 File Allocation Indexed
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

var fit = 2;

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
var outputdata = [];

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
    outputdata = output.outputdata;
    files = input.files;
    slotcount = input.slotcount;
    filecount = input.filecount;
    filedata = input.filedata;
    updateFileSlots();
  }
});

$(document).ready(function () {
  // saves a copy of the default html
  defaultTable = $("#animarea").html();

  memtblpos = $("#disktable").position();

  // displays the whole directory section at once
  displayFullDirectory(files);

  // the button bindings
  $("#next").click(function () {
    if (animLock) return;
    animSpeed = 1000;
    paused = false;
    animate(0);
    paused = true;
  });
  // this doesn't work yet
  $("#back").click(function () {
    if (animLock) return;
    Undo();
  });
  //
  // play button will change into a pause button when clicked
  $("#play").click(function () {
    paused = !paused;
    if (paused == false) {
      document.getElementById("play").value = "Pause";
      if (animLock) return;
      animSpeed = 1000;
      animate(0);
    } else {
      document.getElementById("play").value = "Play";
    }
    //console.log(paused);
  });
  // skips to the end of the animation
  $("#end").click(function () {
    if (animLock) return;
    paused = false;
    animSpeed = 100;
    animate(0);
  });
  // this works
  $("#reset").click(function () {
    reset();
  });
});

// resets the program
//
function reset() {
  // resets the animation area
  $("#animarea").empty().append(defaultTable);
  // resets the buttons
  document.getElementById("play").value = "Play";
  document.getElementById("play").removeAttribute("disabled");
  document.getElementById("back").removeAttribute("disabled");
  document.getElementById("next").removeAttribute("disabled");
  document.getElementById("end").removeAttribute("disabled");
  // displays the whole directory section at once
  displayFullDirectory(files);
  // resets the variables
  paused = true;
  currStep = 0;
}

// runs when the program finishes successfully
//
function completed() {
  // updates play button text
  document.getElementById("play").value = "Finished";
  // disables the buttons
  document.getElementById("play").setAttribute("disabled", "disabled");
  document.getElementById("next").setAttribute("disabled", "disabled");
  document.getElementById("back").setAttribute("disabled", "disabled");
  document.getElementById("end").setAttribute("disabled", "disabled");
}

function updateFileSlots() {
  var tablehtml = "";
  var odd = slotcount % 4;
  var loop = slotcount / 4;

  for (let i = 0; i <= loop; i++) {
    tablehtml += "<tr>\r\n";
    for (let i2 = 1; i * 4 + i2 <= slotcount; i2++) {
      tablehtml += '<td id="' + (i * 4 + i2) + '">' + (i * 4 + i2) + "</td>";
      if (i2 >= 4) break;
    }
    tablehtml += "\r\n</tr>";
  }
  $("#disktable").html(tablehtml);
}

// updates the text in the directory table
function updateDirectory(mfile) {
  var fileName = mfile[1];
  var fileStart = parseInt(slots.indexOf(mfile[0])) + 1;
  var fileLength = parseInt(mfile[2]);
  $("#dir tr:last").after(
    "<tr><td>" +
      fileName +
      "</td>" +
      "<td>" +
      fileStart +
      "</td>" +
      "<td>" +
      fileLength +
      "</tr>",
  );
  //$('#step').text('Step: ' + currStep);
}

// displays the entire directory table at once
function displayFullDirectory(mfile) {
  var fileName;
  var fileStart;
  var fileLength;
  for (var i = 0; i < mfile.length; i++) {
    fileName = mfile[i][1];
    outputdata.forEach(function (fileinfo, index) {
      if (mfile[i][0] == fileinfo[0]) {
        fileStart = parseInt(fileinfo[1]);
      }
    });
    fileLength = parseInt(mfile[i][2]);
    $("#dir tr:last").after(
      "<tr><td>" +
        fileName +
        "</td>" +
        "<td>" +
        fileStart +
        "</td>" +
        "<td>" +
        fileLength +
        "</tr>",
    );
    //$('#step').text('Step: ' + currStep);
  }
}

// creates a random nice color
function getRandomColor() {
  var colorVal = currStep / files.length;
  var color = "hsl(" + 360 * colorVal + "," + 100 + "%," + 75 + "%)";
  return color;
}

function animate(type) {
  //console.log("Step: " + currStep);
  if (animLock) return;
  if (paused) return;
  if (currStep >= files.length) return;

  $("#step").html("Step: " + (currStep + 1));
  //updateDirectory(files[currStep]);

  var blocks = [];

  // makes the block display
  files[currStep].push(getRandomColor());

  // the number of blocks to display on a line
  const numberOfBlocks = parseInt(files[currStep][2]);
  const blockid = files[currStep][0];
  let data;
  for (let j = 0; j < outputdata.length; j++) {
    //This finds teh data for the current block
    if (outputdata[j][0] == blockid) {
      //if outputdata's id is the same as the current block's id
      data = outputdata[j];
    }
  }
  
  // creates each file in the animarea
  for (let i = 0; i < numberOfBlocks + 1; i++) {
    let classes = "";
    let border ="1px";
    //creates circles for table side
    for (let j = 0; j < outputdata.length; j++) {
      //This finds teh data for the current block
      if (outputdata[j][1] == data[i + 1]) {
        //if outputdata's id is the same as the current block's id
        classes += "circle";
        border ="0px";
      } else
      {
        border ="1px";
      }
    }

    blocks +=
      "<td class='" + classes + "' "+
      "id=" +
      (files[currStep][1] + (i + 1)) +
      ' style="width: 30px; height: 30px; background-color:' +
      files[currStep][3] +
      ';border: '+border+'; solid;text-align: center; vertical-align: middle;">' +
      data[i + 1] + //This is the block number
      "</td>";

    // if to display the arrow images
    if (i < numberOfBlocks) {
      blocks += '<td id=arrow><img src="arrow.png" alt="Arrow"><td>';
    }
  }

  $("#filearea").append(
    '<span id="step" style="display: inline; float: left; margin:' +
      (190 + 70 * currStep) +
      'px 0px 0px -280px;">' +
      files[currStep][1] +
      "</span>\r\n<table id='fileblock' style=\"border: 0px solid;display: inline; float: left; margin: " +
      (220 + 70 * currStep) +
      'px 0px 0px -280px;">\r\n<tr>\r\n' +
      blocks +
      "\r\n</tr>\r\n</table>",
  );

  for (let i = 0; i <= files[currStep][2]; i++) {
    //console.log("Inds: " + getInds(files[currStep][0], i));
    moveProcToMem(
      files[currStep][1] + (i + 1),
      getInds(files[currStep][0], i) + 1,
      i,
    );
  }

  // increments the current step
  currStep++;

  // checks if the function is done
  if (files.length == currStep) {
    completed();
  }
}

function getInds(id, num) {
  var count = 0;
  //console.log('id: ' + id + ' num: ' + num);
  if (num == 0) return slots.indexOf(id);

  for (let index = 0; index < slots.length; index++) {
    if (slots[index] == id) count++;
    if (count > num) return index;
  }
}

function moveProcToMem(proc, mem, rem, chck) {
  //console.log('proc: ' + proc + ' mem: ' + mem);
  animLock = true;
  var col = getRandomColor();
  if (fit == 3 && rem == 0) {
    $("#" + proc).css({ backgroundColor: col });
  }
  //$('#' + proc).css({ backgroundColor: getRandomColor() });
  var clonedProc = $("#" + proc).clone();

  let classes = "";
  for (let j = 0; j < outputdata.length; j++) {
    //This finds teh data for the current block
    if (outputdata[j][1] == mem) {
      //if outputdata's id is the same as the current block's id
      classes += "circle ";
    }
  }
  var cloned =
    '<div class="' +
    classes +
    '" id="' +
    (proc + "cloned") +
    '" style="display: inline-block;position: absolute;height: 30px; width:30px;border: 1px solid;"></div>';
  //$('#animarea').append(cloned);
  $("#" + proc).append(cloned);
  animList.push({
    org: $("#" + proc),
    proc: clonedProc,
    mem: mem,
    top: clonedProc.position().top,
  });
  $("#" + (proc + "cloned")).css("background-color", files[currStep][3]);
  if (fit == 3 && rem == 0) {
    $("#" + (proc + "cloned")).css("background-color", col);
  } else {
    $("#" + (proc + "cloned")).css("background-color", files[currStep][3]);
  }
  $("#" + (proc + "cloned")).animate(
    {
      left: memtblpos.left + $("#" + mem).position().left + 100,
      top: memtblpos.top + $("#" + mem).position().top,
      width: 50,
      height: 50,
    },
    animSpeed,
    function () {
      animLock = false;
      if (rem > 0) return;
      //$('#' + mem + 'slot').text('' + rem);
      if (currStep > filecount) return;
      animate(0);
    },
  );
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
    $("#step").html("Step: " + currStep);
  }
  animLock = false;
}
