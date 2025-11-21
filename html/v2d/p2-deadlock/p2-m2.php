<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Classical Problems of Synchronization</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">

    <!-- Latest compiled and minified CSS -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->

    <!-- Latest compiled and minified JavaScript -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script> -->

    <style>
        .pbox{
            border-style: solid;
            background-color: #fff;
            border-radius: 6px;
            color: #4a4a4a;
            display: block;
            width: 70px;
            height:200px;
        }

        .cr{
            border-top-style: solid;
            border-bottom-style: solid;
            background-color: #fff;
            color: #4a4a4a;
            display: block;
            width:64px;
            height:100px;
            margin-top: 3em;
        }

        #buffer {
            border-style: solid;
            background-color: #fff;
            border-radius: 6px;
            color: #4a4a4a;
            height:40px;
            width:70px;
            margin-right:7em;
            margin-top: 150px;
        }

        p{
            text-align: center;
        }

        .rect{
            width: 25px;
            height: 25px;
            background-color: indianred;
            margin: auto;
            position: relative;
        }

        #flag{
            border-style: solid;
            background-color: #fff;
            border-radius: 6px;
            color: #4a4a4a;
            height:40px;
            width:70px;
        }

        .rect.mtbuffer {
           animation:next 2.5s;
           animation-fill-mode: forwards;
        }

        .rect.mbbuffer {
            animation:back 2.5s;
            animation-fill-mode: forwards;
        }

        .rect.start {
            animation:starts 0s;
            animation-fill-mode: forwards;
        }

        @keyframes next {
            from {left: 0px; top: 0px;}
            to {left: 180px; top: 80px;}
        }

        @keyframes back {
            from {left: 180px; top: 80px;}
            to {left: 0px; top: 0px;}
        }

        @keyframes starts {
            from {left: 180px; top: 80px;}
            to {left: 0px; top: 0px;}
        }

        .rect.pause {
            animation-play-state: paused;
        }

        .rects {
            width: 25px;
            height: 25px;
            background-color: indianred;
            margin: auto;
            position: relative;
            margin-top: 5px;
        }

        .ab {
           color: white;
           background-color: lightskyblue;
           padding: 14px 25px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin-right: 2em;
       }

       a:hover, a:active {
            background-color: lightskyblue;
        }
    </style>
</head>

<body>
    <?php include '../navbar.php'; ?>
    <div class="text-right">
        <a class="btn btn-primary" href="index.php" role="button">Go Back</a>&nbsp;
    </div>
    <div class="d-flex align-items-center justify-content-center">
        <h5 class="pr-3">Select Animation Type: </h5>
        <form id="animationType">
            <input type="radio" name="animationType" value="StepByStep" checked/> Step By Step<br/>
            <input type="radio" name="animationType" value="Automatic" /> Automatic<br/>
        </form>
    </div>
    <hr>

    <div class="d-flex align-items-center justify-content-center">
        <input  class="btn btn-primary" type="button" value="Start" id="start" onclick="startAnim();">
        &nbsp;
        <input  class="btn btn-primary" type="button" value="Next" id="next" onclick="nextAnim();">
        &nbsp;
        <input  class="btn btn-primary" type="button" value="Back" id="back" onclick="backAnim();">
        &nbsp;
        <input  class="btn btn-primary" disabled type="button" value="Play" id="play" onclick="playAnim()">
        &nbsp;
        <input  class="btn btn-primary" disabled type="button" value="Pause" id="pause" onclick="pauseAnim()">
        &nbsp;
        <input  class="btn btn-primary" type="button" value="End" id="end" onclick="endAnim();">
    </div>
    <br> <br>

    <div class="d-flex align-items-center justify-content-center" style="flex-direction: column">
        <h4 class="is-size-3 has-text-link has-text-weight-semibold">Reader/Writer Problem</h4>
        <hr><br>
        <div class="d-flex">
        <canvas width="500" height="300" style="position: absolute; z-index: 1" ></canvas>
            <div class="p1 d-flex is-flex-direction-column">
            Process: P1
            <br>
                <div class="pbox" style="margin-right:7em;">
                    <div class="cr">
                        <p id="p1cr">critical section</p>
                        <div class="rect"></div>
                    </div>
                </div>
            </div>
            <div class="d-flex is-flex-direction-column">
                <div id="flag">
                    <p id="flagN">0</p>
                </div>
                Flag
                <div id="buffer">
                    <div class="rects" style="display:none">
                    </div>
                </div>
                Buffer
            </div>
            <div class="p2 d-flex is-flex-direction-column">
            Process: P2
            <br>
                <div class="pbox">
                    <div class="cr">
                        <p id="p2cr">critical section</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    <div class="is-size-4 d-flex align-items-center justify-content-center" style="color: slategrey">
        <br>
        flag:<br>
        0 means old data, 
        1 means new data
    </div>
    <p id="text" style="position: absolute; width: 457px; height: 350px; bottom: 20%; left:5%;">
    </p>
</body>
<script>
    var cvs = document.getElementsByTagName("canvas")[0];
    var ctx = cvs.getContext("2d");

    $('input[type=radio][name=animationType]').change(function () {
        if (this.value == 'StepByStep') {
            document.getElementById('play').setAttribute('disabled','disabled')
            document.getElementById('pause').setAttribute('disabled','disabled')
            document.getElementById('next').removeAttribute("disabled");
            document.getElementById('back').removeAttribute("disabled");
        }
        else if (this.value == 'Automatic'){
            document.getElementById('next').setAttribute('disabled','disabled')
            document.getElementById('back').setAttribute('disabled','disabled')
            document.getElementById('play').removeAttribute("disabled");
            document.getElementById('pause').removeAttribute("disabled");
        }
    });

    function draw(ctx, sx, sy, ex, ey) {
        ctx.beginPath();
        canvas_arrow(ctx, sx, sy, ex, ey);
        ctx.stroke(); 
    }

    function canvas_arrow(context, fromx, fromy, tox, toy) {
        var headlen = 10; // length of head in pixels
        var dx = tox - fromx;
        var dy = toy - fromy;
        var angle = Math.atan2(dy, dx);
        context.moveTo(fromx, fromy);
        context.lineTo(tox, toy);
        context.lineTo(tox - headlen * Math.cos(angle - Math.PI / 6), toy - headlen * Math.sin(angle - Math.PI / 6));
        context.moveTo(tox, toy);
        context.lineTo(tox - headlen * Math.cos(angle + Math.PI / 6), toy - headlen * Math.sin(angle + Math.PI / 6));
    }

    //data for drawline and back for earseline
    data = [];
    data.push([210,20,30,155],[30, 160, 220, 230],[0,0,0,0], [230, 20, 400, 155], [230, 230, 400, 160]);

    back = [];
    back.push([0,0], [30,155], [500, 350], [225,20], [230,157]);

    //animation function
    var index = 0;
    var count = 0;
    var next = 0;
    var intervalID;

    function startAnim() {
        next = 0;
        textArea(next);
        $('.rect').show();
        $('.rects').hide();
        $('.rect').addClass('start');
        $('.rect').removeClass('mtbuffer');
        $('.rect').removeClass('mbbuffer');
        count = 0;
        clearInterval(intervalID);
        ctx.clearRect(0, 0, cvs.width, cvs.height);
        index = 0;
    }

    function nextAnim() {
        $('.rect').removeClass('start');
        if(index == 2) {
            $('.rect').removeClass('mbbuffer');
            $('.rect').addClass('mtbuffer');
            index++;
        }else {
            let StartX = data[index][0];
            let StartY = data[index][1];
            let EndX = data[index][2];
            let EndY = data[index][3];

            draw(ctx, StartX, StartY, EndX, EndY);
            index++;
        }
        next++;
        textArea(next);
    }

    function backAnim() {
        index--;
        if(index == 2) {
            $('.rect').removeClass('mtbuffer');
            $('.rect').addClass('mbbuffer');
        }else{
            let StartX = back[index][0];
            let StartY = back[index][1];
            ctx.clearRect(StartX, StartY, cvs.width, cvs.height);
        }
        next--;
        textArea(next);
    }

    var pause = false;

    function playAnim() {
        $('.rect').removeClass('start');
        intervalID = setInterval(function(){
            if(count < 5)
            {
                if(count == 2) {
                    $('.rect').removeClass('pause');
                    $('.rect').addClass('mtbuffer');
                    $('.rect').removeClass('mbbuffer');
                    count += 1;
                    next++;
                }else if(count == 3 && pause == true) {
                    pause = false;
                    count -= 1;
                    next -= 1;
                }else{
                    let StartX = data[count][0];
                    let StartY = data[count][1];
                    let EndX = data[count][2];
                    let EndY = data[count][3];
                    draw(ctx, StartX, StartY, EndX, EndY);
                    count += 1;
                    next++; 
                }
                textArea(next);
            }
        },1500);
    }

    function pauseAnim() {
        pause = true;
        clearInterval(intervalID);
        $('.rect').addClass('pause');
    }

    function endAnim() {
        for(var i = 0; i < 5; i++) {
            $('.rect').hide();
            $('.rects').show();
            let StartX = data[i][0];
            let StartY = data[i][1];
            let EndX = data[i][2];
            let EndY = data[i][3];
            draw(ctx, StartX, StartY, EndX, EndY);
        }
        $('#text').text("");
        $('#text').append("<p style='text-align: left;'>check flag(for writing): <br> if(flag == 1) <br> <strong> keep checking </strong> <br> else (flag == 0) <br> <strong> WRITE </strong> to buffer, set flag = 1" + 
                            "<br><br> check flag(for reading): <br> if(flag == 0) <br> <strong> keep checking </strong> <br> else (flag == 1) <br> <strong> READ</strong>, set flag = 0 </p>");
    }

    function textArea() {
        if(next == 1){
            $('#p1cr').text("Check flag");
            $('#text').text("");
            $('#text').append("<p><br>if(flag == 1) <br> <strong> keep checking </strong> <br> else (flag == 0) <br> <strong> WRITE </strong> to buffer, set flag = 1</p>");
        }else if(next == 2) {
            $('#text').text("Write");
        }else if(next == 3) {
            $('#text').text("");
            $('#p1cr').text("critical section");
            $('#p2cr').text("critical section");
            setTimeout(() => {$('#flagN').text("1")}, 1000);
        }else if(next == 4) {
            $('#p2cr').text("Check flag");
            $('#text').text("");
            $('#text').append("<p><br>if(flag == 0) <br> <strong> keep checking </strong> <br> else (flag == 1) <br> <strong> READ</strong>, set flag = 0</p>");
        }else if(next == 5){
            $('#p2cr').text("critical section");
            $('#text').text("Read");
            $('#flagN').text("0");
        }else{
            $('#text').text("");
            $('#p1cr').text("critical section");
            $('#p2cr').text("critical section");
            $('#flagN').text("0");
        }
    }
   
</script>
</html>
