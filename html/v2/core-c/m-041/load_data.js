import {readTextFile, head, inputData, outputData,} from './load_data.js';

/*
Contributor Spring 2023 - Dakota Marino
*/

//instance variables
var inputValues = [];
var outputValues = [];

//canvas and context for drawing lines on screen
const canvas = document.getElementById("myCanvas");
const context = canvas.getContext("2d");
context.beginPath();
//variables for canvas
var index = 0;
var vIndex = 0;
var intervalID;
//starts the program upon refresh
runProgram();

//declare all buttons and set on click to run a function
let Previous = document.getElementById('Previous');
Previous.onclick = function(){displayLine("stepBack",null)};

let Next = document.getElementById('Next');
Next.onclick = function(){displayLine("stepForward",null)};

let End = document.getElementById('End');
End.onclick = function(){displayLine("run",null)};

let reset = document.getElementById('Reset');
reset.onclick = function(){displayLine("clearAll",null)};

const btnStart = document.getElementById("play");

//need event listeners for play and visualize
btnStart.addEventListener("click", function() {
    console.log(btnStart.innerHTML);
    if (btnStart.innerHTML.trim() === "Play") {
        console.log("Equals play");
        displayLine("play",intervalID);
        btnStart.innerHTML = "Pause";
    }
    else if (btnStart.innerHTML.trim() === "Pause") {
        console.log("Equals pause");
        displayLine("pause",intervalID);
        btnStart.innerHTML = "Play";
    }
});

//program that runs upon loading the page
async function runProgram() {
    //sets input and output values
    await  readTextFile("input");
    inputValues = inputData;
    console.log(inputValues);
    await readTextFile("output");
    console.log(outputData);
    outputValues = outputData;
    document.getElementById("the-queue").innerHTML = inputValues;
    document.getElementById("the-head").innerHTML = head;
}

//This function handles all of the visualization commands.
function displayLine(type, interval) {
    //set color and line width. We need to do this every time 
    //because <STEP sets the color to white in order to erase a line.
    context.strokeStyle = "blue";
    context.lineWidth = 5;

    //if PLAY or VISUALIZE button was clicked
    if (type == "play") {
        intervalID = setInterval(function() {   
            if (index == 0) {
                document.getElementById("output-data").innerHTML = outputValues[index];
                context.beginPath();
                vIndex+=25;
                context.moveTo(head, vIndex-25);
                context.lineTo(outputValues[index], vIndex);
                context.stroke();
                index++;
            }
            else if (index < outputValues.length) {
                document.getElementById("output-data").innerHTML = document.getElementById("output-data").innerHTML + "," + outputValues[index];
                context.beginPath();
                vIndex+=25;
                context.moveTo(outputValues[index-1], vIndex-25);
                context.lineTo(outputValues[index], vIndex);
                context.stroke();
                index++;
            }
            else {
                clearInterval(interval);
            }
        }, 1000);
    }

    //if PAUSE or VISUALIZING button was clicked
    else if (type == "pause") {
        clearInterval(interval);
    }

    //if STEP> was clicked
    else if (type == "stepForward") {
        if (index < outputValues.length) {
            if (index == 0) {
                document.getElementById("output-data").innerHTML = outputValues[index];
                context.beginPath();
                vIndex+=25;
                context.moveTo(head, vIndex-25);
                context.lineTo(outputValues[index], vIndex);
                context.stroke();
                index++;
            }
            else {
                document.getElementById("output-data").innerHTML = document.getElementById("output-data").innerHTML + "," + outputValues[index];
                context.beginPath();
                vIndex+=25;
                context.moveTo(outputValues[index-1], vIndex-25);
                context.lineTo(outputValues[index], vIndex);
                context.stroke();
                index++;
            }
        }
    }    

    //if <STEP was clicked
    else if (type == "stepBack") {
        context.lineWidth = 7;
        context.strokeStyle = "#ffffff";
        if (index >= 1) {
            console.log(index);
            if (index == 1) {
                document.getElementById("output-data").innerHTML = "";
                index--;       
                context.lineTo(head, vIndex-25);
                context.stroke();
                vIndex-=25;
            }
            else {
                document.getElementById("output-data").innerHTML = outputValues.slice(0,index-1);
                index--;
                context.beginPath();
                context.moveTo(outputValues[index], vIndex);
                context.lineTo(outputValues[index-1], vIndex-25);
                context.stroke();
                vIndex-=25;
            }
        }
        context.strokeStyle = "blue";
        context.lineWidth = 5;
    } 

    //if RESET was clicked
    else if (type == "clearAll") {
        context.clearRect(0, 0, 700, 500);
        index = 0;
        vIndex = 0;
        document.getElementById("output-data").innerHTML = "";
    }

    //if RUN or END was clicked
    else if (type == "run") {
        while (index < outputValues.length) {
            if (index == 0) {
                document.getElementById("output-data").innerHTML = outputValues[index];
                context.beginPath();
                vIndex+=25;
                context.moveTo(head, vIndex-25);
                context.lineTo(outputValues[index], vIndex);
                context.stroke();
                index++;
            }
            else {
                document.getElementById("output-data").innerHTML = document.getElementById("output-data").innerHTML + "," + outputValues[index];
                context.beginPath();
                vIndex+=25;
                context.moveTo(outputValues[index-1], vIndex-25);
                context.lineTo(outputValues[index], vIndex);
                context.stroke();
                index++;
            }
        }
    }
}
