<!DOCTYPE html>
<html lang="en">

    <?php 
        /*
            Contributor Spring 2023 - Dakota Marino
        */
        include '../../system.php'; 
        $mid = "044";  
           
        if(isset($_POST['submit'])) {
            if(empty($_POST['input'])) {
            } else {
                $myfile = fopen($io_files_a . "/m-" . $mid . "/in-" . $mid . ".dat", "w") or die("Unable to open file!");
                fwrite($myfile, $_POST['input']);
                fclose($myfile);
            }
            if($_POST['output3'] == "calculate automatically") {
                exec('java -cp ' . $cgibin_core_a . '/m-' . $mid . '/ m' . $mid . ' 2>&1', $output2);
                print_r($output2);
            } else {
                $myfile = fopen($io_files_a . "/m-" . $mid . "/out-" . $mid . ".dat", "w") or die("Unable to open file!");
                fwrite($myfile, $_POST['output3']) . "\n";
                fclose($myfile);
                
                $myfile2 = fopen($io_files_a . "/m-" . $mid . "/flag-file.txt", "w") or die("Unable to open file!");
                fwrite($myfile2, 1)."\n";
                fclose($myfile2);
                echo "flag-file set to 1";
            }
            header('Location: ./index.php');
        }
    ?>

    <head>
        <link rel="icon" href="/p/f21-13/files/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <title>View Files</title>
        <style>
            body { background-color: #F0FFF0; }
        </style>
        <script>
        </script>
    </head>
    <body>
        
        <?php 
            include '../../navbar.php';
            $input = "0 699\n50\n10 183 40 8 4600";
            $output = "calculate automatically";
        ?>
        
        <div class="section has-text-centered" >
            <div>
                <h1 id="title">Edit Data - LOOK</h1>
                <br />
                <form action="editLOOK.php" method="POST">
                    <div>
                        <div class="columns is-centered has-text-centered">
                            <div class="field column" style="max-width: 500px;">
                                <label for="input"><span><strong>Input:</strong></label>
                                <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
                            </div>
                            <div class="field column" style="max-width: 500px;">
                                <label for="output-clook"><strong>Output:</strong></label>
                                <textarea name="output3" id="output" cols="10" rows="10" class="textarea"><?php echo htmlentities($output); ?></textarea>
                            </div>
                            <div class="field column" style="max-width: 500px;">
                                <label for="output-clook"><strong>Example:</strong></label>
                                <textarea disabled name="output-cscan" id="info" cols="10" rows="10" class="textarea"><?php echo "# do not enter any text followed by '#'\n\n0 699 # size of disk\n50 # head\n10 183 4 4600 # input data "; ?></textarea>
                            </div>
                        </div>
                        
                    </div>
                    <div class="field is-grouped">
                        <div class="control">
                            <button class="button is-link" type="submit" name="submit">Submit</button> 
                            <a class="button is-link is-light" href="./index.php">Cancel</a> 
                        </div>
                    </div>
                </form> 
            </div>
        </div>         
    </body>
</html>