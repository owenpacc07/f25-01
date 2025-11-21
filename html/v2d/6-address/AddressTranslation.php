<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Address Translation</title>
  <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
  <style>
    .main {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }
    #options {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: row;
    }
    .option {
      margin-right: 5px;
    }
    .option-label {
      margin-right: 20px;
    }
    #options-label {
      margin-right: 20px;
    }
    #arrow {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }
    table {
      table-layout: fixed;
    }
    .button-panel {
      margin-top: 20px;
      margin-bottom: 20px;
    }
    .input-field {
      display: flex;
      flex-direction: column;
      margin-bottom: 20px;
    }
    .content {
      margin-left: 20px;
      margin-right: 20px;
    }
  </style>
</head>
<body>
  <?php include '../navbar.php'; ?>
  <div id="app">
    <div class="main">
      <h1>Address Translation</h1>
      <div id="options">
        <b id="options-label">Select Method:</b>
        <input class="option" type="radio" id="paging" value="paging" v-model="option">
        <label class="option-label" for="male">Paging</label><br>
        <input class="option" type="radio" id="segmentation" value="segmentation" v-model="option">
        <label class="option-label" for="segmentation">Segmentation</label><br>
      </div>
      <hr>
      <!--Animation Control Type Form-->
      <div class="d-flex align-items-center justify-content-center">
            <form id="animationType">
		<h5 class="pr-3">Select Animation Type: </h5>
                <input type="radio" name="animationType" v-model="chosen" value="StepByStep" checked/> Step By Step<br/>
                <input type="radio" name="animationType" v-model="chosen" value="Automatic"/> Automatic<br/>
            </form>
        </div><hr>

      <div class="button-panel">
        
        
        <span class="button-panel" v-if="chosen==='Automatic'">
          <button v-if="atStepZero" type="button" class="btn btn-primary" v-on:click="generateInputClicked">Generate INPUT(data)</button>
          <button v-else disabled type="button" class="btn btn-primary">Generate INPUT(data)</button>
          <button disabled type="button" class="btn btn-primary" v-on:click="nextStepClicked">NEXT STEP</button>
          <button type="button" class="btn btn-primary" v-on:click="playClicked">PLAY</button>
          <button type="button" class="btn btn-primary" v-on:click="pauseClicked">PAUSE</button>
          <button type="button" class="btn btn-primary" v-on:click="endClicked">&gt&gt END</button> 
        </span>
        <span class="button-panel" v-else-if="chosen==='StepByStep'">
          <button v-if="atStepZero" type="button" class="btn btn-primary" v-on:click="generateInputClicked">Generate INPUT(data)</button>
          <button v-else disabled type="button" class="btn btn-primary">Generate INPUT(data)</button>
          <button type="button" class="btn btn-primary" v-on:click="nextStepClicked">NEXT STEP</button>
          <button  disabled type="button" class="btn btn-primary" v-on:click="playClicked">PLAY</button>
          <button  disabled type="button" class="btn btn-primary" v-on:click="pauseClicked">PAUSE</button>
          <button type="button" class="btn btn-primary" v-on:click="endClicked">&gt&gt END</button> 
        </span>
        <span class="button-panel" v-else>
          <button v-if="atStepZero" type="button" class="btn btn-primary" v-on:click="generateInputClicked">Generate INPUT(data)</button>
          <button v-else disabled type="button" class="btn btn-primary">Generate INPUT(data)</button>
          <button type="button" class="btn btn-primary" v-on:click="nextStepClicked">NEXT STEP</button>
          <button type="button" class="btn btn-primary" v-on:click="playClicked">PLAY</button>
          <button type="button" class="btn btn-primary" v-on:click="pauseClicked">PAUSE</button>
          <button type="button" class="btn btn-primary" v-on:click="endClicked">&gt&gt END</button> 
        </span>
      </div>
    </div>
    <div class="content">
      <div class="row">
        <div class="col">
          <p id="l-memory-label">Logical Address:</p>
        </div>
        <div class="col"></div>
        <div class="col">
          <p id="p-memory-label">Physical Address:</p>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="input-group">
            <div class="input-field">
              <b v-if="pagingIsSelected">Page #</b>
              <b v-if="segmentationIsSelected">Segment #</b>
              <input v-if="pagingIsSelected" disabled id="page-num-input" v-model="pageNumInput">
              <input v-if="segmentationIsSelected" disabled id="segment-num-input" v-model="segmentNumInput">
            </div>
            <div class="input-field">
              <b>Offset</b>
              <input disabled v-model="offsetInput">
            </div>
          </div>
        </div>
        <div class="col">
          <div id="arrow">
            <h1>------></h1>
          </div>
        </div>
        <div class="col">
          <div class="input-group">
            <div v-if="pagingIsSelected" class="input-field">
              <b>Frame #</b>
              <input disabled id="frame-num-output" v-model="frameNumOutput">
            </div>
            <div v-if="segmentationIsSelected" class="input-field">
              <b>Base</b>
              <input disabled id="base-output" v-model="base">
            </div>
            <div class="input-field">
              <b>Offset</b>
              <input disabled v-model="offsetInput">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container"></div>
        <div class="row">
          <div class="col">
            <div v-if="pagingIsSelected" id="l-memory-box">
              <h3>Logical Memory</h3>
              <table id="l-memory-table" class="table table-bordered">
                <thead>
                  <th>Page Number</th>
                  <th>Data</th>
                </thead>
                <tbody>
                  <tr v-for="entry in pagingLogicalMemory">
                    <td style="background-color: rgba(51, 153, 255, .3);">{{entry.pageNum}}</td>
                    <td style="background-color: rgba(192, 192, 192, .3)">{{entry.data}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="segmentationIsSelected">
              <h3>Logical Memory</h3>
              <table id="l-memory-table-segmentation" class="table table-bordered">
                <thead>
                  <th>Segment Number</th>
                  <th>Data</th>
                </thead>
                <tbody>
                  <tr v-for="entry in segmentationLogicalMemory">
                    <td style="background-color: rgba(51, 153, 255, .3);">{{entry.segmentNum}}</td>
                    <td style="background-color: rgba(192, 192, 192, .3)">{{entry.data}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col">
            <div v-if="pagingIsSelected">
              <h3>Page Table</h3>
              <table id="page-table" class="table table-bordered">
                <thead>
                  <th>Page Number</th>
                  <th>Frame Number</th>
                </thead>
                <tbody>
                  <tr v-for="entry in pageTable">
                    <td>{{entry.pageNum}}</td>
                    <td>{{entry.frameNum}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="segmentationIsSelected">
              <h3>Segment Table</h3>
              <table id="segmentation-table" class="table table-bordered">
                <thead>
                  <th>Segment Number</th>
                  <th>Base</th>
                  <th>Limit</th>
                </thead>
                <tbody>
                  <tr v-for="entry in segmentTable">
                    <td>{{entry.segmentNum}}</td>
                    <td>{{entry.base}}</td>
                    <td>{{entry.limit}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col">
            <div v-if="pagingIsSelected">
              <div id="p-memory-box">
                <table id="p-memory-table" class="table table-bordered">
                  <h3>Physical Memory</h3>
                  <thead>
                    <th>Frame Number</th>
                    <th>Address</th>
                    <th>Data</th>
                  </thead>
                  <tbody>
                    <tr v-for="entry in pagingPhysicalMemory">
                      <td>{{entry.frameNum}}</td>
                      <td>{{entry.address}}</td>
                      <td>{{entry.data}}</td>
                    </tr>
                  </tbody>
                </table>             
              </div>
            </div>
            <div v-if="segmentationIsSelected">
              <div id="p-memory-box">
                <table id="p-memory-table-segmentation" class="table table-bordered">
                  <h3>Physical Memory</h3>
                  <thead>
                    <th>Address</th>
                    <th>Data</th>
                  </thead>
                  <tbody>
                    <tr v-for="entry in segmentationPhysicalMemory">
                      <td>{{entry.address}}</td>
                      <td>{{entry.data}}</td>
                    </tr>
                  </tbody>
                </table>             
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type = "text/javascript">
    var app = new Vue({
      el: '#app',
      data: {
        chosen: "",
        automatic: false,
        stepbystep: false,
        temp: false,
        option: "paging",
        step: 0,
        pageNumInput: "1",
        segmentNumInput: "1",
        offsetInput: "1000",
        frameNumOutput: "",
        base: "",
        limit: "",
        interval: null,
        pagingLogicalMemory: [
          {
            selected: true,
            pageNum: 0,
            data: "bird"
          },
          {
            selected: false,
            pageNum: 1,
            data: "stem"
          },
          {
            selected: false,
            pageNum: 2,
            data: "leaf"
          },
          {
            selected: false,
            pageNum: 3,
            data: "rose"
          },
        ],
        pageTable: [
          {
            pageNum: 0,
            frameNum: 1
          },
          {
            pageNum: 1,
            frameNum: 3
          },
          {
            pageNum: 2,
            frameNum: 6
          },
          {
            pageNum: 3,
            frameNum: 4
          }
        ],
        pagingPhysicalMemory: [
          {
            frameNum: 1,
            address: 1000,
            data: "b"
          },
          {
            frameNum: 1,
            address: 1001,
            data: "i"
          },
          {
            frameNum: 1,
            address: 1002,
            data: "r"
          },
          {
            frameNum: 1,
            address: 1003,
            data: "d"
          },
          {
            frameNum: 2,
            address: 1004,
            data: ""
          },
          {
            frameNum: 2,
            address: 1005,
            data: ""
          },
          {
            frameNum: 2,
            address: 1006,
            data: ""
          },
          {
            frameNum: 2,
            address: 1007,
            data: ""
          },
          {
            frameNum: 3,
            address: 1008,
            data: "s"
          },
          {
            frameNum: 3,
            address: 1009,
            data: "t"
          },
          {
            frameNum: 3,
            address: 1010,
            data: "e"
          },
          {
            frameNum: 3,
            address: 1011,
            data: "m"
          },
          {
            frameNum: 4,
            address: 1012,
            data: "r"
          },
          {
            frameNum: 4,
            address: 1013,
            data: "o"
          },
          {
            frameNum: 4,
            address: 1014,
            data: "s"
          },
          {
            frameNum: 4,
            address: 1015,
            data: "e"
          },
          {
            frameNum: 5,
            address: 1016,
            data: ""
          },
          {
            frameNum: 5,
            address: 1017,
            data: ""
          },
          {
            frameNum: 5,
            address: 1018,
            data: ""
          },
          {
            frameNum: 5,
            address: 1019,
            data: ""
          },
          {
            frameNum: 6,
            address: 1020,
            data: "l"
          },
          {
            frameNum: 6,
            address: 1021,
            data: "e"
          },
          {
            frameNum: 6,
            address: 1022,
            data: "a"
          },
          {
            frameNum: 6,
            address: 1023,
            data: "f"
          },
        ],
        segmentationLogicalMemory:  [
          {
            selected: true,
            segmentNum: 0,
            data: "cat"
          },
          {
            selected: false,
            segmentNum: 1,
            data: "apple"
          },
          {
            selected: false,
            segmentNum: 2,
            data: "bird"
          },
          {
            selected: false,
            segmentNum: 3,
            data: "banana"
          },
        ],
        segmentTable: [
          {
            segmentNum: 0,
            base: 6,
            limit: 3
          },
          {
            segmentNum: 1,
            base: 17,
            limit: 5
          },
          {
            segmentNum: 2,
            base: 0,
            limit: 4
          },
          {
            segmentNum: 3,
            base: 10,
            limit: 6
          }
        ],
        segmentationPhysicalMemory: [
          {
            address: 1000,
            data: 'b'
          },
          {
            address: 1001,
            data: 'i'
          },
          {
            address: 1002,
            data: 'r'
          },
          {
            address: 1003,
            data: 'd'
          },
          {
            address: 1004,
            data: ''
          },
          {
            address: 1005,
            data: ''
          },
          {
            address: 1006,
            data: 'c'
          },
          {
            address: 1007,
            data: 'a'
          },
          {
            address: 1008,
            data: 't'
          },
          {
            address: 1009,
            data: ''
          },
          {
            address: 1010,
            data: 'b'
          },
          {
            address: 1011,
            data: 'a'
          },
          {
            address: 1012,
            data: 'n'
          },
          {
            address: 1013,
            data: 'a'
          },
          {
            address: 1014,
            data: 'n'
          },
          {
            address: 1015,
            data: 'a'
          },
          {
            address: 1016,
            data: ''
          },
          {
            address: 1017,
            data: 'a'
          }, 
          {
            address: 1018,
            data: 'p'
          }, 
          {
            address: 1019,
            data: 'p'
          }, 
          {
            address: 1020,
            data: 'l'
          }, 
          {
            address: 1021,
            data: 'e'
          },    
        ]
      },
      watch: {
        option: function(newOption, oldOption) {
          clearInterval(this.interval);
          this.step = 0;
          this.pageNumInput = "1";
          this.segmentNumInput = "1";
          this.frameNumOuput = "";
          this.base = "";
          this.limit = "";

          if (oldOption === "paging") {
            const pageNumInput = document.getElementById("page-num-input");
            const frameNumOuput = document.getElementById("frame-num-output");
            const lMemoryTable = document.getElementById("l-memory-table");
            const pageTable = document.getElementById("page-table");
            const pMemoryTable = document.getElementById("p-memory-table")

            pageNumInput.style.backgroundColor = "white";
            frameNumOuput.style.backgroundColor = "white";

            for (let row of lMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pageTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }
          } else {
            const lMemoryTable = document.getElementById("l-memory-table-segmentation");
            const segmentTable = document.getElementById("segmentation-table");
            const pMemoryTable = document.getElementById("p-memory-table-segmentation");
            const segmentNumInput = document.getElementById("segment-num-input");
            const baseOutput = document.getElementById("base-output");

            baseOutput.style.backgroundColor = "white";
            segmentNumInput.style.backgroundColor = "white";

            for (let row of lMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of segmentTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }
          }
        }
      },
      computed: {
        pagingIsSelected: function() {
          return this.option === "paging";
        },
        segmentationIsSelected: function() {
          return this.option === "segmentation";
        },
        atStepZero: function () {
          return this.step === 0;
        }
      },
      methods: {
        setPhysicalMemoryPaging: function() {
          const pMemoryTable = document.getElementById("p-memory-table");
          const rows = pMemoryTable.rows;
          currentNum = 1
          for (let x = 1; x < rows.length; x++) {
            if (rows[x].childNodes[0].innerHTML != currentNum) {
              rows[x].style.borderTop = "double";
              currentNum = rows[x].childNodes[0].innerHTML;
            } 
          }
        },
        setPhysicalMemorySegmentation: function() {
          const pMemoryTable = document.getElementById("p-memory-table-segmentation");
          const rows = pMemoryTable.rows;
          currentNum = 1
          for (let x = 1; x < rows.length; x++) {
            rows[x].style.borderStyle = "solid";
          }
        },
        clearRows: function() {
          if (this.option === "paging") {
            const pageNumInput = document.getElementById("page-num-input");
            const frameNumOuput = document.getElementById("frame-num-output");
            const lMemoryTable = document.getElementById("l-memory-table");
            const pageTable = document.getElementById("page-table");
            const pMemoryTable = document.getElementById("p-memory-table")

            pageNumInput.style.backgroundColor = "white";
            frameNumOuput.style.backgroundColor = "white";

            for (let row of lMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pageTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }
          } else {
            const lMemoryTable = document.getElementById("l-memory-table-segmentation");
            const segmentTable = document.getElementById("segmentation-table");
            const pMemoryTable = document.getElementById("p-memory-table-segmentation");
            const segmentNumInput = document.getElementById("segment-num-input");
            const baseOutput = document.getElementById("base-output");

            baseOutput.style.backgroundColor = "white";
            segmentNumInput.style.backgroundColor = "white";

            for (let row of lMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of segmentTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }
          }
        },
        generateInputClicked: function() {
          if (this.option === "paging") {
            this.pageNumInput = Math.floor(Math.random() * 4);
          } else {
            this.segmentNumInput = Math.floor(Math.random() * 4);
          }
        },
        startClicked: function() {
          clearInterval(this.interval);
          this.clearRows();
          this.frameNumOutput = "";
          this.step = 0;
        },
        nextStepClicked: function() {
          if (this.option === "paging") {
            const pageNum = parseInt(this.pageNumInput, 10);
            const frameNum = this.pageTable[pageNum].frameNum;
            const lMemoryTable = document.getElementById("l-memory-table");
            const pageTable = document.getElementById("page-table");
            const pMemoryTable = document.getElementById("p-memory-table");
            const pageNumInput = document.getElementById("page-num-input");
            const frameNumOutput = document.getElementById("frame-num-output");
            
            if (this.step === 0) {
              this.clearRows();
              frameNumOutput.style.backgroundColor = "white"
              pageNumInput.style.backgroundColor = "yellow";
              this.step = 1
            } else if (this.step === 1) {
              pageNumInput.style.backgroundColor = "white"
              lMemoryTable.rows[pageNum + 1].style.backgroundColor = "yellow";
              this.step = 2;
            } else if (this.step === 2) {
              lMemoryTable.rows[pageNum + 1].style.backgroundColor = "white";
              pageTable.rows[pageNum + 1].style.backgroundColor = "yellow";
              this.step = 3;
            } else if (this.step === 3) {
              pageTable.rows[pageNum + 1].style.backgroundColor = "white";
              for (let row of pMemoryTable.rows) {
                if (row.childNodes[0].innerHTML == frameNum) {
                  row.style.backgroundColor = "yellow";
                }
              }
              this.step = 4;
            } else if (this.step === 4) {
              this.clearRows();
              frameNumOutput.style.backgroundColor = "yellow";
              this.frameNumOutput = frameNum;
            }
          } else {
            const segmentNum = parseInt(this.pageNumInput, 10);
            const base = this.segmentTable[segmentNum].base;
            const limit = this.segmentTable[segmentNum].limit;
            const lMemoryTable = document.getElementById("l-memory-table-segmentation");
            const segmentTable = document.getElementById("segmentation-table");
            const pMemoryTable = document.getElementById("p-memory-table-segmentation");
            const segmentNumInput = document.getElementById("segment-num-input");
            const baseOutput = document.getElementById("base-output");

            if (this.step === 0) {
              this.clearRows();
              baseOutput.style.backgroundColor = "white";
              segmentNumInput.style.backgroundColor = "yellow";
              this.step = 1
            } else if (this.step === 1) {
              segmentNumInput.style.backgroundColor = "white"
              lMemoryTable.rows[segmentNum + 1].style.backgroundColor = "yellow";
              this.step = 2;
            } else if (this.step === 2) {
              lMemoryTable.rows[segmentNum + 1].style.backgroundColor = "white";
              segmentTable.rows[segmentNum + 1].style.backgroundColor = "yellow";
              this.step = 3;
            } else if (this.step === 3) {
              segmentTable.rows[segmentNum + 1].style.backgroundColor = "white";
              let i = 1;
              while (i < pMemoryTable.rows.length) {
              if (pMemoryTable.rows[i].childNodes[0].innerHTML == parseInt(base, 10) + 1000) {
                for (let j = 0; j < limit; j++) {
                  pMemoryTable.rows[i].style.backgroundColor = "yellow";
                  i++;
                }
              } else {
                i++;
              }
            }
              this.step = 4;
            } else if (this.step === 4) {
              this.clearRows();
              baseOutput.style.backgroundColor = "yellow";
              this.base = base;
              this.limit = limit;
            }
          }
        },
        playClicked: function() {
          this.interval = setInterval(this.nextStepClicked, 1000);
        },
        pauseClicked: function() {
          clearInterval(this.interval);
        },
        endClicked: function() {
          clearInterval(this.interval);
          if (this.option === "paging") {
            const pageNum = parseInt(this.pageNumInput, 10);
            const frameNum = this.pageTable[pageNum].frameNum;
            const lMemoryTable = document.getElementById("l-memory-table");
            const pageTable = document.getElementById("page-table");
            const pMemoryTable = document.getElementById("p-memory-table");
            const pageNumInput = document.getElementById("page-num-input");
            const frameNumOutput = document.getElementById("frame-num-output");

            this.frameNumOutput = frameNum;

            for (let row of lMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pageTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }
            
            pageNumInput.style.backgroundColor = "yellow";
            lMemoryTable.rows[pageNum + 1].style.backgroundColor = "yellow";
            pageTable.rows[pageNum + 1].style.backgroundColor = "yellow";

            for (let row of pMemoryTable.rows) {
              if (row.childNodes[0].innerHTML == frameNum) {
                row.style.backgroundColor = "yellow";
              } 
            }
            frameNumOutput.style.backgroundColor = "yellow";
          } else {
            const segmentNum = parseInt(this.pageNumInput, 10);
            const segmentNumInput = document.getElementById("segment-num-input");
            const base = this.segmentTable[segmentNum].base;
            const limit = this.segmentTable[segmentNum].limit;
            const lMemoryTable = document.getElementById("l-memory-table-segmentation");
            const pageTable = document.getElementById("segmentation-table");
            const pMemoryTable = document.getElementById("p-memory-table-segmentation");
            const baseOutput = document.getElementById("base-output");

            this.base = base;
            this.limit = limit;
            this.step = 4;

            for (let row of lMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pageTable.rows) {
              row.style.backgroundColor = "white";
            }

            for (let row of pMemoryTable.rows) {
              row.style.backgroundColor = "white";
            }

            segmentNumInput.style.backgroundColor = "yellow";
            lMemoryTable.rows[segmentNum + 1].style.backgroundColor = "yellow";
            pageTable.rows[segmentNum + 1].style.backgroundColor = "yellow";

            let i = 1;
            while (i < pMemoryTable.rows.length) {
              if (pMemoryTable.rows[i].childNodes[0].innerHTML == parseInt(base, 10) + 1000) {
                for (let j = 0; j < limit; j++) {
                  pMemoryTable.rows[i].style.backgroundColor = "yellow";
                  i++;
                }
              } else {
                i++;
              }
            }
            baseOutput.style.backgroundColor = "yellow";    
          }
        }
      },
      mounted() {
        const lMemoryTable = document.getElementById("l-memory-table");
        
        for (let i = 1; i < lMemoryTable.rows.length; i++) {
          lMemoryTable.rows[i].childNodes[0].style.backgroundColor = "rgba(51, 153, 255, .3)";
          lMemoryTable.rows[i].childNodes[2].style.backgroundColor = "rgba(192, 192, 192, .3)";
        }
      }
    });
 </script>
</body>
</html>