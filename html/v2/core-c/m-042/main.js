// cache-bust if needed: import ... from "./load_data.js?v=6"
import { readTextFile, head, inputData, outputData } from "./load_data.js";

/*
  Contributor Spring 2023 - Dakota Marino
  Your style + axis fixes:
  - drawAxis() does NOT clear the canvas
  - axis is redrawn after reset and after white-erase stepBack
*/

console.log("main.js (your-style with axis fixes) v6");

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
const VSTEP = 25;             // vertical step per segment
const VSTART_OFFSET = 50;     // move the whole graph down by this many pixels
const PADDING_LEFT = 30;
const PADDING_RIGHT = 20;
const AXIS_Y = 25;            // axis line position (labels above). Set to VSTART_OFFSET if you want it aligned with first row.
let TICK_EVERY = 100;         // auto chosen below

// ===== dynamic domain =====
let xMin = 0;
let xMax = 700;               // default so we always see an axis

// ===== helpers =====
function xScale(x) {
  if (xMax === xMin) return PADDING_LEFT;
  const usable = canvas.width - (PADDING_LEFT + PADDING_RIGHT);
  return PADDING_LEFT + ((Number(x) - xMin) / (xMax - xMin)) * usable;
}

// choose a nice tick based on span (keeps ~5–12 ticks)
function chooseTick(span) {
  const s = Math.max(span, 1);
  const bases = [1, 2, 5];
  const pow10 = Math.pow(10, Math.floor(Math.log10(s)));
  for (let mag = pow10 / 10; mag <= s * 2; mag *= 10) {
    for (const b of bases) {
      const tick = b * mag;
      const count = s / tick;
      if (count >= 5 && count <= 12) return tick;
    }
  }
  return 100;
}

// draw horizontal axis, ticks, labels ABOVE; do NOT clear here
function drawAxis() {
  context.strokeStyle = "#2b3a67";
  context.lineWidth = 2;

  // axis line
  context.beginPath();
  context.moveTo(PADDING_LEFT, AXIS_Y);
  context.lineTo(canvas.width - PADDING_RIGHT, AXIS_Y);
  context.stroke();

  // ticks + labels (above)
  context.font = "12px sans-serif";
  context.fillStyle = "#000";
  context.textAlign = "center";
  context.textBaseline = "bottom";

  const firstTick = Math.ceil(xMin / TICK_EVERY) * TICK_EVERY;
  for (let t = firstTick; t <= xMax; t += TICK_EVERY) {
    const px = xScale(t);

    // tick up
    context.beginPath();
    context.moveTo(px, AXIS_Y);
    context.lineTo(px, AXIS_Y - 8);
    context.stroke();

    // label above
    context.fillText(String(t), px, AXIS_Y - 12);
  }
}

// draw an initial default axis before data loads
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
  inputValues = inputData || [];
  await readTextFile("output");
  outputValues = outputData || [];

  document.getElementById("the-queue").innerHTML = inputValues;
  document.getElementById("the-head").innerHTML  = head;

  // compute domain from head + outputs
  const allX = [Number(head), ...outputValues.map(Number)].filter(n => !Number.isNaN(n));
  if (allX.length) {
    xMin = Math.min(...allX);
    xMax = Math.max(...allX);
    const span = Math.max(xMax - xMin, 1);
    const pad  = span * 0.05;
    xMin -= pad;
    xMax += pad;
    TICK_EVERY = chooseTick(xMax - xMin);
  }

  // full clear then axis repaint
  context.clearRect(0, 0, canvas.width, canvas.height);
  drawAxis();

  // reset draw state
  index = 0;
  vIndex = 0;
  document.getElementById("output-data").innerHTML = "";
}

// ===== drawing controller (your style) =====
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
    drawAxis(); // re-show axis after clearing
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
  context.strokeStyle = "#ffffff"; // white eraser, per your style

  if (index >= 1) {
    if (index === 1) {
      document.getElementById("output-data").innerHTML = "";
      index--;
      context.beginPath();
      // erase first segment’s visible stroke
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

  // repaint the axis in case the white stroke clipped ticks/labels
  drawAxis();

  // restore draw style
  context.strokeStyle = "blue";
  context.lineWidth = 5;
}
