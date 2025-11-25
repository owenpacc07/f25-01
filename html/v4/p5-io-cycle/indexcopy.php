<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>I/O Cycles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
     
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
    </head>


<body> 
    <?php include '../navbar.php'; ?>
<br>

<div class="d-flex align-items-center justify-content-center">
        <h1 id="title">I/O Cycles</h1>
        <br>

</div>

<br>
<div class="d-flex align-items-center justify-content-center">
        <h5 class="pr-3">Select Animation Type: </h5>
        <form id="animationType">
            <input type="radio" name="animationType" value="StepByStep" checked/> Step By Step<br/>
            <input type="radio" name="animationType" value="Automatic" /> Automatic<br/>
        </form>
    </div>

    <br>

    <div class="d-flex align-items-center justify-content-center">
        <button class="button" id= "start">Start</button>
        <button class="button" id= "next">Next</button>
        <button class="button" id= "back">Back</button>
        <button class="button" id= "play">Play</button>
        <button class="button" id= "pause">Pause</button>
        <button class="button" id= "end">End</button>

    </div>
    <br>


<div id="body">

    <?php 
        $myfile = fopen("data.txt", "r") or die("Unable to open file");
    ?>

    <div id="cpu">
        <div id = "title1">
            <h2> CPU </h2>
        </div>

        <div id = "io-request-box2"> 
            <h6> I/O Request </h6>
        </div>
    </div>


   
    <div id="ram">
        <div id = "title2">
            <h2> RAM </h2>
        </div>

        <div id = "system-call-box">
            <p> System Call </p>
        </div>

        <div id = "device-driver-box">
            <p> Device Driver: Monitor </p>
        </div>

       <div id = "user-program-box">
            <p> User Program X</p>
        </div>

        <div id = "io-request-box1"> 
            <h6> I/O Request </h6>
              <?php  echo fread($myfile,filesize("data.txt")); ?>
        </div>
        
        <div id = "interrupt-handler-box"> <!-- needs to take info from txt file -->
            <h6> Interrupt Handler </h6>
        </div>
    </div>

    <div id="box">
        I/O Process
    </div>

    <div id="monitor" style="display:none">
        <div id = "title3">
            <h2> Monitor </h2>
        </div>
        <div id = "monitor-box-display">
            <h2> Display: </h2>
        </div>
        <div id = "monitor-box-done">
            <h2> Done? </h2>
        </div>
        <div id="image" >
            <img src="monitor.PNG" alt="Monitor" width = "200" height = "150">
        </div>
        <div id = "monitor-box">
            
        </div>
    </div>

    <div id="arrow1" style="display:none">
        <img src="arrow1.png" alt="Arrow1" width = "200" height = "200">
    </div>

    <div id="arrow2" style="display:none">
        <img src="arrow2.png" alt="Arrow2" width = "200" height = "200">
    </div>

    <div id="arrow3" style="display:none">
        <img src="arrow3.png" alt="Arrow3" width = "150" height = "150">
    </div>

    <div id="arrow4" style="display:none">
        <img src="arrow4copy.png" alt="Arrow4" width = "350" height = "150">
    </div>

    <div id="arrow5" style="display:none">
        <img src="arrow5.png" alt="Arrow5" width = "150" height = "100">
    </div>

    <div id="arrow6" style="display:none">
        <img src="arrow6.png" alt="Arrow6" width = "350" height = "350">
    </div>

    <div id="arrow7" style="display:none">
        <img src="arrow7new.png" alt="Arrow7" width = "150" height = "300">
    </div>

    <div id="arrow8" style="display:none">
        <img src="arrow8.png" alt="Arrow8" width = "175" height = "100">
    </div>

    <div id="arrow9" style="display:none">
        <img src="arrow9new.png" alt="Arrow9" width = "100" height = "410">
    </div>

    <div id="arrow10" style="display:none">
        <img src="arrow10.png" alt="Arrow10" width = "105" height = "105">
    </div>

    <div id="arrow11" style="display:none">
        <img src="arrow11.png" alt="Arrow11" width = "105" height = "105">
    </div>

    <div id="arrow12" style="display:none">
        <img src="arrow12.png" alt="Arrow12" width = "105" height = "105">
    </div>

    <style type='text/css'>

    body {
        background-color: #F8C3BE;
    }

    button {
        background-color: #CFFBF8;
        border: 2px solid black;
        border-radius: 8px;
        color: black;
        padding: 10px 25px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 15px;
        font-family: Times New Roman;
    }
   
    #cpu {

      font-family: Times New Roman;
       top: 220px;  
    }

    #cpu #title1  {
        margin: 0px;
    padding: 0px;
    text-align: left;
    position: absolute;
    top: 300px;
    left: 200px;
    font-size: 25px;
    }

    #io-request-box2 {
      width: 150px;
      height: 50px;
      background-color: white;
      border-style: solid;
      border-radius: 3px;
      position: absolute;
      left: 150px;
      top: 350px;
      text-align: center;
      font-family: Times New Roman;
    }


    #ram {
      font-family: Times New Roman;
      top: 400px;
    }

   #ram #title2  {
        margin: 0px;
    padding: 0px;
    text-align: left;
    position: absolute;
    top: 450px;
    left: 200px;
    font-size: 25px;
    }

    #system-call-box {
        width: 150px;
      height: 50px;
      background-color: white;
      border-style: solid;
      border-radius: 3px;
      position: absolute;
      left: 150px;
      top: 500px;
      text-align: center;
      font-family: Times New Roman;
    }

    #device-driver-box {
        width: 150px;
      height: 50px;
      background-color: white;
      border-style: solid;
      border-radius: 3px;
      position: absolute;
      left: 150px;
      top: 550px;
      text-align: center;
      font-family: Times New Roman;
    }

    #user-program-box {
        width: 150px;
      height: 50px;
      background-color: white;
      border-style: solid;
      border-radius: 3px;
      position: absolute;
      left: 150px;
      top: 700px;
      text-align: center;
      font-family: Times New Roman;

    }

    #io-request-box1 {
        width: 150px;
      height: 50px;
      background-color: red;
      border-style: solid;
      border-radius: 3px;
      position: absolute;
      left: 150px;
      top: 650px;
      text-align: center;
      font-family: Times New Roman; 
    }

    #interrupt-handler-box {
      width: 150px;
      height: 50px;
      background-color: white;
      border-style: solid;
      border-radius: 3px;
      position: absolute;
      left: 150px;
      top: 600px;
      text-align: center;
      font-family: Times New Roman;
    }

    #box {
        
       position: absolute;
        right: 30px; bottom: 20px;
        width: 300px;
        height:75px;
        background-color:#CFFBF8;
        padding: 15px;
        font-family: Times New Roman;
        font-size: 20px;
        text-align: center;
    }

    #monitor-box-display {
      width: 150px;
      height: 50px;
      background-color: white;
      border-style: solid;
      border-radius: 3px;
      position: absolute;
      left: 670px;
      top: 420px;
      text-align: center;
      font-family: Times New Roman;
    }

    #monitor-box-done {
      width: 150px;
      height: 50px;
      background-color: white;
      border-style: solid;
      border-radius: 3px;
      position: absolute;
      left: 670px;
      top: 470px;
      text-align: center;
      font-family: Times New Roman;
    }

    #monitor #title3  {
        margin: 0px;
    padding: 0px;
    text-align: right;
    position: absolute;
    top: 380px;
    left: 700px;
    font-size: 25px;
    }

   #monitor #image  {
    position: absolute;
    top: 520px;
    left: 650px;
    } 

    #monitor-box {
        position: absolute;
        top: 530px;
        left: 670px;
        width: 160px;
        height: 100px;
        border-style: solid;
        border-radius: 3px;
        text-align: center;
        font-family: Times New Roman;
        font-size: 50px;
    }

    #arrow1 {
        position: absolute;
        top: 350px;
        
    }

     #arrow2 {
        position: absolute;
        left: 260px;
        top: 350px;
    }

    #arrow3 {
        position: absolute;
        left: 270px;
        top: 475px;
    }

    #arrow4 {
        position: absolute;
        left: 300px;
        top: 440px;
    }

    #arrow5 {
        position: absolute;
        left: 810px;
        top: 410px;
    }

    #arrow6 {
        position: absolute;
        left: 300px;
        top: 310px;
    }

    #arrow7 {
        position: absolute;
        top: 340px;
        left: 260px;
    }

    #arrow8 {
        position: absolute;
        top: 340px;
        left: 250px;
    }

    #arrow9 {
        position: absolute;
        top: 350px;
        left: 50px;
    }

    #arrow10 {
        position: absolute;
        top: 500px;
        left: 830px;
    }

    #arrow11 {
        position: absolute;
        top: 570px;
        left: 310px;
    }

    #arrow12 {
        position: absolute;
        top: 330px;
        left: 40px;
    }

  </style>



   <script>

    <?php 
        $myfile = fopen("data.txt", "r") or die("Unable to open file");
    ?>
   

   document.getElementsByTagName('button')[0].addEventListener('click', function() {
        step1();
        step2();
        step3();
        step4();
        step5();
        step6();
        step7();
        step8();
        step9();
        step10();
        step11();
        step12();
        step13();
        step14();
        
    })


  /* document.getElementsByTagName('button')[5].addEventListener('click', function() {
        setTimeout(() => {
        clearInterval();
        });  
    })

    let pause = false

    document.getElementsByTagName('button')[4].addEventListener('click', function() {
        pause != pause
    } ) 


    let steps = [
        step1,
        step2,
        step3,
        step4,
        step5,
        step6,
        step7,
        step8,
        step9,
        step10,
        step11,
        step12,
        step13,
        step14
    ]

    document.getElementsByTagName('button')[0].addEventListener('click', function() {
        let i = 0;
        while(i < steps.length) {
            if(!pause){
                steps[i]()
                    ++i
            } else{
            //check whether pause is disabled after one sec
                setTimeout(() => {}, 500)
            }
        }
    })
*/
   
    function step1() {
        setTimeout(() =>{
            $("#arrow1").show();
            $("#box").text("1. Device Driver initiates I/O");
            document.getElementById('io-request-box1').style.cssText = 'background-color: white';
            document.getElementById('io-request-box2').style.cssText = 'background-color: red'; 
            document.getElementById("io-request-box2").innerHTML = document.getElementById("io-request-box1").innerHTML;
            document.getElementById("io-request-box1").innerHTML = "";
        }, 1000)  
    }

    function step2() {
        setTimeout(() => {
            $("#arrow1").hide();
            $("#arrow2").show();
            document.getElementById("system-call-box").innerHTML = "System Call" + document.getElementById("io-request-box2").innerHTML;
            document.getElementById('io-request-box2').innerHTML = "";
            document.getElementById('io-request-box2').style.cssText = 'background-color: white';
            document.getElementById('system-call-box').style.cssText = 'background-color: red';
        }, 3000)  
    } 

    function step3() {
        setTimeout(() => {
            $("#arrow2").hide();
            $("#arrow3").show();
            document.getElementById("system-call-box").innerHTML = "System Call";
            document.getElementById("device-driver-box").innerHTML = "Device Driver:" + "   " + "I/O Request";
            document.getElementById('system-call-box').style.cssText = 'background-color: white';
            document.getElementById('device-driver-box').style.cssText = 'background-color: red';
            //document.getElementById('system-call-box').innerHTML = "System Call";
        }, 5000)  
    }

    function step4() {
        setTimeout(() => {
            $("#arrow3").hide();
            $("#monitor").show(); 
        }, 7000)
    }

    function step5() {
        setTimeout(() => {
            $("#arrow4").show();
            document.getElementById('system-call-box').style.cssText = 'background-color: white';
            $("#box").text("2.1 Transfer I/O Data to command");
            document.getElementById("monitor-box-display").innerHTML = "Display:" + "  " + " I/O Request";
            document.getElementById('device-driver-box').innerHTML = "Device Driver";
            document.getElementById('monitor-box-display').style.cssText = 'background-color: red';
            document.getElementById('device-driver-box').style.cssText = 'background-color: white';
        }, 9000)
    }

    function step6() {
        setTimeout(() => {
            $("#arrow4").hide();
            $("#box").text("2.2 Initiates I/O");
            document.getElementById('monitor-box-display').innerHTML = "Display:";
            document.getElementById('monitor-box-display').style.cssText = 'background-color: white';
        }, 11000)
    }

    function step7() {
        setTimeout(() => {
            $("#arrow5").show();
            $("#box").text("2.3 I/O Completion Status: Okay");
            document.getElementById('monitor-box').innerHTML = "<?php  echo fread($myfile,filesize("data.txt")); ?>";
            document.getElementById('monitor-box-done').innerHTML = "Done? Okay";
        }, 13000)
    }

    function step8() {
        setTimeout(() => {
            $("#arrow5").hide();
            $("#arrow10").show();
            $("#box").text("3. Input Ready Generates Interrupt");
        }, 15000)
    }

    function step9() {
        setTimeout(() => {
            $("#arrow6").show();
            $("#arrow10").hide();
            //$("#arrow7").show();
            $("#box").text("4.1 CPU Receiving Interrupt");
            document.getElementById('io-request-box2').innerHTML = "Interrupt"; 
            document.getElementById('io-request-box2').style.cssText = 'background-color: red';
            document.getElementById('monitor-box-done').innerHTML = "Done?";
           /* document.getElementById('io-request-box2').style.cssText = 'background-color: red';
            document.getElementById('interrupt-handler-box').style.cssText = 'background-color: white'; 
            document.getElementById("interrupt-handler-box").innerHTML = "Interrupt Handler";
            document.getElementById("io-request-box2").innerHTML = "Interrupt"; */

        }, 17000)
    }


    function step10() {
        setTimeout(() => {
            $("#arrow6").hide();
            $("#arrow7").show();
            //$("#arrow8").show();
            $("#box").text("4.2 Transfer Control To Interrupt Handler");
            document.getElementById('io-request-box2').style.cssText = 'background-color: white';
            document.getElementById("io-request-box2").innerHTML = "";
            document.getElementById('interrupt-handler-box').style.cssText = 'background-color: red'; 
            document.getElementById("interrupt-handler-box").innerHTML = "Interrupt Handler:" + "    "  + "Interrupt";

        }, 19000)
    }

    function step11() {
        setTimeout(() => {
            $("#arrow7").hide();
            $("#arrow11").show();
            $("#box").text("5. Interrupt Processes Data");
        }, 21000)
    }

    function step12() {
        setTimeout(() => {
            $("#arrow11").hide();
            $("#arrow8").hide();
            $("#arrow9").show();
            $("#box").text("6. CPU Resumes Processing of Interrupted Task");
            document.getElementById('io-request-box2').style.cssText = 'background-color: red';
            document.getElementById("io-request-box2").innerHTML = document.getElementById("user-program-box").innerHTML; 
            document.getElementById("user-program-box").innerHTML = "";
            document.getElementById('interrupt-handler-box').style.cssText = 'background-color: white'; 
            document.getElementById("interrupt-handler-box").innerHTML = "Interrupt Handler";

        }, 23000)
    }

    function step13() {
        setTimeout(() => {
            $("#box").text("7. Process Complete");
            $("#arrow9").hide();
            $("#arrow12").show();
        }, 25000)
    }

    function step14() {
        setTimeout(() => {
            $("#arrow12").hide();
        }, 27000)
    }
    

    </script> 

</div>


<script> 
        //animation type 
 $('input[type=radio][name=animationType]').change(function () {
    if (this.value == 'StepByStep') {
        //disable play/pause
        document.getElementById('play').setAttribute('disabled','disabled')
        document.getElementById('pause').setAttribute('disabled','disabled')
        document.getElementById('next').removeAttribute("disabled");
        document.getElementById('back').removeAttribute("disabled");

        }

    else if (this.value == 'Automatic'){
        //disable next/back
        document.getElementById('next').setAttribute('disabled','disabled')
        document.getElementById('back').setAttribute('disabled','disabled')
        document.getElementById('play').removeAttribute("disabled");
        document.getElementById('pause').removeAttribute("disabled");
       
    }
});

</script>


</div>

</body>
</html>


