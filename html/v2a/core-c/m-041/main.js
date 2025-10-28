import {readTextFile, head, inputData, outputData} from './load_data.js';
/*
  Contributor Spring 2023 - Dakota Marino
  Dynamic axis + VSTART_OFFSET to lower starting Y of graph
*/

console.log("main.js loaded (dynamic axis, start offset) v5");

// ===== canvas & context =====
const canvas = document.getElementById("myCanvas");
const context = canvas.getContext("2d");

// ===== UI/data state =====
let inputValues = [];
let outputValues = [];
let index = 0;          // which output value we are at
let vIndex = 0;         // vertical increment counter
let intervalID = null;  // setInterval handle

// ===== drawing constants =====
const VSTEP = 25;            // vertical step for each line segment
const VSTART_OFFSET = 50;   // <— move the whole graph down by this many pixels
const PADDING_LEFT = 30;
const PADDING_RIGHT = 20;
const AXIS_Y = 25;           // axis line position
let TICK_EVERY = 100;        // tick spacing (auto chosen below)

// ===== dynamic domain =====
let xMin = 0;
let xMax = 700;              // default domain so we always see an axis

// ===== helpers =====
function xScale(x) {
  if (xMax === xMin) return PADDING_LEFT;
  const usable = canvas.width - (PADDING_LEFT + PADDING_RIGHT);
  return PADDING_LEFT + ((Number(x) - xMin) / (xMax - xMin)) * usable;
}

function chooseTick(span) {
  const bases = [1, 2, 5];
  const pow10 = Math.pow(10, Math.floor(Math.log10(span || 1)));
  for (let mag = pow10 / 10; mag <= span * 2; mag *= 10) {
    for (const b of bases) {
      const tick = b * mag;
      const count = span / tick;
      if (count >= 5 && count <= 12) return tick;
    }
  }
  return 100;
}

function drawAxis() {
  context.clearRect(0, 0, canvas.width, canvas.height);
  context.strokeStyle = "#2b3a67";
  context.lineWidth = 2;
  context.beginPath();
  context.moveTo(PADDING_LEFT, AXIS_Y);
  context.lineTo(canvas.width - PADDING_RIGHT, AXIS_Y);
  context.stroke();

  context.font = "12px sans-serif";
  context.fillStyle = "#000";
  context.textAlign = "center";

  const firstTick = Math.ceil(xMin / TICK_EVERY) * TICK_EVERY;
  for (let t = firstTick; t <= xMax; t += TICK_EVERY) {
    const px = xScale(t);
    context.beginPath();
    context.moveTo(px, AXIS_Y);
    context.lineTo(px, AXIS_Y - 8);         // tick up
    context.stroke();
    context.fillText(String(t), px, AXIS_Y - 12); // label above
  }
}

// draw a default axis immediately
drawAxis();

// ===== buttons =====
document.getElementById("Previous").onclick = () => displayLine("stepBack");
document.getElementById("Next").onclick     = () => displayLine("stepForward");
document.getElementById("End").onclick      = () => displayLine("run");
document.getElementById("Reset").onclick    = () => displayLine("clearAll");

const btnStart = document.getElementById("play");
btnStart.addEventListener("click", () => {
  if (btnStart.innerHTML.trim() === "Play") {
    displayLine("play");
    btnStart.innerHTML = "Pause";
  } else {
    displayLine("pause");
    btnStart.innerHTML = "Play";
  }
});

// ===== load & init =====
runProgram();

async function runProgram() {
  await readTextFile("input");
  inputValues = inputData;
  await readTextFile("output");
  outputValues = outputData;

  console.log("head:", head, "inputs:", inputValues, "outputs:", outputValues);

  document.getElementById("the-queue").innerHTML = inputValues;
  document.getElementById("the-head").innerHTML  = head;

  const allX = [Number(head), ...outputValues.map(Number)].filter(n => !Number.isNaN(n));
  if (allX.length === 0) {
    console.warn("No data yet — default axis shown.");
    return;
  }

  xMin = Math.min(...allX);
  xMax = Math.max(...allX);
  const span = xMax - xMin || 1;
  const pad  = span * 0.05;
  xMin -= pad;
  xMax += pad;

  TICK_EVERY = chooseTick(xMax - xMin);
  drawAxis();

  index = 0;
  vIndex = 0;
  document.getElementById("output-data").innerHTML = "";
}

// ===== drawing controller =====
function displayLine(type) {
  context.strokeStyle = "blue";
  context.lineWidth = 5;

  if (type === "play") {
    clearInterval(intervalID);
    intervalID = setInterval(stepForwardDraw, 1000);
  } else if (type === "pause") {
    clearInterval(intervalID);
  } else if (type === "stepForward") {
    stepForwardDraw();
  } else if (type === "stepBack") {
    stepBackErase();
  } else if (type === "clearAll") {
    clearInterval(intervalID);
    context.clearRect(0, 0, canvas.width, canvas.height);
    index = 0;
    vIndex = 0;
    document.getElementById("output-data").innerHTML = "";
    drawAxis();
  } else if (type === "run") {
    clearInterval(intervalID);
    while (index < outputValues.length) stepForwardDraw();
  }
}

function stepForwardDraw() {
  if (index >= outputValues.length) {
    clearInterval(intervalID);
    return;
  }

  const outX = xScale(Number(outputValues[index]));

  if (index === 0) {
    document.getElementById("output-data").innerHTML = outputValues[index];
    context.beginPath();
    vIndex += VSTEP;
    context.moveTo(xScale(Number(head)), VSTART_OFFSET + vIndex - VSTEP);
    context.lineTo(outX,                VSTART_OFFSET + vIndex);
    context.stroke();
  } else {
    document.getElementById("output-data").innerHTML += "," + outputValues[index];
    context.beginPath();
    vIndex += VSTEP;
    context.moveTo(xScale(Number(outputValues[index - 1])), VSTART_OFFSET + vIndex - VSTEP);
    context.lineTo(outX,                                    VSTART_OFFSET + vIndex);
    context.stroke();
  }
  index++;
}

function stepBackErase() {
  context.lineWidth = 7;
  context.strokeStyle = "#ffffff";

  if (index >= 1) {
    if (index === 1) {
      document.getElementById("output-data").innerHTML = "";
      index--;
      context.beginPath();
      context.moveTo(xScale(Number(head)), VSTART_OFFSET + vIndex - VSTEP);
      context.lineTo(xScale(Number(head)), VSTART_OFFSET + vIndex);
      context.stroke();
      vIndex -= VSTEP;
    } else {
      document.getElementById("output-data").innerHTML =
        outputValues.slice(0, index - 1);
      index--;
      context.beginPath();
      context.moveTo(xScale(Number(outputValues[index])),     VSTART_OFFSET + vIndex);
      context.lineTo(xScale(Number(outputValues[index - 1])), VSTART_OFFSET + vIndex - VSTEP);
      context.stroke();
      vIndex -= VSTEP;
    }
  }

  context.strokeStyle = "blue";
  context.lineWidth = 5;
}
