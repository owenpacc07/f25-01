<?php session_start();
require "../../system.php"; //system varibales
require_once '../tempfunctions.php'; //creating session files
      $mid = '023';
      if(isset($_POST['submit'])) {
        
        // Sanitize and validate input
        $input = $_POST['input'];
        $output = $_POST['output-lru'];
        $output_format = $_POST['output-format'];
        if (!preg_match('/^[0-9,]*$/', $input)) {
            echo "<script>alert('Invalid input. Only numbers separated by commas are allowed.')</script>";
            exit();
        }
        if (preg_match('/<\s*script/i', $output) || preg_match('/<\s*script/i', $output_format)) {
            echo "<script>alert('Invalid output. Scripts are not allowed.')</script>";
            exit();
        }
        if (isset($_POST['viz'])){
            if (empty($_POST['input'])) {
            } else {
                $filename = get_temp_file_path($httpcore_a_IO."/m-${mid}/in-${mid}.dat");
     
            file_put_contents($filename, $input);
            
            $_SESSION['temp_files'][$filename] = $filename;
        
            file_put_contents("/var/www/projects/s23-01/html/files/core-a/m-${mid}/in-${mid}.dat", $input);
            }
            if (empty($_POST['output'])) {
            } else {
                $filename = "/var/www/projects/s23-01/html/files/core-a/m-023/out-023.dat";
                file_put_contents($filename, $output);
                $original_output = file_get_contents($filename);
                
            }
            header('Location: ./index.php');
        }
        if (isset($_POST['run'])) {
            $filename = get_temp_file_path($httpcore_a_IO."/m-${mid}/in-${mid}.dat");
     
            file_put_contents($filename, $input);
            
            $_SESSION['temp_files'][$filename] = $filename;
        
            file_put_contents("/var/www/projects/s23-01/html/files/core-a/m-${mid}/in-${mid}.dat", $input);
       
    
           // do same for output
            $filename = get_temp_file_path("https://cs.newpaltz.edu/p/s23-01/files/core-a/m-${mid}/out-${mid}.dat");
            
            file_put_contents($filename, $output);
            
            $_SESSION['temp_files'][$filename] = $filename;
        
    
            header('Location: ./index.php');
            exit();
        }
        else{
            echo "<script>alert('Please Select to Vizualize or Run and Vizualize.')</script>";
        }

        
       
       

         // Write input 
       
    }
    
    $filename = get_temp_file_path("https://cs.newpaltz.edu/p/s23-01/files/core-a/m-${mid}/in-${mid}.dat");
    $original_input = file_get_contents($filename);

    $filename = "/var/www/projects/s23-01/html/files/core-a/m-023/out-023.dat";
    $original_output = file_get_contents($filename);
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

        if (isset($_SESSION['temp_files']["https://cs.newpaltz.edu/p/s23-01/files/core-a/m-${mid}/in-${mid}.dat"])) {
            $input = file_get_contents($_SESSION['temp_files']["https://cs.newpaltz.edu/p/s23-01/files/core-a/m-${mid}/in-${mid}.dat"]);
        }
        $format = file_get_contents("../../../files/core-a/m-023/format-023.txt");
      
    ?>
    
    <div class="section" style="display: inline;" >
        <div>
            <form action="editLRU.php" method="POST">
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="input"><span><strong>Input: (Example Data: </strong></span> <?php echo htmlentities($original_input);?><span><strong> )</strong></span></label>
                        <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($original_input); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-lru">Output Lru:</label>
                        <textarea  name="output-lru" id="output-lru" cols="10" rows="10" class="textarea"><?php echo htmlentities($original_output); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-format">Output Format Explained:</label>
                        <textarea readonly name="output-format" id="output-format" cols="10" rows="10" class="textarea"><?php echo htmlentities($format); ?></textarea>
                    </div>
                </div>
                <div>
                        <input type="checkbox" id="viz" name="viz">
                        <label for="viz"> Visualize</label><br>
                        <input type="checkbox" id="run" name="run">
                        <label for="run"> Run and Visualize</label><br>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit" name="submit">Submit Edits</button> 
                        <a class="button is-link is-light" href="./index.php">Cancel</a> 
                    </div>
                </div>
            </form> 
        </div>
    </div>      
</body>
</html>