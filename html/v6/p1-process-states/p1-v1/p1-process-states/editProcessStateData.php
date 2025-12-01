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
        <?php include '../../navbar.php'; 
        
        $input = file_get_contents("/var/www/projects/f22-02/html/files/p1/ps.input.txt"); 
        $output = file_get_contents("/var/www/projects/f22-02/html/files/p1/ps.output.txt"); 

        ?>
        <br>
        <div class="d-flex align-items-center justify-content-center">
        <div class="columns">
                        <div class="field column" style="max-width: 500px;">
        <form action="editProcessStateData.php" method="POST">
            <label for="input"><span><strong>Input: (Example Data: </strong></span> <?php echo "/var/userof/a.java "?><span><strong> )</strong></span></label>
            <textarea name="input" id="input" cols="5" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
            <label for="output"><span><strong>Output: (Example Data: </strong></span> <?php echo "Proccess ID: 2579 Process State: Ready Program Counter: 1 Registers: 17 Memory Start: 007 Memory Limit: 654 List of Open Files: 85"?><span><strong> )</strong></span></label>
            <textarea name="output" id="output" cols="5" rows="10" class="textarea"><?php echo htmlentities($output); ?></textarea>
            <div class="control">
                    <button class="button is-link" type="submit" name="submit">Submit</button> 
                    <a class="button is-link is-light" href="./index.php">Back</a> 
            </div>
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
            $filename="../../files/p1/ps.input.txt";
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output'])){
        } else {
            $filename="../../files/p1/ps.output.txt";
            $newData = $_POST['output'];
            file_put_contents($filename, $newData);
        }
        echo "<meta http-equiv='refresh' content='0'>";
      }
?>