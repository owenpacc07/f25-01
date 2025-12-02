<!DOCTYPE html>
<html>
    <head>
    <title>Process States</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/konva@8.3.3/konva.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>

    <body>
        <?php include '../../navbar.php'; ?> 
       <div class="d-flex flex-row-reverse">
        <a href="./editProcessStateData.php">Edit</a> 
        </div>
        <br>
        <div class="d-flex align-items-center justify-content-center">
            <h1 id="title"> <b> Process States </b> </h1>
        </div>
        <div class="d-flex align-items-center justify-content-center">
            <form id="processStateType">
                    <input type="radio" name="processStateType" value="newPS" checked onclick="startVisual()"/> New Process Creation<br/>
                    <input type="radio" name="processStateType" value="normalPS" onclick="startVisual()"/> Normal Process State<br/>
                    <input type="radio" name="processStateType" value="pausedPS" onclick="startVisual()"/> Paused Process State<br/>
                    <input type="radio" name="processStateType" value="ioPS" onclick="startVisual()"/> I/O Request Process State<br/>
            </form>
        </div>
        <br>
        <div class="d-flex align-items-center justify-content-center">
            <div id="container">
            </div>
        </div>
         <br>
        <div class="d-flex align-items-center justify-content-center">
            <form id=step>
                <button id="next" type="button" onclick="nextStep()">Next</button>
                <button id="back" type="button" disabled onclick="previousStep()">Back</button>
                <button id="end" type="button" onclick="endVisual()">End</button>
                <button id="reset" type="button" disabled onclick="resetButton()">Reset</button>
            </form>
        </div>
        <br>
        <br>
        <script src="./animations.js"></script>
    </body>
</html>