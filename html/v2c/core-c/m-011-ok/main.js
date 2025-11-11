/* This file controls the visualization
   Memory Allocation (First Fit) */

import { loadData } from "./load_data.js";

//Set "loading data" overlay while data is loading
let overlay = document.getElementById("overlay");
overlay.style.display = "block";

let input, output;

//Load call load_data.js and load input / output data
await loadData().then((data) => {
  if (!data) {
    document.getElementById("text").innerText = "Could not load output data";
    throw new Error("Could not load output data");
  } else {
    overlay.style.display = "none";
    input = data.input;
    output = data.output;
  }
  ps = input.processes.map(p => p.size);
  ms = input.memSlots.map(m => [(m.end - m.start), false]);
});


var animList = [];

var paused = true; //at the start, animation is technically paused

var animSpeed = 1000;
var animLock = false;
var startLock = false;

var memtblpos;





// fills process and memory slots with data
function displayProcsnMems() {
  let procshtml = "";
  let memshtml = "";
  ps.forEach(function (val, index) {
    let val2 = index + 1;
    procshtml +=
      '<div id="p' +
      val2 +
      '"\r\nstyle="display: inline-block; width: 100px; height: 50px; background-color: #E0DDD8; position: absolute; left: 35px; top: ' +
      (100 + 60 * index) +
      'px; z-index: 99999;">\r\n' +
      '<div class="align-items-center justify-content-center"\r\n' +
      'style="display: inline-flex; width: 100%; height: 100%; text-align: center; height: 100%;">\r\n' +
      '<span id="p' +
      val2 +
      'span">P' +
      val2 +
      ": " +
      val +
      "</span>\r\n" +
      '<span id="p' +
      val2 +
      'hide" class="badge badge-light" style="right: 0; bottom: 0;position: absolute; background-color: red; display: none;">0</span>\r\n            </div>\r\n        </div>';
  }, this);

  ms.forEach(function (val, index) {
    let val2 = index + 1;
    memshtml +=
      '<tr id="m' +
      val2 +
      '" style="height: 70px;">\r\n<td id="m' +
      val2 +
      'span"\r\nstyle="text-align: center; border-right: 1px solid; border-bottom: 1px solid; width: 80px;">M' +
      val2 +
      ": " +
      val[0] +
      "\r\n</td>\r\n" +
      '<td id="m' +
      val2 +
      'slot"\r\nstyle="vertical-align:bottom; text-align: center; width: 100px; border-bottom: 1px solid;"></td>\r\n</tr>';
  }, this);

  $("#procsarea").html(procshtml);
  $("#memtbl").html(memshtml);
}

displayProcsnMems();

$(document).ready(function () {
  memtblpos = $("#memdiv").position();

  /**********************************************************************
   *
   * Button functionality
   *
   **********************************************************************/

  // make all buttons inactive by default
  $('#animNavBtns').children().prop("disabled", true);
  $('#animNavBtns').children(0).prop("disabled", false);
  // Does stuff based on which button was pressed
  $('#animNavBtns button').on('click', function (e) {
    let pressed = e.target.id
    // then do stuff based on button pressed
    switch (pressed) {
      case 'refresh':
        window.location.reload(true);
        break;
      case 'play':    
        if (paused) { 
          changeAnimState('mid-auto'); //changes button text to Pause
          paused = false;
          animSpeed = 1000;
          var time = 0; 
          for(let i = 0; i<input.processes.length; i++) {
            setTimeout(animate,time);
            time = time + 1500;
          }    
        } else {
          changeAnimState('mid-step'); //changes button text to Play
          paused = true;       
        }
        break;
      case 'stepBack':
        changeAnimState('mid-step');
        animSpeed = 1000; //reset speed to default
        paused = true;
        if (animList.length < 1) {
          return;
        }
        if (animList.length == 1) {
          changeAnimState('beginning');
        }
        Undo(animList[animList.length - 1], 1);
        break;
      case 'skipBack':
        changeAnimState('beginning');
        animSpeed = 100; // set speed faster
        paused = true; //clicking reset "pauses" the animation
        for (let i = animList.length - 1; i >= 0; i--) {
          Undo(animList[i]);
        }
        currStep = 0;
        occupied = [false, false, false];
        break;
      case 'stepForward':
        changeAnimState('mid-step');
        animSpeed = 1000; // reset to default
        paused = false;
        animate(0);
        paused = true;
        break;
      case 'skipForward':
        changeAnimState('end');
        paused = false;
        animSpeed = 100; // set speed faster
        for(let i=0; i<input.processes.length; i++) {
          animate();
        }
        paused = true;
        break;
    }
  });
  // change activity state of buttons based on current state of the animation
  function changeAnimState(state) {
    console.log("changed anim state!");
    switch (state) {
      case "beginning":
        // console.log("beginning!");
        $('#skipBack').prop("disabled", true);
        $('#stepBack').prop("disabled", true);
        $('#play').prop("disabled", false).text("Play");
        $('#stepForward').prop("disabled", false);
        $('#skipForward').prop("disabled", false);
        break;
      case "end":
        // console.log("end!");
        $('#skipBack').prop("disabled", false);
        $('#stepBack').prop("disabled", false);
        $('#play').prop("disabled", true).text("Play");
        $('#stepForward').prop("disabled", true);
        $('#skipForward').prop("disabled", true);
        break;
      case "mid-step":
        // console.log("mid-step!");
        $('#skipBack').prop("disabled", false);
        $('#stepBack').prop("disabled", false);
        $('#play').prop("disabled", false).text("Play");
        $('#stepForward').prop("disabled", false);
        $('#skipForward').prop("disabled", false);
        break;
      case "mid-auto":
        // console.log("mid-auto!");
        $('#skipBack').prop("disabled", false);
        $('#stepBack').prop("disabled", true);
        $('#play').prop("disabled", false).text("Pause");
        $('#stepForward').prop("disabled", true);
        $('#skipForward').prop("disabled", false);
        break;
    }
  }
  // start @ beginning state
  changeAnimState('beginning');

  /**********************************************************************
   *
   * Visualization controls
   *
   **********************************************************************/

  function getRandomColor() {
    var color = "#ACFF33";
    return color;
  }

  var currStep = 0;

  // move visualization one step forward
  function animate() {
    
    if (paused) return;
    
    let curMemSlot = output[currStep];
    let memSlotOriginal = input.memSlots.find((memSlots) => memSlots.start == curMemSlot.start);

    if (curMemSlot) { 
      moveProcToMem(
        `p${curMemSlot.id}`, // pid
        `m${input.memSlots.indexOf(memSlotOriginal)+1}`, // mid
        memSlotOriginal.end - curMemSlot.end // remaining empty memory
      );

    } else { // mark process as unallocated
      animList.push({
        proc: $("#p" + (currStep + 1)),
        mem: "dot",
        top: $("#p" + (currStep + 1)).position().top,
      });
      displayDot("p" + (currStep + 1));
    }

    currStep++;
    //check to see if we just completed all the processes
    if(currStep >= output.length) {
      changeAnimState("end");
    }
   }

  function moveProcToMem(proc, mem, rem) {
    console.log("proc: " + proc + " mem: " + mem + " rem: " + rem);
    $("#" + proc).css({ backgroundColor: getRandomColor() });
    var clonedProc = $("#" + proc).clone();
    $("#animarea").append(clonedProc);
    var procs = 0;
    animList.forEach(function (val, index, arr) {
      if (val.mem == mem) procs++;
    }, this);
    animList.push({
      org: $("#" + proc),
      proc: clonedProc,
      mem: mem,
      top: clonedProc.position().top,
    });
    clonedProc.animate(
      {
        left: memtblpos.left + $("#" + mem).position().left + 80,
        top: memtblpos.top + $("#" + mem).position().top + procs * 50,
      },
      animSpeed,
      function () {
        animLock = false; 
        $("#" + mem + "slot").text("" + rem);
        if (currStep > ps.length - 1) return;
      }
    );
    if (rem > 0) {
      //$('#' + mem).animate({ height: $('#' + mem).height() + 50 });
    }
  }

  function displayDot(proc) {
    $("#" + proc + "hide").show();
  }

  function hideDot(proc) {
    $("#" + proc + "hide").hide();
  }

  function Undo(anim, rem) {
    //remove remaining memory text 
    $("#" + anim.mem + "slot").text(" ");
  
    if (currStep > 0) currStep--;

    if (anim.mem != "dot") ms[parseInt(anim.mem.slice(1)) - 1][1] = false;
    
    var procs = 0;
    animList.forEach(function (val, index, arr) {
      if (val.mem == anim.mem) procs++;
    }, this);
    if (anim.mem == "dot") hideDot(anim.proc.attr("id"));
    anim.proc.animate({ left: 35, top: anim.top }, animSpeed, function () {
      animLock = false;
      animList.pop();
      anim.proc.css({ backgroundColor: "#E0DDD8" });
      if (anim.mem != "dot") {
        anim.proc.remove();
        anim.org.css({ backgroundColor: "#E0DDD8" });
      }
      procs = 0;
      animList.forEach(function (val, index, arr) {
        if (val.mem == anim.mem) procs++;
      }, this);
    });
    if (procs >= 1) {
      //$('#' + anim.mem).animate({ height: $('#' + anim.mem).height() - 50 });
    }
  }
});
