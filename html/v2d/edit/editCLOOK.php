<?php 
      
      if(isset($_POST['submit'])){
        if(empty($_POST['input'])){
        } else {
            $filename="../../files/p6-disk-input.txt";
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-clook'])){
        } else {
            $filename="../../files/p6-disk-output-clook.txt";
            $newData = $_POST['output-clook'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-look'])){
        } else {
            $filename="../../files/p6-disk-output-look.txt";
            $newData = $_POST['output-look'];
            file_put_contents($filename, $newData);
        }
        
        if(empty($_POST['output-sstf'])){
        } else {
            $filename="../../files/p6-disk-output-sstf.txt";
            $newData = $_POST['output-sstf'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-clook'])){
        } else {
            $filename="../../files/p6-disk-output-clook.txt";
            $newData = $_POST['output-clook'];
            file_put_contents($filename, $newData);
        }
        
        if(empty($_POST['output-fcfs'])){
        } else {
            $filename="../../files/p6-disk-output-fcfs.txt";
            $newData = $_POST['output-fcfs'];
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
        $outputclook = file_get_contents("/var/www/projects/f22-02/html/files/p6-disk-output-clook.txt");
        $outputlook = file_get_contents("/var/www/projects/f22-02/html/files/p6-disk-output-look.txt");
        $outputsstf = file_get_contents("/var/www/projects/f22-02/html/files/p6-disk-output-sstf.txt");
        $outputcscan = file_get_contents("/var/www/projects/f22-02/html/files/p6-disk-output-cscan.txt");
        $outputfcfs = file_get_contents("/var/www/projects/f22-02/html/files/p6-disk-output-fcfs.txt");
        $input = file_get_contents("/var/www/projects/f22-02/html/files/p6-disk-input.txt"); ?>
    
    <div class="section" style="display: inline;" >
        <div>
            <form action="editCLOOK.php" method="POST">
                <div>
                    <div class="columns">
                        <div class="field column" style="max-width: 500px;">
                            <label for="input"><span><strong>Input: (Example Data: </strong></span> <?php echo htmlentities($input);?><span><strong> )</strong></span></label>
                            <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
                        </div>
                        <div class="field column" style="max-width: 500px;">
                            <label for="output-clook">Output clook:</label>
                            <textarea name="output-clook" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($outputclook); ?></textarea>
                        </div>
                        <div class="field column" style="max-width: 500px;">
                            <label for="output-clook">Output cscan:</label>
                            <textarea name="output-cscan" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($outputcscan); ?></textarea>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="field column" style="max-width: 500px;">
                            <label for="output-look">Output look</label>
                            <textarea name="output-look" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($outputlook); ?></textarea>
                        </div>
                        <div class="field column" style="max-width: 500px;">
                            <label for="output-sstf">Output sstf:</label>
                            <textarea name="output-sstf" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($outputsstf); ?></textarea>
                        </div>
                        <div class="field column" style="max-width: 500px;">
                            <label for="output-clook">Output fcfs:</label>
                            <textarea name="output-fcfs" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($outputfcfs); ?></textarea>
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