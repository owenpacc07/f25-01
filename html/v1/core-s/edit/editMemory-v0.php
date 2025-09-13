<?php 
      
      if(isset($_POST['submit'])){
        if(empty($_POST['input'])){
        } else {
            $filename="../../files/p3-memory-input.txt";
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-firstfit'])){
        } else {
            $filename="../../files/p3-memory-output-firstfit.txt";
            $newData = $_POST['output-firstfit'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-bestfit'])){
        } else {
            $filename="../../files/p3-memory-output-bestfit.txt";
            $newData = $_POST['output-bestfit'];
            file_put_contents($filename, $newData);
        }
        
        if(empty($_POST['output-worstfit'])){
        } else {
            $filename="../../files/p3-memory-output-worstfit.txt";
            $newData = $_POST['output-worstfit'];
            file_put_contents($filename, $newData);
        }
        header('Location: ../view.php');
      }
    ?>

<html>
<head>
    <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
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
        //include '/var/www/p/f21-13/html/templates/navbar.php'; 
        include '../../navbar.php';
        $outputfirstfit = file_get_contents("/var/www/projects/f21-13/html/files/p3-memory-output-firstfit.txt");
        $outputbestfit = file_get_contents("/var/www/projects/f21-13/html/files/p3-memory-output-bestfit.txt");
        $outputworstfit = file_get_contents("/var/www/projects/f21-13/html/files/p3-memory-output-worstfit.txt");
        $input = file_get_contents("/var/www/projects/f21-13/html/files/p3-memory-input.txt"); ?>
    
    <div class="section" style="display: inline;" >
        <div>
            <form action="editMemory.php" method="POST">
                <div>
                    <div class="columns">
                        <div class="field column" style="max-width: 500px;">
                            <label for="input"><span><strong>Input: (Example Data: </strong></span> <?php echo htmlentities($input);?><span><strong> )</strong></span></label>
                            <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
                        </div>
                        <div class="field column" style="max-width: 500px;">
                            <label for="output-firstfit">Output first fit:</label>
                            <textarea name="output-firstfit" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($outputfirstfit); ?></textarea>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="field column" style="max-width: 500px;">
                            <label for="output-bestfit">Output look</label>
                            <textarea name="output-bestfit" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($outputbestfit); ?></textarea>
                        </div>
                        <div class="field column" style="max-width: 500px;">
                            <label for="output-worstfit">Output sstf:</label>
                            <textarea name="output-worstfit" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($outputworstfit); ?></textarea>
                        </div>
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