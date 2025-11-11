// main.js — axis with numbers (ticks/labels above), scaled X, lowered start
// Contributor Spring 2023 - Dakota Marino

import { readTextFile, head, inputData, outputData } from "./load_data.js";

// ===== canvas =====
const canvas = document.getElementById("myCanvas");
const ctx = canvas.getContext("2d");

// ===== state =====
let inputValues = [];
let outputValues = [];
let index = 0;
let vIndex = 0;
let intervalID = null;

// ===== drawing constants =====
const VSTEP = 25;           // vertical step per segment
const VSTART_OFFSET = 50;   // vertical offset for the whole graph
const PADDING_LEFT = 30;
const PADDING_RIGHT = 20;
const AXIS_Y = 25;          // axis line y
let TICK_EVERY = 100;       // chosen dynamically after data

// ===== x-domain & scale =====
let xMin = 0;
let xMax = 700;

function xScale(x) {
  if (xMax === xMin) return PADDING_LEFT;
  const usable = canvas.width - (PADDING_LEFT + PADDING_RIGHT);
  return PADDING_LEFT + ((Number(x) - xMin) / (xMax - xMin)) * usable;
}

// choose tick so we get ~5–12 ticks
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

// ===== axis (does NOT clear) =====
function drawAxis() {
  // line
  ctx.strokeStyle = "#2b3a67";
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(PADDING_LEFT, AXIS_Y);
  ctx.lineTo(canvas.width - PADDING_RIGHT, AXIS_Y);
  ctx.stroke();

  // ticks + labels (above)
  ctx.lineWidth = 1.5;
  ctx.font = "12px sans-serif";
  ctx.fillStyle = "#000";
  ctx.textAlign = "center";
  ctx.textBaseline = "bottom";

  const firstTick = Math.ceil(xMin / TICK_EVERY) * TICK_EVERY;
  for (let t = firstTick; t <= xMax; t += TICK_EVERY) {
    const px = xScale(t);
    // tick
    ctx.beginPath();
    ctx.moveTo(px, AXIS_Y);
    ctx.lineTo(px, AXIS_Y - 8);
    ctx.stroke();
    // label
    ctx.fillText(String(t), px, AXIS_Y - 10);
  }
}

// draw an initial axis so the page isn’t blank
drawAxis();

// ===== controls =====
const $ = (id) => document.getElementById(id);
$("Previous").onclick = () => displayLine("stepBack");
$("Next").onclick     = () => displayLine("stepForward");
$("End").onclick      = () => displayLine("run");
$("Reset").onclick    = () => displayLine("clearAll");

const btnStart = $("play");
btnStart.addEventListener("click", () => {
  if (btnStart.textContent.trim() === "Play") {
    displayLine("play");
    btnStart.textContent = "Pause";
  } else {
    displayLine("pause");
    btnStart.textContent = "Play";
  }
});

// ===== load & init =====
runProgram();

async function runProgram() {
  await readTextFile("input");
  inputValues = (inputData || []).map(s => String(s).replace(/(\r\n|\n|\r)/gm, ""));
  await readTextFile("output");
  outputValues = outputData || [];

  $("the-queue").textContent = inputValues.join(",");
  $("the-head").textContent = head;

  // domain from head+outputs
  const allX = [Number(head), ...outputValues.map(Number)].filter(n => !Number.isNaN(n));
  if (allX.length) {
    xMin = Math.min(...allX);
    xMax = Math.max(...allX);
    const span = Math.max(xMax - xMin, 1);
    const pad = span * 0.05;
    xMin -= pad;
    xMax += pad;
    TICK_EVERY = chooseTick(xMax - xMin);
  }

  // clear & paint axis
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  drawAxis();

  index = 0;
  vIndex = 0;
  $("output-data").textContent = "";
}

// ===== drawing =====
function displayLine(type) {
  ctx.strokeStyle = "blue";
  ctx.lineWidth = 5;

  if (type === "play") {
    clearInterval(intervalID);
    intervalID = setInterval(stepForwardDraw, 1000);
    return;
  }
  if (type === "pause") {
    clearInterval(intervalID);
    return;
  }
  if (type === "stepForward") {
    stepForwardDraw();
    return;
  }
  if (type === "stepBack") {
    stepBackErase();
    return;
  }
  if (type === "clearAll") {
    clearInterval(intervalID);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    index = 0;
    vIndex = 0;
    $("output-data").textContent = "";
    drawAxis();
    return;
  }
  if (type === "run") {
    clearInterval(intervalID);
    while (index < outputValues.length) stepForwardDraw();
    return;
  }
}

function stepForwardDraw() {
  if (index >= outputValues.length) {
    clearInterval(intervalID);
    return;
  }

  const outX = xScale(Number(outputValues[index]));

  if (index === 0) {
    $("output-data").textContent = String(outputValues[index]);
    ctx.beginPath();
    vIndex += VSTEP;
    ctx.moveTo(xScale(Number(head)), VSTART_OFFSET + vIndex - VSTEP);
    ctx.lineTo(outX,                 VSTART_OFFSET + vIndex);
    ctx.stroke();
  } else {
    $("output-data").textContent += "," + String(outputValues[index]);
    ctx.beginPath();
    vIndex += VSTEP;
    ctx.moveTo(xScale(Number(outputValues[index - 1])), VSTART_OFFSET + vIndex - VSTEP);
    ctx.lineTo(outX,                                     VSTART_OFFSET + vIndex);
    ctx.stroke();
  }

  index++;
}

function stepBackErase() {
  // overdraw last segment in white
  ctx.lineWidth = 7;
  ctx.strokeStyle = "#ffffff";

  if (index >= 1) {
    if (index === 1) {
      // erase first segment (head -> output[0])
      const y2 = VSTART_OFFSET + vIndex;
      const y1 = y2 - VSTEP;
      ctx.beginPath();
      ctx.moveTo(xScale(Number(head)), y1);
      ctx.lineTo(xScale(Number(outputValues[0])), y2);
      ctx.stroke();
      index = 0;
      vIndex -= VSTEP;
      $("output-data").textContent = "";
    } else {
      const y2 = VSTART_OFFSET + vIndex;
      const y1 = y2 - VSTEP;
      const x2 = xScale(Number(outputValues[index - 1]));
      const x1 = xScale(Number(outputValues[index - 2]));
      ctx.beginPath();
      ctx.moveTo(x1, y1);
      ctx.lineTo(x2, y2);
      ctx.stroke();
      index--;
      vIndex -= VSTEP;
      $("output-data").textContent = outputValues.slice(0, index).join(",");
    }
  }

  // repaint axis in case the erase clipped labels/ticks
  drawAxis();

  // restore draw style
  ctx.strokeStyle = "blue";
  ctx.lineWidth = 5;
}
