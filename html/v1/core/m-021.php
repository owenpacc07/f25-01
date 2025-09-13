<!DOCTYPE html>
<html>

    <head>
    
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Page Replacement</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
         
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="./styles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>
    

    <body>
     <?php include '../navbar.php'; ?>


</head>
<body>
       
        <br>
 
 <div>
 
 <head>
    
  
    
	<div class = "pt-3 pr-3 pl-3">
  
    
</head>
    <div class="bouncing">
    <h1>
        
        <span>P</span>
        <span>A</span>
        <span>G</span>
        <span>E</span>
        <span></span>
        <span>R</span>
        <span>E</span>
        <span>P</span>
        <span>L</span>
        <span>A</span>
        <span>C</span>
        <span>E</span>
        <span>M</span>
        <span>E</span>
        <span>N<span>
        <span>T<span>
       
        
        
    </h1>
    </div>
    </div>


    
</head>
  

 </div>
 
 
 
 
<br><br>

        
            </div>
        </form>
<div class="d-flex align-items-center justify-content-center">
           
        </div>
        <div class="d-flex align-items-center justify-content-center">
            <form id="preType">
                
            </form>
        </div><br>
<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>

<div class="d-flex align-items-center justify-content-center">
<h2>Click On Algorithm To See The Strategy For Page Replacement</h2>
</div>
<div class="d-flex align-items-center justify-content-center">
<div class="dropdown">
  <button onclick="myFunction()" class="dropbtn">FIFO</button>&nbsp;&nbsp;&nbsp;
  <div id="myDropdown" class="dropdown-content">
    <a href="#home">Oldest page in main memory is the one which will be selected for replacement.</a>
   
  </div>
  
</div>
<div class="dropdown">
  <button onclick="myFunction1()" class="dropbtn">LRU</button>&nbsp;&nbsp;&nbsp;
  <div id="myDropdown1" class="dropdown-content">
    <a href="#home">We look to the left of the table, that we have created we choose the further most page to get replaced.</a>
 </div>
  
</div>
<div class="dropdown">
  <button onclick="myFunction2()" class="dropbtn">Optimal</button>&nbsp;&nbsp;&nbsp;
  <div id="myDropdown2" class="dropdown-content">
    <a href="#home">Replace the page that will not be used for the longest period of time. We look to the right further most.</a>
 
  </div>
</div>
<div class="dropdown">
  <button onclick="myFunction3()" class="dropbtn">LFU</button>&nbsp;&nbsp;&nbsp;
  <div id="myDropdown3" class="dropdown-content">
    <a href="#home">In current stack at any iteration we choose that element for replacement which has smallest count in the incoming page stream.</a>
   
  </div>
</div>
<div class="dropdown">
  <button onclick="myFunction4()" class="dropbtn">MFU</button>&nbsp;&nbsp;&nbsp;
  <div id="myDropdown4" class="dropdown-content">
    <a href="#home">In current stack at any iteration we choose that element for replacement which has highest count in the incoming page stream.</a>
    
  </div>
</div>
</div>

  
        
<br><br>



<div class="container">
   <div class="table-responsive">
  
    <div align="center">
     <button type="button" name="load_data" id="load_data" class="btn btn-info">Load Data</button>
    </div>
    <br />
    <div id="input_table">
    </div>
   </div>
  </div>
  <script>
columns=[];

$(document).ready(function(){
 $('#load_data').click(function(){
  $.ajax({
   url:"../../files/p4-page-input.txt",
   dataType:"text",
   success:function(data)
   {
    var pagereference_data = data.split(/\r?\n|\r/);
    var table_data = '<p>Queue (Reference String) :</p><table class="table table-bordered table-striped">';
    for(var count = 0; count<pagereference_data.length; count++)
    {
     var cell_data = pagereference_data[count].split(",");
     table_data += '<tr>';

     for(var cell_count=1; cell_count<cell_data.length; cell_count++)
     {
      if(count === 1)
      {
       table_data += '<th>'+cell_data[cell_count]+'</th>';
      }
      else
      {
       table_data += '<td>'+cell_data[cell_count]+'</td>';
      }
     }
     table_data += '</tr>';
    }
    table_data += '</table>';
    $('#input_table').html(table_data);
   }
  });

 });
 
});
</script>
</div>

        <div class="container">
     
        
    </body>




<body>
<div class="container">
    <div class="d-flex align-items-center justify-content-center">
            <form id="schedule">
                <input type="radio" class="btn-check" name="schedule" value="FIFO" id="option1" autocomplete="off">
                    <label class="btn btn-info" for="option1" onclick="return StartImage()">First In First Out</label>
                <input type="radio" class="btn-check" name="schedule" value="LRU" id="option2" autocomplete="off">
                    <label class="btn btn-secondary" for="option2" onclick="return StartImageLRU()">Least Recently Used</label>
                <input type="radio" class="btn-check" name="schedule" value="Opt" id="option3" autocomplete="off">
                    <label class="btn btn-danger" for="option3" onclick="return StartImageOPT()">Optimal</label>
                <input type="radio" class="btn-check" name="schedule" value="LFU" id="option4" autocomplete="off">
                    <label class="btn btn-warning" for="option4" onclick="return StartImageLFU()">Least Frequently Used</label>
				<input type="radio" class="btn-check" name="schedule" value="MFU" id="option5" autocomplete="off">
                   <label class="btn btn-light" for="option5" onclick="return StartImageMFU()">Most Frequently Used</label>
				
            </form>
        </div>
            <div class="d-flex align-items-center justify-content-center" id="timeSlice">
                
            </div>
  

 
   
</div>
<hr>
<div class="container">
   <!--Animation Control Type Form-->
   
   <div class="d-flex align-items-center justify-content-center">
	    <h5 class="pr-3">Select Animation Type: </h5>
            <form id="animationType">
                <input type="radio" name="animationType" value="StepByStep" checked/> Step By Step<br/>
                <input type="radio" name="animationType" value="Automatic"/> Automatic<br/>
            </form>
    </div><hr>

  <div class="d-flex align-items-center justify-content-center">
  
<input type="button" class="btn btn-dark" role="button" value="Start" id="start" onclick="StartOutput()">&nbsp;&nbsp;&nbsp;
<input type="button" class="btn btn-dark" role="button" value="Back" id="back" onclick="BackOutput()">&nbsp;&nbsp;&nbsp;
<input type="button" class="btn btn-dark" role="button" value="Play/Pause" disabled id="playpause" onclick="PlayOutput()">&nbsp;&nbsp;&nbsp;
<input type="button" class="btn btn-dark" role="button" value ="Next" id="next" onclick="NextOutput()">&nbsp;&nbsp;&nbsp;
<input type="button" class="btn btn-dark" role="button" value="End" id="end" onclick="EndOutput()">&nbsp;&nbsp;&nbsp;
</div>

</div> 
 <br>
<div class="container">
   <div class="table-responsive">
     <div id="output_table">
    </div>
   </div>
 </div>
<br><br><hr>
</body>
</html>



<script language="javascript" type="text/javascript">
var imgNumber =0;
var path = [
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO1.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO2.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO3.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO4.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO5.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO6.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO7.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO8.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO9.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO10.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO1.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO2.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO3.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO4.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO5.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/LRU6.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/LRU7.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/LRU8.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/LRU9.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/LRU10.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO1.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO2.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO3.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO4.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO5.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/OPT6.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/OPT7.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/OPT8.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/OPT9.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/OPT10.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO1.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO2.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO3.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO4.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO5.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/OPT6.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/OPT7.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/LFU8.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/LFU9.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/LFU10.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO1.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO2.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/FIFO3.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/MFU4.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/MFU5.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/MFU6.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/MFU7.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/MFU8.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/MFU9.PNG",
  "http://cs.newpaltz.edu/p/s21-06/s21-v4/2-replace/Images/MFU10.PNG",
  
    
];

 //animation type 
 $('input[type=radio][name=animationType]').change(function () {
                if (this.value == 'StepByStep') {
                    //disable play/pause
                    document.getElementById('playpause').setAttribute('disabled','disabled')
                    
                    document.getElementById('next').removeAttribute("disabled");
                    document.getElementById('back').removeAttribute("disabled");
                }
                else if (this.value == 'Automatic'){
                    //disable next/back
                    document.getElementById('next').setAttribute('disabled','disabled')
                    document.getElementById('back').setAttribute('disabled','disabled')
                    document.getElementById('playpause').removeAttribute("disabled");
                }
            });

var colIndex=0;
var timer=null;

function LoadOutput(){
    const val = document.querySelector('input[name="schedule"]:checked').value;
    if (val == 'FIFO'){
        return "../../files/p4-page-output-fifo.txt"
    }
    else if (val == 'LFU'){
        return "../../files/p4-page-output-lfu.txt"
    }
    else if (val == 'Opt'){
        return "../../files/p4-page-output-optimal.txt"
    }
    else if (val == 'LRU'){
        return "../../files/p4-page-output-lru.txt"
    }
    else if (val == 'MFU'){
        return "../../files/p4-page-output-mfu.txt"
    }
}

function PlayOutput() {
    if(timer == null){
	console.log('starting...');
	timer = setInterval(()=>{
console.log('in cycle');
NextOutput()
}, 1500);
	
    } else {
	console.log('ending...');
	clearInterval(timer);
        timer = null;
    }
}
function NextOutput() {
console.log('next..');
 $.ajax({
   url: LoadOutput() ?? "../../files/p4-page-output-fifo.txt",
   dataType:"text",
   success:function(data)
   {
    var pagereference_data = data.split(/\r?\n|\r/); 
    if(colIndex == pagereference_data.length-1){
	colIndex = 0;
	} 
	else {
	colIndex++;
	} 
	console.log(colIndex);
   var table_data = '<table class="table table-bordered table-striped"><tr><th>Page #</th><th>Frame 1</th><th>Frame 2</th><th>Frame 3</th><th>Page Fault</th><th>Total Faults</th></tr>';
    for(var count = 0; count<colIndex; count++)
    {
     var col_data = pagereference_data[count].split(",");
     table_data += '<tr>';
	

     for(var cell_count=0; cell_count<col_data.length; cell_count++)
     {
         table_data += '<td>'+col_data[cell_count]+'</td>';
	
     }
     table_data += '</tr>';
    }
    table_data += '</table>';
	
    $('#output_table').html(table_data);
}
  });

}
function BackOutput() {
console.log('next..');
 $.ajax({
   url: LoadOutput() ?? "../../files/p4-page-output-fifo.txt",
   dataType:"text",
   success:function(data)
   {
    var pagereference_data = data.split(/\r?\n|\r/); 
    if(colIndex == 0){
	colIndex = pagereference_data.length-1;
	} 
	else {
	colIndex--;
	} 
	console.log(colIndex);
   var table_data = '<table class="table table-bordered table-striped"><tr><th>Page #</th><th>Frame 1</th><th>Frame 2</th><th>Frame 3</th><th>Page Fault</th><th>Total Faults</th></tr>';
    for(var count = 0; count<colIndex; count++)
    {
     var col_data = pagereference_data[count].split(",");
     table_data += '<tr>';
	

     for(var cell_count=0; cell_count<col_data.length; cell_count++)
     {
         table_data += '<td>'+col_data[cell_count]+'</td>';
	
     }
     table_data += '</tr>';
    }
    table_data += '</table>';
	
    $('#output_table').html(table_data);
}
  });

}
function StartOutput() {
 colIndex=0;
 console.log(colIndex);
 $.ajax({
   url: LoadOutput() ?? "../../files/p4-page-output-fifo.txt",
   dataType:"text",
   success:function(data)
   {
    var pagereference_data = data.split(/\r?\n|\r/);
       

   var table_data = '<table class="table table-bordered table-striped"><tr><th>Page #</th><th>Frame 1</th><th>Frame 2</th><th>Frame 3</th><th>Page Fault</th><th>Total Faults</th></tr>';
    for(var count = 0; count<1; count++)
    {
     var col_data = pagereference_data[count].split(",");
     table_data += '<tr>';
	
     for(var cell_count=0; cell_count<col_data.length; cell_count++)
     {
         table_data += '<td>'+col_data[cell_count]+'</td>';
	 
     }
     table_data += '</tr>';
    }
    table_data += '</table>';
	
    $('#output_table').html(table_data);
    
    console.log(colIndex);
}
  });

}
function EndOutput() {
    $.ajax({
   url: LoadOutput() ?? "../../files/p4-page-output-fifo.txt",
   dataType:"text",
   success:function(data)
   {
    var pagereference_data = data.split(/\r?\n|\r/);
  
    colIndex = pagereference_data.length-1
   var table_data = '<table class="table table-bordered table-striped"><tr><th>Page #</th><th>Frame 1</th><th>Frame 2</th><th>Frame 3</th><th>Page Fault</th><th>Total Faults</th></tr>';
    for(var count = 0; count<pagereference_data.length-1; count++)
    {
     var col_data = pagereference_data[count].split(",");
     table_data += '<tr>';
	

     for(var cell_count=0; cell_count<col_data.length; cell_count++)
     {
         table_data += '<td>'+col_data[cell_count]+'</td>';

     }
     table_data += '</tr>';
    }
    table_data += '</table>';

    $('#output_table').html(table_data);
}
  });

}
/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
function myFunction1() {
  document.getElementById("myDropdown1").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
function myFunction2() {
  document.getElementById("myDropdown2").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
function myFunction3() {
  document.getElementById("myDropdown3").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
function myFunction4() {
  document.getElementById("myDropdown4").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}

</script>
