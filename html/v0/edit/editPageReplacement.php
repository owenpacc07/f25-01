<?php 
      
      if(isset($_POST['submit'])){
        if(empty($_POST['input'])){
        } else {
            $filename="../../files/p4-page-input.txt";
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-fifo'])){
        } else {
            $filename="../../files/p4-page-output-fifo.txt";
            $newData = $_POST['output-fifo'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-lru'])){
        } else {
            $filename="../../files/p4-page-output-lru.txt";
            $newData = $_POST['output-lru'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-optimal'])){
        } else {
            $filename="../../files/p4-page-output-optimal.txt";
            $newData = $_POST['output-optimal'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-lfu'])){
        } else {
            $filename="../../files/p4-page-output-lfu.txt";
            $newData = $_POST['output-lfu'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-mfu'])){
        } else {
            $filename="../../files/p4-page-output-mfu.txt";
            $newData = $_POST['output-mfu'];
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

        $input = file_get_contents("/var/www/projects/f22-02/html/files/p4-page-input.txt");
        $fifo = file_get_contents("/var/www/projects/f22-02/html/files/p4-page-output-fifo.txt");
        $lru = file_get_contents("/var/www/projects/f22-02/html/files/p4-page-output-lru.txt");
        $optimal= file_get_contents("/var/www/projects/f22-02/html/files/p4-page-output-optimal.txt");
        $lfu = file_get_contents("/var/www/projects/f22-02/html/files/p4-page-output-lfu.txt");
        $mfu = file_get_contents("/var/www/projects/f22-02/html/files/p4-page-output-mfu.txt");
    ?>
    
    <div class="section" style="display: inline;" >
        <div>
            <form action="editPageReplacement.php" method="POST">
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="input"><span><strong>Input: (Example Data: </strong></span> <?php echo htmlentities($input);?><span><strong> )</strong></span></label>
                        <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-fifo">Output fifo:</label>
                        <textarea name="output-fifo" id="output-fifo" cols="10" rows="10" class="textarea"><?php echo htmlentities($fifo); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-lru">Output lru:</label>
                        <textarea name="output-lru" id="output-lru" cols="10" rows="10" class="textarea"><?php echo htmlentities($lru); ?></textarea>
                    </div>
                </div>
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-optimal">Output optimal:</label>
                        <textarea name="output-optimal" id="output-optimal" cols="10" rows="10" class="textarea"><?php echo htmlentities($optimal); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-lfu">Output lfu:</label>
                        <textarea name="output-lfu" id="output-lfu" cols="10" rows="10" class="textarea"><?php echo htmlentities($lfu); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-mfu">Output mfu:</label>
                        <textarea name="output-mfu" id="output-mfu" cols="10" rows="10" class="textarea"><?php echo htmlentities($mfu); ?></textarea>
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