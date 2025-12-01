<?php 
      if(isset($_POST['submit'])){
        if(empty($_POST['input'])){
        } else {
            $filename="../../files/p4/MA-Input.txt";
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['otable'])){
        } else {
            $filename="../../files/p4/MA-Output.txt";
            $newData = $_POST['otable'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['oanimation'])){
        } else {
            $filename="../../files/p4/MA-Animation.txt";
            $newData = $_POST['oanimation'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['oanswer'])){
        } else {
            $filename="../../files/p4/MA-Answer.txt";
            $newData = $_POST['oanswer'];
            file_put_contents($filename, $newData);
        }
        header('Location: ../view.php');
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
        $input = file_get_contents("/var/www/projects/f22-02/html/files/p4/MA-Input.txt");
       $otable = file_get_contents("/var/www/projects/f22-02/html/files/p4/MA-Output.txt");
        $oanimation = file_get_contents("/var/www/projects/f22-02/html/files/p4/MA-Animation.txt");
        $oanswer = file_get_contents("/var/www/projects/f22-02/html/files/p4/MA-Answer.txt");
    ?>
    
    <div class="section" style="display: inline;" >
        <div>
            <form action="editMemoryAccess.php" method="POST">
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
                        <label for="output-amswer">Output answer:</label>
                        <textarea name="oanswer" id="oanswer" cols="10" rows="10" class="textarea"><?php echo htmlentities($oanswer); ?></textarea>
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
</html>
