<?php 
      
      if(isset($_POST['submit'])){
        if(empty($_POST['input'])){
        } else {
            $filename="../../files/p1-cpu-input.txt";
            $newData = $_POST['input'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-fcfs'])){
        } else {
            $filename="../../files/p1-cpu-output-fcfs.txt";
            $newData = $_POST['output-fcfs'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-sjf'])){
        } else {
            $filename="../../files/p1-cpu-output-sjf.txt";
            $newData = $_POST['output-sjf'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-sjf-p'])){
        } else {
            $filename="../../files/p1-cpu-output-sjf-p.txt";
            $newData = $_POST['output-sjf-p'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-priority'])){
        } else {
            $filename="../../files/p1-cpu-output-priority.txt";
            $newData = $_POST['output-priority'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-priority-p'])){
        } else {
            $filename="../../files/p1-cpu-output-priority-p.txt";
            $newData = $_POST['output-priority-p'];
            file_put_contents($filename, $newData);
        }
        if(empty($_POST['output-roundrobin'])){
        } else {
            $filename="../../files/p1-cpu-output-roundrobin.txt";
            $newData = $_POST['output-roundrobin'];
            file_put_contents($filename, $newData);
        }
        header('Location: ../../view.php');
       
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
        $input = file_get_contents("/var/www/projects/f21-13/html/files/p1-cpu-input.txt");
        $fcfs = file_get_contents("/var/www/projects/f21-13/html/files/p1-cpu-output-fcfs.txt");
        $sjf = file_get_contents("/var/www/projects/f21-13/html/files/p1-cpu-output-sjf.txt");
        $sjfp= file_get_contents("/var/www/projects/f21-13/html/files/p1-cpu-output-sjf-p.txt");
        $priority = file_get_contents("/var/www/projects/f21-13/html/files/p1-cpu-output-priority.txt");
        $priorityp = file_get_contents("/var/www/projects/f21-13/html/files/p1-cpu-output-priority-p.txt");
        $roundrobin = file_get_contents("/var/www/projects/f21-13/html/files/p1-cpu-output-roundrobin.txt");
    ?>
    
    <div class="section" style="display: inline;" >
        <div>
            <form action="editCPU.php" method="POST">
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="input"><span><strong>Input: (Example Data: </strong></span> <?php echo htmlentities($input);?><span><strong> )</strong></span></label>
                        <textarea name="input" id="input" cols="10" rows="10" class="textarea"><?php echo htmlentities($input); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-fcfs">Output fcfs:</label>
                        <textarea name="output-fcfs" id="output-fcfs" cols="10" rows="10" class="textarea"><?php echo htmlentities($fcfs); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-sjf">Output sjf:</label>
                        <textarea name="output-sjf" id="output-sjf" cols="10" rows="10" class="textarea"><?php echo htmlentities($sjf); ?></textarea>
                    </div>
                </div>
                <div class="columns">
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-sjf-p">Output sjf-p:</label>
                        <textarea name="output-sjf-p" id="output-sjf-p" cols="10" rows="10" class="textarea"><?php echo htmlentities($sjfp); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-priority">Output priority:</label>
                        <textarea name="output-priority" id="output-priority" cols="10" rows="10" class="textarea"><?php echo htmlentities($priority); ?></textarea>
                    </div>
                    <div class="field column" style="max-width: 500px;">
                        <label for="output-priority-p">Output priority-p:</label>
                        <textarea name="output-priority-p" id="output-priority-p" cols="10" rows="10" class="textarea"><?php echo htmlentities($priorityp); ?></textarea>
                    </div>
                </div>
               
                <div class="field" style="max-width: 500px;">
                    <label for="output-roundrobin">Output roundrobin:</label>
                    <textarea name="output-roundrobin" id="output-roundrobin" cols="10" rows="10" class="textarea"><?php echo htmlentities($roundrobin); ?></textarea>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit" name="submit">Submit</button> 
                        <a class="button is-link is-light" href="../../view.php">Cancel</a> 
                    </div>
                </div>
            </form> 
        </div>
    </div>   

</body>
</html>