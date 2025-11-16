// Konva Stage
var stage = new Konva.Stage({
  container: "container",
  width: 880,
  height: 470,
});
//Initializing vairables.
//Initializing arrays for different new process creation and process state steps.
var newProcessCreation = [];
var normalProcessStateSteps = [];
var pausedProcessStateSteps = [];
var ioProcessStateSteps = [];

//Initializing index for our array.
var stepIndex = 0;

//Variable to declare users selection from radio button.
var typeOfProcess;

//Create text object.
var textBox = new Konva.Layer();
var text = new Konva.Text({
  x: 15,
  y: 335,
  fontFamily: "Times New Roman",
  fontStyle: "bold",
  fontSize: 15,
  text: "here",
  fill: "black",
  width: 310,
  align: "center",
});
textBox.add(text);
stage.add(textBox);
textBox.hide();

//Create textbox object.
var rectBox = new Konva.Layer();
var rect1 = new Konva.Rect({
  x: 13,
  y: 317,
  width: 313,
  height: 125,
  stroke: "black",
  strokeWidth: 0.5,
  cornerRadius: 10,
});
// add the shape to the layer
rectBox.add(rect1);
stage.add(rectBox);
rectBox.hide();

//Function to change text for each step.
function writeMessage(message) {
  text.text(message);
}
// Shapes for newProcessCreation
//Creating layer for step.
var userLayer = new Konva.Layer();

//Creating image object
var userImage = new Image();
userImage.onload = function () {
  var userIcon = new Konva.Image({
    x: 15,
    y: 150,
    image: userImage,
    width: 75,
    height: 100,
  });
  userIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "User starts a program like “java a.class” in the Operating System's GUI (Graphic User Interface) which is located in the RAM (Main Memory)."
    );
    textBox.show();
  });
  userIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });

  //Adding image and text to layer.
  userLayer.add(userIcon);
};

//Initializing image source.
userImage.src = "./assets/userIcon.jpg";

//---------------------------------------//
var callImage = new Image();
callImage.onload = function () {
  var callIcon = new Konva.Image({
    x: 86,
    y: 71,
    image: callImage,
    width: 165,
    height: 243,
  });
  callIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "User starts a program like “java a.class” in the Operating System's GUI (Graphic User Interface) which is located in the RAM (Main Memory)."
    );
    textBox.show();
  });
  callIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });

  userLayer.add(callIcon);
};

callImage.src = "./assets/callIcon.jpg";

stage.add(userLayer);
//userLayer.hide();
newProcessCreation.push(userLayer);

//---------------------------------------//
var loaderLayer = new Konva.Layer();

var loaderImage = new Image();
loaderImage.onload = function () {
  var loaderIcon = new Konva.Image({
    x: 245,
    y: 137,
    image: loaderImage,
    width: 150,
    height: 110,
  });
  loaderIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "Program is loaded into the loader which is tasked with locating the given program from the hard disk."
    );
    textBox.show();
  });
  loaderIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });

  loaderLayer.add(loaderIcon);
};

loaderImage.src = "./assets/loaderIcon.jpg";

stage.add(loaderLayer);
loaderLayer.hide();
newProcessCreation.push(loaderLayer);

//---------------------------------------//
var diskLayer = new Konva.Layer();

var diskImage = new Image();
diskImage.onload = function () {
  var diskIcon = new Konva.Image({
    x: 393,
    y: 132,
    image: diskImage,
    width: 150,
    height: 120,
  });
  diskIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage("Program is located in the hard disk.");
    textBox.show();
  });
  diskIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });

  diskLayer.add(diskIcon);
};

diskImage.src = "./assets/diskIcon.jpg";

stage.add(diskLayer);
diskLayer.hide();
newProcessCreation.push(diskLayer);

//---------------------------------------//
var userSpaceLayer = new Konva.Layer();

var userSpaceImage = new Image();
userSpaceImage.onload = function () {
  var userSpaceIcon = new Konva.Image({
    x: 527,
    y: 138,
    image: userSpaceImage,
    width: 150,
    height: 110,
  });
  userSpaceIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "After program is located, program is loaded in it's user space where the Operating System assigns it an ID and other information."
    );
    textBox.show();
  });
  userSpaceIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });

  userSpaceLayer.add(userSpaceIcon);
};

userSpaceImage.src = "./assets/userSpaceIcon.jpg";

stage.add(userSpaceLayer);
userSpaceLayer.hide();
newProcessCreation.push(userSpaceLayer);

//---------------------------------------//
var pcbLayer = new Konva.Layer();

var pcbImage = new Image();
pcbImage.onload = function () {
  var pcbIcon = new Konva.Image({
    x: 675,
    y: 140,
    image: pcbImage,
    width: 150,
    height: 110,
  });
  pcbIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      'Process information including it\'s ID is stored in the PCB, and process state in now set to "New." Congrats, you have created a new process in the Operating System.'
    );
    textBox.show();
  });
  pcbIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });

  pcbLayer.add(pcbIcon);
};

pcbImage.src = "./assets/pcbIcon.jpg";

stage.add(pcbLayer);
pcbLayer.hide();
newProcessCreation.push(pcbLayer);

//Shapes for processStateSteps
//---------------------------------------//
var newLayer = new Konva.Layer();

var pcb = new Konva.Rect({
  x: 670,
  y: 259,
  width: 195,
  height: 207,
  stroke: "black",
  strokeWidth: 2,
});

var pcbName = new Konva.Text({
  x: 750,
  y: 243,
  fontFamily: "Times New Roman",
  fontStyle: "bold",
  fontSize: 15,
  text: "PCB",
  fill: "black",
  align: "center",
});

var newImage = new Image();
newImage.onload = function () {
  var newIcon = new Konva.Image({
    x: 10,
    y: 130,
    image: newImage,
    width: 120,
    height: 110,
    name: "New",
  });
  newIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "A program that is going to be picked up by the Operating System into the main memory enters as a new process state."
    );
    textBox.show();
  });
  newIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });

  newLayer.add(newIcon);
  newLayer.add(pcb);
  newLayer.add(pcbName);
};

newImage.src = "./assets/newIcon.jpg";

stage.add(newLayer);
newLayer.hide();
normalProcessStateSteps.push(newLayer);
pausedProcessStateSteps.push(newLayer);
ioProcessStateSteps.push(newLayer);

//---------------------------------------//
var jobQueueLayer = new Konva.Layer();

var jobQueueImage = new Image();
jobQueueImage.onload = function () {
  var jobQueueIcon = new Konva.Image({
    x: 128.95,
    y: 131,
    image: jobQueueImage,
    width: 123,
    height: 110,
    name: "New",
  });
  jobQueueIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "The new process goes through the long term scheduler which takes in new “jobs” to manage and then locates them into the job queue until it is ready to be executed."
    );
    textBox.show();
  });
  jobQueueIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  jobQueueLayer.add(jobQueueIcon);
};

jobQueueImage.src = "./assets/jobQueueIcon.jpg";

stage.add(jobQueueLayer);
jobQueueLayer.hide();
normalProcessStateSteps.push(jobQueueLayer);
pausedProcessStateSteps.push(jobQueueLayer);
ioProcessStateSteps.push(jobQueueLayer);

//---------------------------------------//
var readyLayer = new Konva.Layer();

var readyImage = new Image();
readyImage.onload = function () {
  var readyIcon = new Konva.Image({
    x: 251,
    y: 132,
    image: readyImage,
    width: 150,
    height: 110,
    name: "Ready",
  });
  readyIcon.on("mouseover touchstart touchstart", function () {
    rectBox.show();
    writeMessage(
      'Once the process has all necessary resources that are required for execution, it enters the "Ready" state.'
    );
    textBox.show();
  });
  readyIcon.on("mouseout touchend touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  readyLayer.add(readyIcon);
};

readyImage.src = "./assets/readyIcon.jpg";

stage.add(readyLayer);
readyLayer.hide();
normalProcessStateSteps.push(readyLayer);
pausedProcessStateSteps.push(readyLayer);
ioProcessStateSteps.push(readyLayer);

//---------------------------------------//
var readyQueueLayer = new Konva.Layer();

var readyQueueImage = new Image();
readyQueueImage.onload = function () {
  var readyQueueIcon = new Konva.Image({
    x: 399,
    y: 130,
    image: readyQueueImage,
    width: 125,
    height: 110,
    name: "Ready",
  });
  readyQueueIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "The process goes through the short term scheduler which is what decides which processes will be placed on the ready queue. The ready queue stores all the processes that are waiting to be assigned a CPU."
    );
    textBox.show();
  });
  readyQueueIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  readyQueueLayer.add(readyQueueIcon);
};

readyQueueImage.src = "./assets/readyQueueIcon.jpg";

stage.add(readyQueueLayer);
readyQueueLayer.hide();
normalProcessStateSteps.push(readyQueueLayer);
pausedProcessStateSteps.push(readyQueueLayer);
ioProcessStateSteps.push(readyQueueLayer);

//---------------------------------------//
var runningLayer = new Konva.Layer();

var runningImage = new Image();
runningImage.onload = function () {
  var runningIcon = new Konva.Image({
    x: 523,
    y: 132,
    image: runningImage,
    width: 153,
    height: 110,
    name: "Running",
  });
  runningIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "Once the process is picked to be executed from the ready queue, it will be assigned to a CPU, where it will then enter the “running” state, and has begin executing."
    );
    textBox.show();
  });
  runningIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  runningLayer.add(runningIcon);
};

runningImage.src = "./assets/runningIcon.jpg";

stage.add(runningLayer);
runningLayer.hide();
normalProcessStateSteps.push(runningLayer);
pausedProcessStateSteps.push(runningLayer);
ioProcessStateSteps.push(runningLayer);

//---------------------------------------//
var postponedLayer = new Konva.Layer();

var postponedImage = new Image();
postponedImage.onload = function () {
  var postponedIcon = new Konva.Image({
    x: 305,
    y: 25,
    image: postponedImage,
    width: 350,
    height: 110,
    name: "Ready",
  });
  postponedIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "If the process is stopped or a high priority process comes in the OS, the CPU will stop executing the process and the current process state will return to “ready,” until the CPU is ready to pick it up again to execute."
    );
    textBox.show();
  });
  postponedIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  postponedLayer.add(postponedIcon);
};

postponedImage.src = "./assets/postponedIcon.jpg";

stage.add(postponedLayer);
postponedLayer.hide();
pausedProcessStateSteps.push(postponedLayer);
//---------------------------------------//
var runningLayer2 = new Konva.Layer();

var runningImage2 = new Image();
runningImage2.onload = function () {
  var runningIcon2 = new Konva.Image({
    x: 523,
    y: 132,
    image: runningImage2,
    width: 153,
    height: 110,
    name: "Running",
  });
  runningIcon2.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "Once the process is picked to be executed from the ready queue, it will be assigned to a CPU, where it will then enter the “running” state, and has begin executing."
    );
    textBox.show();
  });
  runningIcon2.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  runningLayer2.add(runningIcon2);
};

runningImage2.src = "./assets/runningIcon.jpg";

stage.add(runningLayer2);
runningLayer2.hide();

pausedProcessStateSteps.push(runningLayer2);

//---------------------------------------//
var waitingLayer = new Konva.Layer();

var waitingImage = new Image();
waitingImage.onload = function () {
  var waitingIcon = new Konva.Image({
    x: 470,
    y: 240,
    image: waitingImage,
    width: 195,
    height: 135,
    name: "Waiting",
  });
  waitingIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "If the process has an I/O request, the CPU will enter the waiting state, where it’ll wait for this event / request to occur."
    );
    textBox.show();
  });
  waitingIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  waitingLayer.add(waitingIcon);
};

waitingImage.src = "./assets/waitingIcon.jpg";

stage.add(waitingLayer);
waitingLayer.hide();
ioProcessStateSteps.push(waitingLayer);

//---------------------------------------//
var waitingQueueLayer = new Konva.Layer();

var waitingQueueImage = new Image();
waitingQueueImage.onload = function () {
  var waitingQueueIcon = new Konva.Image({
    x: 328,
    y: 263,
    image: waitingQueueImage,
    width: 143,
    height: 110,
    name: "Waiting",
  });
  waitingQueueIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "When in the waiting state, the process will be entered into the waiting queue until that process is completed. The process that is in this queue is what will be used by the CPU when the event is completed."
    );
    textBox.show();
  });
  waitingQueueIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  waitingQueueLayer.add(waitingQueueIcon);
};

waitingQueueImage.src = "./assets/waitingQueueIcon.jpg";

stage.add(waitingQueueLayer);
waitingQueueLayer.hide();
ioProcessStateSteps.push(waitingQueueLayer);

//---------------------------------------//
var returnToReadyLayer = new Konva.Layer();

var returnToReadyImage = new Image();
returnToReadyImage.onload = function () {
  var returnToReadyIcon = new Konva.Image({
    x: 330,
    y: 245,
    image: returnToReadyImage,
    width: 40,
    height: 50,
    name:"Ready",
  });
  returnToReadyIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "Once the I/O event has been taken care of and completed, the process will then once again enter the ready state."
    );
    textBox.show();
  });
  returnToReadyIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  returnToReadyLayer.add(returnToReadyIcon);
};

returnToReadyImage.src = "./assets/returnToReadyIcon.jpg";

stage.add(returnToReadyLayer);
returnToReadyLayer.hide();
ioProcessStateSteps.push(returnToReadyLayer);
ioProcessStateSteps.push(runningLayer2);
//---------------------------------------//
var terminatedLayer = new Konva.Layer();

var terminatedImage = new Image();
terminatedImage.onload = function () {
  var terminatedIcon = new Konva.Image({
    x: 673,
    y: 132,
    image: terminatedImage,
    width: 167,
    height: 110,
    name: "Terminated",
  });
  terminatedIcon.on("mouseover touchstart", function () {
    rectBox.show();
    writeMessage(
      "Once the CPU has finished executing the process and there is nothing else left to complete, the process will then enter a terminated state, which indicates that all instructions have been executed."
    );
    textBox.show();
  });
  terminatedIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox.hide();
    textBox.hide();
  });
  terminatedLayer.add(terminatedIcon);
};

terminatedImage.src = "./assets/terminatedIcon.jpg";

stage.add(terminatedLayer);
terminatedLayer.hide();
normalProcessStateSteps.push(terminatedLayer);
pausedProcessStateSteps.push(terminatedLayer);
ioProcessStateSteps.push(terminatedLayer);

var pcbTextBox = new Konva.Layer();

var processStateText = new Konva.Text({
  x: 674,
  y: 295,
  fontFamily: "Times New Roman",
  fontSize: 15,
  text: "",
  fill: "black",
  width: 200,
});
pcbTextBox.add(processStateText);

var pcbInfoText = new Konva.Text({
  x: 675,
  y: 265,
  fontFamily: "Times New Roman",
  fontSize: 15,
  text: "",
  fill: "black",
  width: 175,
});
pcbTextBox.add(pcbInfoText);

var verticalLine = new Konva.Line({
  points: [785, 260, 785, 465],
  stroke: "black",
  strokeWidth: 2,
});
pcbTextBox.add(verticalLine);

var horizontalLine = new Konva.Line({
  points: [670, 285, 865, 285],
  stroke: "black",
  strokeWidth: 2,
});
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
    points: [670, 315, 865, 315],
    stroke: "black",
    strokeWidth: 2,
  });
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
    points: [670, 345, 865, 345],
    stroke: "black",
    strokeWidth: 2,
  });
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
    points: [670, 375, 865, 375],
    stroke: "black",
    strokeWidth: 2,
  });
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
    points: [670, 405, 865, 405],
    stroke: "black",
    strokeWidth: 2,
  });
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
  points: [670, 435, 865, 435],
  stroke: "black",
  strokeWidth: 2,
});
pcbTextBox.add(horizontalLine);

stage.add(pcbTextBox);
pcbTextBox.hide();

//Array that will be assigned depending on users selection.
typeOfProcess = "newPS";
var arrayToTraverse = [];
for (let i = 0; i < newProcessCreation.length; i++) {
  arrayToTraverse[i] = newProcessCreation[i];
}

//When "start" we'll figure out which choice user has selected and display the first step
//Next, end and reset button will be enabled and "start" will be disabled.
function startVisual() {
  console.log(readTextFile('../../files/p1/ps.output.txt'))
  resetAnimation();
  document.getElementById("next").disabled = false;
  document.getElementById("end").disabled = false;
  document.getElementById("reset").disabled = true;
  // Getting processType we'll be using
  var display = document.getElementsByName("processStateType");
  for (let i of display) {
    if (i.checked) {
      typeOfProcess = i.value;
    }
  }
  if (typeOfProcess == "newPS") {
    pcbTextBox.hide();
    arrayToTraverse = [];
    for (let i = 0; i < newProcessCreation.length; i++) {
      arrayToTraverse[i] = newProcessCreation[i];
    }
  } else if (typeOfProcess == "normalPS") {
    arrayToTraverse = [];
    for (let i = 0; i < normalProcessStateSteps.length; i++) {
      arrayToTraverse[i] = normalProcessStateSteps[i];
    }
    pcbTextBox.show();
    updatePCBInfo();
    updatePCBState(arrayToTraverse[0].children[0].attrs.name);
  } else if (typeOfProcess == "pausedPS") {
    arrayToTraverse = [];
    for (let i = 0; i < pausedProcessStateSteps.length; i++) {
      arrayToTraverse[i] = pausedProcessStateSteps[i];
    }
    pcbTextBox.show();
    updatePCBInfo();
    updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  } else {
    arrayToTraverse = [];
    for (let i = 0; i < ioProcessStateSteps.length; i++) {
      arrayToTraverse[i] = ioProcessStateSteps[i];
    }
    pcbTextBox.show();
    updatePCBInfo();
    updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  }
  arrayToTraverse[stepIndex].show();
}
function previousStep() {
  //display previous step by hiding current.
  arrayToTraverse[stepIndex].hide();
  stepIndex--;
  if (
    typeOfProcess !== "newPS" &&
    arrayToTraverse[stepIndex].children[0].attrs.name !== undefined
  ) {
    updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  }
  //if after pressing back, we are at the first step, we will disable button
  if (stepIndex == 0) {
    document.getElementById("back").disabled = true;
  }
  //if "next" & "end" are disabled, enable
  if (
    document.getElementById("next").disabled &&
    document.getElementById("end").disabled
  ) {
    document.getElementById("next").disabled = false;
    document.getElementById("end").disabled = false;
  }
}
function nextStep() {
  //if reset is disabled, enable reset button
  if (document.getElementById("reset").disabled) {
    document.getElementById("reset").disabled = false;
  }
  //next step will be disaplyed
  stepIndex++;
  arrayToTraverse[stepIndex].show();
  if (
    typeOfProcess !== "newPS" &&
    arrayToTraverse[stepIndex].children[0].attrs.name !== undefined
  ) {
    updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  }
  //if it is not the first step, check if back is not enabled, if not back button will be enabled
  if (stepIndex !== 0 && document.getElementById("back").disabled) {
    document.getElementById("back").disabled = false;
  }
  //if it is the last step, next & end will be disabled
  if (stepIndex == arrayToTraverse.length - 1) {
    document.getElementById("next").disabled = true;
    document.getElementById("end").disabled = true;
  }
}
function endVisual() {
  //Disable all buttons exept "reset" & "back"
  document.getElementById("reset").disabled = false;
  document.getElementById("next").disabled = true;
  document.getElementById("back").disabled = false;
  document.getElementById("end").disabled = true;
  //Display final visual
  for (let step of arrayToTraverse) {
    step.show();
  }
  stepIndex = arrayToTraverse.length - 1;
  if (
    typeOfProcess !== "newPS" &&
    arrayToTraverse[stepIndex].children[0].attrs.name !== undefined
  ) {
    updatePCBInfo();
    updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  }
}
//Reset animations on page in order to allow a different process state choice to be displayed.
function resetAnimation() {
  //Disable all but "start" button.
  document.getElementById("reset").disabled = true;
  document.getElementById("next").disabled = true;
  document.getElementById("back").disabled = true;
  document.getElementById("end").disabled = true;

  //reset stepIndex
  stepIndex = 0;
  typeOfProcess = "";

  //Erase visual from sreen
  for (let step of arrayToTraverse) {
    step.hide();
  }
}

//Reset page to it's original state.
function resetButton() {
  //Call reset animation which ereases everything from screen.
  resetAnimation();
  //Set buttons to its original state
  document.getElementById("next").disabled = false;
  document.getElementById("end").disabled = false;

  //Hide visual from sreen
  for (let step of arrayToTraverse) {
    step.hide();
  }
  //Display first index
  arrayToTraverse[stepIndex].show();

  //If displaying process states. Update PCB state to original.
  if (
    typeOfProcess !== "newPS" &&
    arrayToTraverse[stepIndex].children[0].attrs.name !== undefined
  ) {
    updatePCBInfo();
    updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  }
}

//Function to update PCB process state
function updatePCBState(nameOfState) {
  processStateText.text("Process State:        " + nameOfState);
  //update output file here.

  
}

//update PCB information from output file.
function updatePCBInfo() {
  //get info from file & assign them to variables
  var data = readTextFile('../../files/p1/ps.output.txt').split("\n");
  
  var info = data[0].split(":");
  var PID = info[1];

  info = data[2].split(":");
  var progCounter = info[1];

  info = data[3].split(":");
  var registers = info[1];

  info = data[4].split(":");
  var memoryStart = info[1];

  info = data[5].split(":");
  var memoryLimit = info[1];

  info = data[6].split(":");
  var listOfFiles = info[1];

  //update textbox
  pcbInfoText.text(
    "Process ID:           " +
      PID + "\n\n\n\nProgram Counter: " + progCounter +
      "\n\nRegisters:             " +
      registers +
      "\n\nMemory Start:      " +
      memoryStart +
      "\n\nMemory Limit:     " +
      memoryLimit +
      "\n\nList of Files:         " +
      listOfFiles
  );

}
//Read in file
function readTextFile(file) {
  var rawFile = new XMLHttpRequest();
  var allText; 
  rawFile.open("GET", file, false);
  rawFile.onreadystatechange = function () {
      if(rawFile.readyState === 4) {
          if(rawFile.status === 200 || rawFile.status == 0) {
              allText = rawFile.responseText;
          }
      }
  }
  rawFile.send(null);
  return allText;
}