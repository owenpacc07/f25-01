import { readTextFile, head, inputData, outputData } from "./load_data.js";

/* Contributor Spring 2023 - Dakota Marino */

// state
var inputValues = [];
var outputValues = [];
var index = 0;
var vIndex = 0;
var intervalID = null;

// canvas
const canvas = document.getElementById("myCanvas");
const context = canvas.getContext("2d");

// constants
const VSTEP = 25;
const VSTART_OFFSET = 50;
const PADDING_LEFT = 30;
const PADDING_RIGHT = 20;
const AXIS_Y = 25;

// axis + scale
let xMin = 0;
let xMax = 700;
let TICK_EVERY = 100;

function xScale(x) {
  if (xMax === xMin) return PADDING_LEFT;
  const usable = canvas.width - (PADDING_LEFT + PADDING_RIGHT);
  return PADDING_LEFT + ((Number(x) - xMin) / (xMax - xMin)) * usable;
}

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

function drawAxis() {
  context.strokeStyle = "#2b3a67";
  context.lineWidth = 2;
  context.beginPath();
  context.moveTo(PADDING_LEFT, AXIS_Y);
  context.lineTo(canvas.width - PADDING_RIGHT, AXIS_Y);
  context.stroke();

  context.lineWidth = 1.5;
  context.font = "12px sans-serif";
  context.fillStyle = "#000";
  context.textAlign = "center";
  context.textBaseline = "bottom";

  const firstTick = Math.ceil(xMin / TICK_EVERY) * TICK_EVERY;
  for (let t = firstTick; t <= xMax; t += TICK_EVERY) {
    const px = xScale(t);
    context.beginPath();
    context.moveTo(px, AXIS_Y);
    context.lineTo(px, AXIS_Y - 8);
    context.stroke();
    context.fillText(String(t), px, AXIS_Y - 10);
  }
}

// initial axis
drawAxis();

// buttons
let Previous = document.getElementById("Previous");
Previous.onclick = function () {
  displayLine("stepBack");
};

let Next = document.getElementById("Next");
Next.onclick = function () {
  displayLine("stepForward");
};

let End = document.getElementById("End");
End.onclick = function () {
  displayLine("run");
};

let reset = document.getElementById("Reset");
reset.onclick = function () {
  displayLine("clearAll");
};

const btnStart = document.getElementById("play");
btnStart.addEventListener("click", function () {
  if (btnStart.innerHTML.trim() === "Play") {
    displayLine("play");
    btnStart.innerHTML = "Pause";
  } else if (btnStart.innerHTML.trim() === "Pause") {
    displayLine("pause");
    btnStart.innerHTML = "Play";
  }
});

// boot
runProgram();

async function runProgram() {
  await readTextFile("input");
  inputValues = inputData;
  await readTextFile("output");
  outputValues = outputData;

  document.getElementById("the-queue").innerHTML = inputValues;
  document.getElementById("the-head").innerHTML = head;

  const allX = [Number(head), ...outputValues.map(Number)].filter(
    (n) => !Number.isNaN(n),
  );
  if (allX.length) {
    xMin = Math.min(...allX);
    xMax = Math.max(...allX);
    const span = Math.max(xMax - xMin, 1);
    const pad = span * 0.05;
    xMin -= pad;
    xMax += pad;
    TICK_EVERY = chooseTick(xMax - xMin);
  }

  context.clearRect(0, 0, canvas.width, canvas.height);
  drawAxis();
  index = 0;
  vIndex = 0;
  document.getElementById("output-data").innerHTML = "";
}

// viz controller
function displayLine(type) {
  context.strokeStyle = "blue";
  context.lineWidth = 5;

  if (type == "play") {
    clearInterval(intervalID);
    intervalID = setInterval(function () {
      stepForwardDraw();
    }, 1000);
  } else if (type == "pause") {
    clearInterval(intervalID);
  } else if (type == "stepForward") {
    stepForwardDraw();
  } else if (type == "stepBack") {
    stepBackErase();
  } else if (type == "clearAll") {
    clearInterval(intervalID);
    context.clearRect(0, 0, canvas.width, canvas.height);
    index = 0;
    vIndex = 0;
    document.getElementById("output-data").innerHTML = "";
    drawAxis();
  } else if (type == "run") {
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
  if (index == 0) {
    document.getElementById("output-data").innerHTML = outputValues[index];
    vIndex += VSTEP;
    context.beginPath();
    context.moveTo(xScale(Number(head)), VSTART_OFFSET + vIndex - VSTEP);
    context.lineTo(outX, VSTART_OFFSET + vIndex);
    context.stroke();
  } else {
    document.getElementById("output-data").innerHTML =
      document.getElementById("output-data").innerHTML +
      "," +
      outputValues[index];
    vIndex += VSTEP;
    context.beginPath();
    context.moveTo(
      xScale(Number(outputValues[index - 1])),
      VSTART_OFFSET + vIndex - VSTEP,
    );
    context.lineTo(outX, VSTART_OFFSET + vIndex);
    context.stroke();
  }
  index++;
}

function stepBackErase() {
  context.lineWidth = 7;
  context.strokeStyle = "#ffffff";

  if (index >= 1) {
    if (index == 1) {
      document.getElementById("output-data").innerHTML = "";
      const y2 = VSTART_OFFSET + vIndex;
      const y1 = y2 - VSTEP;
      context.beginPath();
      context.moveTo(xScale(Number(head)), y1);
      context.lineTo(xScale(Number(outputValues[0])), y2);
      context.stroke();
      index--;
      vIndex -= VSTEP;
    } else {
      document.getElementById("output-data").innerHTML = outputValues
        .slice(0, index - 1)
        .join(",");
      const y2 = VSTART_OFFSET + vIndex;
      const y1 = y2 - VSTEP;
      const x2 = xScale(Number(outputValues[index - 1]));
      const x1 = xScale(Number(outputValues[index - 2]));
      context.beginPath();
      context.moveTo(x1, y1);
      context.lineTo(x2, y2);
      context.stroke();
      index--;
      vIndex -= VSTEP;
    }
  }

  drawAxis();
  context.strokeStyle = "blue";
  context.lineWidth = 5;
}
