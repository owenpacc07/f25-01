/* This file controls the visualization
   Memory Allocation (First/Best/Worst Fit with splitting & reuse)
   - Containment row mapping (start ? [slot.start, slot.end))
   - Unallocated process handling
   - Remaining space display per row
   - Row height grows with stacked processes
   - Realigns all placed boxes after row height changes
*/

import { loadData } from "./load_data.js";

// ===== UI constants =====
const BASE_ROW_HEIGHT = 70;   // default table row height (px)
const PER_PROC_STACK  = 50;   // extra height per stacked process (px)
const PROC_STACK_OFFSET = 50; // vertical offset per stacked box (px)

// Show "loading" overlay while data is loading
let overlay = document.getElementById("overlay");
overlay.style.display = "block";

let input, output;

// Load input / output data
await loadData().then((data) => {
  if (!data) {
    document.getElementById("text").innerText = "Could not load output data";
    throw new Error("Could not load output data");
  } else {
    overlay.style.display = "none";
    input  = data.input;   // { memSlots: [{start,end}], processes: [{id,size}] }
    output = data.output;  // [{start,end,id}] only for allocated processes
  }
});

// ==============================
// Prep: arrays for display
// ==============================

// Build memory slot list from input slots (NOT from processes)
let ms = []; // each = [slotSize, usedFlag]
for (let i = 0; i < input.memSlots.length; i++) {
  const s = input.memSlots[i];
  ms.push([s.end - s.start, false]);
}

// Process sizes (for labels)
let ps = input.processes.map(p => p.size);

// Build a process-indexed allocation array so we animate every process in order
const procIndexById = new Map();
input.processes.forEach((p, idx) => procIndexById.set(p.id, idx));
const allocByProcessIndex = new Array(input.processes.length).fill(null);
output.forEach((alloc) => {
  const idx = procIndexById.get(alloc.id);
  if (idx !== undefined) allocByProcessIndex[idx] = alloc; // {start,end,id}
});

var animList = [];          // placed boxes (for undo/realign)
var paused = true;
var animSpeed = 1000;
var animLock = false;
var startLock = false;
var memtblpos;

// ==============================
// Fill process and memory slots
// ==============================
function displayProcsnMems() {
  let procshtml = "";
  let memshtml  = "";

  ps.forEach(function (val, index) {
    const val2 = index + 1;
    procshtml +=
      '<div id="p' + val2 + '" ' +
      'style="display:inline-block;width:100px;height:50px;background-color:#E0DDD8;position:absolute;left:35px;top:' +
      (100 + 60 * index) + 'px;z-index:99999;">' +
        '<div class="align-items-center justify-content-center" ' +
        'style="display:inline-flex;width:100%;height:100%;text-align:center;">' +
          '<span id="p' + val2 + 'span">P' + val2 + ': ' + val + '</span>' +
          '<span id="p' + val2 + 'hide" class="badge badge-light" ' +
          'style="right:0;bottom:0;position:absolute;background-color:red;display:none;">0</span>' +
        '</div>' +
      '</div>';
  });

  ms.forEach(function (val, index) {
    const val2 = index + 1;
    memshtml +=
      '<tr id="m' + val2 + '" style="height:' + BASE_ROW_HEIGHT + 'px;">' +
        '<td id="m' + val2 + 'span" ' +
        'style="text-align:center;border-right:1px solid;border-bottom:1px solid;width:80px;">' +
          'M' + val2 + ': ' + val[0] +
        '</td>' +
        '<td id="m' + val2 + 'slot" ' +
        'style="vertical-align:bottom;text-align:center;width:100px;border-bottom:1px solid;"></td>' +
      '</tr>';
  });

  $("#procsarea").html(procshtml);
  $("#memtbl").html(memshtml);
}
displayProcsnMems();

$(document).ready(function () {
  memtblpos = $("#memdiv").position();

  /**********************************************************************
   * Buttons
   **********************************************************************/
  $('#animNavBtns').children().prop("disabled", true);
  $('#animNavBtns').children(0).prop("disabled", false);

  $('#animNavBtns button').on('click', function (e) {
    let pressed = e.target.id;
    switch (pressed) {
      case 'refresh':
        window.location.reload(true);
        break;
      case 'play':
        if (paused) {
          changeAnimState('mid-auto');
          paused = false;
          animSpeed = 1000;
          let time = 0;
          for (let i = 0; i < input.processes.length; i++) {
            setTimeout(animate, time);
            time += 1500;
          }
        } else {
          changeAnimState('mid-step');
          paused = true;
        }
        break;
      case 'stepBack':
        changeAnimState('mid-step');
        animSpeed = 1000;
        paused = true;
        if (animList.length < 1) return;
        if (animList.length == 1) changeAnimState('beginning');
        Undo(animList[animList.length - 1], 1);
        break;
      case 'skipBack':
        changeAnimState('beginning');
        animSpeed = 100;
        paused = true;
        for (let i = animList.length - 1; i >= 0; i--) {
          Undo(animList[i]);
        }
        currStep = 0;
        break;
      case 'stepForward':
        changeAnimState('mid-step');
        animSpeed = 1000;
        paused = false;
        animate(0);
        paused = true;
        break;
      case 'skipForward':
        changeAnimState('end');
        paused = false;
        animSpeed = 100;
        for (let i = 0; i < input.processes.length; i++) animate();
        paused = true;
        break;
    }
  });

  function changeAnimState(state) {
    switch (state) {
      case "beginning":
        $('#skipBack').prop("disabled", true);
        $('#stepBack').prop("disabled", true);
        $('#play').prop("disabled", false).text("Play");
        $('#stepForward').prop("disabled", false);
        $('#skipForward').prop("disabled", false);
        break;
      case "end":
        $('#skipBack').prop("disabled", false);
        $('#stepBack').prop("disabled", false);
        $('#play').prop("disabled", true).text("Play");
        $('#stepForward').prop("disabled", true);
        $('#skipForward').prop("disabled", true);
        break;
      case "mid-step":
        $('#skipBack').prop("disabled", false);
        $('#stepBack').prop("disabled", false);
        $('#play').prop("disabled", false).text("Play");
        $('#stepForward').prop("disabled", false);
        $('#skipForward').prop("disabled", false);
        break;
      case "mid-auto":
        $('#skipBack').prop("disabled", false);
        $('#stepBack').prop("disabled", true);
        $('#play').prop("disabled", false).text("Pause");
        $('#stepForward').prop("disabled", true);
        $('#skipForward').prop("disabled", false);
        break;
    }
  }
  changeAnimState('beginning');

  /**********************************************************************
   * Visualization helpers
   **********************************************************************/
  function getRandomColor() {
    return "#ACFF33";
  }

  // Which original memory slot contains this address?
  function findMemRowByAddress(addr) {
    for (let i = 0; i < input.memSlots.length; i++) {
      const m = input.memSlots[i];
      if (addr >= m.start && addr < m.end) return i;
    }
    return -1;
  }

  // Grow/shrink row based on how many procs are stacked in that row
  function adjustRowHeight(memId) {
    const count = animList.filter(item => item.mem === memId).length;
    const extra = Math.max(0, count - 1) * PER_PROC_STACK;
    const newH = BASE_ROW_HEIGHT + extra;
    $("#" + memId).css("height", newH + "px");
  }

  // After any row height change, realign ALL placed boxes to their rows
  function realignAllPlaced() {
    memtblpos = $("#memdiv").position();
    const byRow = new Map();
    animList.forEach((item) => {
      if (item.mem === "dot") return; // unallocated stay put
      if (!byRow.has(item.mem)) byRow.set(item.mem, []);
      byRow.get(item.mem).push(item);
    });
    byRow.forEach((items, memId) => {
      const rowTop = memtblpos.top + $("#" + memId).position().top;
      items.forEach((item, k) => {
        const targetTop = rowTop + k * PROC_STACK_OFFSET;
        item.proc.css("top", targetTop + "px"); // snap (no animation)
      });
    });
  }

  /**********************************************************************
   * Visualization controls
   **********************************************************************/
  var currStep = 0;

  // One animation step per process (in original order)
  function animate() {
    if (paused) return;

    const curAlloc = allocByProcessIndex[currStep]; // {start,end,id} or null
    const procId = `p${currStep + 1}`;

    if (curAlloc) {
      const rowIdx = findMemRowByAddress(curAlloc.start);
      if (rowIdx !== -1) {
        const memId = `m${rowIdx + 1}`;
        // Remaining = original slot end - this allocation's end
        let rem = input.memSlots[rowIdx].end - curAlloc.end;
        if (rem < 0) rem = 0;
        moveProcToMem(procId, memId, rem);
      } else {
        // No containing row found (defensive) -> mark unallocated
        animList.push({
          proc: $("#" + procId),
          mem: "dot",
          top: $("#" + procId).position().top,
        });
        displayDot(procId);
      }
    } else {
      // Unallocated process
      animList.push({
        proc: $("#" + procId),
        mem: "dot",
        top: $("#" + procId).position().top,
      });
      displayDot(procId);
    }

    currStep++;
    if (currStep >= input.processes.length) {
      changeAnimState("end");
    }
  }

  function moveProcToMem(proc, mem, rem) {
    $("#" + proc).css({ backgroundColor: getRandomColor() });
    const clonedProc = $("#" + proc).clone();
    $("#animarea").append(clonedProc);

    // how many already placed in this mem row?
    let procsInRow = 0;
    animList.forEach((val) => { if (val.mem === mem) procsInRow++; });

    animList.push({
      org: $("#" + proc),
      proc: clonedProc,
      mem: mem,
      top: clonedProc.position().top,
    });

    // Expand the row to fit stacks and then realign everything
    adjustRowHeight(mem);
    realignAllPlaced();

    clonedProc.animate(
      {
        left: memtblpos.left + $("#" + mem).position().left + 80,
        top:  memtblpos.top  + $("#" + mem).position().top + procsInRow * PROC_STACK_OFFSET,
      },
      animSpeed,
      function () {
        animLock = false;
        $("#" + mem + "slot").text("" + rem);
        if (currStep > ps.length - 1) return;
      }
    );
  }

  function displayDot(proc) { $("#" + proc + "hide").show(); }
  function hideDot(proc)    { $("#" + proc + "hide").hide(); }

  function Undo(anim, rem) {
    // clear remaining text for that row
    $("#" + anim.mem + "slot").text(" ");

    if (currStep > 0) currStep--;

    if (anim.mem !== "dot") {
      ms[parseInt(anim.mem.slice(1)) - 1][1] = false;
    }

    if (anim.mem === "dot") hideDot(anim.proc.attr("id"));

    anim.proc.animate({ left: 35, top: anim.top }, animSpeed, function () {
      animLock = false;
      animList.pop();
      anim.proc.css({ backgroundColor: "#E0DDD8" });
      if (anim.mem !== "dot") {
        anim.proc.remove();
        anim.org.css({ backgroundColor: "#E0DDD8" });
      }

      // Shrink row height if needed
      if (anim.mem !== "dot") adjustRowHeight(anim.mem);

      // Keep all boxes aligned after geometry change
      realignAllPlaced();
    });
  }

  // Keep things aligned if window resizes
  window.addEventListener('resize', realignAllPlaced);
});
