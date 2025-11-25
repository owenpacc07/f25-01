<?php
session_start();
//if no user is logged in, go to login page
if ($_SESSION['logged_in'] != 1) {
    header('Location: login.php');
}

?>
<!--this display function is called later on if an admin user is logged in. The html in this function is the contents of the view.php page 
and is a table containing the algorithm, inputs and outputs.  -->
<?php function display()
{ ?>
    <br>
    <p class="title has-text-link pl-5">Submissions for Algorithms:</p>

    <table class="table is-bordered is-striped is-hoverable">
        <tr>
            <th>ALGORITHM</th>
            <th>INPUT</th>
            <th>OUTPUT</th>
        </tr>
        <tbody>
            <?php
            $unitName = $query_search = $input = $output = "";
            require_once '../config-legacy.php';
            $files = scandir('../../files/users/user' . $_SESSION['userid'] . '/');
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $query_search = "select Name from units where UnitID='" . substr($file, 4) . "';";
                    $result = mysqli_query($link, $query_search);
                    $unitName = mysqli_fetch_array($result)[0];
                    $input = file_get_contents('../../files/users/user' . $_SESSION['userid'] . '/' . $file . '/input.txt');
                }
            }

            ?>
            <tr>
                <th class="max-th-width-270"><a href="edit/editCPU.php">CPU Scheduling</a><br><br><br>
                    <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f23-01/v2/1-cpu/index.php">Run</a>
                </th>
                <th class="max-th-width-550"><?php readfile("../../p1-cpu-input.txt"); ?></th>
                <th><span class="is-green">fcfs:</span> <?php readfile("../../p1-cpu-output-fcfs.txt"); ?>
                    <br><span class="is-green">sjf:</span> <?php readfile("../../p1-cpu-output-sjf.txt"); ?>
                    <br><span class="is-green">sjf-p:</span> <?php readfile("../../p1-cpu-output-sjf-p.txt"); ?>
                    <br><span class="is-green">priority:</span> <?php readfile("../../p1-cpu-output-priority.txt"); ?>
                    <br><span class="is-green">priority-p:</span> <?php readfile("../../p1-cpu-output-priority-p.txt"); ?>
                    <br><span class="is-green">round robin:</span><?php readfile("../../p1-cpu-output-roundrobin.txt"); ?>
                </th>
            </tr>
            <tr>
                <th class="max-th-width-270"><a href="edit/editMemory.php">Memory Allocation</a>
                    <br>
                    <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f23-01/v2/4-memory/index.php">Run</a>
                </th>
                <th class="max-th-width-550"><?php readfile("../../p3-memory-input.txt"); ?> </th>
                <th><span class="is-green">firstfit:</span> <?php readfile("../../p3-memory-output-firstfit.txt"); ?>
                    <br><span class="is-green">bestfit:</span> <?php readfile("../../p3-memory-output-bestfit.txt"); ?>
                    <br><span class="is-green">worstfit:</span> <?php readfile("../../p3-memory-output-worstfit.txt"); ?>
                </th>
            </tr>
            <tr>
                <th class="max-th-width-270"><a href="edit/editPageReplacement.php">Page Replacement</a>
                    <br><br>
                    <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f23-01/v2/2-replace/index.php">Run</a>
                </th>
                <th class="max-th-width-550"><?php readfile("../../p4-page-input.txt"); ?> </th>
                <th><span class="is-green">fifo: </span> <?php readfile("../../p4-page-output-fifo.txt"); ?>
                    <br><span class="is-green">lru: </span> <?php readfile("../../p4-page-output-lru.txt"); ?>
                    <br><span class="is-green">optimal:</span> <?php readfile("../../p4-page-output-optimal.txt"); ?>
                    <br><span class="is-green">lfu: </span> <?php readfile("../../p4-page-output-lfu.txt"); ?>
                    <br><span class="is-green">mfu: </span> <?php readfile("../../p4-page-output-mfu.txt"); ?>
                </th>
            </tr>
            <tr>
                <th class="max-th-width-270"><a href="edit/editContinuous.php">File Allocation (Continuous)</a>
                    <br>
                    <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f23-01/v2/5-files/index.php">Run</a>
                </th>

                <th class="max-th-width-550"><?php readfile("../../p5-file-input-continuous.txt"); ?></th>
                <th><span class="is-green">continuous:</span> <?php readfile("../../p5-file-output.txt"); ?></th>
            </tr>
            <tr>
                <th class="max-th-width-270"><a href="edit/editLinked.php">File Allocation (Linked)</a>
                    <br>
                    <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f23-01/v2/5-files/index.php">Run</a>
                </th>
                <th class="max-th-width-550"><?php readfile("../../p5-file-input-linked.txt"); ?></th>
                <th><span class="is-green">linked:</span> <?php readfile("../../p5-file-output.txt"); ?></th>
            </tr>
            <tr>
                <th class="max-th-width-270"><a href="edit/editIndexed.php">File Allocation (Indexed)</a>
                    <br>
                    <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f23-01/v2/5-files/index.php">Run</a>
                </th>
                <th class="max-th-width-550"><?php readfile("../../p5-file-input-indexed.txt"); ?></th>
                <th><span class="is-green">indexed:</span> <?php readfile("../../p5-file-output.txt"); ?></th>
            </tr>
            <tr>
                <th class="max-th-width-270"><a href="edit/editCLOOK.php">Disk Scheduling</a>
                    <br>
                    <a class="px-4 button mt-2 has-background-primary-light" href="https://cs.newpaltz.edu/p/f23-01/v2/3-disk/index.php">Run</a>
                </th>
                <th class="max-th-width-550"><?php readfile("../../p6-disk-input.txt"); ?></th>
                <th><span class="is-green">clook:</span> <?php readfile("../../p6-disk-output-clook.txt"); ?>
                    <br><span class="is-green">look:</span> <?php readfile("../../p6-disk-output-look.txt"); ?>
                    <br><span class="is-green">sstf:</span> <?php readfile("../../p6-disk-output-sstf.txt"); ?>
                    <br><span class="is-green">cscan:</span> <?php readfile("../../p6-disk-output-cscan.txt"); ?>
                    <br><span class="is-green">fcfs:</span> <?php readfile("../../p6-disk-output-fcfs.txt"); ?>
                </th>

            </tr>

        </tbody>
    </table>
<?php } ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="icon" href="/p/f23-01/files/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>View Files</title>
</head>

<body>
    <?php include '../navbar.php'; ?>
    <?php
    display();
    ?>

</body>

</html>