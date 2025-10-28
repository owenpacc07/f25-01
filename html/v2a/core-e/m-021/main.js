// This file controls the visualization
// Page Replacement (FIFO)
import { loadData } from "./load_data.js";

let input, output;



/*
const HardCodedOutput =[7,7,`-`,`-`,1,``,
0,7,0,`-`,1,``,
1,7,0,1,1,``,
2,2,0,1,1,``,
0,2,0,1,0,``,
3,2,3,1,1,``,
0,2,3,0,1,``,
4,4,3,0,1,``,
2,4,2,0,1,``,
3,4,2,3,1,``,
0,0,2,3,1,``,
3,0,2,3,0,``,
2,0,2,3,0,``,
1,0,1,3,1,``,
2,0,1,2,1,``,
0,0,1,2,0,``,
1,0,1,2,0,``,
7,7,1,2,1,``,
0,7,0,2,1,``,
1,7,0,1,1,``,
15];
//formate of output explained: `-` is for "used in" values, `+` is for frames never allocated again
//empty string = end of line, Last slot in array is # of faults
const HardCodedInput = [7,0,1,2,0,3,0,4,2,3,0,3,2,1,2,0,1,7,0,1];
// input = HardCodedInput
// output = HardCodedOutput
*/
let overlay = document.getElementById("overlay");
overlay.style.display = "block";

await loadData()
.then((data) => {
    if (!data) {
      document.getElementById("text").innerText = "Could not load output data";
      throw new Error("Could not load output data");
    } else {
      overlay.style.display = "none";
      input = data.input;
      output = data.output;
      // console.log(input);
      // console.log(output);
    }
  })
  .catch((err) => {
    console.log(err);
    document.getElementById("text").innerText = "Could not load output data";
  });

  let slotSize = output[0].frames.length; // number of pages that can be allocated at once //output[0].frames.length


  function populateTable() {
    let table = document.getElementById("input_table_row");
    // table.appendChild(document.createElement("tr"));
    input.forEach((page, i) => {
      let cell = document.createElement("td");
      cell.innerText = page;
      cell.setAttribute("id", "page" + i);
      table.appendChild(cell);
    });
  }
  populateTable();
  
  /*****************************************************
   *
   * Animation functions
   *
   *****************************************************/
  
  // variables to control animation
  let outputTable = document.getElementById("output_table");
  let infoTableBody = document.getElementById("info_table_body");
  let nextPage = -1; // index of next page to be allocated
  let timeInterval = false; // used to play animation automatically
  let playButton = document.getElementById("play");
  let nextButton = document.getElementById("stepForward");
  let backButton = document.getElementById("stepBack");
  let resetButton = document.getElementById("skipBack");
  let endButton = document.getElementById("skipForward");
  let faultCount = 0;
  let faultCounter = document.getElementById("faultCounter_value");
  
  
  
  // initialize output table
  // page row
  let pageRow = document.createElement("tr");
  pageRow.setAttribute("id", "page");
  outputTable.appendChild(pageRow);
  // frame rows
  for (let i = 0; i < slotSize; i++) {
    let frameRow = document.createElement("tr");
    frameRow.setAttribute("id", "frame" + i);
    outputTable.appendChild(frameRow);
  }
  // faulted row
  let faultedRow = document.createElement("tr");
  faultedRow.setAttribute("id", "faulted");
  outputTable.appendChild(faultedRow);
  
  // initialize info table
  for (let i = 0; i < slotSize; i++) {
    let infoRow = document.createElement("tr");
    infoRow.setAttribute("id", "info" + i);
    infoTableBody.appendChild(infoRow);
  
    let infoPage = document.createElement("td");
    infoPage.setAttribute("id", "infoPage" + i);
    infoPage.innerText = "-";
    infoRow.appendChild(infoPage);
  
    
  }
  
  function refreshAnim() {
    changeAnimState("beginning");
  
    // turn off auto play
    if (timeInterval) 
      clearInterval(timeInterval); 
    timeInterval = false;
    playButton.innerText = "Play";
  
    nextPage = -1;
  
    // reset animation
    // reset input table
    for (let i = 0; i < input.length; i++) {
      let page = document.getElementById("page" + i);
      page.style.backgroundColor = "white";
    }
  
    // reset output table
    pageRow.innerHTML = "";
    for (let i = 0; i < slotSize; i++) {
      let frame = document.getElementById("frame" + i);
      frame.innerHTML = "";
    }
    faultedRow.innerHTML = "";
  
    // reset fault counter
    faultCount = 0;
    faultCounter.innerText = faultCount;
  
    // reset info table
    for (let i = 0; i < slotSize; i++) {
      let infoPage = document.getElementById("infoPage" + i);
      infoPage.innerText = "-";
     
    }
  }
  
  function nextAnim() {
    nextPage += 1;
    if (nextPage >= output.length){
      nextPage = output.length-1;
      return;
    }
  
    if(!timeInterval) { // as long as automatic play is not on
      changeAnimState("mid-step");
    }
  
    // highlight page in input table
    if (nextPage > 0) {
      document.getElementById("page" + (nextPage - 1)).style.backgroundColor = "lightgray";
    }
    document.getElementById("page" + nextPage).style.backgroundColor = "rgb(25, 226, 25)";
  
    let curData = output[nextPage];
    
    // update page row
    let pageCell = document.createElement("td");
    pageCell.innerText = curData.page;
    pageCell.style.fontWeight = "bold";
    pageRow.appendChild(pageCell);
  
    // update frame rows
    for (let i = 0; i < slotSize; i++) {
      let frameCell = document.createElement("td");
      frameCell.innerText = isNaN(curData.frames[i]) ? "-" : curData.frames[i];
      frameCell.style.borderInline = "1px solid black";
      // highlight replacing page
      if (curData.page == curData.frames[i]) {
        // set background color based on whether page swap occurred
        frameCell.style.backgroundColor = curData.faulted ? "#FFCCCB" : "#90EE90";
      }
      // top/bottom border
      if (i == 0) {
        frameCell.style.borderTop = "1px solid black";
      } else if (i == slotSize - 1) {
        frameCell.style.borderBottom = "1px solid black";
      }
      document.getElementById("frame" + i).appendChild(frameCell);
    }
  
    // update faulted row
    let faultedCell = document.createElement("td");
    faultedCell.innerText = curData.faulted ? "Fault" : "OK";
    if (curData.faulted) {
      faultedCell.style.color = "red";
    } else {
      faultedCell.style.color = "green";
    }
    faultedRow.appendChild(faultedCell);
  
    // update fault counter
    if (output[nextPage].faulted) {
      faultCount += 1;
      faultCounter.innerText = faultCount;
    }
  
    // update info table
    let prevFrames = (nextPage == 0) ? ['-','-','-'] : output[nextPage - 1].frames;
    let replaceIndex = curData.frames.indexOf(`${curData.page}`);
    // display the steps that pages in prev step are needed in
    for (let i = 0; i < prevFrames.length; i++) {
      let infoPage = document.getElementById("infoPage" + i);
     
      infoPage.innerText = prevFrames[i];
     
     
    }
    
    // update play buttons
    if (nextPage == output.length-1) {
      changeAnimState("end");
      clearInterval(timeInterval);
      timeInterval = false;
    }
  
  }
  
  function backAnim() {
    nextPage -= 1;
    if (nextPage < 0) {
      nextPage = 0;
      refreshAnim();
      return;
    } else {
      changeAnimState("mid-step");
    }
    if (nextPage >= 0) {
      document.getElementById("page" + nextPage).style.backgroundColor = "rgb(25, 226, 25)";
    }
    document.getElementById("page" + (nextPage + 1)).style.backgroundColor = "white";
  
    // update output table
    pageRow.removeChild(pageRow.lastChild);
    for (let i = 0; i < slotSize; i++) {
      document.getElementById("frame" + i).removeChild(document.getElementById("frame" + i).lastChild);
    }
    faultedRow.removeChild(faultedRow.lastChild);
  
    // update fault counter
    if (output[nextPage + 1].faulted) {
      faultCount -= 1;
      faultCounter.innerText = faultCount;
    }
  
    // update info table
    let curData = output[nextPage];
    let prevFrames = (nextPage <= 0) ? ['-','-','-'] : output[nextPage - 1].frames;
    let replaceIndex = curData.frames.indexOf(`${curData.page}`);
    // display the steps that pages in prev step are needed in
    for (let i = 0; i < prevFrames.length; i++) {
      let infoPage = document.getElementById("infoPage" + i);
      
      infoPage.innerText = prevFrames[i];

      
    }
  }
  
  function playAnim() {
    if (timeInterval) { // pause
      changeAnimState("mid-step");
      playButton.innerText = "Play";
      clearInterval(timeInterval);
      timeInterval = false;
    } else { // play
      changeAnimState("mid-auto"); 
      playButton.innerText = "Pause";
      timeInterval = setInterval(nextAnim, 1000);
    }
  }
  
  function endAnim() {
    // turn off auto play
    if (timeInterval) { clearInterval(timeInterval); }
    timeInterval = false;
    playButton.innerText = "Play";
  
    while (nextPage < output.length-1) {
      nextAnim();
    }
  }
  
  // Set up event listeners for animation buttons
  playButton.onclick = playAnim;
  nextButton.onclick = nextAnim;
  backButton.onclick = backAnim;
  resetButton.onclick = refreshAnim;
  endButton.onclick = endAnim;
  
  // change activity state of buttons based on current state of the animation
  function changeAnimState(state) {
    // console.log("changed anim state!");
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