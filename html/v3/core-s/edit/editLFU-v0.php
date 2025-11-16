<?php 
session_start();
require "../../system.php";
require_once '../tempfunctions.php';
    $mid = '024';
    if(isset($_POST['submit'])) {
        // Sanitize and validate input
        $input = $_POST['input'];
        if (!preg_match('/^[0-9,]*$/', $input)) {//only allow numbers delintied by commas
            echo "<script>alert('Invalid input. Only numbers separated by commas are allowed.')</script>";
            exit();
        }

        // Sanitize and validate output
        $output_lfu = $_POST['output-lfu'];
        $output_format = $_POST['output-format'];
        if (preg_match('/<\s*script/i', $output_lfu) || preg_match('/<\s*script/i', $output_format)) {
            echo "<script>alert('Invalid output. Scripts are not allowed.')</script>";
            exit();
        }

        // Write input and output to files
        $filename = get_temp_file_path($httpcore_a_IO."/m-${mid}/in-${mid}.dat");
     
        file_put_contents($filename, $input);
        
        $_SESSION['temp_files'][$filename] = $filename;

        file_put_contents("/var/www/projects/s23-01/html/files/core-a/m-024/in-024.dat", $input);
        
        
       
        
        

       // do same for output
        $filename = get_temp_file_path("https://cs.newpaltz.edu/p/s23-01/files/core-a/m-${mid}/out-${mid}.dat");
        
        file_put_contents($filename, $output_lfu);
        
        $_SESSION['temp_files'][$filename] = $filename;
    

      

        // Redirect to index page

        header('Location: ../m-024/index.php');
        exit();
    }
    $filename = get_temp_file_path("https://cs.newpaltz.edu/p/s23-01/files/core-a/m-${mid}/in-${mid}.dat");
    $original_input = file_get_contents($filename);

    $filename = "/var/www/projects/s23-01/html/files/core-a/m-024/out-024.dat";
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
        $input='';
        
   
   
       
        if (isset($_SESSION['temp_files']["https://cs.newpaltz.edu/p/s23-01/files/core-a/m-${mid}/in-${mid}.dat"])) {
            $input = file_get_contents($_SESSION['temp_files']["https://cs.newpaltz.edu/p/s23-01/files/core-a/m-${mid}/in-${mid}.dat"]);
        }
        $format = file_get_contents("../../../files/core-a/m-024/format-024.txt");
      
       
      
    ?>
    
    <div class="section" style="display: inline;" >
        <div>
            <form action="editLFU.php" method="POST">
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="input"><span><strong>Input: (origioanl Data: </strong></span> <?php echo htmlentities($original_input);?><span><strong> )</strong></span></label>
    
                        <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($original_input); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-lfu">Output LFU:</label>
                        <textarea  name="output-lfu" id="output-lfu" cols="10" rows="10" readonly class="textarea"><?php echo htmlentities($original_output ); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-format">Output Format Explained:</label>
                        <textarea name="output-format" id="output-format" cols="10" rows="10" readonly class="textarea"><?php echo htmlentities($format); ?></textarea>
                    </div>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit" name="submit">Run and Vizualize</button> 
                        <a class="button is-link is-light" href="../m-024/index.php'">Cancel</a> 
                    </div>
                </div>
            </form> 
        </div>
    </div>      
</body>
</html>