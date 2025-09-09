// Konva Stage for Local State
var stage2 = new Konva.Stage({
  container: "container2",
  width: 880,
  height: 470,
});

stage2.hide();

//Konva Stage for Global Processes
var stage = new Konva.Stage({
  container: "container",
  width: 880,
  height: 470,
});

//Initializing arrays for different new process creation and process state steps.
var newProcessCreation = [];
var readyProcess = [];
var runningProcess = [];
var waitingProcess = [];
var terminatedProcess = [];
var processStateSteps = [];
var PSArrayToTraverse = [];

//Initializing index for our array.
var stepIndex = 0;
var PSIndex = 0;

//Variable to declare users selection from radio button.
var typeOfProcess;

//Create textbox object.
var rectBox = new Konva.Layer();
var rect1 = new Konva.Rect({
  x: 13,
  y: 317,
  width: 313,
  height: 125,
  stroke: "black",
  fill: "#b6d7a8ff",
  strokeWidth: 0.5,
  cornerRadius: 10,
});
rectBox.add(rect1);
stage.add(rectBox);
rectBox.hide();

//Create text object.
var textBox = new Konva.Layer();
var text = new Konva.Text({
  x: 15,
  y: 335,
  fontFamily: "Times New Roman",
  fontStyle: "bold",
  fontSize: 15,
  text: "",
  fill: "black",
  width: 310,
  align: "center",
});
textBox.add(text);
stage.add(textBox);
textBox.hide();

//Create textbox object for stage two, use these text boxes for each type of process
var rectBox2 = new Konva.Layer();
var rect2 = new Konva.Rect({
  x: 13,
  y: 317,
  width: 313,
  height: 125,
  stroke: "black",
  fill: "#b6d7a8ff",
  strokeWidth: 0.5,
  cornerRadius: 10,
});

// add the shape to the layer
rectBox2.add(rect2);
stage2.add(rectBox2);
rectBox2.hide();

//Create text object for stage two, use these text boxes for each type of process
var textBox2 = new Konva.Layer();
var text2 = new Konva.Text({
  x: 15,
  y: 335,
  fontFamily: "Times New Roman",
  fontStyle: "bold",
  fontSize: 15,
  text: "",
  fill: "black",
  width: 310,
  align: "center",
});
textBox2.add(text2);
stage2.add(textBox2);
textBox2.hide();

//Function to change text for each step.
function writeMessage(message) {
  text.text(message);
  text2.text(message);
}

//Shapes for newProcessCreation

//Creating layer for step.
var userLayer = new Konva.Layer();

//Creating image object
var userImage = new Image();
userImage.onload = function () {
  var userIcon = new Konva.Image({
    x: 15,
    y: 143,
    image: userImage,
    width: 75,
    height: 100,
  });
  userIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage(
      "User starts a program like “java a.class” in the Operating System's GUI (Graphic User Interface) which is located in the RAM (Main Memory)."
    );
    textBox2.show();
  });
  userIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
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
    x: 88,
    y: 55,
    image: callImage,
    width: 165,
    height: 261,
  });
  callIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage(
      "User starts a program like “java a.class” in the Operating System's GUI (Graphic User Interface) which is located in the RAM (Main Memory)."
    );
    textBox2.show();
  });
  callIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  userLayer.add(callIcon);
};

callImage.src = "./assets/callIcon.jpg";

userLayer.hide();
stage2.add(userLayer);
newProcessCreation.push(userLayer);

//---------------------------------------//
var loaderLayer = new Konva.Layer();

var loaderImage = new Image();
loaderImage.onload = function () {
  var loaderIcon = new Konva.Image({
    x: 252,
    y: 131,
    image: loaderImage,
    width: 150,
    height: 110,
  });
  loaderIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage(
      "Program is loaded into the loader which is tasked with locating the given program from the hard disk."
    );
    textBox2.show();
  });
  loaderIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  loaderLayer.add(loaderIcon);
};

loaderImage.src = "./assets/loaderIcon.jpg";

loaderLayer.hide();
stage2.add(loaderLayer);
newProcessCreation.push(loaderLayer);

//---------------------------------------//
var diskLayer = new Konva.Layer();

var diskImage = new Image();
diskImage.onload = function () {
  var diskIcon = new Konva.Image({
    x: 399,
    y: 129,
    image: diskImage,
    width: 150,
    height: 110,
  });
  diskIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("Program is located in the hard disk.");
    textBox2.show();
  });
  diskIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  diskLayer.add(diskIcon);
};

diskImage.src = "./assets/diskIcon.jpg";

diskLayer.hide();
stage2.add(diskLayer);
newProcessCreation.push(diskLayer);

//---------------------------------------//
var userSpaceLayer = new Konva.Layer();

var userSpaceImage = new Image();
userSpaceImage.onload = function () {
  var userSpaceIcon = new Konva.Image({
    x: 547,
    y: 129,
    image: userSpaceImage,
    width: 150,
    height: 112,
  });
  userSpaceIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage(
      "After program is located, program is loaded in it's user space where the Operating System assigns it an ID and other information."
    );
    textBox2.show();
  });
  userSpaceIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  userSpaceLayer.add(userSpaceIcon);
};

userSpaceImage.src = "./assets/userSpaceIcon.jpg";

userSpaceLayer.hide();
stage2.add(userSpaceLayer);
newProcessCreation.push(userSpaceLayer);

//---------------------------------------//
var pcbLayer = new Konva.Layer();

var pcbImage = new Image();
pcbImage.onload = function () {
  var pcbIcon = new Konva.Image({
    x: 696,
    y: 56,
    image: pcbImage,
    width: 150,
    height: 261,
  });
  pcbIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage(
      'Process information including it\'s ID is stored in the PCB, and process state in now set to "New." Congrats, you have created a new process in the Operating System.'
    );
    textBox2.show();
  });
  pcbIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  pcbLayer.add(pcbIcon);
};

pcbImage.src = "./assets/pcbIcon.jpg";

pcbLayer.hide();
stage2.add(pcbLayer);
newProcessCreation.push(pcbLayer);

//Shapes for Ready process
//---------------------------------------//
var readyRSLayer = new Konva.Layer();

var readyRSImage = new Image();
readyRSImage.onload = function () {
  var readyRSIcon = new Konva.Image({
    x: 15,
    y: 108,
    image: readyRSImage,
    width: 113,
    height: 120,
  });
  readyRSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("Process has entered into the PCB in a \"Ready\" state.");
    textBox2.show();
  });
  readyRSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  readyRSLayer.add(readyRSIcon);
};

readyRSImage.src = "./assets/readyRSIcon.jpg";

readyRSLayer.hide();
stage2.add(readyRSLayer);
readyProcess.push(readyRSLayer);

//---------------------------------------//

var CPURSLayer = new Konva.Layer();

var CPURSImage = new Image();
CPURSImage.onload = function () {
  var CPURSIcon = new Konva.Image({
    x: 125,
    y: 23,
    image: CPURSImage,
    width: 575,
    height: 293,
  });
  CPURSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("Process enters the CPU short term scheduler where the Ready Queue and Dispatcher are located.");
    textBox2.show();
  });
  CPURSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  CPURSLayer.add(CPURSIcon);
};

CPURSImage.src = "./assets/CPURSIcon.jpg";

CPURSLayer.hide();
stage2.add(CPURSLayer);
readyProcess.push(CPURSLayer);

//---------------------------------------//

var readyQueueRSLayer = new Konva.Layer();

var readyQueueRSImage = new Image();
readyQueueRSImage.onload = function () {
  var readyQueueRSIcon = new Konva.Image({
    x: 215,
    y: 120,
    image: readyQueueRSImage,
    width: 273,
    height: 90,
  });
  readyQueueRSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("The process will sit in the Ready Queue in the CPU until the short term scheduler selects it to be executed.");
    textBox2.show();
  });
  readyQueueRSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  readyQueueRSLayer.add(readyQueueRSIcon);
};

readyQueueRSImage.src = "./assets/readyQueueRSIcon.jpg";

readyQueueRSLayer.hide();
stage2.add(readyQueueRSLayer);
readyProcess.push(readyQueueRSLayer);

//---------------------------------------//

var dispatcherRSLayer = new Konva.Layer();

var dispatcherRSImage = new Image();
dispatcherRSImage.onload = function () {
  var dispatcherRSIcon = new Konva.Image({
    x: 490,
    y: 68,
    image: dispatcherRSImage,
    width: 175,
    height: 200,
  });
  dispatcherRSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("After the short term scheduler selects a process from the ready queue, the dispatcher allocates this process to the CPU.");
    textBox2.show();
  });
  dispatcherRSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  dispatcherRSLayer.add(dispatcherRSIcon);
};

dispatcherRSImage.src = "./assets/dispatcherRSIcon.jpg";

dispatcherRSLayer.hide();
stage2.add(dispatcherRSLayer);
readyProcess.push(dispatcherRSLayer);

//---------------------------------------//

var runningRSLayer = new Konva.Layer();

var runningRSImage = new Image();
runningRSImage.onload = function () {
  var runningRSIcon = new Konva.Image({
    x: 690,
    y: 113,
    image: runningRSImage,
    width: 190,
    height: 120,
  });
  runningRSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("Once the process has completed going through the CPU Short Term Scheduler, it will then enter the next process state:  Running.");
    textBox2.show();
  });
  runningRSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  runningRSLayer.add(runningRSIcon);
};

runningRSImage.src = "./assets/runningRSIcon.jpg";

runningRSLayer.hide();
stage2.add(runningRSLayer);
readyProcess.push(runningRSLayer);

//Shapes for Running process
//---------------------------------------//
//---------------------------------------//

var CPURunSLayer = new Konva.Layer();

var CPURunSImage = new Image();
CPURunSImage.onload = function () {
  var CPURunSIcon = new Konva.Image({
    x: 20,
    y: 175,
    image: CPURunSImage,
    width: 150,
    height: 130,
  });
  CPURunSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("In the Running state, the process is in the CPU that it was allocated to.");
    textBox2.show();
  });
  CPURunSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  CPURunSLayer.add(CPURunSIcon);
};

CPURunSImage.src = "./assets/CPURunSIcon.jpg";

CPURunSLayer.hide();
stage2.add(CPURunSLayer);
runningProcess.push(CPURunSLayer);

//---------------------------------------//

var ifPostponedRunSLayer = new Konva.Layer();

var ifPostponedRunSImage = new Image();
ifPostponedRunSImage.onload = function () {
  var ifPostponedRunSIcon = new Konva.Image({
    x: 188,
    y: 25,
    image: ifPostponedRunSImage,
    width: 652,
    height: 145,
  });
  ifPostponedRunSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("If a process is postponed, the CPU will then move the process back to the Ready state, where it'll then go into the Ready Queue");
    textBox2.show();
  });
  ifPostponedRunSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  ifPostponedRunSLayer.add(ifPostponedRunSIcon);
};

ifPostponedRunSImage.src = "./assets/ifPostponedRunSIcon.jpg";

ifPostponedRunSLayer.hide();
stage2.add(ifPostponedRunSLayer);
runningProcess.push(ifPostponedRunSLayer);

//---------------------------------------//

var ifIORunSLayer = new Konva.Layer();

var ifIORunSImage = new Image();
ifIORunSImage.onload = function () {
  var ifIORunSIcon = new Konva.Image({
    x: 203,
    y: 165,
    image: ifIORunSImage,
    width: 637,
    height: 150,
  });
  ifIORunSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("If while being executed, the process requires an I/O request, the CPU will move the process to a Waiting state, where it'll then go into the Waiting Queue.");
    textBox2.show();
  });
  ifIORunSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  ifIORunSLayer.add(ifIORunSIcon);
};

ifIORunSImage.src = "./assets/ifIORunSIcon.jpg";

ifIORunSLayer.hide();
stage2.add(ifIORunSLayer);
runningProcess.push(ifIORunSLayer);

//---------------------------------------//

var ifTerminateRunSLayer = new Konva.Layer();

var ifTerminateRunSImage = new Image();
ifTerminateRunSImage.onload = function () {
  var ifTerminateRunSIcon = new Konva.Image({
    x: 328,
    y: 310,
    image: ifTerminateRunSImage,
    width: 516,
    height: 154,
  });
  ifTerminateRunSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("If there are no interruptions while the process is being executed in the CPU, once the process has finished executing, it will move on to a Terminated state.");
    textBox2.show();
  });
  ifTerminateRunSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  ifTerminateRunSLayer.add(ifTerminateRunSIcon);
};

ifTerminateRunSImage.src = "./assets/ifTerminateRunSIcon.jpg";

ifTerminateRunSLayer.hide();
stage2.add(ifTerminateRunSLayer);
runningProcess.push(ifTerminateRunSLayer);

//Shapes for Waiting process
//---------------------------------------//
//---------------------------------------//

var CPUWSLayer = new Konva.Layer();

var CPUWSImage = new Image();
CPUWSImage.onload = function () {
  var CPUWSIcon = new Konva.Image({
    x: 10,
    y: 155,
    image: CPUWSImage,
    width: 130,
    height: 130,
  });
  CPUWSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("Process in the CPU requires an IO request.");
    textBox2.show();
  });
  CPUWSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  CPUWSLayer.add(CPUWSIcon);
};

CPUWSImage.src = "./assets/CPUWSIcon.jpg";

CPUWSLayer.hide();
stage2.add(CPUWSLayer);
waitingProcess.push(CPUWSLayer);

//---------------------------------------//

var waitingWSLayer = new Konva.Layer();

var waitingWSImage = new Image();
waitingWSImage.onload = function () {
  var waitingWSIcon = new Konva.Image({
    x: 138,
    y: 160,
    image: waitingWSImage,
    width: 150,
    height: 120,
  });
  waitingWSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("Process enters Waiting State.");
    textBox2.show();
  });
  waitingWSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  waitingWSLayer.add(waitingWSIcon);
};

waitingWSImage.src = "./assets/waitingWSIcon.jpg";

waitingWSLayer.hide();
stage2.add(waitingWSLayer);
waitingProcess.push(waitingWSLayer);

//---------------------------------------//

var waitingQueueWSLayer = new Konva.Layer();

var waitingQueueWSImage = new Image();
waitingQueueWSImage.onload = function () {
  var waitingQueueWSIcon = new Konva.Image({
    x: 285,
    y: 160,
    image: waitingQueueWSImage,
    width: 273,
    height: 90,
  });
  waitingQueueWSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("When entered in the Waiting state, process also enters the Waiting Queue, where it will wait until IO request is executed.");
    textBox2.show();
  });
  waitingQueueWSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  waitingQueueWSLayer.add(waitingQueueWSIcon);
};

waitingQueueWSImage.src = "./assets/waitingQueueWSIcon.jpg";

waitingQueueWSLayer.hide();
stage2.add(waitingQueueWSLayer);
waitingProcess.push(waitingQueueWSLayer);
//---------------------------------------//

var internalSchedulerWSLayer = new Konva.Layer();

var internalSchedulerWSImage = new Image();
internalSchedulerWSImage.onload = function () {
  var internalSchedulerWSIcon = new Konva.Image({
    x: 555,
    y: 120,
    image: internalSchedulerWSImage,
    width: 175,
    height: 190,
  });
  internalSchedulerWSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("The Internal Scheduler(I.S), will process each request in the Waiting Queue.");
    textBox2.show();
  });
  internalSchedulerWSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  internalSchedulerWSLayer.add(internalSchedulerWSIcon);
};

internalSchedulerWSImage.src = "./assets/internalSchedulerWSIcon.jpg";

internalSchedulerWSLayer.hide();
stage2.add(internalSchedulerWSLayer);
waitingProcess.push(internalSchedulerWSLayer);

//---------------------------------------//

var readyWSLayer = new Konva.Layer();

var readyWSImage = new Image();
readyWSImage.onload = function () {
  var readyWSIcon = new Konva.Image({
    x: 724,
    y: 146,
    image: readyWSImage,
    width: 154,
    height: 130,
  });
  readyWSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("After going through the Waiting Queue and I.S, the process will then go back into a Ready State, where it will wait to be picked up by the CPU to finish executing.");
    textBox2.show();
  });
  readyWSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  readyWSLayer.add(readyWSIcon);
};

readyWSImage.src = "./assets/readyWSIcon.jpg";

readyWSLayer.hide();
stage2.add(readyWSLayer);
waitingProcess.push(readyWSLayer);

//Shapes for Terminated process
//---------------------------------------//
//---------------------------------------//

var CPUTSLayer = new Konva.Layer();

var CPUTSImage = new Image();
CPUTSImage.onload = function () {
  var CPUTSIcon = new Konva.Image({
    x: 230,
    y: 150,
    image: CPUTSImage,
    width: 145,
    height: 140,
  });
  CPUTSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("The CPU will continue to execute the process until it is complete.");
    textBox2.show();
  });
  CPUTSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  CPUTSLayer.add(CPUTSIcon);
};

CPUTSImage.src = "./assets/CPUTSIcon.jpg";

CPUTSLayer.hide();
stage2.add(CPUTSLayer);
terminatedProcess.push(CPUTSLayer);

//---------------------------------------//

var terminatedTSLayer = new Konva.Layer();

var terminatedTSImage = new Image();
terminatedTSImage.onload = function () {
  var terminatedTSIcon = new Konva.Image({
    x: 380,
    y: 160,
    image: terminatedTSImage,
    width: 255,
    height: 125,
  });
  terminatedTSIcon.on("mouseover touchstart", function () {
    rectBox2.show();
    writeMessage("After CPU finishes executing, the process will then enter a Terminated state, and be removed from the PCB.");
    textBox2.show();
  });
  terminatedTSIcon.on("mouseout touchend", function () {
    writeMessage("");
    rectBox2.hide();
    textBox2.hide();
  });

  terminatedTSLayer.add(terminatedTSIcon);
};

terminatedTSImage.src = "./assets/terminatedTSIcon.jpg";

terminatedTSLayer.hide();
stage2.add(terminatedTSLayer);
terminatedProcess.push(terminatedTSLayer);


//Shapes for processStateSteps
//---------------------------------------//
var newLayer = new Konva.Layer();

var pcb = new Konva.Rect({
  x: 670,
  y: 259,
  width: 195,
  height: 207,
  fill: 'white',
  strokeWidth: 20,
  shadowBlur: 3,
  cornerRadius: 5,
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
//newLayer.hide();
processStateSteps.push(newLayer);

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
processStateSteps.push(jobQueueLayer);

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
processStateSteps.push(readyLayer);

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
    id: "Ready2",
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
processStateSteps.push(readyQueueLayer);

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
    id: "Running",
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
processStateSteps.push(runningLayer);

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
    id: "Running2"
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

//---------------------------------------//

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
  strokeWidth: 1,
});
pcbTextBox.add(verticalLine);

var horizontalLine = new Konva.Line({
  points: [670, 285, 865, 285],
  stroke: "black",
  strokeWidth: 1,
});
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
    points: [670, 315, 865, 315],
    stroke: "black",
    strokeWidth: 1,
  });
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
    points: [670, 345, 865, 345],
    stroke: "black",
    strokeWidth: 1,
  });
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
    points: [670, 375, 865, 375],
    stroke: "black",
    strokeWidth: 1,
  });
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
    points: [670, 405, 865, 405],
    stroke: "black",
    strokeWidth: 1,
  });
pcbTextBox.add(horizontalLine);

var horizontalLine = new Konva.Line({
  points: [670, 435, 865, 435],
  stroke: "black",
  strokeWidth: 1,
});
pcbTextBox.add(horizontalLine);
stage.add(pcbTextBox);

//Create textbox for title of diagrams.
var textBox0 = new Konva.Layer();
var text0 = new Konva.Text({
  x: 300,
  y: 9,
  fontFamily: "Times New Roman",
  fontSize: 20,
  text: "Global Process Diagram",
  fill: "black",
  width: 310,
  align: "center",
});
textBox0.add(text0);
stage.add(textBox0);
//textBox0.hide();

var textBox01 = new Konva.Layer();
var text01 = new Konva.Text({
  x: 300,
  y: 9,
  fontFamily: "Times New Roman",
  fontSize: 20,
  text: "Local State Diagram",
  fill: "black",
  width: 310,
  align: "center",
});
textBox01.add(text01);
stage2.add(textBox01);

//Initiate PCB with current info.
updatePCBState("New");
updatePCBInfo();

//Initiate array that user will traverse through and push layers in.
var arrayToTraverse = [];
for (let step of processStateSteps) {
  arrayToTraverse.push(step);
}

function nextStep() {
  //if reset is disabled, enable reset button
  if (document.getElementById("reset").disabled) {
    document.getElementById("reset").disabled = false;
  }
  //next step will be disaplyed
  stepIndex++;
  arrayToTraverse[stepIndex].show();
  updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  UpdateStateButtons(arrayToTraverse[stepIndex].children[0].attrs.name);
    //if it is not the first step, check if back is not enabled, if not back button will be enabled
    if (stepIndex !== 0 && document.getElementById("back").disabled) {
      document.getElementById("back").disabled = false;
    }
    //if it is the last step, next & end will be disabled
    if (stepIndex == arrayToTraverse.length - 1) {
      document.getElementById("next").disabled = true;
      document.getElementById("endCurrentDiagram").disabled = true;
    }
    //If we get to "Running" state disable "next" and force user to select which type of process they'd like to see.
    if(arrayToTraverse[stepIndex].children[0].attrs.id == "Running"){
      typeOfProcess="";
      document.getElementById("next").disabled = true;
      document.getElementById("back").disabled = false;
      document.getElementById("postpone").hidden = false;
      document.getElementById("ioRequest").hidden = false;
      document.getElementById("terminateProg").hidden = false;
    }
  }
function previousStep() {
  //If you're going back and endCurrentDiagram is disabled, enable button to allow user to view end of diagram.
  if(document.getElementById("endCurrentDiagram").disabled){
    document.getElementById("endCurrentDiagram").disabled = false;
  }
  //If current step is "Running," hide user options before going back a step.
  if(arrayToTraverse[stepIndex].children[0].attrs.id == "Running"){
    typeOfProcess="";
    document.getElementById("postpone").hidden = true;
    document.getElementById("ioRequest").hidden = true;
    document.getElementById("terminateProg").hidden = true;
    document.getElementById("endCurrentDiagram").hidden = true;
  }
  //Display previous step by hiding current.
  arrayToTraverse[stepIndex].hide();
  stepIndex--;
  //Update PCB info and update Process State buttons user can select.
  updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  UpdateStateButtons(arrayToTraverse[stepIndex].children[0].attrs.name);
  //if after pressing back, we are at the first step, we will disable button
  if (stepIndex == 0) {
    document.getElementById("back").disabled = true;
    document.getElementById("reset").disabled = true;
  }
  //if "next" is disabled, enable
  if (document.getElementById("next").disabled) {
    document.getElementById("next").disabled = false;
  }
}
//This function displays the final diagram of user selected option instead of the full diagram.
function endCurrentProcess(){
  document.getElementById("next").disabled = true;
  document.getElementById("back").disabled = false;
  document.getElementById("endCurrentDiagram").disabled = true;
  document.getElementById("reset").disabled = false;
  for (let step of arrayToTraverse) {
    step.show();
  }
  stepIndex = arrayToTraverse.length -1;
  UpdateStateButtons(arrayToTraverse[stepIndex].children[0].attrs.name);
  updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
}
function endVisual() {
  //Enable all buttons except "next" "back" and "end"
  document.getElementById("next").disabled = true;
  document.getElementById("back").disabled = true;
  document.getElementById("end").disabled = true;
  document.getElementById("endCurrentDiagram").hidden = true;
  document.getElementById("reset").disabled = false;
  document.getElementById("Ready").disabled = false;
  document.getElementById("Running").disabled = false;
  document.getElementById("Waiting").disabled = false;
  document.getElementById("Terminated").disabled = false;
  document.getElementById("postpone").hidden = true;
  document.getElementById("ioRequest").hidden = true;
  document.getElementById("terminateProg").hidden = true;


  //Push all layers to array.
  arrayToTraverse.push(postponedLayer);
  arrayToTraverse.push(waitingLayer);
  arrayToTraverse.push(waitingQueueLayer);
  arrayToTraverse.push(returnToReadyLayer);
  arrayToTraverse.push(terminatedLayer);
  //Display rest of the array from where user left off.
  for (let i=stepIndex; i<arrayToTraverse.length;i++) {
    arrayToTraverse[i].show();
  }
  //StepIndex to last index and update the state.
  stepIndex = arrayToTraverse.length -1;
  updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name)
}

function resetButton(){
  //Disable / enable needed buttons.
  document.getElementById("next").disabled = false;
  document.getElementById("back").disabled = true;
  document.getElementById("reset").disabled = true;
  document.getElementById("end").disabled = false;
  document.getElementById("endCurrentDiagram").hidden = true;
  document.getElementById("Ready").disabled = true;
  document.getElementById("Running").disabled = true;
  document.getElementById("Waiting").disabled = true;
  document.getElementById("Terminated").disabled = true;

  //Erase visual from sreen
  for (let i=1; i<arrayToTraverse.length;i++) {
      arrayToTraverse[i].hide();
  }
  //Begin new arrayToTraverse from it's initial state.
  arrayToTraverse = [];
  for (let step of processStateSteps) {
    arrayToTraverse.push(step);
  }
  //Update variables to initial state.
  stepIndex = 0;
  typeOfProcess="";
  updatePCBState(arrayToTraverse[stepIndex].children[0].attrs.name);
  updatePCBInfo();
}

function updatePCBState(nameOfState) {
  processStateText.text("Process State:        " + nameOfState);
  //update output file here. "../../files/p1/ps.output.txt"

}

//update PCB information from output file.
function updatePCBInfo() {
  //get info from file in an array and assign to "data" variable.
  var data = readTextFile('../../files/p1/ps-output.txt').split("\n");
  
  //go through array and assign values to corresponding variables.
  var PID = data[0];

  var progCounter = data[1];

  var registers = data[2];

  var memoryStart = data[3]

  var memoryLimit = data[4]

  var listOfFiles = data[5]

  //update textbox with data from file.
  pcbInfoText.text(
    "Process ID:            " +
      PID + "\n\n\n\nProgram Counter:  " + progCounter +
      "\n\nRegisters:               " +
      registers +
      "\n\nMemory Start:       " +
      memoryStart +
      "\n\nMemory Limit:      " +
      memoryLimit +
      "\n\nList of Files:          " +
      listOfFiles
  );

}
//Function to set rest of the array to a postponed Process State process.
function setToPostponed(){
  //Hide buttons once user has selected.
  document.getElementById("postpone").hidden = true;
  document.getElementById("ioRequest").hidden = true;
  document.getElementById("terminateProg").hidden = true;
  document.getElementById("endCurrentDiagram").hidden = false;
  document.getElementById("endCurrentDiagram").disabled = false;
  //Erase previous array and begin as new.
  arrayToTraverse = [];
  for (let i = 0; i < processStateSteps.length; i++) {
    arrayToTraverse.push(processStateSteps[i]);
    arrayToTraverse[i].show();
  }
  //Push layers needed to complete this process.
  arrayToTraverse.push(postponedLayer);
  arrayToTraverse.push(runningLayer2);
  arrayToTraverse.push(terminatedLayer);
  //Enable "next"
  document.getElementById("next").disabled = false;
  //Display next step.
  nextStep();
  //Enable end
}
//Function to set rest of the array to an IO Requested Process State process.
function setToIORequest(){
  //Hide buttons once user has selected.
  document.getElementById("postpone").hidden = true;
  document.getElementById("ioRequest").hidden = true;
  document.getElementById("terminateProg").hidden = true;
  document.getElementById("endCurrentDiagram").hidden = false;
  document.getElementById("endCurrentDiagram").disabled = false;
  //Erase previous array and begin as new.
  arrayToTraverse = [];
  for (let i = 0; i < processStateSteps.length; i++) {
    arrayToTraverse.push(processStateSteps[i]);
    arrayToTraverse[i].show();
  }
  //Push layers needed to complete this process.
  arrayToTraverse.push(waitingLayer);
  arrayToTraverse.push(waitingQueueLayer);
  arrayToTraverse.push(returnToReadyLayer);
  arrayToTraverse.push(runningLayer2);
  arrayToTraverse.push(terminatedLayer);

  //Decalre typeOfProcess to IORequest to know when to enable "Waiting" button
  typeOfProcess = "IORequest";
  //Enable "next"
  document.getElementById("next").disabled = false;
  //Display next step.
  nextStep();
  //Enable end
}
//Function to set rest of the array to a terminated Process State process.
function setToTerminate(){
  //Hide buttons once user has selected.
  document.getElementById("postpone").hidden = true;
  document.getElementById("ioRequest").hidden = true;
  document.getElementById("terminateProg").hidden = true;
  document.getElementById("endCurrentDiagram").hidden = true;
  //Erase previous array and begin as new.
  arrayToTraverse = [];
  for (let i = 0; i < processStateSteps.length; i++) {
    arrayToTraverse.push(processStateSteps[i]);
    arrayToTraverse[i].show();
  }
  //Push layers needed to complete this process.
  arrayToTraverse.push(terminatedLayer);

  //Display next step.
  nextStep();
}
//Reads in a file
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
//Update Process State buttons depending on where user is in animation.
function UpdateStateButtons(pcbState){
  if(pcbState == "New"){
    document.getElementById("Ready").disabled = true;
    document.getElementById("Running").disabled = true;
    document.getElementById("Waiting").disabled = true;
    document.getElementById("Terminated").disabled = true;
  }
  if(pcbState == "Ready"){
    document.getElementById("Ready").disabled = false;
    document.getElementById("Running").disabled = true;
    document.getElementById("Waiting").disabled = true;
    document.getElementById("Terminated").disabled = true;
  }
  if(pcbState == "Running"){
    document.getElementById("Ready").disabled = false;
    document.getElementById("Running").disabled = false;
    document.getElementById("Waiting").disabled = true;
    document.getElementById("Terminated").disabled = true;
  }
  if(pcbState == "Waiting"){
    document.getElementById("Ready").disabled = false;
    document.getElementById("Running").disabled = false;
    document.getElementById("Waiting").disabled = false;
    document.getElementById("Terminated").disabled = true;
  }
  if(pcbState == "Terminated"){
    document.getElementById("Ready").disabled = false;
    document.getElementById("Running").disabled = false;
    document.getElementById("Terminated").disabled = false;
    if(typeOfProcess == "IORequest"){
      document.getElementById("Waiting").disabled = false;
    }
  }
}
function displayNewPS(){
  if(PSArrayToTraverse !== null){
    for (let step of PSArrayToTraverse) {
      step.hide();
    }
    PSArrayToTraverse = [];
  }
  //Set display to new.
  PSArrayToTraverse = initiateProcessInfo("New");
  PSArrayToTraverse[PSIndex].show();
}

function displayReadyPS(){
  if(PSArrayToTraverse !== null){
    for (let step of PSArrayToTraverse) {
      step.hide();
    }
    PSArrayToTraverse = [];
  }
  //Set display to Ready.
  PSArrayToTraverse = initiateProcessInfo("Ready");
  PSArrayToTraverse[PSIndex].show();
}

function displayRunningPS(){
  if(PSArrayToTraverse !== null){
    for (let step of PSArrayToTraverse) {
      step.hide();
    }
    PSArrayToTraverse = [];
  }
  //Set display to Running.
  PSArrayToTraverse = initiateProcessInfo("Running");
  PSArrayToTraverse[PSIndex].show();
}

function displayWaitingPS(){
  if(PSArrayToTraverse !== null){
    for (let step of PSArrayToTraverse) {
      step.hide();
    }
    PSArrayToTraverse = [];
  }
  //Set display to Waiting.
  PSArrayToTraverse = initiateProcessInfo("Waiting");
  PSArrayToTraverse[PSIndex].show();
}

function displayTerminatedPS(){
  if(PSArrayToTraverse !== null){
    for (let step of PSArrayToTraverse) {
      step.hide();
    }
    PSArrayToTraverse = [];
  }
  //Set display to Terminated.
  PSArrayToTraverse = initiateProcessInfo("Terminated");
  PSArrayToTraverse[PSIndex].show();
}

function nextPSStep(){
    //if reset is disabled, enable reset button
    if (document.getElementById("resetPS").disabled) {
      document.getElementById("resetPS").disabled = false;
    }
    //next step will be disaplyed
    PSIndex++;
    PSArrayToTraverse[PSIndex].show();
    //if it is not the first step, check if back is not enabled, if not back button will be enabled
    if (PSIndex !== 0 && document.getElementById("backPS").disabled) {
      document.getElementById("backPS").disabled = false;
    }
    //if it is the last step, next & end will be disabled
    if (PSIndex == PSArrayToTraverse.length - 1) {
      document.getElementById("nextPS").disabled = true;
      document.getElementById("displayPS").disabled = true;
    }
}

function previousPSStep(){
   //display previous step by hiding current.
   PSArrayToTraverse[PSIndex].hide();
   PSIndex--;
   //if after pressing back, we are at the first step, we will disable button
   if (PSIndex == 0) {
    document.getElementById("backPS").disabled = true;
    document.getElementById("resetPS").disabled = true;
  }
   //if "next" is disabled, enable.
   if (
     document.getElementById("nextPS").disabled && document.getElementById("displayPS").disabled
   ) {
     document.getElementById("nextPS").disabled = false;
     document.getElementById("displayPS").disabled = false;
   }
}

function resetPSButton(){
  //Hide all shapes.
  for (let step of PSArrayToTraverse) {
    step.hide();
  }
  //Set variables to initital settings.
  PSIndex = 0;
  //Display first shape.
  PSArrayToTraverse[PSIndex].show();
  //Disable / enable buttons.
  if (
    document.getElementById("nextPS").disabled && document.getElementById("displayPS").disabled
  ) {
    document.getElementById("nextPS").disabled = false;
    document.getElementById("displayPS").disabled = false;
  }
  document.getElementById("backPS").disabled = true;
  document.getElementById("resetPS").disabled = true;

}

function displayAllPS(){
    //Disable all buttons exept "resetPS" & "backPS"
    document.getElementById("resetPS").disabled = false;
    document.getElementById("nextPS").disabled = true;
    document.getElementById("backPS").disabled = false;
    document.getElementById("displayPS").disabled = true;
    //Display final visual
    for (let step of PSArrayToTraverse) {
      step.show();
    }
    PSIndex = PSArrayToTraverse.length - 1;
}

function endPSVisual(){
  //Set display to new.
    resetPSButton();
    //Show buttons for process state process.
    document.getElementById("next").hidden = false;
    document.getElementById("back").hidden = false;
    document.getElementById("reset").hidden = false;
    document.getElementById("end").hidden = false;
    if(stepIndex > 4){
      document.getElementById("endCurrentDiagram").hidden = false;
    }
    //Toggle user options on or off
    //if running show if not done 
    if(arrayToTraverse[stepIndex].children[0].attrs.id == "Running"){
      document.getElementById("postpone").hidden = false;
      document.getElementById("ioRequest").hidden = false;
      document.getElementById("terminateProg").hidden = false;
    }else{
      document.getElementById("postpone").hidden = true;
      document.getElementById("ioRequest").hidden = true;
      document.getElementById("terminateProg").hidden = true;
    }
    //Hide buttons for specific process state.
    document.getElementById("nextPS").hidden= true;
    document.getElementById("backPS").hidden= true;
    document.getElementById("resetPS").hidden= true;
    document.getElementById("displayPS").hidden= true;
    document.getElementById("endPS").hidden= true;

    hideStage(stage2);
    showStage(stage);
}

function initiateProcessInfo(typeOfPS){
  PSIndex=0;
  hideStage(stage);
  showStage(stage2);
  //Hide buttons for process state process.
  document.getElementById("next").hidden = true;
  document.getElementById("back").hidden = true;
  document.getElementById("reset").hidden = true;
  document.getElementById("end").hidden = true;
  document.getElementById("endCurrentDiagram").hidden = true;
  //Toggle user options on or off
  document.getElementById("postpone").hidden = true;
  document.getElementById("ioRequest").hidden = true;
  document.getElementById("terminateProg").hidden = true;
  //Show buttons for specific process state.
  document.getElementById("nextPS").hidden= false;
  document.getElementById("backPS").hidden= false;
  document.getElementById("resetPS").hidden= false;
  document.getElementById("displayPS").hidden= false;
  document.getElementById("endPS").hidden= false;
  document.getElementById("nextPS").disabled= false;
  document.getElementById("displayPS").disabled= false;
  document.getElementById("backPS").disabled= true;
  document.getElementById("resetPS").disabled= true;
  if(typeOfPS == "New") {
    return newProcessCreation;
  }
  if(typeOfPS == "Ready") {
    return readyProcess;
  }
  if(typeOfPS == "Running") {
    return runningProcess;
  }
  if(typeOfPS == "Waiting") {
    return waitingProcess;
  }
  if(typeOfPS == "Terminated") {
    return terminatedProcess;
  }
}

//Hide stage and remove its border.
function hideStage(stageName){
  stageName.hide();
  document.getElementById(stageName.attrs.container.id).style.borderStyle = "none";
  
}
//Show stage and display a solid border.
function showStage(stageName){
  stageName.show();
  document.getElementById(stageName.attrs.container.id).style.borderStyle = "solid";
}

  