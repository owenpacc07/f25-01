<!DOCTYPE html>
<html>
    <head>
    <title>Process States</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/konva@8.3.3/konva.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>

    <body>
        <!-- Credit to editMemory.php member, who's file I used as a template to create this for the input / output file -->  
        <?php include '../navbar.php'; 
        
        $input = file_get_contents("/var/www/projects/f22-02/html/files/p1/ps-input.txt"); 
        $output = file_get_contents("/var/www/projects/f22-02/html/files/p1/ps-output.txt"); 

        ?>
        <br>
        <div class="d-flex align-items-center justify-content-center">
        <div class="columns">
        <div class="field column" style="max-width: 500px;">
        <form action="editProcessStateData.php" method="POST">
            <label for="input"><span><strong>Input: (Example Data: </strong></span> <?php echo "/var/userof/a.java "?><span><strong> )</strong></span></label>
            <br>
            <textarea name="input" id="input" cols="40" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
            <p><b>Output: </b></p>
            <label for="processID">ProcessID: </label>
            <input type="number" min="0" id="processID" placeholder="ProcessID" name="processID"><br><br>
            <label for="programCounter">Program Counter: </label>
            <input type="number" min="0" id="programCounter" placeholder="Program Counter" name="programCounter"><br><br>
            <label for="registers">Registers: </label>
            <input type="number" min="0" id="registers" placeholder="Registers" name="registers"><br><br>
            <label for="memoryStart">Memory Start: </label>
            <input type="number" min="0" id="memoryStart" placeholder="Memory Start" name="memoryStart"><br><br>
            <label for="memoryLimit">Memory Limit:</label>
            <input type="number" min="0" id="memoryLimit" placeholder="Memory Limit" name="memoryLimit"><br><br>
            <label for="listOfOpenFiles">List of Open Files: </label>
            <input type="number" min="0" id="listOfOpenFiles" placeholder="List Of Open Files" name="listOfOpenFiles"><br><br>

            <button class="btn btn-primary" type="submit" name="submit">Submit</button> 
            <a href="./index.php" class="btn btn-primary btn-md" tabindex="-1" role="button">Back</a> 
        </form>
        </div>
</div>
        </div>
    </body>
</html>

<?php
      if(isset($_POST['submit'])){
        if(empty($_POST['input'])){
        } else {
            $filename="../../files/p1/ps-input.txt";
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        // If not empty.. do something.
        if(empty($_POST['processID']) || empty($_POST['programCounter']) ||empty( $_POST['memoryStart']) || empty($_POST['memoryLimit']) || empty($_POST['registers']) || empty($_POST['listOfOpenFiles']) ){
            echo "<script>alert('Please submit data for all fields.');</script>";
        } else {
            $filename="../../files/p1/ps-output.txt";
            $newData = "".$_POST['processID']."\n".$_POST['programCounter']."\n".$_POST['registers']."\n".$_POST['memoryStart']."\n".$_POST['memoryLimit']."\n".$_POST['listOfOpenFiles']."" ;
            file_put_contents($filename, $newData);
            header('Location: ./index.php');
        }
      }
?>