<?php 
      if(isset($_POST['submit'])){
        if(empty($_POST['input'])){
        } else {
            $filename="../../files/p2/p2-deadlock-input.txt";
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['otable'])){
        } else {
            $filename="../../files/p2/p2-deadlock-output-table.txt";
            $newData = $_POST['otable'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['oanimation'])){
        } else {
            $filename="../../files/p2/p2-deadlock-output-animation.txt";
            $newData = $_POST['oanimation'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['oinstance'])){
        } else {
            $filename="../../files/p2/p2-deadlock-output-instance.txt";
            $newData = $_POST['oinstance'];
            file_put_contents($filename, $newData);
        }
        header('Location: ../p2-deadlock/p2-m1.php'); 
      }
      
    ?>


<html>
<head>
     
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>View Files</title>
    <style>
        body { background-color: #F0FFF0; }
    </style>
</head>
<body>
    
    <?php 
        //include '/var/www/p/f22-02/html/templates/navbar.php'; 
        include '../navbar.php';
        $input = file_get_contents("/var/www/projects/f22-02/html/files/p2/p2-deadlock-input.txt");
        $otable = file_get_contents("/var/www/projects/f22-02/html/files/p2/p2-deadlock-output-table.txt");
        $oanimation = file_get_contents("/var/www/projects/f22-02/html/files/p2/p2-deadlock-output-animation.txt");
        $oinstance = file_get_contents("/var/www/projects/f22-02/html/files/p2/p2-deadlock-output-instance.txt");
    ?>
    
    <div class="section" style="display: inline;" >
        <div>
            <form action="editDeadlock.php" method="POST">
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="input"><span><strong>Input: (Example Data: </strong></span> <?php echo htmlentities($input); ?><span><strong> )</strong></span></label>
                        <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-table">Output table:</label>
                        <textarea name="otable" id="otable" cols="10" rows="10" class="textarea"><?php echo htmlentities($otable); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-animation">Output animation:</label>
                        <textarea name="oanimation" id="oanimation" cols="10" rows="10" class="textarea"><?php echo htmlentities($oanimation); ?></textarea>
                    </div>
                </div>
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-amswer">Output instance:</label>
                        <textarea name="oinstance" id="oinstance" cols="10" rows="10" class="textarea"><?php echo htmlentities($oinstance); ?></textarea>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit" name="submit">Submit</button> 
                        <a class="button is-link is-light" href="../view.php">Cancel</a> 
                    </div>
                </div>
            </form> 
        </div>
    </div>   

</body>

<script>
    //input file
    var procLoad2 = [];

    function readInputTextFile(file)
    {
        var rawFile = new XMLHttpRequest();
        rawFile.open("POST", file, false);
        rawFile.onreadystatechange = function ()
        {
            if(rawFile.readyState === 4)
            {
                if(rawFile.status === 200 || rawFile.status == 0)
                {
                    var allText = rawFile.responseText;
                    allText.split(',').forEach(function(number) {
                        procLoad2.push(number);
                    });
                }
            }
        }
        rawFile.send();
    }
    readInputTextFile("../../files/p2/p2-deadlock-input.txt");

    //random table data
    var outputData = [];
    function TableData(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        for(var i = 0; i < (procLoad2[0] * procLoad2[1]); i++) {
        var randomtest = Math.floor(Math.random() * (max - min + 1)) + min;
        outputData.push(randomtest);
        }
    }

    TableData(0,2);
    
    var table = document.getElementById("otable");
    if(table.value == 0) {
        for(var i = 0; i < outputData.length; i++) {
            if(i % procLoad2[1] == 0){
                var number = Number(procLoad2[1]);
                if((i + number) <= outputData.length) {
                    table.append(outputData.slice(i, i + number));
                    table.append('\n');
                }
            }
        }
    }
    
    //animation data
    function listToMatrix(list, elementsPerSubArray) {
        var matrix = [], i, k;

        for (i = 0, k = -1; i < list.length; i++) {
            if (i % elementsPerSubArray === 0) {
                k++;
                matrix[k] = [];
            }
            matrix[k].push(list[i]);
        }
        return matrix;
    }

    var matrix = listToMatrix(outputData, procLoad2[1]);

    var animation = document.getElementById("oanimation");
    if(animation.value == 0) {
        for(var i = 0; i < matrix.length; i++) {
            for(var j = 0; j < matrix[i].length; j++) {
                if(matrix[i][j] == 1){
                    animation.append(i + ',');
                    animation.append(j + ',');
                    animation.append("holding");
                    animation.append('\n');
                }else if(matrix[i][j] == 2) {
                    animation.append(i + ',');
                    animation.append(j + ',');
                    animation.append("request");
                    animation.append('\n');
                }
            }
        }
    }
    
    //instance data
    var instance = document.getElementById("oinstance");
    function randombetween(min, max) {
        return Math.floor(Math.random()*(max-min+1)+min);
    }

    function generate(max, thecount) {
        var r = [];
        var currsum = 0;
        for(var i=0; i<thecount-1; i++) {
            r[i] = randombetween(1, max-(thecount-i-1)-currsum);
            currsum += r[i];
        }
        r[thecount-1] = max - currsum;
        return r;
    }
    var instanceN = generate(procLoad2[2], procLoad2[1]);
    if(instance.value == 0){
        for(var i = 1; i <= procLoad2[1]; i++) {
        instance.append(i + ',');
        instance.append(instanceN[i - 1]);
        instance.append('\n')
        }
    }
   
</script>
</html>
